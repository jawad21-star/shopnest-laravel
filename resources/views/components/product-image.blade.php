@props(['src' => null, 'alt' => ''])

@if ($src)
    <img src="{{ $src }}" alt="{{ $alt }}" loading="lazy" {{ $attributes->merge(['class' => 'object-contain']) }}>
@else
    <div {{ $attributes->merge(['class' => 'flex items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200 text-slate-400']) }}>
        <x-shop-icon name="bag" class="size-10" />
    </div>
@endif
