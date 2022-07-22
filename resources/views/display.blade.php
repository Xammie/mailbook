<div class="flex flex-col flex-1">
    <div class="bg-white text-black flex-1 flex justify-center">
        <iframe @class([
                    'w-full h-full',
                    'max-w-md' => request()->get('display') === 'phone',
                    'max-w-3xl' => request()->get('display') === 'tablet',
                ])
                src="{{ route('mailbook.content', ['class' => $current->class(), 'variant' => $current->currentVariant()?->slug]) }}"></iframe>
    </div>
</div>
