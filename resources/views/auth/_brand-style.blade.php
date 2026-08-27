@php
    use App\Helpers\Helper;

    $bmPrimary  = Helper::app_config('brand_primary_color') ?: '#4f46e5';
    $bmAccent   = Helper::app_config('brand_accent_color') ?: '#06b6d4';
    $bmPrimaryD = Helper::hexShade($bmPrimary, -12);
    $bmPrimaryRgb = Helper::hexToRgbTriplet($bmPrimary);
    $bmAccentRgb = Helper::hexToRgbTriplet($bmAccent);
@endphp
<style>
    :root {
        --auth-primary: {{ $bmPrimary }};
        --auth-primary-dark: {{ $bmPrimaryD }};
        --auth-accent: {{ $bmAccent }};
        --auth-ink: #0f1729;
    }

    .auth-wrapper.auth-cover .auth-inner {
        min-height: 100vh;
    }

    /* Branded left panel */
    .brand-panel {
        position: relative;
        background: linear-gradient(180deg, #0f1729 0%, #171f38 100%);
        overflow: hidden;
        justify-content: center;
    }

    .brand-panel::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            radial-gradient(600px 300px at 15% 20%, rgba({{ $bmPrimaryRgb }}, 0.35), transparent 60%),
            radial-gradient(500px 260px at 85% 80%, rgba({{ $bmAccentRgb }}, 0.28), transparent 60%);
        pointer-events: none;
    }

    .brand-panel-inner {
        position: relative;
        max-width: 420px;
        margin: 0 auto;
        color: #fff;
        text-align: left;
    }

    .brand-panel .brand-mark {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 52px;
        padding: 10px 18px;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        margin-bottom: 28px;
    }

    .brand-panel .brand-mark img {
        max-height: 32px;
        width: auto;
        display: block;
    }

    .brand-panel h3 {
        font-size: 30px;
        font-weight: 800;
        letter-spacing: -0.02em;
        margin-bottom: 14px;
    }

    .brand-panel p {
        color: #cbd3e6;
        font-size: 15.5px;
        line-height: 1.7;
        margin-bottom: 28px;
    }

    .brand-highlights {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .brand-highlights li {
        position: relative;
        padding: 10px 0 10px 30px;
        font-size: 14.5px;
        color: #e2e6f5;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
    }

    .brand-highlights li:first-child { border-top: none; }

    .brand-highlights li::before {
        content: "✓";
        position: absolute;
        left: 0;
        top: 9px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: rgba({{ $bmAccentRgb }}, 0.18);
        color: #67e8f9;
        font-size: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Right form column */
    .auth-bg {
        background: #fff;
    }

    .auth-bg .card-title {
        font-size: 26px;
        letter-spacing: -0.02em;
    }

    .auth-bg .form-control {
        border-radius: 6px;
        border-color: #e4e4e7;
        padding: 0.65rem 1rem;
    }

    .auth-bg .form-control:focus {
        border-color: var(--auth-primary);
        box-shadow: 0 0 0 3px rgba({{ $bmPrimaryRgb }}, 0.12);
    }

    .auth-bg .btn-primary,
    .btn-submit {
        background: var(--auth-primary) !important;
        border-color: var(--auth-primary) !important;
        border-radius: 6px !important;
        padding: 0.7rem 1rem !important;
        font-weight: 600 !important;
        box-shadow: none !important;
    }

    .auth-bg .btn-primary:hover,
    .btn-submit:hover {
        background: var(--auth-primary-dark) !important;
        border-color: var(--auth-primary-dark) !important;
    }

    .auth-bg a { color: var(--auth-primary); }

    .brand-logo img { max-height: 42px; width: auto !important; object-fit: contain; }
</style>
