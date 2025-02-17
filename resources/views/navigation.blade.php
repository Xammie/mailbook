<div class="flex items-stretch shrink-0 grow-0 bg-[#151e2b] z-20 h-16">
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
            @if($send)
                <div class="py-2">
                    <a href="{{ route('mailbook.send', ['class' => $current->class(), 'variant' => $current->currentVariant()?->slug, 'locale' => $currentLocale]) }}"
                       class="flex items-center justify-center p-3 rounded-lg transition-colors duration-100 hover:bg-gray-700"
                       aria-label="Send to {{ $send_to }}"
                       title="Send to {{ $send_to }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-6 h-6 fill-white">
                            <path
                                d="M498.1 5.6c10.1 7 15.4 19.1 13.5 31.2l-64 416c-1.5 9.7-7.4 18.2-16 23s-18.9 5.4-28 1.6L284 427.7l-68.5 74.1c-8.9 9.7-22.9 12.9-35.2 8.1S160 493.2 160 480V396.4c0-4 1.5-7.8 4.2-10.7L331.8 202.8c5.8-6.3 5.6-16-.4-22s-15.7-6.4-22-.7L106 360.8 17.7 316.6C7.1 311.3 .3 300.7 0 288.9s5.9-22.8 16.1-28.7l448-256c10.7-6.1 23.9-5.5 34 1.4z"/>
                        </svg>
                    </a>
                </div>
            @endif
            @if($locales)
                <div class="flex flex-col gap-1 justify-center p-2">
                    <label for="locale" class="px-2 text-xs font-bold tracking-wide uppercase">Locale</label>
                    <select id="locale" name="locale" class="bg-[#475569] p-1 rounded-lg text-sm text-white">
                        @foreach($locales as $key => $locale)
                            <option value="{{ $key }}" @selected($currentLocale == $key)>
                                {{ $locale }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>
</div>
