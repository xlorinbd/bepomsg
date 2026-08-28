<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.title', config('app.name')))</title>
    <meta name="description" content="@yield('meta_description', 'BepoMSG is the next-gen bulk SMS platform for marketing campaigns, OTP verification, and transactional messaging with guaranteed high-speed delivery.')">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-primary: #ff5500;
            --brand-primary-hover: #e04a00;
            --brand-glow: rgba(255, 85, 0, 0.35);
            --brand-orange-light: #fff7ed;
            --brand-orange-border: #ffedd5;
            
            --dark-base: #080b11;
            --dark-surface: #0e131f;
            --dark-card: #141b2b;
            --dark-card-hover: #192236;
            --dark-border: rgba(255, 255, 255, 0.08);
            --dark-border-glow: rgba(255, 85, 0, 0.25);
            
            --light-bg: #f8fafc;
            --light-surface: #ffffff;
            --light-border: #e2e8f0;
            --light-border-soft: #f1f5f9;
            
            --text-white: #ffffff;
            --text-muted-dark: #94a3b8;
            --text-subtle-dark: #64748b;
            
            --text-ink: #0f172a;
            --text-ink-soft: #475569;
            --text-ink-subtle: #64748b;
            
            --success: #10b981;
            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-full: 9999px;
            
            --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.04);
            --shadow-card: 0 10px 30px rgba(15, 23, 42, 0.06);
            --shadow-card-hover: 0 20px 40px rgba(15, 23, 42, 0.12);
            --shadow-glow: 0 0 40px rgba(255, 85, 0, 0.22);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background: var(--light-bg);
            color: var(--text-ink);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        a {
            color: inherit;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        img {
            max-width: 100%;
            display: block;
        }

        .w-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* ===== NAVBAR ===== */
        .w-header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(8, 11, 17, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--dark-border);
        }

        .w-header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 76px;
        }

        .w-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            font-size: 20px;
            letter-spacing: -0.02em;
            color: var(--text-white);
        }

        .w-logo-badge {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #ff6b00 0%, #ff3b00 100%);
            box-shadow: 0 4px 14px rgba(255, 85, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .w-logo-badge svg {
            width: 20px;
            height: 20px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2.2;
        }

        .w-nav-links {
            display: flex;
            align-items: center;
            gap: 32px;
        }

        .w-nav-links a {
            font-size: 14.5px;
            font-weight: 600;
            color: var(--text-muted-dark);
            transition: color 0.15s ease;
        }

        .w-nav-links a:hover {
            color: var(--text-white);
        }

        .w-nav-cta {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .w-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 700;
            font-size: 14px;
            border-radius: var(--radius-full);
            padding: 10px 22px;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .w-btn-signin {
            color: var(--text-muted-dark);
            font-weight: 600;
            padding: 8px 12px;
        }

        .w-btn-signin:hover {
            color: var(--text-white);
        }

        .w-btn-primary {
            background: linear-gradient(135deg, #ff6000 0%, #ff4500 100%);
            color: #fff;
            box-shadow: 0 4px 18px rgba(255, 85, 0, 0.35);
        }

        .w-btn-primary:hover {
            background: linear-gradient(135deg, #ff6f14 0%, #eb3e00 100%);
            box-shadow: 0 6px 24px rgba(255, 85, 0, 0.45);
            transform: translateY(-1px);
        }

        .w-btn-dark {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: var(--text-white);
        }

        .w-btn-dark:hover {
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(255, 255, 255, 0.28);
            transform: translateY(-1px);
        }

        .w-btn-lg {
            padding: 13px 28px;
            font-size: 15.5px;
        }

        .w-mobile-toggle {
            display: none;
            background: transparent;
            border: 1px solid var(--dark-border);
            color: var(--text-white);
            width: 40px;
            height: 40px;
            border-radius: 8px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
        }

        /* Mobile drawer */
        .w-mobile-drawer {
            display: none;
            position: fixed;
            top: 76px;
            left: 0;
            right: 0;
            background: #0d121c;
            border-bottom: 1px solid var(--dark-border);
            padding: 24px;
            flex-direction: column;
            gap: 16px;
            z-index: 999;
        }

        .w-mobile-drawer.active {
            display: flex;
        }

        .w-mobile-drawer a {
            color: #cbd5e1;
            font-size: 16px;
            font-weight: 600;
            padding: 8px 0;
        }

        /* ===== FOOTER ===== */
        .w-footer {
            background: #06090e;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
            color: var(--text-muted-dark);
            padding: 72px 0 36px;
        }

        .w-footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 48px;
            padding-bottom: 48px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
            margin-bottom: 32px;
        }

        .w-footer-brand p {
            font-size: 14.5px;
            color: #94a3b8;
            margin-top: 16px;
            max-width: 320px;
            line-height: 1.6;
        }

        .w-footer-col h4 {
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 18px;
        }

        .w-footer-col ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .w-footer-col a {
            color: #94a3b8;
            font-size: 14px;
            font-weight: 500;
        }

        .w-footer-col a:hover {
            color: #ff6b00;
        }

        .w-footer-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13.5px;
            color: #64748b;
            flex-wrap: wrap;
            gap: 16px;
        }

        .w-footer-bottom a {
            color: #64748b;
        }

        .w-footer-bottom a:hover {
            color: #cbd5e1;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            .w-footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 36px;
            }
        }

        @media (max-width: 768px) {
            .w-nav-links, .w-btn-signin {
                display: none;
            }
            .w-mobile-toggle {
                display: flex;
            }
            .w-footer-grid {
                grid-template-columns: 1fr;
                gap: 32px;
            }
        }
    </style>
    @yield('page-style')
</head>
<body>

<header class="w-header">
    <div class="w-container w-header-inner">
        <a href="{{ route('home') }}" class="w-logo">
            <div class="w-logo-badge">
                <svg viewBox="0 0 24 24">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                </svg>
            </div>
            <span>{{ config('app.name', 'BepoMSG') }}</span>
        </a>

        <nav class="w-nav-links">
            <a href="{{ route('home') }}#features">Features</a>
            <a href="{{ route('home') }}#solutions">Solutions</a>
            <a href="{{ route('home') }}#how-it-works">How It Works</a>
            <a href="{{ route('home') }}#developers">Developers</a>
            <a href="{{ route('website.packages') }}">Pricing</a>
            <a href="{{ route('home') }}#faq">FAQ</a>
        </nav>

        <div class="w-nav-cta">
            <a href="{{ route('login') }}" class="w-btn-signin">Sign In</a>
            <a href="{{ route('register') }}" class="w-btn w-btn-primary">Get started &rarr;</a>
            <button class="w-mobile-toggle" id="mobileMenuBtn" aria-label="Toggle Navigation">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>
</header>

<div class="w-mobile-drawer" id="mobileDrawer">
    <a href="{{ route('home') }}#features">Features</a>
    <a href="{{ route('home') }}#solutions">Solutions</a>
    <a href="{{ route('home') }}#how-it-works">How It Works</a>
    <a href="{{ route('home') }}#developers">Developers</a>
    <a href="{{ route('website.packages') }}">Pricing</a>
    <a href="{{ route('home') }}#faq">FAQ</a>
    <hr style="border: 0; border-top: 1px solid var(--dark-border); margin: 8px 0;">
    <a href="{{ route('login') }}">Sign In</a>
    <a href="{{ route('register') }}" class="w-btn w-btn-primary" style="text-align: center;">Get started &rarr;</a>
</div>

@yield('content')

<footer class="w-footer">
    <div class="w-container">
        <div class="w-footer-grid">
            <div class="w-footer-brand">
                <a href="{{ route('home') }}" class="w-logo">
                    <div class="w-logo-badge">
                        <svg viewBox="0 0 24 24">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                        </svg>
                    </div>
                    <span>{{ config('app.name', 'BepoMSG') }}</span>
                </a>
                <p>The next-generation bulk SMS and messaging platform for high-converting marketing campaigns, OTPs, and alerts at global scale.</p>
            </div>

            <div class="w-footer-col">
                <h4>Product</h4>
                <ul>
                    <li><a href="{{ route('home') }}#features">Bulk Campaigns</a></li>
                    <li><a href="{{ route('home') }}#solutions">OTP & 2FA Delivery</a></li>
                    <li><a href="{{ route('home') }}#developers">REST API & SDKs</a></li>
                    <li><a href="{{ route('website.packages') }}">Pricing Plans</a></li>
                </ul>
            </div>

            <div class="w-footer-col">
                <h4>Solutions</h4>
                <ul>
                    <li><a href="{{ route('home') }}#solutions">Marketing Campaigns</a></li>
                    <li><a href="{{ route('home') }}#solutions">Operations & Alerts</a></li>
                    <li><a href="{{ route('home') }}#solutions">Authentication</a></li>
                    <li><a href="{{ route('home') }}#faq">Help & FAQ</a></li>
                </ul>
            </div>

            <div class="w-footer-col">
                <h4>Account</h4>
                <ul>
                    <li><a href="{{ route('login') }}">Client Portal</a></li>
                    <li><a href="{{ route('register') }}">Create Account</a></li>
                    <li><a href="{{ route('home') }}#pricing">Coverage Rates</a></li>
                </ul>
            </div>
        </div>

        <div class="w-footer-bottom">
            <div>{!! config('app.footer_text', 'Copyright &copy; ' . config('app.name', 'BepoMSG') . ' - ' . date('Y')) !!}</div>
            <div style="display:flex; gap: 20px;">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Security</a>
            </div>
        </div>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileBtn = document.getElementById('mobileMenuBtn');
        const drawer = document.getElementById('mobileDrawer');
        if (mobileBtn && drawer) {
            mobileBtn.addEventListener('click', function() {
                drawer.classList.toggle('active');
            });
            drawer.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => drawer.classList.remove('active'));
            });
        }
    });
</script>

</body>
</html>
