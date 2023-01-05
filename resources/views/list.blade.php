<div class="hidden md:flex flex-col w-[300px] max-w-full overflow-x-hidden overflow-y-auto">
    <div class="flex-col gap-[2px]">
        @foreach($mailables as $mailable)
            @if($mailable->hasVariants())
                <div class="px-3 py-[3px] text-sm flex gap-1 items-center text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-4 h-4 text-green-500 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M7.875 14.25l1.214 1.942a2.25 2.25 0 001.908 1.058h2.006c.776 0 1.497-.4 1.908-1.058l1.214-1.942M2.41 9h4.636a2.25 2.25 0 011.872 1.002l.164.246a2.25 2.25 0 001.872 1.002h2.092a2.25 2.25 0 001.872-1.002l.164-.246A2.25 2.25 0 0116.954 9h4.636M2.41 9a2.25 2.25 0 00-.16.832V12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 12V9.832c0-.287-.055-.57-.16-.832M2.41 9a2.25 2.25 0 01.382-.632l3.285-3.832a2.25 2.25 0 011.708-.786h8.43c.657 0 1.281.287 1.709.786l3.284 3.832c.163.19.291.404.382.632M4.5 20.25h15A2.25 2.25 0 0021.75 18v-2.625c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125V18a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                    {{ $mailable->getLabel() }}
                </div>
                @foreach($mailable->getVariants() as $variant)
                    <a href="{{ route('mailbook.dashboard', ['selected' => $mailable->class(), 'variant' => $variant->slug, 'display' => $display, 'locale' => $currentLocale]) }}"
                        @class([
                             'px-3 pl-8 py-[3px] text-sm flex gap-1 items-center transition-colors duration-100',
                              $mailable->is($current) && $variant->slug === $current->currentVariant()?->slug
                              ? 'bg-gray-600 text-white'
                              : 'text-gray-200 hover:text-white hover:bg-gray-700',
                        ])>
                        @if($mailable->is($current) && $variant->slug === $current->currentVariant()?->slug)
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                 class="w-4 h-4 text-blue-500 flex-shrink-0">
                                <path
                                    d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z"/>
                                <path
                                    d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z"/>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="w-4 h-4 text-blue-500 flex-shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                            </svg>
                        @endif
                        {{ $variant->label }}
                    </a>
                @endforeach
            @else
                <a href="{{ route('mailbook.dashboard', ['selected' => $mailable->class(), 'display' => $display, 'locale' => $currentLocale]) }}"
                    @class([
                        'px-3 py-[3px] text-sm flex gap-1 items-center',
                        'bg-gray-600 text-white group flex items-center' => $mailable->is($current),
                        'text-gray-200 hover:text-white hover:bg-gray-700 transition-colors duration-100' => !$mailable->is($current),
                     ])>
                    @if($mailable->is($current))
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                             class="w-4 h-4 text-blue-500 flex-shrink-0">
                            <path
                                d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z"/>
                            <path
                                d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z"/>
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="w-4 h-4 text-blue-500 flex-shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                        </svg>
                    @endif
                    {{ $mailable->getLabel() }}
                </a>
            @endif
        @endforeach
    </div>
</div>
