@props(['tone' => 'mute'])
<span {{ $attributes->class(['bm-pill', 'bm-pill--' . $tone => $tone !== 'mute']) }}>{{ $slot }}</span>
