<!-- Dynamic Alert Container (Bootstrap 5) -->
<div class="js-auth-alert d-none alert alert-danger py-2 small mb-3" role="alert">
    <i class="js-alert-icon bi bi-exclamation-triangle me-1"></i>
    <span class="js-alert-text"></span>
</div>

@if(session('success'))
    <div class="alert alert-success py-2 small mb-3">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger py-2 small mb-3">
        <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
    </div>
@endif

@if(session('status'))
    <div class="alert alert-info py-2 small mb-3">
        <i class="bi bi-info-circle me-1"></i> {{ session('status') }}
    </div>
@endif
