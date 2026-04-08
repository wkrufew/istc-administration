<div {{ $attributes->merge(['class' => '']) }}>
    {{-- <div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-3 md:gap-6']) }}> --}}
    {{-- <x-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-section-title> --}}

    <div class="">
        <div class="px-4 py-5 sm:p-6 bg-white shadow sm:rounded-lg">
            {{ $content }}
        </div>
    </div>
</div>
