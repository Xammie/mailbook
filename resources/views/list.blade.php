<div class="hidden md:flex flex-col gap-[2px] w-[300px] overflow-x-hidden overflow-y-auto p-3 max-w-full">
    @foreach($mailables as $mailable)
        @if($mailable->hasVariants())
            <div class="uppercase tracking-wider font-bold text-white text-xs pt-2 pb-1 px-3">
                {{ $mailable->getLabel() }}
            </div>
            @foreach($mailable->getVariants() as $variant)
                <a href="{{ route('mailbook.dashboard', ['selected' => $mailable->class(), 'variant' => $variant->slug, 'display' => $display]) }}"
                    @class([
                         'px-3 py-[3px] ml-3 text-base font-medium flex items-center transition-colors duration-100 rounded-md',
                          $mailable->is($current) && $variant->slug === $current->currentVariant()?->slug
                          ? 'bg-gray-600 text-white'
                          : 'text-gray-200 hover:text-white hover:bg-gray-700',
                    ])>
                    {{ $variant->label }}
                </a>
            @endforeach
        @else
            <a href="{{ route('mailbook.dashboard', ['selected' => $mailable->class(), 'display' => $display]) }}"
                @class([
                    'px-3 py-[3px] text-base font-medium rounded-md flex items-center',
                    'bg-gray-600 text-white group flex items-center' => $mailable->is($current),
                    'text-gray-200 hover:text-white hover:bg-gray-700 transition-colors duration-100' => !$mailable->is($current),
                 ])>
                {{ $mailable->getLabel() }}
            </a>
        @endif
    @endforeach
</div>
