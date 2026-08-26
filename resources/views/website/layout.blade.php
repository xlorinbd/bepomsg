<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.title', config('app.name')))</title>
    <meta name="description" content="@yield('meta_description', 'Beposms is a powerful bulk SMS and WhatsApp marketing platform for businesses of every size.')">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <style>
        :root {
            --w-ink: #0f1729;
            --w-ink-soft: #4b5573;
            --w-bg: #ffffff;
            --w-bg-soft: #f6f7fb;
            --w-border: #e6e8f0;
            --w-primary: #4f46e5;
            --w-primary-dark: #4338ca;
            --w-primary-soft: #eef0fe;
            --w-accent: #06b6d4;
            --w-success: #16a34a;
            --w-radius: 16px;
            --w-shadow: 0 8px 30px rgba(15, 23, 41, 0.06);
            --w-shadow-lg: 0 20px 50px rgba(15, 23, 41, 0.12);
        }

        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: var(--w-ink);
            background: var(--w-bg);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        img { max-width: 100%; display: block; }
        a { color: inherit; text-decoration: none; }

        .w-container {
            width: 100%;
            max-width: 1160px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* Header */
        .w-header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: saturate(180%) blur(10px);
            border-bottom: 1px solid var(--w-border);
        }

        .w-header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 0;
        }

        .w-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 20px;
            letter-spacing: -0.02em;
            color: var(--w-ink);
        }

        .w-logo-mark {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--w-primary), var(--w-accent));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 800;
            font-size: 16px;
        }

        .w-nav { display: flex; align-items: center; gap: 32px; }

        .w-nav-links { display: flex; align-items: center; gap: 28px; }

        .w-nav-links a {
            font-size: 15px;
            font-weight: 600;
            color: var(--w-ink-soft);
            transition: color 0.15s ease;
        }

        .w-nav-links a:hover { color: var(--w-primary); }

        .w-nav-cta { display: flex; align-items: center; gap: 10px; }

        .w-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 22px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 14.5px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
            white-space: nowrap;
        }

        .w-btn-primary {
            background: var(--w-primary);
            color: #fff;
            box-shadow: 0 6px 18px rgba(79, 70, 229, 0.28);
        }

        .w-btn-primary:hover { background: var(--w-primary-dark); transform: translateY(-1px); }

        .w-btn-ghost { background: transparent; color: var(--w-ink); border-color: var(--w-border); }

        .w-btn-ghost:hover { border-color: var(--w-primary); color: var(--w-primary); }

        .w-btn-light { background: #fff; color: var(--w-primary); }

        .w-btn-light:hover { transform: translateY(-1px); box-shadow: 0 10px 24px rgba(0,0,0,0.18); }

        .w-btn-lg { padding: 15px 30px; font-size: 16px; }

        .w-menu-toggle { display: none; }

        /* Hero */
        .w-hero {
            position: relative;
            background: linear-gradient(180deg, #0f1729 0%, #171f38 100%);
            color: #fff;
            overflow: hidden;
            padding: 96px 0 120px;
        }

        .w-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(600px 300px at 15% 20%, rgba(79, 70, 229, 0.35), transparent 60%),
                radial-gradient(500px 260px at 85% 10%, rgba(6, 182, 212, 0.28), transparent 60%);
            pointer-events: none;
        }

        .w-hero-inner { position: relative; text-align: center; }

        .w-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 16px;
            border-radius: 999px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.14);
            font-size: 13.5px;
            font-weight: 700;
            color: #c7d2fe;
            margin-bottom: 24px;
        }

        .w-eyebrow .dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: #34d399;
            box-shadow: 0 0 0 4px rgba(52, 211, 153, 0.2);
        }

        .w-hero h1 {
            font-size: clamp(34px, 5vw, 56px);
            line-height: 1.12;
            font-weight: 800;
            letter-spacing: -0.03em;
            margin: 0 0 20px;
        }

        .w-hero h1 span {
            background: linear-gradient(90deg, #a5b4fc, #67e8f9);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .w-hero p {
            max-width: 640px;
            margin: 0 auto 36px;
            font-size: 18px;
            color: #cbd3e6;
        }

        .w-hero-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .w-hero-stats {
            position: relative;
            display: flex;
            justify-content: center;
            gap: 56px;
            margin-top: 72px;
            flex-wrap: wrap;
        }

        .w-hero-stat { text-align: center; }
        .w-hero-stat b { display: block; font-size: 30px; font-weight: 800; }
        .w-hero-stat span { font-size: 13.5px; color: #9aa4c2; font-weight: 600; }

        /* Sections */
        .w-section { padding: 96px 0; }
        .w-section-soft { background: var(--w-bg-soft); }

        .w-section-head { text-align: center; max-width: 620px; margin: 0 auto 56px; }

        .w-kicker {
            display: inline-block;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--w-primary);
            margin-bottom: 14px;
        }

        .w-section-head h2 {
            font-size: clamp(26px, 3.4vw, 38px);
            font-weight: 800;
            letter-spacing: -0.02em;
            margin: 0 0 14px;
        }

        .w-section-head p { color: var(--w-ink-soft); font-size: 16.5px; margin: 0; }

        /* Feature grid */
        .w-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .w-card {
            background: #fff;
            border: 1px solid var(--w-border);
            border-radius: var(--w-radius);
            padding: 28px;
            box-shadow: var(--w-shadow);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .w-card:hover { transform: translateY(-4px); box-shadow: var(--w-shadow-lg); }

        .w-feature-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: var(--w-primary-soft);
            color: var(--w-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
            margin-bottom: 18px;
        }

        .w-card h3 { font-size: 17.5px; font-weight: 800; margin: 0 0 8px; letter-spacing: -0.01em; }
        .w-card p { font-size: 15px; color: var(--w-ink-soft); margin: 0; }

        /* Steps */
        .w-steps {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 32px;
            counter-reset: step;
        }

        .w-step { position: relative; padding-left: 4px; }

        .w-step-num {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: var(--w-ink);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 15px;
            margin-bottom: 18px;
        }

        .w-step h3 { font-size: 17px; font-weight: 800; margin: 0 0 8px; }
        .w-step p { font-size: 15px; color: var(--w-ink-soft); margin: 0; }

        /* Pricing */
        .w-pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
            gap: 24px;
            align-items: stretch;
        }

        .w-price-card {
            position: relative;
            background: #fff;
            border: 1px solid var(--w-border);
            border-radius: 20px;
            padding: 32px 28px;
            box-shadow: var(--w-shadow);
            display: flex;
            flex-direction: column;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .w-price-card:hover { transform: translateY(-4px); box-shadow: var(--w-shadow-lg); }

        .w-price-card.is-popular {
            border-color: var(--w-primary);
            box-shadow: var(--w-shadow-lg);
        }

        .w-badge-popular {
            position: absolute;
            top: -13px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(90deg, var(--w-primary), var(--w-accent));
            color: #fff;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            padding: 6px 16px;
            border-radius: 999px;
        }

        .w-price-name { font-size: 18px; font-weight: 800; margin: 4px 0 6px; }
        .w-price-desc { font-size: 14px; color: var(--w-ink-soft); min-height: 20px; margin-bottom: 20px; }

        .w-price-amount { display: flex; align-items: baseline; gap: 6px; margin-bottom: 4px; }
        .w-price-amount .num { font-size: 40px; font-weight: 800; letter-spacing: -0.02em; }
        .w-price-amount .cur { font-size: 16px; font-weight: 700; color: var(--w-ink-soft); }
        .w-price-cycle { font-size: 13.5px; color: var(--w-ink-soft); margin-bottom: 24px; }

        .w-price-features { list-style: none; margin: 0 0 28px; padding: 0; flex: 1; }
        .w-price-features li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 14.5px;
            color: var(--w-ink);
            padding: 8px 0;
            border-top: 1px solid var(--w-border);
        }
        .w-price-features li:first-child { border-top: none; }

        .w-check {
            flex: none;
            width: 18px; height: 18px;
            border-radius: 50%;
            background: rgba(22, 163, 74, 0.12);
            color: var(--w-success);
            display: flex; align-items: center; justify-content: center;
            font-size: 11px;
            margin-top: 2px;
        }

        .w-price-card .w-btn { width: 100%; }

        .w-price-note { text-align: center; margin-top: 40px; color: var(--w-ink-soft); font-size: 14.5px; }

        /* CTA band */
        .w-cta {
            border-radius: 28px;
            background: linear-gradient(135deg, #4f46e5, #06b6d4);
            color: #fff;
            padding: 64px 48px;
            text-align: center;
            box-shadow: var(--w-shadow-lg);
        }

        .w-cta h2 { font-size: clamp(24px, 3vw, 32px); font-weight: 800; margin: 0 0 12px; letter-spacing: -0.02em; }
        .w-cta p { max-width: 520px; margin: 0 auto 28px; color: rgba(255,255,255,0.9); font-size: 16px; }

        /* Footer */
        .w-footer { background: #0f1729; color: #9aa4c2; padding: 56px 0 28px; }

        .w-footer-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 32px;
            padding-bottom: 36px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            margin-bottom: 24px;
        }

        .w-footer-brand p { max-width: 320px; font-size: 14px; margin-top: 12px; }
        .w-footer-links { display: flex; gap: 64px; flex-wrap: wrap; }
        .w-footer-col h4 { color: #fff; font-size: 13.5px; margin: 0 0 14px; letter-spacing: 0.04em; text-transform: uppercase; }
        .w-footer-col a { display: block; font-size: 14.5px; padding: 6px 0; color: #9aa4c2; }
        .w-footer-col a:hover { color: #fff; }
        .w-footer-bottom { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; font-size: 13.5px; }

        @media (max-width: 900px) {
            .w-grid, .w-steps { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 760px) {
            .w-nav-links { display: none; }
            .w-menu-toggle {
                display: inline-flex;
                align-items: center; justify-content: center;
                width: 40px; height: 40px;
                border-radius: 10px;
                border: 1px solid var(--w-border);
                background: #fff;
            }
            .w-grid, .w-steps { grid-template-columns: 1fr; }
            .w-hero { padding: 72px 0 88px; }
            .w-hero-stats { gap: 32px; }
            .w-cta { padding: 48px 24px; }
            .w-footer-top { flex-direction: column; }
        }
    </style>
    @yield('page-style')
</head>
<body>

<header class="w-header">
    <div class="w-container w-header-inner">
        <a href="{{ route('home') }}" class="w-logo">
            <span class="w-logo-mark">B</span>
            {{ config('app.name', 'Beposms') }}
        </a>
        <nav class="w-nav">
            <div class="w-nav-links">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('website.packages') }}">Packages</a>
                <a href="{{ route('home') }}#features">Features</a>
            </div>
            <div class="w-nav-cta">
                <a href="{{ route('login') }}" class="w-btn w-btn-ghost">Login</a>
                <a href="{{ route('register') }}" class="w-btn w-btn-primary">Get Started</a>
            </div>
        </nav>
    </div>
</header>

@yield('content')

<footer class="w-footer">
    <div class="w-container">
        <div class="w-footer-top">
            <div class="w-footer-brand">
                <a href="{{ route('home') }}" class="w-logo" style="color:#fff;">
                    <span class="w-logo-mark">B</span>
                    {{ config('app.name', 'Beposms') }}
                </a>
                <p>{{ config('app.title', 'Bulk SMS & WhatsApp marketing platform') }} — reach your customers instantly, at scale.</p>
            </div>
            <div class="w-footer-links">
                <div class="w-footer-col">
                    <h4>Product</h4>
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('website.packages') }}">Packages</a>
                </div>
                <div class="w-footer-col">
                    <h4>Account</h4>
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ route('register') }}">Register</a>
                </div>
            </div>
        </div>
        <div class="w-footer-bottom">
            <span>{!! config('app.footer_text', 'Copyright &copy; ' . config('app.name') . ' - ' . date('Y')) !!}</span>
            <span>Powered by {{ config('app.name', 'Beposms') }}</span>
        </div>
    </div>
</footer>

</body>
</html>
