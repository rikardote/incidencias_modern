<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="avatar" value="Selecciona un Avatar" />
            <div class="grid grid-cols-4 md:grid-cols-5 gap-4 mt-2">
                @php
                $avatars = ['man', 'woman', 'dog', 'cat', 'bird', 'guitar', 'drum', 'palette', 'robot', 'alien'];
                $currentAvatar = old('avatar', $user->avatar ?? 'man');
                @endphp
                @foreach($avatars as $avatar)
                <label class="cursor-pointer relative flex flex-col items-center">
                    <input type="radio" name="avatar" value="{{ $avatar }}" class="peer sr-only" {{
                        $currentAvatar===$avatar ? 'checked' : '' }}>
                    <div
                        class="rounded-full border-4 border-transparent peer-checked:border-oro hover:scale-105 transition-all p-1">
                        <x-user-avatar :avatar="$avatar" :name="$user->name" size="w-12 h-12" />
                    </div>
                    <div
                        class="absolute -bottom-1 -right-1 bg-oro text-white rounded-full p-1 opacity-0 peer-checked:opacity-100 transition-opacity">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                </label>
                @endforeach
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>



        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>