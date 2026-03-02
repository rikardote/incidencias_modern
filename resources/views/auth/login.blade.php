<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Username -->
        <div>
            <x-input-label for="username" value="Núm. Empleado (Usuario)" class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400" />
            <x-text-input id="username" class="block mt-1.5 w-full py-3 px-4 shadow-sm border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 focus:ring-oro" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" inputmode="numeric" pattern="[0-9]*" placeholder="Ej: 123456" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-6">
            <x-input-label for="password" :value="__('Password')" class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400" />

            <x-text-input id="password" class="block mt-1.5 w-full py-3 px-4 shadow-sm border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 focus:ring-oro"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-6">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="w-5 h-5 rounded border-gray-300 dark:border-gray-700 text-[#13322B] shadow-sm focus:ring-oro dark:bg-gray-800 transition-all cursor-pointer" name="remember">
                <span class="ms-3 text-sm font-medium text-gray-600 dark:text-gray-400 group-hover:text-[#13322B] dark:group-hover:text-oro transition-colors">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between mt-8 gap-4 pt-6 border-t border-gray-100 dark:border-gray-800">
            @if (Route::has('password.request'))
                <a class="text-xs font-bold text-gray-400 dark:text-gray-500 hover:text-[#13322B] dark:hover:text-oro transition-colors uppercase tracking-widest" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="w-full sm:w-auto justify-center py-3 px-8 shadow-lg shadow-[#13322B]/20">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
