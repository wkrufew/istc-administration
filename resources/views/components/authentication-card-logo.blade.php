<a href="/">
    @php
        $logoPath = \App\Services\SettingService::get('instituto.logo_path');
        $logoSrc  = ($logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath))
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($logoPath)
            : asset('../imagenes/icono.webp');
    @endphp
    <img class="object-cover overflow-hidden size-16" src="{{ $logoSrc }}" alt="">
</a>
