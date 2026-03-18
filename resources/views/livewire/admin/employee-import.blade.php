<div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight uppercase nothing-font">
                    Importación de Empleados
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Carga o actualiza datos (CURP/RFC) de empleados de forma masiva desde un archivo CSV.
                </p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-oro/10 flex items-center justify-center border border-oro/20 shadow-inner">
                <svg class="w-6 h-6 text-oro" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
            </div>
        </div>

        <!-- Section Main -->
        <div class="bg-white dark:bg-gray-800/50 overflow-hidden shadow-2xl rounded-2xl border border-gray-200 dark:border-white/5 backdrop-blur-xl">
            <div class="p-8">
                <!-- Instructions -->
                <div class="mb-8 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/30">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h4 class="text-sm font-bold text-blue-900 dark:text-blue-300">Instrucciones del Formato CSV</h4>
                            <p class="text-xs text-blue-800 dark:text-blue-400 mt-1">
                                El archivo debe ser un **.csv** con **10 columnas** en este orden literal: <br>
                                <span class="font-mono bg-blue-100 dark:bg-blue-900/40 px-1 rounded">num_empleado</span>, 
                                <span class="font-mono bg-blue-100 dark:bg-blue-900/40 px-1 rounded">father_lastname</span>, 
                                <span class="font-mono bg-blue-100 dark:bg-blue-900/40 px-1 rounded">mother_lastname</span>, 
                                <span class="font-mono bg-blue-100 dark:bg-blue-900/40 px-1 rounded">name</span>, 
                                <span class="font-mono bg-blue-100 dark:bg-blue-900/40 px-1 rounded">rfc</span>, 
                                <span class="font-mono bg-blue-100 dark:bg-blue-900/40 px-1 rounded">curp</span>, 
                                <span class="font-mono bg-blue-100 dark:bg-blue-900/40 px-1 rounded">fecha_ingreso</span>, 
                                <span class="font-mono bg-blue-100 dark:bg-blue-900/40 px-1 rounded font-bold">condicion</span>, 
                                <span class="font-mono bg-blue-100 dark:bg-blue-900/40 px-1 rounded">num_seguro</span>, 
                                <span class="font-mono bg-blue-100 dark:bg-blue-900/40 px-1 rounded">num_plaza</span>.
                                <br>No incluya encabezados. En **condicion** escriba "BASE" o "CONFIANZA".
                            </p>
                        </div>
                    </div>
                </div>

                <form wire:submit.prevent="import" class="space-y-6">
                    <!-- Drop Zone -->
                    <div 
                        x-data="{ isDragging: false }"
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="isDragging = false; @this.upload('csvFile', $event.dataTransfer.files[0])"
                        class="relative group"
                    >
                        <div class="absolute -inset-1 bg-gradient-to-r from-oro/20 to-emerald-500/20 rounded-2xl blur opacity-25 group-hover:opacity-100 transition duration-1000 group-hover:duration-200"></div>
                        
                        <div class="relative flex flex-col items-center justify-center border-2 border-dashed rounded-2xl transition-all duration-300 p-12"
                             :class="isDragging ? 'border-oro bg-oro/5 ring-4 ring-oro/10' : 'border-gray-300 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50'">
                            
                            @if(!$csvFile)
                                <div class="flex flex-col items-center">
                                    <div class="h-20 w-20 rounded-full bg-oro/5 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                        <svg class="h-10 w-10 text-oro opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 text-center">
                                        Arrastra tu archivo CSV aquí o 
                                        <label class="text-oro hover:underline cursor-pointer font-bold">
                                            búscalo en tu PC
                                            <input type="file" wire:model="csvFile" class="hidden" accept=".csv,.txt">
                                        </label>
                                    </p>
                                    <p class="text-xs text-gray-400 mt-2">Máximo 10MB</p>
                                </div>
                            @else
                                <div class="flex flex-col items-center animate-in fade-in zoom-in duration-300">
                                    <div class="h-16 w-16 rounded-full bg-emerald-500/10 flex items-center justify-center mb-4">
                                        <svg class="h-8 w-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-black text-gray-900 dark:text-white uppercase nothing-font">Archivo Listo</p>
                                    <p class="text-xs font-mono text-emerald-500 mt-1">{{ $csvFile->getClientOriginalName() }}</p>
                                    <button type="button" @click="$wire.set('csvFile', null)" class="mt-4 text-xs text-red-500 hover:text-red-600 font-bold uppercase tracking-wider">
                                        Quitar archivo
                                    </button>
                                </div>
                            @endif

                            <div wire:loading wire:target="csvFile" class="absolute inset-0 bg-white/80 dark:bg-gray-900/80 rounded-2xl flex items-center justify-center backdrop-blur-sm z-10 font-bold text-oro uppercase nothing-font tracking-widest text-sm animate-pulse">
                                Cargando...
                            </div>
                        </div>
                    </div>

                    @error('csvFile') <span class="text-xs text-red-500 font-bold mt-1">{{ $message }}</span> @enderror

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                @if(!$csvFile) disabled @endif
                                class="w-full h-14 bg-[#13322B] dark:bg-[#0d2a23] hover:bg-black text-white rounded-xl font-black uppercase tracking-[0.2em] nothing-font shadow-lg hover:shadow-oro/20 transition-all duration-300 disabled:opacity-30 disabled:grayscale relative overflow-hidden group">
                            
                            <span wire:loading.remove wire:target="import" class="relative z-10 flex items-center justify-center gap-2">
                                Iniciar Importación
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                </svg>
                            </span>

                            <span wire:loading wire:target="import" class="relative z-10">
                                <span class="flex items-center justify-center gap-3">
                                    <svg class="animate-spin h-5 w-5 text-oro" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Procesando...
                                </span>
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Results/Status -->
                @if($status)
                    <div class="mt-10 p-6 rounded-2xl border bg-gray-50 dark:bg-gray-900/50 {{ $status == 'success' ? 'border-emerald-500/20 shadow-[0_0_20px_rgba(16,185,129,0.05)]' : 'border-red-500/20 shadow-[0_0_20px_rgba(239,68,68,0.05)]' }} animate-in slide-in-from-bottom duration-500">
                        <div class="flex items-start gap-4">
                            <div class="h-10 w-10 rounded-full flex items-center justify-center shrink-0 {{ $status == 'success' ? 'bg-emerald-500/20 text-emerald-500' : 'bg-red-500/20 text-red-500' }}">
                                @if($status == 'success')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-black uppercase nothing-font {{ $status == 'success' ? 'text-emerald-500' : 'text-red-500' }}">
                                    {{ $message }}
                                </p>
                                
                                @if($status == 'success')
                                    <div class="mt-4 grid grid-cols-3 gap-4">
                                        <div class="p-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-white/5">
                                            <p class="text-[10px] text-gray-400 uppercase tracking-widest font-black">Total</p>
                                            <p class="text-xl font-bold dark:text-white">{{ number_format($total) }}</p>
                                        </div>
                                        <div class="p-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-white/5">
                                            <p class="text-[10px] text-emerald-500/70 uppercase tracking-widest font-black">Actualizados</p>
                                            <p class="text-xl font-bold text-emerald-500">{{ number_format($updated) }}</p>
                                        </div>
                                        <div class="p-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-white/5">
                                            <p class="text-[10px] text-gray-400 uppercase tracking-widest font-black">Omitidos</p>
                                            <p class="text-xl font-bold text-gray-500">{{ number_format($skipped) }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Warning Area -->
        <div class="mt-8 text-center px-12">
            <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] leading-relaxed">
                Este es un proceso administrativo crítico. Los datos existentes en las columnas de CURP y RFC serán sobrescritos por los valores del archivo si el número de empleado coincide.
            </p>
        </div>
    </div>
</div>
