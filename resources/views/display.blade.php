<div class="flex flex-col flex-1">
    <div class="bg-white text-black flex-1">
        <iframe class="w-full h-full" src="{{ route('mailbook.content', ['class' => $current->class(), 'variant' => $current->currentVariant()?->slug]) }}"></iframe>
    </div>
</div>
