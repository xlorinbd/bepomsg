@extends('website.layout')

@section('title', config('app.name', 'BepoMSG') . ' — Next-Gen Bulk SMS & Marketing Platform')
@section('meta_description', 'Reach millions in seconds with BepoMSG. Enterprise-grade Bulk SMS, OTP verification, and marketing automation with guaranteed high-speed delivery.')

@section('page-style')
<style>
    /* ===== HERO SECTION ===== */
    .hero-wrapper {
        background: radial-gradient(circle at 80% 20%, rgba(255, 85, 0, 0.16) 0%, rgba(8, 11, 17, 1) 70%),
                    linear-gradient(180deg, #080b11 0%, #0d121c 100%);
        color: #ffffff;
        padding: 80px 0 100px;
        position: relative;
        overflow: hidden;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .hero-grid {
        display: grid;
        grid-template-columns: 1.15fr 0.85fr;
        gap: 56px;
        align-items: center;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 85, 0, 0.12);
        border: 1px solid rgba(255, 85, 0, 0.35);
        color: #ff8533;
        padding: 6px 16px;
        border-radius: var(--radius-full);
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        margin-bottom: 24px;
    }

    .hero-badge .dot {
        width: 6px;
        height: 6px;
        background: #ff5500;
        border-radius: 50%;
        box-shadow: 0 0 10px #ff5500;
    }

    .hero-title {
        font-size: clamp(38px, 4.8vw, 62px);
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -0.03em;
        margin-bottom: 22px;
        color: #ffffff;
    }

    .hero-title span {
        color: #ff5500;
        text-shadow: 0 0 35px rgba(255, 85, 0, 0.4);
    }

    .hero-subtitle {
        font-size: 17.5px;
        line-height: 1.65;
        color: #94a3b8;
        max-width: 540px;
        margin-bottom: 36px;
    }

    .hero-cta-group {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }

    .hero-trust {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13.5px;
        color: #64748b;
        font-weight: 500;
    }

    .hero-trust svg {
        color: #10b981;
    }

    /* Mockup Visual */
    .hero-visual {
        position: relative;
    }

    .hero-visual::before {
        content: '';
        position: absolute;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(255, 85, 0, 0.4) 0%, rgba(255, 85, 0, 0) 70%);
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        filter: blur(40px);
        pointer-events: none;
        z-index: 0;
    }

    .mockup-card {
        position: relative;
        z-index: 1;
        background: rgba(18, 24, 38, 0.85);
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6), 0 0 30px rgba(255, 85, 0, 0.15);
        border-radius: 20px;
        padding: 24px;
        backdrop-filter: blur(20px);
    }

    .mockup-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        margin-bottom: 20px;
    }

    .mockup-dots {
        display: flex;
        gap: 6px;
    }

    .mockup-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .mockup-dot.red { background: #ef4444; }
    .mockup-dot.yellow { background: #f59e0b; }
    .mockup-dot.green { background: #10b981; }

    .mockup-tag {
        font-size: 11.5px;
        font-weight: 700;
        color: #ff8533;
        background: rgba(255, 85, 0, 0.15);
        padding: 3px 10px;
        border-radius: 6px;
    }

    .mockup-stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }

    .mockup-mini-stat {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        padding: 12px;
    }

    .mockup-mini-stat .label {
        font-size: 11px;
        color: #94a3b8;
        font-weight: 500;
        margin-bottom: 4px;
    }

    .mockup-mini-stat .val {
        font-size: 16px;
        font-weight: 800;
        color: #ffffff;
    }

    .mockup-mini-stat .val span {
        color: #10b981;
        font-size: 11px;
        font-weight: 600;
        margin-left: 4px;
    }

    .mockup-chart-box {
        background: rgba(10, 14, 22, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 16px;
    }

    .mockup-chart-head {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: #94a3b8;
        margin-bottom: 12px;
        font-weight: 600;
    }

    .mockup-chart-head b {
        color: #ffffff;
    }

    .mockup-chart-svg {
        width: 100%;
        height: 90px;
    }

    .mockup-activity {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 12px;
        color: #94a3b8;
        padding: 8px 12px;
        background: rgba(255, 85, 0, 0.08);
        border: 1px solid rgba(255, 85, 0, 0.2);
        border-radius: 8px;
    }

    .mockup-activity .pulse {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 8px #10b981;
    }

    /* ===== STATS STRIP ===== */
    .stats-strip {
        background: #080b11;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        padding: 42px 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
        text-align: center;
    }

    .stat-item {
        position: relative;
    }

    .stat-item:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0;
        top: 20%;
        height: 60%;
        width: 1px;
        background: rgba(255, 255, 255, 0.08);
    }

    .stat-number {
        font-size: clamp(32px, 3.5vw, 44px);
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.02em;
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 13.5px;
        color: #94a3b8;
        font-weight: 600;
    }

    /* ===== SECTION STYLES ===== */
    .sec-light {
        background: #ffffff;
        padding: 96px 0;
    }

    .sec-soft {
        background: #f8fafc;
        padding: 96px 0;
    }

    .sec-dark {
        background: #080b11;
        color: #ffffff;
        padding: 96px 0;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .sec-header {
        text-align: center;
        max-width: 680px;
        margin: 0 auto 56px;
    }

    .sec-kicker {
        display: inline-block;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #ff5500;
        margin-bottom: 12px;
    }

    .sec-title {
        font-size: clamp(28px, 3.5vw, 42px);
        font-weight: 800;
        letter-spacing: -0.02em;
        margin-bottom: 14px;
        line-height: 1.2;
    }

    .sec-subtitle {
        font-size: 16.5px;
        color: var(--text-ink-soft);
        line-height: 1.6;
    }

    .sec-dark .sec-subtitle {
        color: #94a3b8;
    }

    /* ===== FEATURES GRID ===== */
    .features-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 28px;
    }

    .feature-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-md);
        padding: 32px 28px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        transition: all 0.25s ease;
    }

    .feature-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
        border-color: #cbd5e1;
    }

    .feature-icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #fff3eb;
        color: #ff5500;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }

    .feature-icon-circle svg {
        width: 24px;
        height: 24px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
    }

    .feature-card h3 {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 10px;
    }

    .feature-card p {
        font-size: 14.5px;
        color: #475569;
        line-height: 1.6;
    }

    /* ===== SOLUTIONS (ONE PLATFORM, THREE JOBS) ===== */
    .solutions-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 28px;
    }

    .solution-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-lg);
        padding: 36px 30px;
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
    }

    .solution-pill {
        align-self: flex-start;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #ff5500;
        background: #fff2ea;
        padding: 4px 12px;
        border-radius: var(--radius-full);
        margin-bottom: 18px;
    }

    .solution-card h3 {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 12px;
        line-height: 1.3;
    }

    .solution-card p {
        font-size: 14.5px;
        color: #475569;
        margin-bottom: 24px;
        line-height: 1.6;
    }

    .solution-list {
        list-style: none;
        margin-top: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
        border-top: 1px solid #f1f5f9;
        padding-top: 20px;
    }

    .solution-list li {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        color: #334155;
        font-weight: 600;
    }

    .solution-list li svg {
        width: 18px;
        height: 18px;
        color: #ff5500;
        flex-shrink: 0;
    }

    /* ===== HOW IT WORKS (FOUR STEPS) ===== */
    .steps-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
        position: relative;
    }

    .step-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-md);
        padding: 28px 22px;
        position: relative;
    }

    .step-num-badge {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #0f172a;
        color: #ffffff;
        font-size: 15px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 18px;
        border: 2px solid #ff5500;
    }

    .step-card h3 {
        font-size: 16.5px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .step-card p {
        font-size: 13.5px;
        color: #64748b;
        line-height: 1.5;
    }

    /* ===== DEVELOPERS SECTION ===== */
    .dev-grid {
        display: grid;
        grid-template-columns: 1fr 1.15fr;
        gap: 56px;
        align-items: center;
    }

    .dev-features {
        display: flex;
        flex-direction: column;
        gap: 18px;
        margin-top: 28px;
    }

    .dev-feat-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        font-size: 15px;
        color: #cbd5e1;
    }

    .dev-feat-item svg {
        width: 20px;
        height: 20px;
        color: #ff5500;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .code-window {
        background: #0f1523;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
    }

    .code-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #141c2e;
        padding: 12px 18px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .code-tabs {
        display: flex;
        gap: 8px;
    }

    .code-tab-btn {
        background: transparent;
        border: none;
        color: #94a3b8;
        font-size: 13px;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .code-tab-btn.active {
        background: #ff5500;
        color: #ffffff;
    }

    .code-body {
        padding: 20px;
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-size: 13.5px;
        line-height: 1.6;
        color: #e2e8f0;
        overflow-x: auto;
    }

    .code-pane {
        display: none;
        white-space: pre;
    }

    .code-pane.active {
        display: block;
    }

    .c-hl-key { color: #38bdf8; }
    .c-hl-str { color: #34d399; }
    .c-hl-num { color: #f59e0b; }
    .c-hl-kw { color: #f472b6; }
    .c-hl-com { color: #64748b; }

    /* ===== PRICING CARDS ===== */
    .pricing-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 28px;
        align-items: stretch;
    }

    .pricing-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-lg);
        padding: 36px 30px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
        position: relative;
        transition: all 0.25s ease;
    }

    .pricing-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.09);
    }

    .pricing-card.featured {
        border: 2px solid #ff5500;
        box-shadow: 0 12px 35px rgba(255, 85, 0, 0.15);
    }

    .pricing-featured-badge {
        position: absolute;
        top: -13px;
        left: 50%;
        transform: translateX(-50%);
        background: #ff5500;
        color: #ffffff;
        font-size: 11.5px;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 5px 16px;
        border-radius: var(--radius-full);
    }

    .pricing-title {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .pricing-desc {
        font-size: 13.5px;
        color: #64748b;
        min-height: 38px;
        margin-bottom: 20px;
    }

    .pricing-rate {
        display: flex;
        align-items: baseline;
        gap: 6px;
        margin-bottom: 6px;
    }

    .pricing-rate .price {
        font-size: 42px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.03em;
    }

    .pricing-rate .period {
        font-size: 14px;
        color: #64748b;
        font-weight: 600;
    }

    .pricing-features {
        list-style: none;
        margin: 24px 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 12px;
        flex: 1;
        border-top: 1px solid #f1f5f9;
        padding-top: 24px;
    }

    .pricing-features li {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        color: #334155;
    }

    .pricing-features li svg {
        width: 18px;
        height: 18px;
        color: #ff5500;
        flex-shrink: 0;
    }

    /* ===== GLOBAL COVERAGE STRIP ===== */
    .coverage-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-md);
        padding: 24px 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 48px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        flex-wrap: wrap;
        gap: 20px;
    }

    .coverage-info {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .coverage-icon {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #fff2ea;
        color: #ff5500;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .coverage-text h4 {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 2px;
    }

    .coverage-text p {
        font-size: 13.5px;
        color: #64748b;
    }

    .coverage-chips {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .coverage-chip {
        background: #f1f5f9;
        padding: 6px 14px;
        border-radius: var(--radius-full);
        font-size: 13px;
        font-weight: 600;
        color: #334155;
    }

    /* ===== FAQ SECTION ===== */
    .faq-wrapper {
        max-width: 780px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .faq-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-md);
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .faq-item.active {
        border-color: #cbd5e1;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
    }

    .faq-question {
        padding: 20px 24px;
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        user-select: none;
    }

    .faq-question svg {
        width: 20px;
        height: 20px;
        color: #64748b;
        transition: transform 0.25s ease;
    }

    .faq-item.active .faq-question svg {
        transform: rotate(180deg);
        color: #ff5500;
    }

    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, padding 0.3s ease;
        padding: 0 24px;
        font-size: 14.5px;
        color: #475569;
        line-height: 1.65;
    }

    .faq-item.active .faq-answer {
        max-height: 250px;
        padding: 0 24px 22px;
    }

    /* ===== FINAL CTA BANNER ===== */
    .cta-banner {
        background: linear-gradient(135deg, #ff5500 0%, #ff3700 100%);
        border-radius: 28px;
        padding: 72px 48px;
        text-align: center;
        color: #ffffff;
        box-shadow: 0 20px 50px rgba(255, 85, 0, 0.35);
        position: relative;
        overflow: hidden;
    }

    .cta-banner::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 10% 20%, rgba(255, 255, 255, 0.2) 0%, transparent 50%),
                    radial-gradient(circle at 90% 80%, rgba(0, 0, 0, 0.25) 0%, transparent 60%);
        pointer-events: none;
    }

    .cta-banner h2 {
        font-size: clamp(30px, 4vw, 44px);
        font-weight: 800;
        margin-bottom: 14px;
        letter-spacing: -0.02em;
    }

    .cta-banner p {
        font-size: 17px;
        max-width: 580px;
        margin: 0 auto 36px;
        color: rgba(255, 255, 255, 0.92);
    }

    .cta-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .w-btn-cta-dark {
        background: #080b11;
        color: #ffffff;
        padding: 14px 32px;
        font-size: 15.5px;
    }

    .w-btn-cta-dark:hover {
        background: #141b2b;
        transform: translateY(-2px);
    }

    .w-btn-cta-light {
        background: rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.4);
        color: #ffffff;
        padding: 14px 32px;
        font-size: 15.5px;
    }

    .w-btn-cta-light:hover {
        background: rgba(255, 255, 255, 0.28);
        transform: translateY(-2px);
    }

    /* ===== RESPONSIVE BREAKPOINTS ===== */
    @media (max-width: 1024px) {
        .hero-grid, .dev-grid {
            grid-template-columns: 1fr;
            gap: 48px;
        }
        .features-grid, .solutions-grid, .pricing-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .steps-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .hero-wrapper {
            padding: 60px 0 80px;
        }
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 32px;
        }
        .stat-item:nth-child(2)::after {
            display: none;
        }
        .features-grid, .solutions-grid, .pricing-grid, .steps-grid {
            grid-template-columns: 1fr;
        }
        .cta-banner {
            padding: 48px 24px;
        }
    }
</style>
@endsection

@section('content')

    <!-- HERO SECTION -->
    <section class="hero-wrapper">
        <div class="w-container">
            <div class="hero-grid">
                <div>
                    <div class="hero-badge">
                        <span class="dot"></span>
                        NEXT-GEN BULK SMS ENGINE
                    </div>
                    <h1 class="hero-title">
                        Reach millions in <span>seconds.</span>
                    </h1>
                    <p class="hero-subtitle">
                        {{ config('app.name', 'BepoMSG') }} is the bulk SMS platform built for high-converting marketing campaigns, instant OTP verification, and transactional alerts with guaranteed high-speed delivery.
                    </p>
                    <div class="hero-cta-group">
                        <a href="{{ route('register') }}" class="w-btn w-btn-primary w-btn-lg">Start sending free &rarr;</a>
                        <a href="{{ route('login') }}" class="w-btn w-btn-dark w-btn-lg">Talk to sales</a>
                    </div>
                    <div class="hero-trust">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>No credit card required &bull; Instant API credentials</span>
                    </div>
                </div>

                <!-- Hero Visual Dashboard Mockup -->
                <div class="hero-visual">
                    <div class="mockup-card">
                        <div class="mockup-header">
                            <div class="mockup-dots">
                                <span class="mockup-dot red"></span>
                                <span class="mockup-dot yellow"></span>
                                <span class="mockup-dot green"></span>
                            </div>
                            <span class="mockup-tag">LIVE CONSOLE</span>
                        </div>

                        <div class="mockup-stats-row">
                            <div class="mockup-mini-stat">
                                <div class="label">Delivered</div>
                                <div class="val">99.8% <span>&uarr; 0.4%</span></div>
                            </div>
                            <div class="mockup-mini-stat">
                                <div class="label">Latency</div>
                                <div class="val">1.2s <span>Fast</span></div>
                            </div>
                            <div class="mockup-mini-stat">
                                <div class="label">Campaign ROI</div>
                                <div class="val">4.8x <span>+22%</span></div>
                            </div>
                        </div>

                        <div class="mockup-chart-box">
                            <div class="mockup-chart-head">
                                <span>Throughput (Messages / sec)</span>
                                <b>18,400 TPS Peak</b>
                            </div>
                            <svg class="mockup-chart-svg" viewBox="0 0 400 100" preserveAspectRatio="none">
                                <defs>
                                    <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#ff5500" stop-opacity="0.45"></stop>
                                        <stop offset="100%" stop-color="#ff5500" stop-opacity="0"></stop>
                                    </linearGradient>
                                </defs>
                                <path d="M0,80 Q40,65 80,75 T160,40 T240,55 T320,20 T400,10 L400,100 L0,100 Z" fill="url(#chartGrad)"></path>
                                <path d="M0,80 Q40,65 80,75 T160,40 T240,55 T320,20 T400,10" fill="none" stroke="#ff5500" stroke-width="3" stroke-linecap="round"></path>
                            </svg>
                        </div>

                        <div class="mockup-activity">
                            <span class="pulse"></span>
                            <span>OTP verification route active &bull; Latency: 0.8s &bull; Delivered</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- METRICS STRIP -->
    <section class="stats-strip">
        <div class="w-container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">2.4B+</div>
                    <div class="stat-label">Messages delivered</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">99.4%</div>
                    <div class="stat-label">Average delivery rate</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">190+</div>
                    <div class="stat-label">Countries covered</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">&lt;3s</div>
                    <div class="stat-label">Direct-route latency</div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section class="sec-light" id="features">
        <div class="w-container">
            <div class="sec-header">
                <span class="sec-kicker">Features</span>
                <h2 class="sec-title">Everything you need to run SMS at scale</h2>
                <p class="sec-subtitle">
                    Cut campaign complexity, improve deliverability, and scale your SMS messaging with powerful built-in tools.
                </p>
            </div>

            <div class="features-grid">
                <!-- Card 1 -->
                <div class="feature-card">
                    <div class="feature-icon-circle">
                        <svg viewBox="0 0 24 24"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"></path></svg>
                    </div>
                    <h3>Bulk Campaigns</h3>
                    <p>Send to millions without delays. Dynamic tags, scheduling, contact lists, CSV import, and real-time delivery reports.</p>
                </div>

                <!-- Card 2 -->
                <div class="feature-card">
                    <div class="feature-icon-circle">
                        <svg viewBox="0 0 24 24"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                    </div>
                    <h3>REST API & Webhooks</h3>
                    <p>Zero-friction developer onboarding. High-throughput endpoints, automatic retries, and real-time DLR callbacks.</p>
                </div>

                <!-- Card 3 -->
                <div class="feature-card">
                    <div class="feature-icon-circle">
                        <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </div>
                    <h3>2FA & OTP Delivery</h3>
                    <p>Sub-3-second latency OTP routes with automatic fallbacks, short expiration windows, and fraud protection.</p>
                </div>

                <!-- Card 4 -->
                <div class="feature-card">
                    <div class="feature-icon-circle">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                    </div>
                    <h3>Smart Routing & Telco</h3>
                    <p>Multi-carrier intelligent routing selects the highest delivery route in real time. Zero single points of failure.</p>
                </div>

                <!-- Card 5 -->
                <div class="feature-card">
                    <div class="feature-icon-circle">
                        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <h3>Contact & List Manager</h3>
                    <p>Organize audience segments, clean invalid numbers, handle unsubscribes automatically, and manage custom fields.</p>
                </div>

                <!-- Card 6 -->
                <div class="feature-card">
                    <div class="feature-icon-circle">
                        <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    </div>
                    <h3>Compliance & Sender ID</h3>
                    <p>Branded Sender IDs with fast approval. Automatic opt-out management and carrier regulation compliance.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SOLUTIONS (ONE PLATFORM, THREE JOBS) -->
    <section class="sec-soft" id="solutions">
        <div class="w-container">
            <div class="sec-header">
                <span class="sec-kicker">Solutions</span>
                <h2 class="sec-title">One platform, three jobs</h2>
                <p class="sec-subtitle">
                    Purpose-built infrastructure engineered for marketing conversion, critical operations, and security.
                </p>
            </div>

            <div class="solutions-grid">
                <!-- Solution 1 -->
                <div class="solution-card">
                    <span class="solution-pill">Marketing</span>
                    <h3>Promotional alerts that actually convert</h3>
                    <p>Reach customers with flash sales, product drops, and personalized discount codes that drive instant conversions.</p>
                    <ul class="solution-list">
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span>Flash sales & seasonal discounts</span>
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span>Personalized customer tags & merge fields</span>
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span>Link tracking, shortener & click analytics</span>
                        </li>
                    </ul>
                </div>

                <!-- Solution 2 -->
                <div class="solution-card">
                    <span class="solution-pill">Operations</span>
                    <h3>Alerts, transactional & updates</h3>
                    <p>Keep customers informed at every step of their journey with mission-critical automated notifications.</p>
                    <ul class="solution-list">
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span>Order confirmations & live parcel tracking</span>
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span>Account security & balance updates</span>
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span>Appointment & event reminders</span>
                        </li>
                    </ul>
                </div>

                <!-- Solution 3 -->
                <div class="solution-card">
                    <span class="solution-pill">Authentication</span>
                    <h3>OTP delivery in under three seconds</h3>
                    <p>Prevent signup drop-offs and secure user accounts with ultra-low latency verification routes.</p>
                    <ul class="solution-list">
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span>Direct carrier bypass routing</span>
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span>Automatic fallback & retry logic</span>
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span>Bank-grade fraud & spam protection</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS (FOUR STEPS) -->
    <section class="sec-light" id="how-it-works">
        <div class="w-container">
            <div class="sec-header">
                <span class="sec-kicker">How It Works</span>
                <h2 class="sec-title">From CSV to delivered in four steps</h2>
                <p class="sec-subtitle">
                    Launch your next SMS campaign in minutes with our streamlined four-step workflow.
                </p>
            </div>

            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-num-badge">1</div>
                    <h3>Create your account</h3>
                    <p>Register in under a minute and access your instant sandbox & production keys.</p>
                </div>

                <div class="step-card">
                    <div class="step-num-badge">2</div>
                    <h3>Upload your contacts</h3>
                    <p>Drag-and-drop CSV, Excel, or sync contacts via REST API and webhooks.</p>
                </div>

                <div class="step-card">
                    <div class="step-num-badge">3</div>
                    <h3>Compose your message</h3>
                    <p>Craft personalized messages with dynamic merge tags and preview live.</p>
                </div>

                <div class="step-card">
                    <div class="step-num-badge">4</div>
                    <h3>Send and track live</h3>
                    <p>Deliver at blazing speed and monitor delivery receipts (DLR) in real time.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- DEVELOPERS API SECTION -->
    <section class="sec-dark" id="developers">
        <div class="w-container">
            <div class="dev-grid">
                <div>
                    <span class="sec-kicker">For Developers</span>
                    <h2 class="sec-title">One API call. That's the integration.</h2>
                    <p class="sec-subtitle">
                        A clean RESTful API with fast responses, detailed error codes, and production-ready SDKs for PHP, Python, Node, and cURL.
                    </p>

                    <div class="dev-features">
                        <div class="dev-feat-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span><b>99.99% Uptime SLA</b> backed by redundant global gateways</span>
                        </div>
                        <div class="dev-feat-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span><b>Real-time Webhooks</b> for DLR status and incoming replies</span>
                        </div>
                        <div class="dev-feat-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span><b>Pre-built SDKs</b> &amp; Postman collections ready for testing</span>
                        </div>
                        <div class="dev-feat-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span><b>Tier-1 Direct Carrier Routes</b> for sub-second OTP speeds</span>
                        </div>
                    </div>
                </div>

                <!-- Code Terminal Box -->
                <div class="code-window">
                    <div class="code-topbar">
                        <div class="code-tabs">
                            <button class="code-tab-btn active" onclick="switchCodeTab('curl')">cURL</button>
                            <button class="code-tab-btn" onclick="switchCodeTab('php')">PHP</button>
                            <button class="code-tab-btn" onclick="switchCodeTab('node')">Node.js</button>
                            <button class="code-tab-btn" onclick="switchCodeTab('python')">Python</button>
                        </div>
                        <span style="font-size: 11.5px; color: #64748b; font-weight:600;">v2.1 REST API</span>
                    </div>
                    <div class="code-body">
<!-- cURL -->
<div id="tab-curl" class="code-pane active">curl -X POST {{ url('/api/v2/sms/send') }} \
  -H <span class="c-hl-str">"Authorization: Bearer YOUR_API_KEY"</span> \
  -H <span class="c-hl-str">"Content-Type: application/json"</span> \
  -d <span class="c-hl-str">'{
    <span class="c-hl-key">"recipient"</span>: <span class="c-hl-str">"+8801700000000"</span>,
    <span class="c-hl-key">"sender_id"</span>: <span class="c-hl-str">"BepoMSG"</span>,
    <span class="c-hl-key">"type"</span>: <span class="c-hl-str">"plain"</span>,
    <span class="c-hl-key">"message"</span>: <span class="c-hl-str">"Your verification code is 849201. Valid for 5 mins."</span>
  }'</span></div>

<!-- PHP -->
<div id="tab-php" class="code-pane"><span class="c-hl-kw">&lt;?php</span>
<span class="c-hl-com">// Send SMS via BepoMSG PHP Client</span>
<span class="c-hl-key">$response</span> = Http::withToken(<span class="c-hl-str">'YOUR_API_KEY'</span>)-&gt;post(<span class="c-hl-str">'{{ url('/api/v2/sms/send') }}'</span>, [
    <span class="c-hl-key">'recipient'</span> =&gt; <span class="c-hl-str">'+8801700000000'</span>,
    <span class="c-hl-key">'sender_id'</span> =&gt; <span class="c-hl-str">'BepoMSG'</span>,
    <span class="c-hl-key">'message'</span>   =&gt; <span class="c-hl-str">'Your verification code is 849201.'</span>,
]);

<span class="c-hl-kw">return</span> <span class="c-hl-key">$response</span>-&gt;json();</div>

<!-- Node -->
<div id="tab-node" class="code-pane"><span class="c-hl-kw">const</span> axios = <span class="c-hl-kw">require</span>(<span class="c-hl-str">'axios'</span>);

<span class="c-hl-kw">const</span> res = <span class="c-hl-kw">await</span> axios.post(<span class="c-hl-str">'{{ url('/api/v2/sms/send') }}'</span>, {
  recipient: <span class="c-hl-str">'+8801700000000'</span>,
  sender_id: <span class="c-hl-str">'BepoMSG'</span>,
  message: <span class="c-hl-str">'Your verification code is 849201.'</span>
}, {
  headers: { <span class="c-hl-str">'Authorization'</span>: <span class="c-hl-str">'Bearer YOUR_API_KEY'</span> }
});

console.log(res.data);</div>

<!-- Python -->
<div id="tab-python" class="code-pane"><span class="c-hl-kw">import</span> requests

url = <span class="c-hl-str">"{{ url('/api/v2/sms/send') }}"</span>
payload = {
    <span class="c-hl-key">"recipient"</span>: <span class="c-hl-str">"+8801700000000"</span>,
    <span class="c-hl-key">"sender_id"</span>: <span class="c-hl-str">"BepoMSG"</span>,
    <span class="c-hl-key">"message"</span>: <span class="c-hl-str">"Your verification code is 849201."</span>
}
headers = {<span class="c-hl-str">"Authorization"</span>: <span class="c-hl-str">"Bearer YOUR_API_KEY"</span>}

response = requests.post(url, json=payload, headers=headers)
print(response.json())</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PRICING SECTION -->
    <section class="sec-light" id="pricing">
        <div class="w-container">
            <div class="sec-header">
                <span class="sec-kicker">Pricing</span>
                <h2 class="sec-title">Pay for what you send</h2>
                <p class="sec-subtitle">
                    Transparent rates with no hidden fees, flexible credit options, and volume discounts as you scale.
                </p>
            </div>

            <div class="pricing-grid">
                @if(isset($plans) && $plans->count() > 0)
                    @foreach($plans as $plan)
                        <div class="pricing-card @if($plan->is_popular) featured @endif">
                            @if($plan->is_popular)
                                <div class="pricing-featured-badge">Most Popular</div>
                            @endif
                            <div class="pricing-title">{{ $plan->name }}</div>
                            <div class="pricing-desc">{{ $plan->description ?: 'Ideal for fast-growing businesses and transactional messaging.' }}</div>

                            <div class="pricing-rate">
                                <span class="price">{{ $plan->getBillableFormattedPrice() }}</span>
                                <span class="period">/ {{ $plan->displayFrequencyTime() ?: 'month' }}</span>
                            </div>

                            <ul class="pricing-features">
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <span><b>{{ $plan->displayTotalQuota() }}</b> SMS credits</span>
                                </li>
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <span><b>{{ $plan->displayWhatsAppQuota() }}</b> WhatsApp credits</span>
                                </li>
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <span><b>{{ $plan->displayMaxContact() }}</b> Contacts limit</span>
                                </li>
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <span>REST API &amp; Webhook access</span>
                                </li>
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <span>Real-time delivery receipts (DLR)</span>
                                </li>
                            </ul>

                            <a href="{{ route('register') }}" class="w-btn @if($plan->is_popular) w-btn-primary @else w-btn-dark @endif" style="width: 100%; @if(!$plan->is_popular) background: #0f172a; color:#fff; @endif">Get started</a>
                        </div>
                    @endforeach
                @else
                    <!-- Default Static Cards if no DB plans -->
                    <div class="pricing-card">
                        <div class="pricing-title">Starter</div>
                        <div class="pricing-desc">For small teams and startups testing out SMS campaigns.</div>
                        <div class="pricing-rate">
                            <span class="price">$29</span>
                            <span class="period">/ month</span>
                        </div>
                        <ul class="pricing-features">
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><span><b>5,000</b> SMS credits</span></li>
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><span>Custom Sender ID</span></li>
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><span>REST API access</span></li>
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><span>Basic analytics</span></li>
                        </ul>
                        <a href="{{ route('register') }}" class="w-btn" style="width:100%; background:#0f172a; color:#fff;">Start free trial</a>
                    </div>

                    <div class="pricing-card featured">
                        <div class="pricing-featured-badge">Most Popular</div>
                        <div class="pricing-title">Growth</div>
                        <div class="pricing-desc">For high-converting marketing campaigns and fast OTPs.</div>
                        <div class="pricing-rate">
                            <span class="price">$89</span>
                            <span class="period">/ month</span>
                        </div>
                        <ul class="pricing-features">
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><span><b>25,000</b> SMS credits</span></li>
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><span>Dedicated direct routes</span></li>
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><span>Real-time webhooks & DLR</span></li>
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><span>Priority support 24/7</span></li>
                        </ul>
                        <a href="{{ route('register') }}" class="w-btn w-btn-primary" style="width:100%;">Get started now</a>
                    </div>

                    <div class="pricing-card">
                        <div class="pricing-title">Enterprise</div>
                        <div class="pricing-desc">For large platforms needing custom volume and high throughput.</div>
                        <div class="pricing-rate">
                            <span class="price">Custom</span>
                        </div>
                        <ul class="pricing-features">
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><span>Unlimited volume quota</span></li>
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><span>Dedicated account manager</span></li>
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><span>99.99% Custom SLA guarantee</span></li>
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><span>Custom billing &amp; Postpaid</span></li>
                        </ul>
                        <a href="{{ route('login') }}" class="w-btn" style="width:100%; background:#0f172a; color:#fff;">Talk to sales</a>
                    </div>
                @endif
            </div>

            <!-- Global Coverage Box -->
            <div class="coverage-box">
                <div class="coverage-info">
                    <div class="coverage-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                    </div>
                    <div class="coverage-text">
                        <h4>Global coverage &amp; direct routes</h4>
                        <p>Low latency delivery in over 190 countries with transparent wholesale rates.</p>
                    </div>
                </div>
                <div class="coverage-chips">
                    <span class="coverage-chip">🇧🇩 Bangladesh</span>
                    <span class="coverage-chip">🇺🇸 United States</span>
                    <span class="coverage-chip">🇬🇧 United Kingdom</span>
                    <span class="coverage-chip">🇦🇪 UAE</span>
                    <span class="coverage-chip">🇮🇳 India</span>
                    <span class="coverage-chip">+185 more</span>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ SECTION -->
    <section class="sec-soft" id="faq">
        <div class="w-container">
            <div class="sec-header">
                <span class="sec-kicker">FAQ</span>
                <h2 class="sec-title">Questions, answered</h2>
                <p class="sec-subtitle">
                    Everything you need to know about getting started, sending campaigns, and integrating via API.
                </p>
            </div>

            <div class="faq-wrapper">
                <div class="faq-item active">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>How fast are messages delivered?</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                    <div class="faq-answer">
                        With our direct Tier-1 telecom routes, OTP and transactional messages are delivered in under 3 seconds on average. Bulk marketing campaigns are throttled smartly to maintain 99.4%+ delivery rates without network congestion.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>Can I use my custom brand Sender ID (Masking)?</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                    <div class="faq-answer">
                        Yes, you can register alphanumeric Sender IDs (Masking) for your brand. Approvals are processed quickly in accordance with local telecom authority regulations.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>How does the Developer API integration work?</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                    <div class="faq-answer">
                        You can send SMS with a single HTTP POST request. We support JSON payloads, Bearer token authentication, real-time webhooks for delivery status, and pre-built SDKs for PHP, Node.js, and Python.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>What payment methods are supported?</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                    <div class="faq-answer">
                        We support bKash, Nagad, Rocket, Credit/Debit cards, bank transfers, and international payment gateways for global clients.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FINAL CTA BANNER -->
    <section class="sec-light">
        <div class="w-container">
            <div class="cta-banner">
                <h2>Send your first campaign today</h2>
                <p>Join thousands of businesses scaling their customer communication with {{ config('app.name', 'BepoMSG') }}.</p>
                <div class="cta-actions">
                    <a href="{{ route('register') }}" class="w-btn w-btn-cta-dark">Create free account &rarr;</a>
                    <a href="{{ route('login') }}" class="w-btn w-btn-cta-light">Talk to sales</a>
                </div>
            </div>
        </div>
    </section>

<script>
    function switchCodeTab(lang) {
        document.querySelectorAll('.code-tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.code-pane').forEach(pane => pane.classList.remove('active'));
        
        event.target.classList.add('active');
        const activePane = document.getElementById('tab-' + lang);
        if (activePane) {
            activePane.classList.add('active');
        }
    }

    function toggleFaq(elem) {
        const item = elem.parentElement;
        item.classList.toggle('active');
    }
</script>

@endsection

