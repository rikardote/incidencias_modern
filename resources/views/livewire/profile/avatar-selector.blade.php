<div>
    <div class="grid grid-cols-5 md:grid-cols-10 gap-3 mt-2" x-on:avatar-updated.window="$dispatch('avatar-changed', { avatar: $event.detail.avatar })">
        @php
            $avatars = ['man', 'woman', 'dog', 'cat', 'bird', 'guitar', 'drum', 'palette', 'robot', 'alien'];
            $avatarData = [
                'man' => '👨', 'woman' => '👩', 'dog' => '🐶', 'cat' => '🐱', 'bird' => '🐦',
                'guitar' => '🎸', 'drum' => '🥁', 'palette' => '🎨', 'robot' => '🤖', 'alien' => '👽'
            ];
        @endphp
        @foreach($avatars as $av)
        <button type="button" 
            wire:click="selectAvatar('{{ $av }}')"
            class="relative group p-1.5 rounded-2xl border-2 transition-all duration-300 outline-none
            {{ $avatar === $av ? 'border-oro bg-oro/10 scale-110 shadow-lg' : 'border-gray-100 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-800 grayscale hover:grayscale-0' }}">
            
            <x-user-avatar :avatar="$av" :name="auth()->user()->name" size="w-10 h-10" />
            
            @if($avatar === $av)
            <div class="absolute -top-1.5 -right-1.5 bg-oro text-[#1a0a0a] rounded-full p-0.5 shadow-md z-10 transition-transform animate-bounce">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            @endif
        </button>
        @endforeach
    </div>
</div>
