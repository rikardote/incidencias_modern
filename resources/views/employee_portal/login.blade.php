<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="text-2xl font-bold text-gray-900">Portal de Empleados</h2>
        <p class="text-sm text-gray-600 mt-2">Inicia sesión con tu Número de Empleado.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('employee.login') }}">
        @csrf

        <!-- Num Empleado -->
        <div>
            <x-input-label for="num_empleado" value="{{ __('Número de Empleado') }}" />
            <x-text-input id="num_empleado" class="block mt-1 w-full" type="text" name="num_empleado" :value="old('num_empleado')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('num_empleado')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="{{ __('Contraseña') }}" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <p class="text-xs text-gray-500 mt-2">Por defecto, tu contraseña es tu RFC.</p>
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Recordarme') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            <a class="text-sm text-gray-600 hover:text-gray-900 underline" href="{{ route('login') }}">
                {{ __('¿Eres Administrador?') }}
            </a>

            <x-primary-button class="ms-3">
                {{ __('Iniciar Sesión') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
