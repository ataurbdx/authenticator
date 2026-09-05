/**
 * Authenticator Master JavaScript API Bridge
 * Completely container-scoped, class-based, and zero hardcoded ID dependencies.
 * Automatically detects whether running in "page" or "modal" via [data-auth-source].
 */
(function () {
    'use strict';

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    // =========================================================================
    // DYNAMIC CONTAINER DETECTION & SCOPING
    // =========================================================================

    function getContainer(element) {
        const container = element.closest('[data-auth-source]') || element.closest('.js-auth-container') || element.closest('form') || document;
        
        // Dynamically assign an instance ID if missing
        if (container && container !== document && !container.id) {
            const source = container.getAttribute('data-auth-source') || 'auth';
            container.id = `auth-${source}-${Math.random().toString(36).substring(2, 9)}`;
        }
        return container;
    }

    function getAuthSource(container) {
        return container.getAttribute('data-auth-source') || (container.closest('.js-auth-modal') ? 'modal' : 'page');
    }

    // =========================================================================
    // ALERT & ERROR MESSAGING (CONTAINER-SCOPED)
    // =========================================================================

    function showContainerAlert(container, message, type = 'danger') {
        const alertBox = container.querySelector('.js-auth-alert');
        if (alertBox) {
            const textEl = alertBox.querySelector('.js-alert-text') || alertBox;
            textEl.textContent = message;
            alertBox.classList.remove('hidden', 'd-none');

            // Style alert according to CSS framework
            if (alertBox.classList.contains('alert')) {
                // Bootstrap 5
                alertBox.className = `js-auth-alert alert alert-${type === 'success' ? 'success' : 'danger'} py-2 small mb-3`;
            } else {
                // Tailwind CSS
                if (type === 'success') {
                    alertBox.className = 'js-auth-alert p-3 rounded-xl text-xs font-medium mb-3 flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400';
                } else {
                    alertBox.className = 'js-auth-alert p-3 rounded-xl text-xs font-medium mb-3 flex items-center gap-2 bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400';
                }
            }
        }
    }

    function clearContainerAlert(container) {
        const alertBox = container.querySelector('.js-auth-alert');
        if (alertBox) {
            alertBox.classList.add('hidden', 'd-none');
        }
    }

    function clearErrors(form) {
        const container = getContainer(form);
        clearContainerAlert(container);

        form.querySelectorAll('.error-message').forEach(el => {
            el.classList.add('hidden', 'd-none');
            const text = el.querySelector('.error-text');
            if (text) text.textContent = '';
        });

        form.querySelectorAll('.invalid-feedback').forEach(el => {
            el.textContent = '';
            el.classList.add('d-none');
        });

        form.querySelectorAll('input, select, textarea').forEach(el => {
            el.classList.remove('border-red-500', 'focus:border-red-500', 'is-invalid');
        });
    }

    function displayErrors(form, errors) {
        for (const [field, messages] of Object.entries(errors)) {
            const input = form.querySelector(`[name="${field}"]`);
            const msg = Array.isArray(messages) ? messages[0] : messages;

            if (input) {
                input.classList.add('border-red-500', 'focus:border-red-500', 'is-invalid');
                const fieldWrap = input.closest('.form-field-wrap') || input.parentElement;

                const errorBox = fieldWrap?.querySelector('.error-message');
                if (errorBox) {
                    const text = errorBox.querySelector('.error-text') || errorBox;
                    text.textContent = msg;
                    errorBox.classList.remove('hidden', 'd-none');
                }

                const feedback = fieldWrap?.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.textContent = msg;
                    feedback.classList.remove('d-none');
                }
            }
        }
    }

    function setBtnLoading(button, loadingText = 'Processing...') {
        if (!button) return;
        button.disabled = true;
        if (!button.getAttribute('data-original-html')) {
            button.setAttribute('data-original-html', button.innerHTML);
        }
        button.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> <i class="fa-solid fa-spinner fa-spin text-xs me-1"></i> <span>${loadingText}</span>`;
    }

    function resetBtn(button) {
        if (!button) return;
        button.disabled = false;
        const original = button.getAttribute('data-original-html');
        if (original) {
            button.innerHTML = original;
        }
    }

    // =========================================================================
    // MODAL TAB SWITCHING (SCOPED TO CONTAINER)
    // =========================================================================

    function switchModalTab(container, targetTab) {
        clearContainerAlert(container);

        // Update tab buttons
        container.querySelectorAll('.js-tab-btn').forEach(tabEl => {
            const tabName = tabEl.getAttribute('data-tab');
            const isActive = tabName === targetTab;

            if (tabEl.classList.contains('nav-link')) {
                // Bootstrap 5
                if (isActive) {
                    tabEl.classList.add('active', 'text-primary', 'border-bottom', 'border-primary', 'border-2');
                    tabEl.classList.remove('text-muted');
                } else {
                    tabEl.classList.remove('active', 'text-primary', 'border-bottom', 'border-primary', 'border-2');
                    tabEl.classList.add('text-muted');
                }
            } else {
                // Tailwind
                if (isActive) {
                    tabEl.classList.add('text-theme-primary', 'border-b-2', 'border-theme-primary');
                    tabEl.classList.remove('text-slate-400', 'border-transparent');
                } else {
                    tabEl.classList.remove('text-theme-primary', 'border-theme-primary');
                    tabEl.classList.add('text-slate-400', 'border-transparent');
                }
            }
        });

        // Toggle panels
        container.querySelectorAll('.js-panel').forEach(panel => {
            const panelName = panel.getAttribute('data-panel');
            if (panelName === targetTab) {
                panel.classList.remove('hidden', 'd-none');
            } else {
                panel.classList.add('hidden', 'd-none');
            }
        });
    }

    // =========================================================================
    // IDENTIFIER TABS (EMAIL / USERNAME / PHONE)
    // =========================================================================

    function switchIdentifierType(formOrContainer, targetType) {
        const typeGroup = formOrContainer.querySelector('.js-type-toggle-group');
        if (typeGroup) {
            typeGroup.querySelectorAll('.js-identifier-tab-btn').forEach(btn => {
                const isTarget = btn.getAttribute('data-target') === targetType;
                btn.classList.toggle('is-active', isTarget);

                if (btn.classList.contains('btn')) {
                    // Bootstrap 5
                    if (isTarget) {
                        btn.classList.add('bg-white', 'text-primary', 'shadow-sm');
                        btn.classList.remove('text-muted');
                    } else {
                        btn.classList.remove('bg-white', 'text-primary', 'shadow-sm');
                        btn.classList.add('text-muted');
                    }
                } else {
                    // Tailwind
                    if (isTarget) {
                        btn.classList.add('bg-white', 'dark:bg-slate-700', 'text-theme-primary', 'shadow-sm');
                        btn.classList.remove('bg-transparent', 'text-slate-500', 'dark:text-slate-400');
                    } else {
                        btn.classList.remove('bg-white', 'dark:bg-slate-700', 'text-theme-primary', 'shadow-sm');
                        btn.classList.add('bg-transparent', 'text-slate-500', 'dark:text-slate-400');
                    }
                }
            });
        }

        // Update hidden input
        const hiddenType = formOrContainer.querySelector('.js-active-identifier-type');
        if (hiddenType) hiddenType.value = targetType;

        // Hide all field panels and show the targeted one
        formOrContainer.querySelectorAll('.js-field-panel').forEach(panel => {
            panel.classList.add('hidden', 'd-none');
        });

        const targetPanel = formOrContainer.querySelector(`.js-field-${targetType}`);
        if (targetPanel) {
            targetPanel.classList.remove('hidden', 'd-none');
            const input = targetPanel.querySelector('input');
            if (input) input.focus();
        }
    }

    // =========================================================================
    // EVENT DELEGATION
    // =========================================================================

    document.addEventListener('DOMContentLoaded', function () {

        // 1. Modal Tab Switching Button Delegation
        document.addEventListener('click', function (e) {
            const tabBtn = e.target.closest('.js-tab-btn');
            if (tabBtn) {
                e.preventDefault();
                const container = getContainer(tabBtn);
                const targetTab = tabBtn.getAttribute('data-tab');
                switchModalTab(container, targetTab);
            }

            // Identifier type pill toggle (Email / Username / Phone)
            const idTabBtn = e.target.closest('.js-identifier-tab-btn');
            if (idTabBtn) {
                e.preventDefault();
                const form = idTabBtn.closest('form') || getContainer(idTabBtn);
                const targetType = idTabBtn.getAttribute('data-target');
                switchIdentifierType(form, targetType);
            }

            // Back to Account button (from preview banner)
            const backBtn = e.target.closest('.js-back-to-account-btn');
            if (backBtn) {
                e.preventDefault();
                const container = getContainer(backBtn);
                const source = getAuthSource(container);
                if (source === 'modal') {
                    switchModalTab(container, 'account');
                } else {
                    window.location.href = '/auth/account';
                }
            }
        });

        // 2. Password Visibility Toggle
        document.addEventListener('click', function (e) {
            const toggleBtn = e.target.closest('.js-password-toggle-btn');
            if (!toggleBtn) return;

            const wrap = toggleBtn.closest('.relative') || toggleBtn.closest('.input-group') || toggleBtn.parentElement;
            const input = wrap.querySelector('input[type="password"], input[type="text"]');
            if (input) {
                const isPassword = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPassword ? 'text' : 'password');
                toggleBtn.innerHTML = isPassword 
                    ? '<i class="fa-regular fa-eye-slash text-xs bi bi-eye-slash"></i>' 
                    : '<i class="fa-regular fa-eye text-xs bi bi-eye"></i>';
            }
        });

        // 3. Modal Open & Close Triggers
        document.addEventListener('click', function (e) {
            // Close Modal
            if (e.target.closest('.js-close-auth-modal') || (e.target.classList.contains('js-auth-modal') && e.target === e.currentTarget)) {
                const modal = e.target.closest('.js-auth-modal') || document.querySelector('.js-auth-modal');
                if (modal) {
                    modal.classList.add('hidden', 'd-none');
                    modal.style.display = 'none';
                    document.body.classList.remove('overflow-hidden');
                }
            }

            // Open Modal Trigger button: <button data-open-auth-modal data-auth-tab="sign-in">
            const openTrigger = e.target.closest('[data-open-auth-modal]');
            if (openTrigger) {
                e.preventDefault();
                const tab = openTrigger.getAttribute('data-auth-tab') || 'login';
                window.openAuthModal(tab);
            }
        });

        window.openAuthModal = function (tab = 'login') {
            const modal = document.querySelector('.js-auth-modal');
            if (modal) {
                modal.classList.remove('hidden', 'd-none');
                modal.style.display = 'flex';
                document.body.classList.add('overflow-hidden');
                const container = getContainer(modal);
                switchModalTab(container, tab === 'sign-in' ? 'login' : (tab === 'sign-up' ? 'register' : tab));
            }
        };

        // 4. Step 1: Account Identifier Check Submit
        document.addEventListener('submit', async function (e) {
            const form = e.target.closest('.js-account-form');
            if (!form) return;

            e.preventDefault();
            clearErrors(form);

            const container = getContainer(form);
            const source = getAuthSource(container);
            const submitBtn = form.querySelector('.js-submit-btn');
            setBtnLoading(submitBtn, 'Checking...');

            const activeType = form.querySelector('.js-active-identifier-type')?.value || 'email';
            const activeInput = form.querySelector(`.js-input-${activeType}`);
            const identifier = activeInput?.value?.trim() || '';

            if (!identifier) {
                displayErrors(form, { [activeType]: `Please enter your ${activeType}.` });
                resetBtn(submitBtn);
                return;
            }

            const endpoint = form.getAttribute('data-api-endpoint') || '/api/v1/auth/check-identifier';

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                    },
                    body: JSON.stringify({ identifier })
                });

                const data = await response.json();

                if (!response.ok) {
                    showContainerAlert(container, data.message || 'Identifier check failed.');
                    return;
                }

                if (data.exists) {
                    // USER EXISTS -> Transition to SIGN IN
                    if (source === 'modal') {
                        const signInForm = container.querySelector('.js-sign-in-form');
                        if (signInForm) {
                            signInForm.querySelector('.js-preview-banner')?.classList.remove('hidden', 'd-none');
                            signInForm.querySelector('.js-direct-wrap')?.classList.add('hidden', 'd-none');
                            const userPreview = signInForm.querySelector('.js-identifier-preview');
                            if (userPreview) userPreview.textContent = identifier;
                            const namePreview = signInForm.querySelector('.js-user-name');
                            if (namePreview && data.user_name) namePreview.textContent = `Welcome Back, ${data.user_name}!`;
                            const prefilledInput = signInForm.querySelector('.js-prefilled-identifier');
                            if (prefilledInput) prefilledInput.value = identifier;
                        }
                        switchModalTab(container, 'login');
                        signInForm?.querySelector('.js-input-password')?.focus();
                    } else {
                        // On page, redirect to sign-in with query or state
                        window.location.href = `/auth/sign-in?identifier=${encodeURIComponent(identifier)}`;
                    }
                } else {
                    // USER DOES NOT EXIST -> Transition to SIGN UP
                    if (source === 'modal') {
                        const signUpForm = container.querySelector('.js-sign-up-form');
                        if (signUpForm) {
                            signUpForm.querySelector('.js-preview-banner')?.classList.remove('hidden', 'd-none');
                            const userPreview = signUpForm.querySelector('.js-identifier-preview');
                            if (userPreview) userPreview.textContent = identifier;

                            if (activeType === 'email') {
                                const emailInput = signUpForm.querySelector('.js-input-email');
                                if (emailInput) emailInput.value = identifier;
                                signUpForm.querySelector('.js-email-lock')?.classList.remove('hidden', 'd-none');
                            } else if (activeType === 'phone') {
                                const phoneInput = signUpForm.querySelector('.js-input-phone');
                                if (phoneInput) phoneInput.value = identifier.replace(/^\+?880/, '');
                                signUpForm.querySelector('.js-phone-lock')?.classList.remove('hidden', 'd-none');
                            } else {
                                const userInput = signUpForm.querySelector('.js-input-username');
                                if (userInput) userInput.value = identifier;
                            }
                        }
                        switchModalTab(container, 'register');
                        signUpForm?.querySelector('input[name="first_name"]')?.focus();
                    } else {
                        window.location.href = `/auth/sign-up?identifier=${encodeURIComponent(identifier)}`;
                    }
                }

            } catch (err) {
                showContainerAlert(container, 'Network connection error. Please try again.');
            } finally {
                resetBtn(submitBtn);
            }
        });

        // 5. Generic Auth Form Submissions (Sign In, Sign Up, etc.)
        document.addEventListener('submit', async function (e) {
            const form = e.target.closest('.js-auth-form');
            if (!form || form.classList.contains('js-account-form')) return;

            e.preventDefault();
            clearErrors(form);

            const container = getContainer(form);
            const authSource = getAuthSource(container);
            const submitBtn = form.querySelector('.js-submit-btn') || form.querySelector('button[type="submit"]');
            setBtnLoading(submitBtn);

            const endpoint = form.getAttribute('data-api-endpoint');
            const formData = new FormData(form);

            // Handle active identifier in direct sign-in form
            if (form.classList.contains('js-sign-in-form')) {
                const prefilled = form.querySelector('.js-prefilled-identifier')?.value;
                if (!prefilled) {
                    const activeType = form.querySelector('.js-active-identifier-type')?.value || 'email';
                    const activeVal = form.querySelector(`.js-input-${activeType}`)?.value;
                    if (activeVal) {
                        formData.set('identifier', activeVal);
                    }
                }
            }

            const payload = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422 && data.errors) {
                        displayErrors(form, data.errors);
                    } else {
                        showContainerAlert(container, data.message || 'An error occurred.');
                    }
                    return;
                }

                // Handle Two-Factor Interception
                if (data.requires_two_factor) {
                    window.location.href = `/auth/2fa-challenge?user_id=${data.user_id}`;
                    return;
                }

                // Authentication Success
                showContainerAlert(container, data.message || 'Success! Processing...', 'success');

                // Dispatch Custom Event for host applications to hook into
                window.dispatchEvent(new CustomEvent('auth:success', {
                    detail: {
                        data: data,
                        source: authSource,
                        form: form
                    }
                }));

                setTimeout(() => {
                    if (authSource === 'modal') {
                        // Close modal
                        const modal = container.closest('.js-auth-modal') || document.querySelector('.js-auth-modal');
                        if (modal) {
                            modal.classList.add('hidden', 'd-none');
                            modal.style.display = 'none';
                            document.body.classList.remove('overflow-hidden');
                        }
                    }
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else if (authSource === 'page') {
                        window.location.href = '/';
                    }
                }, 800);

            } catch (err) {
                showContainerAlert(container, 'Network connection error. Please try again.');
            } finally {
                resetBtn(submitBtn);
            }
        });

        // 6. 6-Digit OTP Auto-Advance Input
        document.addEventListener('input', function (e) {
            const input = e.target.closest('.js-otp-digit');
            if (!input) return;

            const wrap = input.closest('.js-otp-inputs-wrap');
            const inputs = Array.from(wrap.querySelectorAll('.js-otp-digit'));
            const index = inputs.indexOf(input);

            if (input.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }

            const form = input.closest('form');
            const fullCodeInput = form?.querySelector('.js-otp-full-code');
            if (fullCodeInput) {
                fullCodeInput.value = inputs.map(i => i.value).join('');
            }
        });

        document.addEventListener('keydown', function (e) {
            const input = e.target.closest('.js-otp-digit');
            if (!input) return;

            if (e.key === 'Backspace' && !input.value) {
                const wrap = input.closest('.js-otp-inputs-wrap');
                const inputs = Array.from(wrap.querySelectorAll('.js-otp-digit'));
                const index = inputs.indexOf(input);
                if (index > 0) {
                    inputs[index - 1].focus();
                }
            }
        });

        // 7. 2FA Emergency Recovery Code Toggle
        document.addEventListener('click', function (e) {
            const toggleRecovery = e.target.closest('.js-toggle-recovery-btn');
            if (!toggleRecovery) return;

            const form = toggleRecovery.closest('form') || getContainer(toggleRecovery);
            const codeInput = form.querySelector('.js-input-2fa-code');
            const label = form.querySelector('.js-2fa-label');
            const isRecovery = toggleRecovery.getAttribute('data-is-recovery') === '1';

            if (!isRecovery) {
                toggleRecovery.setAttribute('data-is-recovery', '1');
                toggleRecovery.textContent = 'Use 6-digit authenticator app code';
                if (label) label.textContent = 'Emergency Recovery Backup Code';
                if (codeInput) {
                    codeInput.placeholder = 'e.g. AB12CD34EF';
                    codeInput.maxLength = 20;
                    codeInput.removeAttribute('inputmode');
                    codeInput.value = '';
                    codeInput.focus();
                }
            } else {
                toggleRecovery.setAttribute('data-is-recovery', '0');
                toggleRecovery.textContent = 'Use emergency recovery code';
                if (label) label.textContent = '6-Digit Security Code';
                if (codeInput) {
                    codeInput.placeholder = '000 000';
                    codeInput.maxLength = 10;
                    codeInput.setAttribute('inputmode', 'numeric');
                    codeInput.value = '';
                    codeInput.focus();
                }
            }
        });

    });

})();
