<section>
    <header>
        <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wide">
            {{ __('Identidad Visual de la Isla') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Personaliza la apariencia de la Isla Dinámica y las notificaciones del sistema.') }}
        </p>
    </header>

    <div class="mt-6 p-6 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-200 dark:border-gray-700"
         x-data="{ 
            activeStyle: Alpine.store('island').activeStyle,
            showFaces: Alpine.store('island').showFaces,
            styles: {
                'classic': 'Gesto ASCII + Texto. El balance perfecto.',
                'progress': 'Barra de proceso técnica. Ideal para capturas.',
                'minimal': 'Solo texto. Rápido y limpio.',
                'glass': 'Icono + Blur. Estética refinada y moderna.',
                'cyberpunk': 'Neón + Glitch. Estética futurista y agresiva.',
                'matrix': 'Lluvia de código + Terminal. El origen.',
                'kinetic': 'Energía Cinética. 100% Visual, sin textos.',
                'starwars': 'Hyperspace. Velocidad luz hasta tus notificaciones.',
                'avengers': 'The Snap. Desintegración cinematográfica al terminar.'
            },
            setStyle(style) {
                this.activeStyle = style;
                Alpine.store('island').setStyle(style);
                window.dispatchEvent(new CustomEvent('island-notif', { 
                    detail: { message: 'Estilo ' + style.toUpperCase() + ' activado', type: 'success' } 
                }));
            },
            toggleFaces() {
                this.showFaces = !this.showFaces;
                Alpine.store('island').setFaces(this.showFaces);
            }
         }">
        
        <div class="flex items-center justify-between mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
            <div>
                <h5 class="text-xs font-black text-gray-400 dark:text-gray-500 nothing-font uppercase tracking-[0.3em]">
                    Configuración Local</h5>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-0.5 italic">Cambios aplicados solo a tu navegador</p>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-gray-800 px-3 py-1.5 rounded-lg border dark:border-gray-700 shadow-sm">
                <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest nothing-font">Mostrar Caritas</span>
                <button @click="toggleFaces()"
                    class="relative inline-flex flex-shrink-0 h-4 w-8 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none"
                    :class="showFaces ? 'bg-oro' : 'bg-gray-300 dark:bg-gray-700'">
                    <span aria-hidden="true"
                        class="pointer-events-none inline-block h-3 w-3 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"
                        :class="showFaces ? 'translate-x-4' : 'translate-x-0'"></span>
                </button>
            </div>
        </div>

        <div class="space-y-4">
            <h6 class="text-[9px] font-black text-[#9b2247] dark:text-[#e6d194] uppercase tracking-[0.2em] nothing-font">Selector de Estilo</h6>
            <div class="flex flex-wrap gap-2">
                <template x-for="(desc, style) in styles" :key="style">
                    <button @click="setStyle(style)" :title="desc"
                        class="px-4 py-2 rounded-full border-2 nothing-font text-[10px] font-black uppercase tracking-widest transition-all shadow-sm"
                        :class="activeStyle === style 
                                ? (style === 'cyberpunk' ? 'border-[#ff00ff] bg-[#ff00ff]/10 text-[#ff00ff] shadow-[0_0_15px_rgba(255,0,255,0.4)]' : 
                                   (style === 'matrix' ? 'border-green-500 bg-green-500/10 text-green-500 shadow-[0_0_15px_rgba(34,211,94,0.4)]' : 
                                    'border-oro bg-oro/10 text-oro shadow-[0_0_15px_rgba(212,175,55,0.2)]')) 
                                : 'border-white dark:border-gray-800 bg-white dark:bg-gray-800 text-gray-400 dark:text-gray-500 hover:border-gray-200 dark:hover:border-gray-700' ">
                        <span x-text="style"></span>
                    </button>
                </template>
            </div>

            <p class="text-[10px] text-gray-500 dark:text-gray-400 italic mt-4 bg-white/50 dark:bg-black/20 p-2 rounded border border-dashed border-gray-200 dark:border-gray-700">
                * El estilo seleccionado se aplica a la Isla Dinámica que ves en la navegación superior.
            </p>
        </div>
    </div>
</section>
