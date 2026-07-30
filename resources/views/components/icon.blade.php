@props([
    'name',
    'class' => 'text-xl'
])

<i {{ $attributes->merge(['class' => "ti ti-{$name} {$class} inline-flex items-center justify-center leading-none"]) }}></i>
