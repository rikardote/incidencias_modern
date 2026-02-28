<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight tracking-wide">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow rounded border border-gray-200 dark:border-gray-700">
                <div class="max-w-xl">
                    @include('profile.partials.update-theme-form')
                </div>
            </div>

            {{-- Widget Selection section will be added here --}}
            <div id="widget-selection-container" class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow rounded border border-gray-200 dark:border-gray-700">
                <div class="max-w-xl">
                    @include('profile.partials.update-widgets-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
