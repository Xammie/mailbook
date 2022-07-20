<div class="flex flex-col gap-2 border-r border-gray-500 border-solid w-[300px]">
    <div class="flex flex-col">
        @foreach($mailables as $mailable)
            @if($mailable->hasVariants())
                <div class="uppercase tracking-wider font-bold text-white text-xs pt-2 pb-1 px-3">
                    {{ $mailable->getLabel() }}
                </div>
                @foreach($mailable->getVariants() as $variant)
                    @if($mailable->is($current) && $variant->slug === $current->currentVariant()?->slug)
                        <a href="{{ route('mailbook.dashboard', ['selected' => $mailable->class(), 'variant' => $variant->slug]) }}"
                           class="bg-gray-600 text-white group flex items-center pl-5 px-3 py-2 text-base font-medium">
                            {{ $variant->label }}
                        </a>
                    @else
                        <a href="{{ route('mailbook.dashboard', ['selected' => $mailable->class(), 'variant' => $variant->slug]) }}"
                           class="text-white hover:bg-gray-700 group flex items-center pl-5 px-3 py-2 text-base font-medium transition-colors duration-100">
                            {{ $variant->label }}
                        </a>
                    @endif
                @endforeach
                <div class="border-b border-solid border-gray-500"></div>
            @else
                @if($mailable->is($current))
                    <a href="{{ route('mailbook.dashboard', ['selected' => $mailable->class()]) }}"
                       class="bg-gray-600 text-white group flex items-center px-3 py-2 text-base font-medium">
                        {{ $mailable->getLabel() }}
                    </a>
                @else
                    <a href="{{ route('mailbook.dashboard', ['selected' => $mailable->class()]) }}"
                       class="text-white hover:bg-gray-700 group flex items-center px-3 py-2 text-base font-medium transition-colors duration-100">
                        {{ $mailable->getLabel() }}
                    </a>
                @endif
            @endif
        @endforeach
    </div>
</div>
