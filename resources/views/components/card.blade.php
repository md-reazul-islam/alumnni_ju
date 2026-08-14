@props(['padded' => true])

<div {{ $attributes->class(['card']) }}>
    <div @if($padded) class="card-body" @endif>
        {{ $slot }}
    </div>
</div>
