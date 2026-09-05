<!-- Dynamic Alert Container (Tailwind) -->
<div class="js-auth-alert hidden p-3 rounded-xl text-xs font-medium mb-3 flex items-center gap-2" role="alert">
    <i class="js-alert-icon fa-solid fa-circle-exclamation text-sm shrink-0"></i>
    <span class="js-alert-text flex-1"></span>
</div>

@if(session('success'))
    <div class="p-3 rounded-xl text-xs font-medium mb-3 flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400">
        <i class="fa-solid fa-circle-check text-sm shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="p-3 rounded-xl text-xs font-medium mb-3 flex items-center gap-2 bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400">
        <i class="fa-solid fa-triangle-exclamation text-sm shrink-0"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

@if(session('status'))
    <div class="p-3 rounded-xl text-xs font-medium mb-3 flex items-center gap-2 bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-blue-400">
        <i class="fa-solid fa-circle-info text-sm shrink-0"></i>
        <span>{{ session('status') }}</span>
    </div>
@endif
