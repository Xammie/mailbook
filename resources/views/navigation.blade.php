<div class="flex items-stretch flex-shrink-0 flex-grow-0 bg-[#151e2b] z-20 h-16">
    <div class="flex flex-1 items-stretch justify-between">
        <div class="flex flex-col justify-center px-5 py-1">
            <div class="text-xs uppercase font-bold tracking-wide">
                Subject
            </div>
            <div class="text-xl">
                {{ $subject }}
            </div>
        </div>
        <div class="hidden sm:flex">
            @if($locales)
                <div x-data="{ open: false }" class="relative flex">
                    <button x-on:click="open = ! open"
                            class="p-5 h-full flex justify-center items-center gap-1 hover:bg-gray-700">
                        <div class="text-xs uppercase font-bold tracking-wide">
                            {{ $localeLabel }}
                        </div>
                    </button>
                    <div x-show="open" class="absolute top-[100%] bg-[#151e2b] z-10" x-cloak>
                        @foreach($locales as $key => $locale)
                            @if($key !== $currentLocale)
                                <a href="{{ request()->fullUrlWithQuery(['locale' => $key]) }}"
                                   class="flex flex-col justify-center px-5 py-3 hover:bg-gray-700">
                                    <div class="text-xs uppercase font-bold tracking-wide">
                                        {{ $locale }}
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
            @if(config('mailbook.display_preview'))
                <div class="p-2">
                    <a href="{{ request()->fullUrlWithQuery(['display' => 'phone']) }}"
                       @class([
                           'flex items-center justify-center p-3 rounded-full  transition-colors duration-100',
                           'bg-gray-600 text-white' => $display === 'phone',
                           'hover:bg-gray-700' => $display !== 'phone',
                       ])
                       aria-label="Phone" title="Phone">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </a>
                </div>
                <div class="p-2">
                    <a href="{{ request()->fullUrlWithQuery(['display' => 'tablet']) }}"
                       @class([
                            'flex items-center justify-center p-3 rounded-full transition-colors duration-100',
                            'bg-gray-600 text-white' => $display === 'tablet',
                            'hover:bg-gray-700' => $display !== 'tablet',
                       ])
                       aria-label="Tablet" title="Tablet">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </a>
                </div>
                <div class="p-2">
                    <a href="{{ request()->fullUrlWithQuery(['display' => null]) }}"
                       @class([
                            'flex items-center justify-center p-3 rounded-full transition-colors duration-100',
                            'bg-gray-600 text-white' => $display === null,
                            'hover:bg-gray-700' => $display !== null,
                       ])
                       aria-label="Desktop" title="Desktop">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </a>
                </div>
            @endif
            @if($send)
                <div class="p-2">
                    <a href="{{ route('mailbook.send', ['class' => $current->class(), 'variant' => $current->currentVariant()?->slug, 'locale' => $currentLocale]) }}"
                       @class([
                           'flex items-center justify-center p-3 rounded-full  transition-colors duration-100',
                           'bg-gray-600 text-white' => $display === 'phone',
                           'hover:bg-gray-700' => $display !== 'phone',
                       ])
                       aria-label="Send" title="Send">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-6 h-6 fill-white">
                            <path
                                d="M498.1 5.6c10.1 7 15.4 19.1 13.5 31.2l-64 416c-1.5 9.7-7.4 18.2-16 23s-18.9 5.4-28 1.6L284 427.7l-68.5 74.1c-8.9 9.7-22.9 12.9-35.2 8.1S160 493.2 160 480V396.4c0-4 1.5-7.8 4.2-10.7L331.8 202.8c5.8-6.3 5.6-16-.4-22s-15.7-6.4-22-.7L106 360.8 17.7 316.6C7.1 311.3 .3 300.7 0 288.9s5.9-22.8 16.1-28.7l448-256c10.7-6.1 23.9-5.5 34 1.4z"/>
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
