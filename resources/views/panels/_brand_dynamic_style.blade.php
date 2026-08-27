@php
    use App\Helpers\Helper;

    $bmPrimary  = Helper::app_config('brand_primary_color') ?: '#4f46e5';
    $bmAccent   = Helper::app_config('brand_accent_color') ?: '#06b6d4';
    $bmPrimaryD = Helper::hexShade($bmPrimary, -12); // darker, for hover/active
    $bmPrimaryRgb = Helper::hexToRgbTriplet($bmPrimary);
@endphp
<style id="bepomsg-brand-dynamic">
    :root {
        --bm-primary: {{ $bmPrimary }};
        --bm-primary-dark: {{ $bmPrimaryD }};
        --bm-accent: {{ $bmAccent }};
    }

    /* Bootstrap component overrides — these classes are compiled with a
       fixed hex at build time, so we re-point them here at request time
       whenever the admin picks a different brand color from Settings. */
    a,
    .text-primary { color: {{ $bmPrimary }} !important; }

    .btn-primary,
    .badge-primary,
    .bg-primary {
        background-color: {{ $bmPrimary }} !important;
        border-color: {{ $bmPrimary }} !important;
    }

    .btn-primary:hover,
    .btn-primary:focus,
    .btn-primary:active,
    .btn-primary:not(:disabled):not(.disabled):active {
        background-color: {{ $bmPrimaryD }} !important;
        border-color: {{ $bmPrimaryD }} !important;
    }

    .btn-outline-primary {
        color: {{ $bmPrimary }} !important;
        border-color: {{ $bmPrimary }} !important;
    }

    .btn-outline-primary:hover,
    .btn-outline-primary:active {
        background-color: {{ $bmPrimary }} !important;
        border-color: {{ $bmPrimary }} !important;
        color: #fff !important;
    }

    .border-primary { border-color: {{ $bmPrimary }} !important; }

    .badge-light-primary {
        background-color: rgba({{ $bmPrimaryRgb }}, 0.12) !important;
        color: {{ $bmPrimary }} !important;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: {{ $bmPrimary }} !important;
        box-shadow: 0 0 0 3px rgba({{ $bmPrimaryRgb }}, 0.15) !important;
    }

    .form-check-input:checked,
    .form-switch .form-check-input:checked {
        background-color: {{ $bmPrimary }} !important;
        border-color: {{ $bmPrimary }} !important;
    }

    .nav-tabs .nav-link.active,
    .nav-pills .nav-link.active {
        color: #fff !important;
        background-color: {{ $bmPrimary }} !important;
    }

    .pagination .page-item.active .page-link {
        background-color: {{ $bmPrimary }} !important;
        border-color: {{ $bmPrimary }} !important;
    }

    .dropdown-item:active,
    .dropdown-item.active {
        background-color: {{ $bmPrimary }} !important;
    }

    .progress-bar {
        background-color: {{ $bmPrimary }} !important;
    }
</style>
