<div class="flex flex-col gap-2 border-r border-gray-500 border-solid w-[300px]">
    <div class="flex flex-col p-2 gap-1">
        @foreach($mailables as $mailable)
            @if($mailable->hasVariants())
                <div class="uppercase tracking-wider font-bold text-white text-xs pt-2 pb-1 px-3">
                    {{ $mailable->getLabel() }}
                </div>
                @foreach($mailable->getVariants() as $variant)
                    <a href="{{ route('mailbook.dashboard', ['selected' => $mailable->class(), 'variant' => $variant->slug, 'display' => $display]) }}"
                       @class([
                            'px-3 py-1 pl-5 text-base font-medium flex items-center transition-colors duration-100',
                             $mailable->is($current) && $variant->slug === $current->currentVariant()?->slug
                             ? 'bg-gray-600 text-white'
                             : 'text-white hover:bg-gray-700 group flex items-center text-base font-medium ',
                       ])
                       class="bg-gray-600 text-white group flex items-center pl-5 px-3 py-2 text-base font-medium">
                        {{ $variant->label }}
                    </a>
                @endforeach
                <div class="border-b border-solid border-gray-500"></div>
            @else
                <a href="{{ route('mailbook.dashboard', ['selected' => $mailable->class(), 'display' => $display]) }}"
                    @class([
                        'px-3 py-1 text-base font-medium rounded-md flex items-center ',
                        'bg-gray-600 text-white group flex items-center' => $mailable->is($current),
                        'text-gray-200 hover:bg-gray-700 transition-colors duration-100' => !$mailable->is($current),
                     ])>
                    {{ $mailable->getLabel() }}
                </a>
            @endif
        @endforeach
    </div>
</div>
