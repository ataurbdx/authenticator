<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Authentication</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-950 text-slate-100 flex items-center justify-center min-h-screen p-4 font-sans select-none overflow-hidden">
    <!-- Glow Elements -->
    <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative w-full max-w-sm p-6 bg-slate-900/90 border border-slate-800 rounded-3xl shadow-2xl backdrop-blur-xl text-center space-y-4">
        @if(($status ?? 'success') === 'success')
            <div class="w-16 h-16 mx-auto rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-2xl animate-pulse">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-white tracking-tight">Authentication Successful</h3>
                <p class="text-xs text-slate-400 mt-1 font-medium">{{ $message ?? 'Logged in successfully. Redirecting...' }}</p>
            </div>
            <div class="flex items-center justify-center gap-2 text-xs text-emerald-400/80 font-semibold pt-2">
                <i class="fa-solid fa-circle-notch fa-spin"></i>
                <span>Closing window...</span>
            </div>
        @else
            <div class="w-16 h-16 mx-auto rounded-2xl bg-rose-500/10 border border-rose-500/30 flex items-center justify-center text-rose-400 text-2xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-white tracking-tight">Authentication Failed</h3>
                <p class="text-xs text-rose-400/90 mt-1 font-medium">{{ $message ?? 'An error occurred during social login.' }}</p>
            </div>
            <div class="pt-2">
                <button type="button" onclick="window.close()" 
                    class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-all">
                    Close Window
                </button>
            </div>
        @endif
    </div>

    <script>
        const status = "{{ $status ?? 'success' }}";
        const message = "{{ addslashes($message ?? '') }}";
        const redirectUrl = @json($redirectUrl);

        if (window.opener) {
            if (status === 'success') {
                window.opener.postMessage({
                    type: 'socialLoginSuccess',
                    message: message,
                    redirectUrl: redirectUrl
                }, '*');

                setTimeout(() => {
                    window.close();
                }, 800);
            } else {
                window.opener.postMessage({
                    type: 'socialLoginError',
                    message: message
                }, '*');

                setTimeout(() => {
                    window.close();
                }, 2500);
            }
        } else {
            // Not in a popup, redirect directly to intended or reload
            setTimeout(() => {
                if (status === 'success') {
                    if (redirectUrl && redirectUrl !== window.location.href) {
                        window.location.href = redirectUrl;
                    } else {
                        window.location.reload();
                    }
                } else {
                    window.location.href = "{{ route('authenticator.sign-in') }}";
                }
            }, 1200);
        }
    </script>
</body>
</html>
