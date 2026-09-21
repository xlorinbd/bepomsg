{{-- Status pill: maps a raw status string (campaign/sender id/report/contact/invoice) to a tone. --}}
@props(['value' => '', 'label' => null])
@php
    $v = strtolower(trim((string) $value));
    $tone = match (true) {
        in_array($v, ['done', 'delivered', 'active', 'approved', 'paid', 'subscribe', 'subscribed', 'success', 'completed', 'verified']) => 'ok',
        in_array($v, ['sending', 'processing', 'running', 'queuing']) => 'info',
        in_array($v, ['failed', 'error', 'cancelled', 'canceled', 'rejected', 'block', 'blocked', 'expired', 'bounced', 'undelivered', 'inactive']) => 'bad',
        in_array($v, ['pending', 'new', 'queued', 'paused', 'payment_required', 'unpaid', 'blacklisted', 'invited']) => 'warn',
        default => 'mute',
    };
    $text = $label ?? (\Illuminate\Support\Facades\Lang::has('locale.labels.' . $v)
        ? __('locale.labels.' . $v)
        : ucfirst(str_replace('_', ' ', $v)));
@endphp
<x-bm.pill :tone="$tone">{{ $text }}</x-bm.pill>
