document.addEventListener('DOMContentLoaded', function () {
    // ----------------------------------------------------
    // Event Delegation: Handles dynamic elements (modals & ajax loads)
    // ----------------------------------------------------
    document.addEventListener('click', function (e) {
        // Handle Password Visibility Toggle
        const toggleBtn = e.target.closest('.auth-password-toggle');
        if (toggleBtn) {
            e.preventDefault();
            const container = toggleBtn.closest('.auth-password-group');
            if (container) {
                const input = container.querySelector('input');
                const icon = toggleBtn.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        }

        // Handle Sign-in Method Selection (Email/Phone/Username)
        const methodBtn = e.target.closest('.auth-method-btn');
        if (methodBtn) {
            e.preventDefault();
            const type = methodBtn.dataset.method;
            const wrapper = methodBtn.closest('.auth-form-wrapper');
            if (wrapper) {
                const selectionWrap = wrapper.querySelector('.auth-methods-selection');
                const formContainer = wrapper.querySelector('.auth-form-container');
                const title = wrapper.querySelector('.auth-input-title');
                const input = wrapper.querySelector('.auth-identifier-input');
                const icon = wrapper.querySelector('.auth-input-icon');
                const ccWrap = wrapper.querySelector('.auth-country-code-wrap');
                const typeInput = wrapper.querySelector('.auth-type-input');

                const oldType = selectionWrap.dataset.currentType;
                if (oldType) {
                    input.dataset[oldType + 'Value'] = input.value;
                }

                selectionWrap.classList.add('hidden');
                selectionWrap.dataset.currentType = type;
                if (typeInput) typeInput.value = type;
                
                input.value = input.dataset[type + 'Value'] || '';
                
                formContainer.classList.remove('hidden');

                if (type === 'email') {
                    title.innerText = 'Email Address';
                    input.type = 'email';
                    input.placeholder = 'name@example.com';
                    icon.className = 'fa-regular fa-envelope';
                    if (ccWrap) ccWrap.classList.add('hidden');
                } else if (type === 'phone') {
                    title.innerText = 'Phone Number';
                    input.type = 'tel';
                    input.placeholder = '1700000000';
                    icon.className = 'fa-solid fa-phone';
                    if (ccWrap) ccWrap.classList.remove('hidden');
                } else {
                    title.innerText = 'Username';
                    input.type = 'text';
                    input.placeholder = 'Enter your username';
                    icon.className = 'fa-regular fa-user';
                    if (ccWrap) ccWrap.classList.add('hidden');
                }

                input.focus();
            }
        }

        // Handle Change Method (Back)
        const backBtn = e.target.closest('.auth-change-method-btn');
        if (backBtn) {
            e.preventDefault();
            const wrapper = backBtn.closest('.auth-form-wrapper');
            if (wrapper) {
                wrapper.querySelector('.auth-form-container').classList.add('hidden');
                wrapper.querySelector('.auth-methods-selection').classList.remove('hidden');
                hideFeedback(wrapper);
            }
        }
    });

    // ----------------------------------------------------
    // Gateway Feedback Helper
    // ----------------------------------------------------
    function hideFeedback(wrapper) {
        const wrap = wrapper.querySelector('.auth-gateway-feedback');
        if (wrap) wrap.classList.add('hidden');
    }

    function showFeedback(wrapper, message, isSuccess = true) {
        const wrap = wrapper.querySelector('.auth-gateway-feedback');
        if (!wrap) return;
        const icon = wrap.querySelector('.auth-feedback-icon');
        const text = wrap.querySelector('.auth-feedback-text');

        wrap.className = 'auth-gateway-feedback p-4 rounded-xl text-sm transition-all duration-300 ease-in-out transform flex items-center gap-3 ' +
            (isSuccess
                ? 'bg-emerald-500/20 border border-emerald-500/40 text-emerald-100 shadow-[0_0_20px_rgba(16,185,129,0.15)]'
                : 'bg-amber-500/20 border border-amber-500/40 text-amber-100 shadow-[0_0_20px_rgba(245,158,11,0.15)]');

        if (icon) {
            icon.innerHTML = isSuccess
                ? '<i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>'
                : '<i class="fa-solid fa-circle-info text-amber-400 text-lg"></i>';
        }

        if (text) text.innerText = message;
        wrap.classList.remove('hidden');
        wrap.classList.add('animate-pulse');
        setTimeout(() => wrap.classList.remove('animate-pulse'), 1000);
    }

    // ----------------------------------------------------
    // Gateway Form Submit
    // ----------------------------------------------------
    document.addEventListener('submit', async function (e) {
        if (e.target.matches('.auth-gateway-form')) {
            e.preventDefault();
            const form = e.target;
            const wrapper = form.closest('.auth-form-wrapper');

            const input = form.querySelector('.auth-identifier-input');
            const val = input.value.trim();
            if (!val) return;

            const typeField = form.closest('.auth-form-wrapper').querySelector('.auth-methods-selection').dataset.currentType || 'email';

            let payload = { identifier: val };

            if (typeField === 'phone') {
                const ccInput = form.querySelector('input[name="code"]');
                const ccVal = ccInput ? ccInput.value.trim() : '';
                payload = { identifier: val, code: ccVal };
            }

            const btn = form.querySelector('button[type="submit"]');
            const btnText = btn.querySelector('.btn-text');
            const btnIcon = btn.querySelector('.btn-icon');

            // Loading state
            btn.disabled = true;
            if (btnText) btnText.innerText = 'Checking Account...';
            if (btnIcon) btnIcon.className = 'btn-icon fa-solid fa-circle-notch fa-spin text-xs';
            hideFeedback(wrapper);

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    || document.querySelector('input[name="_token"]')?.value;

                const res = await fetch(form.dataset.actionUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (data.exists) {
                    showFeedback(wrapper, data.message || 'Account found! Redirecting...', true);
                    setTimeout(() => {
                        window.location.href = data.data.redirect_url;
                    }, 2000);
                } else {
                    showFeedback(wrapper, data.message || 'No account found. Create one...', false);
                    setTimeout(() => {
                        window.location.href = data.data.redirect_url;
                    }, 2000);
                }
            } catch (err) {
                console.error('Identifier check error:', err);
                window.location.href = form.dataset.signinUrl;
            }
        }
    });

    // ----------------------------------------------------
    // Standard Form Submit Loader (for Sign In, Sign Up, etc)
    // ----------------------------------------------------
    document.addEventListener('submit', function (e) {
        if (e.target.matches('.auth-standard-form')) {
            const form = e.target;
            const btn = form.querySelector('button[type="submit"]');
            if (btn) {
                const btnText = btn.querySelector('.btn-text');
                const btnIcon = btn.querySelector('.btn-icon');
                btn.disabled = true;
                if (btnText) btnText.innerText = 'Processing...';
                if (btnIcon) btnIcon.className = 'btn-icon fa-solid fa-circle-notch fa-spin text-xs';
            }
        }
    });

    // ----------------------------------------------------
    // Handle OTP Input Auto-Focus
    // ----------------------------------------------------
    function initOtpInputs(container) {
        if (!container) return;

        const inputs = container.querySelectorAll('.otp-input');
        const hiddenInput = container.querySelector('.otp-hidden-input');
        if (!inputs.length || !hiddenInput) return;

        const updateHiddenInput = () => {
            let code = '';
            inputs.forEach(input => code += input.value);
            hiddenInput.value = code;
        };

        inputs.forEach((input, index) => {
            // Prevent binding multiple times if re-initialized
            if (input.dataset.otpInitialized) return;
            input.dataset.otpInitialized = 'true';

            input.addEventListener('input', function () {
                if (this.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                updateHiddenInput();
            });

            input.addEventListener('keydown', function (e) {
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', function (e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').slice(0, inputs.length).split('');
                if (pastedData.length) {
                    inputs.forEach((inp, i) => {
                        if (pastedData[i]) inp.value = pastedData[i];
                    });
                    if (pastedData.length < inputs.length) {
                        inputs[pastedData.length].focus();
                    } else {
                        inputs[inputs.length - 1].focus();
                    }
                    updateHiddenInput();
                }
            });
        });

        // Focus first input on load
        if (inputs.length > 0) {
            inputs[0].focus();
        }
    }

    // Initialize OTP if present on page load
    const otpContainer = document.querySelector('.auth-otp-container');
    if (otpContainer) {
        initOtpInputs(otpContainer);
    }

    // Expose init function globally in case forms are loaded via AJAX later
    window.Authenticator = {
        initOtp: initOtpInputs
    };
});

// ----------------------------------------------------
// Social Authentication: Popup & PostMessage Handler (Asset Sheba Pattern)
// ----------------------------------------------------
window.openSocialPopup = function (provider, event, customUrl = null) {
    if (event) event.preventDefault();

    const width = 500;
    const height = 620;
    const screenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screenX;
    const screenTop = window.screenTop !== undefined ? window.screenTop : window.screenY;
    const outerWidth = window.outerWidth || document.documentElement.clientWidth || screen.width;
    const outerHeight = window.outerHeight || document.documentElement.clientHeight || screen.height;

    const left = screenLeft + Math.max(0, (outerWidth - width) / 2);
    const top = screenTop + Math.max(0, (outerHeight - height) / 2);

    const url = customUrl || `/social/${provider}`;

    const popup = window.open(
        url,
        'socialAuthPopup',
        `width=${width},height=${height},left=${left},top=${top},scrollbars=yes,resizable=yes,status=no,toolbar=no,menubar=no`
    );

    if (popup) {
        popup.focus();
    }
};

window.addEventListener('message', function (event) {
    if (!event.data || typeof event.data !== 'object') return;

    if (event.data.type === 'socialLoginSuccess') {
        const redirectUrl = event.data.redirectUrl;
        if (redirectUrl && redirectUrl !== window.location.href && redirectUrl !== window.location.pathname) {
            window.location.href = redirectUrl;
        } else {
            window.location.reload();
        }
    } else if (event.data.type === 'socialLoginError') {
        alert(event.data.message || 'Social authentication failed.');
    }
});
