/**
 * Authenticator Master JavaScript API Bridge
 * Supports both standard page workflows & AJAX modals.
 */
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    // ----------------------------------------------------
    // Helper: CSRF Token
    // ----------------------------------------------------
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || document.querySelector('input[name="_token"]')?.value
            || '';
    }

    // ----------------------------------------------------
    // Event Delegation: Click Listeners
    // ----------------------------------------------------
    document.addEventListener('click', function (e) {
        // 1. Password Visibility Toggle
        const toggleBtn = e.target.closest('.auth-password-toggle') || e.target.closest('.js-password-toggle-btn');
        if (toggleBtn) {
            e.preventDefault();
            const container = toggleBtn.closest('.auth-password-group') || toggleBtn.closest('.relative') || toggleBtn.parentElement;
            if (container) {
                const input = container.querySelector('input');
                const icon = toggleBtn.querySelector('i');

                if (input) {
                    const isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';
                    if (icon) {
                        icon.classList.toggle('fa-eye', !isPassword);
                        icon.classList.toggle('fa-eye-slash', isPassword);
                    }
                }
            }
        }

        // 2. Sign-in / Gateway Method Switcher (Email / Phone / Username)
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

                const oldType = selectionWrap?.dataset?.currentType;
                if (oldType && input) {
                    input.dataset[oldType + 'Value'] = input.value;
                }

                if (selectionWrap) {
                    selectionWrap.classList.add('hidden');
                    selectionWrap.dataset.currentType = type;
                }
                if (typeInput) typeInput.value = type;

                if (input) {
                    input.value = input.dataset[type + 'Value'] || '';
                }

                if (formContainer) {
                    formContainer.classList.remove('hidden');
                }

                if (type === 'email') {
                    if (title) title.innerText = 'Email Address';
                    if (input) {
                        input.type = 'email';
                        input.placeholder = 'name@example.com';
                    }
                    if (icon) icon.className = 'auth-input-icon fa-regular fa-envelope';
                    if (ccWrap) ccWrap.classList.add('hidden');
                } else if (type === 'phone') {
                    if (title) title.innerText = 'Phone Number';
                    if (input) {
                        input.type = 'tel';
                        input.placeholder = '1700000000';
                    }
                    if (icon) icon.className = 'auth-input-icon fa-solid fa-phone';
                    if (ccWrap) ccWrap.classList.remove('hidden');
                } else {
                    if (title) title.innerText = 'Username';
                    if (input) {
                        input.type = 'text';
                        input.placeholder = 'Enter your username';
                    }
                    if (icon) icon.className = 'auth-input-icon fa-regular fa-user';
                    if (ccWrap) ccWrap.classList.add('hidden');
                }

                if (input) input.focus();
            }
        }

        // 3. Change Method (Back) Button
        const backBtn = e.target.closest('.auth-change-method-btn');
        if (backBtn) {
            e.preventDefault();
            const wrapper = backBtn.closest('.auth-form-wrapper');
            if (wrapper) {
                wrapper.querySelector('.auth-form-container')?.classList.add('hidden');
                wrapper.querySelector('.auth-methods-selection')?.classList.remove('hidden');
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
    // Gateway Identifier Check Submit (AJAX)
    // ----------------------------------------------------
    document.addEventListener('submit', async function (e) {
        if (e.target.matches('.auth-gateway-form')) {
            e.preventDefault();
            const form = e.target;
            const wrapper = form.closest('.auth-form-wrapper');

            const input = form.querySelector('.auth-identifier-input');
            const val = input ? input.value.trim() : '';
            if (!val) return;

            const selection = wrapper?.querySelector('.auth-methods-selection');
            const typeField = selection?.dataset?.currentType || 'email';

            let payload = { identifier: val };

            if (typeField === 'phone') {
                const ccInput = form.querySelector('input[name="code"]');
                const ccVal = ccInput ? ccInput.value.trim() : '';
                payload = { identifier: val, code: ccVal };
            }

            const btn = form.querySelector('button[type="submit"]');
            const btnText = btn?.querySelector('.btn-text');
            const btnIcon = btn?.querySelector('.btn-icon');

            // Loading state
            if (btn) btn.disabled = true;
            if (btnText) btnText.innerText = 'Checking Account...';
            if (btnIcon) btnIcon.className = 'btn-icon fa-solid fa-circle-notch fa-spin text-xs';
            if (wrapper) hideFeedback(wrapper);

            try {
                const res = await fetch(form.dataset.actionUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (data.exists) {
                    if (wrapper) showFeedback(wrapper, data.message || 'Account found! Redirecting...', true);
                    setTimeout(() => {
                        window.location.href = data.data.redirect_url;
                    }, 1500);
                } else {
                    if (wrapper) showFeedback(wrapper, data.message || 'No account found. Let\'s create one!', false);
                    setTimeout(() => {
                        window.location.href = data.data.redirect_url;
                    }, 1500);
                }
            } catch (err) {
                console.error('Identifier check error:', err);
                if (btn) btn.disabled = false;
                if (btnText) btnText.innerText = 'Find Account & Continue';
                if (btnIcon) btnIcon.className = 'btn-icon fa-solid fa-arrow-right text-xs';
            }
        }
    });

    // ----------------------------------------------------
    // Standard Form Submit Loader (Sign In, Sign Up, etc.)
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
    // Handle OTP Input Auto-Focus & Paste
    // ----------------------------------------------------
    function initOtpInputs(container) {
        if (!container) return;

        const inputs = container.querySelectorAll('.otp-input, .js-otp-digit');
        const hiddenInput = container.querySelector('.otp-hidden-input, .js-otp-full-code');
        if (!inputs.length) return;

        const updateHiddenInput = () => {
            if (!hiddenInput) return;
            let code = '';
            inputs.forEach(input => code += input.value);
            hiddenInput.value = code;
        };

        inputs.forEach((input, index) => {
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

        if (inputs.length > 0) {
            inputs[0].focus();
        }
    }

    const otpContainer = document.querySelector('.auth-otp-container, .js-otp-inputs-wrap');
    if (otpContainer) {
        initOtpInputs(otpContainer);
    }

    // Expose Authenticator API on window
    window.Authenticator = {
        initOtp: initOtpInputs
    };
});
