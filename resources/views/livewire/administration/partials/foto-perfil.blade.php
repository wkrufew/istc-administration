{{-- Foto de perfil (trait App\Livewire\Concerns\GestionaFotoPerfil) --}}
{{-- ══ FOTO DE PERFIL ═══════════════════════════════════════════════ --}}
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    <div class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 px-6 py-3.5 flex items-center gap-2">
        <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <h3 class="text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Foto de Perfil</h3>
    </div>
    <div class="p-6">
        <div class="flex items-center gap-6">
            <div class="flex-shrink-0">
                @if($profile_photo)
                    <img class="w-20 h-20 rounded-2xl object-cover ring-2 ring-blue-500/20 shadow-sm"
                         src="{{ $profile_photo->temporaryUrl() }}" alt="Vista previa">
                @elseif($profile_photo_actual)
                    <img class="w-20 h-20 rounded-2xl object-cover ring-2 ring-slate-200 dark:ring-slate-600 shadow-sm"
                         src="{{ Storage::url($profile_photo_actual) }}" alt="Foto actual">
                @else
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200
                                dark:from-slate-700 dark:to-slate-800
                                flex items-center justify-center shadow-sm">
                        <svg class="w-8 h-8 text-slate-400 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                @endif
            </div>
            <div class="flex-1 space-y-2">
                <input type="file" wire:model="profile_photo" accept="image/*"
                       class="block w-full text-sm text-slate-500 dark:text-slate-400
                              file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0
                              file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700
                              hover:file:bg-blue-100 file:transition-colors cursor-pointer"/>
                <p class="text-xs text-slate-400 dark:text-slate-500">JPG, PNG o GIF. Máx. 2MB.</p>
                @if($profile_photo_actual)
                    <button type="button" wire:click="deletePhoto"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-500
                                   hover:text-red-700 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Eliminar foto actual
                    </button>
                @endif
                @error('profile_photo')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>
