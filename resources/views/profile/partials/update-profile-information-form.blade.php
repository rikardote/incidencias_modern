<section>
    <header>
        <h2 class="text-lg font-black text-gray-900 dark:text-gray-100 uppercase tracking-widest">
            {{ __('Información de Perfil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Personaliza tu avatar en el sistema. Los cambios se aplican al instante.") }}
        </p>
    </header>

    <div class="mt-8">
        <livewire:profile.avatar-selector />
        <p class="mt-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider italic">
            → Tu elección se reflejará en todo el sistema.
        </p>
    </div>
</section>