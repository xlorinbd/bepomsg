@extends('website.layout')

@section('title', config('app.name', 'Beposms') . ' — Bulk SMS & WhatsApp Marketing Platform')
@section('meta_description', 'Send bulk SMS and WhatsApp campaigns, manage contacts, and automate messaging — all from one powerful, reliable platform.')

@section('content')

    <section class="w-hero">
        <div class="w-container w-hero-inner">
            <span class="w-eyebrow"><span class="dot"></span> Trusted by growing businesses</span>
            <h1>Reach every customer with <span>bulk SMS &amp; WhatsApp</span> marketing that works</h1>
            <p>{{ config('app.name', 'Beposms') }} helps you send campaigns, manage contacts, and automate messaging at scale — with reliable delivery and real-time reporting built in.</p>
            <div class="w-hero-actions">
                <a href="{{ route('register') }}" class="w-btn w-btn-light w-btn-lg">Get Started Free</a>
                <a href="{{ route('website.packages') }}" class="w-btn w-btn-ghost w-btn-lg" style="border-color:rgba(255,255,255,0.25); color:#fff;">View Pricing</a>
            </div>

            <div class="w-hero-stats">
                <div class="w-hero-stat"><b>99.9%</b><span>Uptime</span></div>
                <div class="w-hero-stat"><b>190+</b><span>Countries Covered</span></div>
                <div class="w-hero-stat"><b>&lt;3s</b><span>Average Delivery</span></div>
                <div class="w-hero-stat"><b>24/7</b><span>Support</span></div>
            </div>
        </div>
    </section>

    <section class="w-section" id="features">
        <div class="w-container">
            <div class="w-section-head">
                <span class="w-kicker">Features</span>
                <h2>Everything you need to run messaging campaigns</h2>
                <p>From your first campaign to global scale, {{ config('app.name', 'Beposms') }} gives you the tools to reach customers reliably.</p>
            </div>

            <div class="w-grid">
                <div class="w-card">
                    <div class="w-feature-icon">✉</div>
                    <h3>Bulk SMS Campaigns</h3>
                    <p>Send personalized SMS campaigns to thousands of contacts in seconds, with scheduling and delivery tracking.</p>
                </div>
                <div class="w-card">
                    <div class="w-feature-icon">💬</div>
                    <h3>WhatsApp Messaging</h3>
                    <p>Reach customers on WhatsApp with rich, templated messages alongside your SMS campaigns.</p>
                </div>
                <div class="w-card">
                    <div class="w-feature-icon">👥</div>
                    <h3>Contact Management</h3>
                    <p>Organize contacts into lists and segments, import in bulk, and keep your audience up to date.</p>
                </div>
                <div class="w-card">
                    <div class="w-feature-icon">⚡</div>
                    <h3>Automation</h3>
                    <p>Trigger automated messages based on events, keywords, or schedules — no manual work required.</p>
                </div>
                <div class="w-card">
                    <div class="w-feature-icon">🌍</div>
                    <h3>Global Coverage</h3>
                    <p>Deliver messages reliably across 190+ countries through a network of trusted sending routes.</p>
                </div>
                <div class="w-card">
                    <div class="w-feature-icon">🔌</div>
                    <h3>Developer API</h3>
                    <p>Integrate messaging directly into your own apps and workflows with a simple, well-documented API.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="w-section w-section-soft">
        <div class="w-container">
            <div class="w-section-head">
                <span class="w-kicker">How it works</span>
                <h2>Launch your first campaign in minutes</h2>
                <p>No complicated setup. Create an account, add your contacts, and start sending.</p>
            </div>

            <div class="w-steps">
                <div class="w-step">
                    <div class="w-step-num">1</div>
                    <h3>Create your account</h3>
                    <p>Sign up and choose the plan that fits your sending volume — upgrade any time as you grow.</p>
                </div>
                <div class="w-step">
                    <div class="w-step-num">2</div>
                    <h3>Import your contacts</h3>
                    <p>Upload your contact lists or connect via API, then organize them into segments.</p>
                </div>
                <div class="w-step">
                    <div class="w-step-num">3</div>
                    <h3>Send &amp; track results</h3>
                    <p>Launch your campaign and monitor delivery, clicks, and replies in real time.</p>
                </div>
            </div>
        </div>
    </section>

    @if($plans->count())
    <section class="w-section" id="plans-preview">
        <div class="w-container">
            <div class="w-section-head">
                <span class="w-kicker">Pricing</span>
                <h2>Simple plans for every stage</h2>
                <p>Pick a plan that matches your sending needs. No hidden fees.</p>
            </div>

            <div class="w-pricing-grid">
                @foreach($plans as $plan)
                    <div class="w-price-card @if($plan->is_popular) is-popular @endif">
                        @if($plan->is_popular)
                            <span class="w-badge-popular">Most Popular</span>
                        @endif
                        <div class="w-price-name">{{ $plan->name }}</div>
                        <div class="w-price-desc">{{ $plan->description }}</div>
                        <div class="w-price-amount">
                            <span class="num">{{ $plan->getBillableFormattedPrice() }}</span>
                        </div>
                        <div class="w-price-cycle">{{ $plan->displayFrequencyTime() }}</div>
                        <a href="{{ route('register') }}" class="w-btn @if($plan->is_popular) w-btn-primary @else w-btn-ghost @endif">Get Started</a>
                    </div>
                @endforeach
            </div>

            <p class="w-price-note">
                <a href="{{ route('website.packages') }}" style="color:var(--w-primary); font-weight:700;">See full plan details &amp; features →</a>
            </p>
        </div>
    </section>
    @endif

    <section class="w-section">
        <div class="w-container">
            <div class="w-cta">
                <h2>Ready to start sending?</h2>
                <p>Join businesses using {{ config('app.name', 'Beposms') }} to reach their customers reliably, every time.</p>
                <a href="{{ route('register') }}" class="w-btn w-btn-light w-btn-lg">Create Your Free Account</a>
            </div>
        </div>
    </section>

@endsection
