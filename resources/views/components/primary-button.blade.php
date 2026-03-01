<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#13322B] dark:bg-[#13322B] border border-transparent rounded font-bold text-xs text-white uppercase tracking-wider hover:bg-[#0a1f1a] dark:hover:bg-[#0a1f1a] focus:bg-[#0a1f1a] dark:focus:bg-[#0a1f1a] active:bg-[#0a1f1a] dark:active:bg-[#0a1f1a] focus:outline-none focus:ring-2 focus:ring-[#13322B] focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
