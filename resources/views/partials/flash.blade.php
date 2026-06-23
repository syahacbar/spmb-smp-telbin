<style>
    .flash-alert {
        display: flex;
        gap: .8rem;
        align-items: flex-start;
        border: 1px solid transparent;
        border-left-width: 4px;
        border-radius: .65rem;
        padding: .9rem 1rem;
        margin-bottom: 1rem;
        background: #fff;
        box-shadow: 0 12px 28px rgba(16, 24, 40, .08);
    }
    .flash-alert-icon {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        border-radius: 50%;
        color: #fff;
        font-weight: 900;
    }
    .flash-alert-title {
        color: #172033;
        font-weight: 900;
        line-height: 1.2;
        margin-bottom: .12rem;
    }
    .flash-alert-message {
        color: #475467;
        font-weight: 600;
        line-height: 1.4;
    }
    .flash-alert-success {
        border-color: #bbf7d0;
        border-left-color: #16a34a;
        background: linear-gradient(90deg, #f0fdf4 0%, #fff 42%);
    }
    .flash-alert-success .flash-alert-icon {
        background: #16a34a;
    }
    .flash-alert-warning {
        border-color: #fde68a;
        border-left-color: #d97706;
        background: linear-gradient(90deg, #fffbeb 0%, #fff 42%);
    }
    .flash-alert-warning .flash-alert-icon {
        background: #d97706;
    }
    .flash-alert-danger {
        border-color: #fecaca;
        border-left-color: #dc2626;
        background: linear-gradient(90deg, #fef2f2 0%, #fff 42%);
    }
    .flash-alert-danger .flash-alert-icon {
        background: #dc2626;
    }
</style>

@if(session('success'))
    <div class="flash-alert flash-alert-success" role="alert">
        <span class="flash-alert-icon" aria-hidden="true">✓</span>
        <div class="flex-grow-1">
            <div class="flash-alert-title">Berhasil disimpan</div>
            <div class="flash-alert-message">{{ session('success') }}</div>
        </div>
    </div>
@endif

@if(session('warning'))
    <div class="flash-alert flash-alert-warning" role="alert">
        <span class="flash-alert-icon" aria-hidden="true">!</span>
        <div class="flex-grow-1">
            <div class="flash-alert-title">Perlu perhatian</div>
            <div class="flash-alert-message">{{ session('warning') }}</div>
        </div>
    </div>
@endif

@if($errors->any())
    <div class="flash-alert flash-alert-danger" role="alert">
        <span class="flash-alert-icon" aria-hidden="true">!</span>
        <div class="flex-grow-1">
            <div class="flash-alert-title">Periksa kembali data berikut</div>
            <ul class="flash-alert-message mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
