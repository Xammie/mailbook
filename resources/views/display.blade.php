<div class="flex flex-col flex-1 bg-gray-900">
    <div class="text-black flex-1 flex justify-center">
        <iframe @class([
                    'w-full h-full bg-white',
                    'max-w-md' => $display === 'phone',
                    'max-w-3xl' => $display === 'tablet',
                ])
                src="{{ route('mailbook.content', ['class' => $current->class(), 'variant' => $current->currentVariant()?->slug]) }}"></iframe>
    </div>
    @if($attachments->isNotEmpty())
        <div class="flex flex-col px-5 py-2 gap-2">
            <div class="text-sm font-bold uppercase tracking-wider">
                Attachments
            </div>
            <div class="flex gap-3">
                @foreach($attachments as $attachment)
                    <div class="px-2 py-1 rounded-full shadow-lg bg-gray-600 flex gap-1 items-center text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"/>
                        </svg>
                        {{ $attachment->name }}
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
