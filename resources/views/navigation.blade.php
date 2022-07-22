<div class="flex items-stretch">
    <div class="flex flex-col justify-center gap-2 px-5 py-3 border-r border-b border-gray-500 border-solid w-[300px]">
        @include('mailbook::logo')
    </div>
    <div class="flex flex-1 items-stretch justify-between border-l border-b border-gray-500 border-solid">
        <div class="flex flex-col px-5 py-3">
            <div class="text-xs font-bold uppercase tracking-wider">
                Subject
            </div>
            <div class="text-xl">
                {{ $subject }}
            </div>
        </div>
        <a href="{{ request()->fullUrl() }}" class="flex items-center justify-center hover:bg-gray-700 p-5 transition-colors duration-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
        </a>
    </div>
</div>
