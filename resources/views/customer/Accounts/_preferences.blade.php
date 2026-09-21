<div class="bm-card" style="padding: 18px;">
    <span class="bm-card__title">{{ __('portal.notification_settings') }}</span>

    <form class="form bm-form" action="{{ route('customer.subscriptions.preferences', $subscription->uid) }}" method="post">
        @csrf

        {{-- Low balance --}}
        <div class="bm-toggle bm-toggle--plain" style="margin-bottom: 4px;">
            <label for="credit_warning" style="cursor: pointer; font-weight: 500;">{{ __('locale.labels.credit_warning') }}</label>
            <div class="form-check form-switch m-0">
                <input type="checkbox" class="form-check-input" name="credit_warning" id="credit_warning" value="true"
                        {{ $subscription->getOption('credit_warning') ? 'checked': null }}>
            </div>
        </div>
        <p class="bm-muted" style="font-size: 12px; margin-bottom: 14px;">{{ __('locale.subscription.credit_runs_bellow_description') }}</p>

        <div class="row" style="margin-bottom: 14px;">
            <div class="col-md-6 col-12">
                <label for="credit" class="form-label required">{{__('locale.subscription.credit_runs_bellow')}}</label>
                <input type="number" id="credit" class="form-control text-end @error('credit') is-invalid @enderror"
                       value="{{ $subscription->getOption('credit') }}" name="credit" required>
                @error('credit')
                <p><small class="text-danger">{{ $message }}</small></p>
                @enderror
            </div>
            <div class="col-md-6 col-12">
                <label for="credit_notify" class="form-label required">{{ __('locale.labels.notify_me_by') }}</label>
                <select class="form-select" id="credit_notify" name="credit_notify">
                    <option value="sms" {{ $subscription->getOption('credit_notify') == 'sms' ? 'selected': null }}> {{__('locale.labels.sms')}}</option>
                    <option value="email" {{ $subscription->getOption('credit_notify') == 'email' ? 'selected': null }}>  {{__('locale.labels.email')}}</option>
                    <option value="both" {{ $subscription->getOption('credit_notify') == 'both' ? 'selected': null }}>  {{__('locale.labels.both')}}</option>
                </select>
                @error('credit_notify')
                <p><small class="text-danger">{{ $message }}</small></p>
                @enderror
            </div>
        </div>

        {{-- Subscription end --}}
        <div style="padding-top: 14px; border-top: 1px solid var(--bm-line-soft);">
            <div class="bm-toggle bm-toggle--plain" style="margin-bottom: 4px;">
                <label for="subscription_warning" style="cursor: pointer; font-weight: 500;">{{ __('locale.labels.subscription_warning') }}</label>
                <div class="form-check form-switch m-0">
                    <input type="checkbox" class="form-check-input" name="subscription_warning" id="subscription_warning" value="true"
                            {{ $subscription->getOption('subscription_warning') ? 'checked': null }}>
                </div>
            </div>
            <p class="bm-muted" style="font-size: 12px; margin-bottom: 14px;">{{ __('locale.subscription.subscription_warning_description') }}</p>

            <div class="row">
                <div class="col-md-6 col-12">
                    <label for="end_period_last_days" class="form-label required">{{ __('locale.subscription.subscription_period_end') }}</label>
                    <input type="number" id="end_period_last_days" class="form-control text-end @error('end_period_last_days') is-invalid @enderror"
                           value="{{ $subscription->end_period_last_days }}" name="end_period_last_days" required>
                    @error('end_period_last_days')
                    <p><small class="text-danger">{{ $message }}</small></p>
                    @enderror
                </div>
                <div class="col-md-6 col-12">
                    <label for="subscription_notify" class="form-label required">{{ __('locale.labels.notify_me_by') }}</label>
                    <select class="form-select" id="subscription_notify" name="subscription_notify">
                        <option value="sms" {{ $subscription->getOption('subscription_notify') == 'sms' ? 'selected': null }}> {{__('locale.labels.sms')}}</option>
                        <option value="email" {{ $subscription->getOption('subscription_notify') == 'email' ? 'selected': null }}>  {{__('locale.labels.email')}}</option>
                        <option value="both" {{ $subscription->getOption('subscription_notify') == 'both' ? 'selected': null }}>  {{__('locale.labels.both')}}</option>
                    </select>
                    @error('subscription_notify')
                    <p><small class="text-danger">{{ $message }}</small></p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bm-row" style="justify-content: flex-end; margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--bm-line-soft);">
            <button type="submit" class="bm-btn bm-btn--primary bm-btn--lg">{{__('locale.buttons.save')}}</button>
        </div>
    </form>
</div>
