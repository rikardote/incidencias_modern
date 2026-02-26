<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center">
                <div class="bg-indigo-100 p-2 rounded-lg mr-3">
                    <i class="fas fa-fingerprint text-indigo-600"></i>
                </div>
                {{ __('Control Biométrico') }}
            </h2>
            <div class="text-sm text-slate-500 font-medium bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                {{ now()->translatedFormat('l, d \d\e F Y') }}
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            @livewire('biometrico.index')
        </div>
    </div>
</x-app-layout>
