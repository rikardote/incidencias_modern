<section>
    <header>
        <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wide">
            {{ __('Apariencia del Sistema') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Personaliza cómo se ve el sistema. El modo oscuro reduce la fatiga visual en ambientes con poca luz.') }}
        </p>
    </header>

    <div class="mt-6 space-y-6">
        <div class="flex items-center gap-4" x-data="{ 
            darkMode: false,
            init() {
                this.darkMode = localStorage.getItem('theme') === 'dark';
            },
            toggle() {
                this.darkMode = !this.darkMode;
                if (this.darkMode) {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                }
            }
        }">
            <button 
                type="button" 
                @click.prevent="toggle()"
                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-[#13322B] focus:ring-offset-2"
                :class="darkMode ? 'bg-[#13322B]' : 'bg-gray-200'"
            >
                <span class="sr-only">Toggle Dark Mode</span>
                <span 
                    aria-hidden="true" 
                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                    :class="darkMode ? 'translate-x-5' : 'translate-x-0'"
                ></span>
            </button>
            <span class="text-sm font-bold text-gray-700 dark:text-gray-300" x-text="darkMode ? 'Modo Oscuro Activado' : 'Modo Claro Activado'"></span>
        </div>
    </div>
</section>
