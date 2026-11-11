@props([
    'invitation',
    'primaryColor' => '#C6A76B',
])

@if(!empty($invitation->music_url))
    <div x-data="{ playing: false, audio: null }" 
         x-init="audio = new Audio('{{ $invitation->music_url }}'); audio.loop = true;"
         class="fixed bottom-6 right-6 z-40">
        <button @click="if(playing) { audio.pause(); playing = false; } else { audio.play(); playing = true; }"
                class="w-12 h-12 rounded-full shadow-2xl flex items-center justify-center text-white border-2 border-white transition transform hover:scale-110"
                style="background-color: {{ $primaryColor }};"
                title="موسيقى الخلفية">
            <x-app-icon name="music" class="w-5 h-5 animate-spin" x-show="playing" />
            <x-app-icon name="music" class="w-5 h-5" x-show="!playing" />
        </button>
    </div>
@endif

