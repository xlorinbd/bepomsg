@extends('website.layout')

@section('title', 'Packages & Pricing — ' . config('app.name', 'Beposms'))
@section('meta_description', 'Compare Beposms SMS and WhatsApp marketing plans and find the right package for your business.')

@section('content')

    <section class="w-hero" style="padding: 80px 0 100px;">
        <div class="w-container w-hero-inner">
            <span class="w-eyebrow"><span class="dot"></span> Pricing</span>
            <h1>Simple, transparent <span>packages</span></h1>
            <p>Choose the plan that fits your sending volume today — upgrade, downgrade, or cancel any time.</p>
        </div>
    </section>

    <section class="w-section">
        <div class="w-container">

            @if($plans->count())
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

                            <ul class="w-price-features">
                                <li><span class="w-check">✓</span> {{ $plan->displayTotalQuota() }} SMS credits</li>
                                <li><span class="w-check">✓</span> {{ $plan->displayWhatsAppQuota() }} WhatsApp credits</li>
                                <li><span class="w-check">✓</span> {{ $plan->displayMaxList() }} contact lists</li>
                                <li><span class="w-check">✓</span> {{ $plan->displayMaxContact() }} contacts</li>
                                <li><span class="w-check">✓</span> {{ strtoupper($plan->getOption('api_access')) === 'YES' ? 'API access included' : 'Standard support' }}</li>
                                @if($plan->getOption('sender_id_verification') === 'yes')
                                    <li><span class="w-check">✓</span> Sender ID verification</li>
                                @endif
                            </ul>

                            <a href="{{ route('register') }}" class="w-btn @if($plan->is_popular) w-btn-primary @else w-btn-ghost @endif">Get Started</a>
                        </div>
                    @endforeach
                </div>

                <p class="w-price-note">Need a custom plan for higher volume? <a href="{{ route('register') }}" style="color:var(--w-primary); font-weight:700;">Contact us after signing up</a>.</p>
            @else
                <div class="w-section-head">
                    <h2>Plans coming soon</h2>
                    <p>We're setting up our pricing. Please check back shortly, or <a href="{{ route('register') }}" style="color:var(--w-primary); font-weight:700;">create an account</a> to get notified.</p>
                </div>
            @endif

        </div>
    </section>

    <section class="w-section w-section-soft">
        <div class="w-container">
            <div class="w-cta">
                <h2>Not sure which plan is right for you?</h2>
                <p>Create a free account and upgrade any time as your sending needs grow.</p>
                <a href="{{ route('register') }}" class="w-btn w-btn-light w-btn-lg">Get Started</a>
            </div>
        </div>
    </section>

@endsection
