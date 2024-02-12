@php use Xammie\Mailbook\Data\MailableGroup; @endphp
<div class="hidden md:flex flex-col w-[300px] max-w-full overflow-x-hidden overflow-y-auto">
    <div class="flex-col gap-[2px]">
        @foreach($items as $item)
            @if($item instanceof MailableGroup)
                <div class="mt-4 first:mt-0 px-3 py-[3px] text-sm text-gray-200 font-bold uppercase tracking-wide">
                    {{ $item->label }}
                </div>

                @foreach($item->items as $subItem)
                    <x-mailbook::items
                        :mailable="$subItem"
                        :current="$current"
                        :display="$display"
                        :currentLocale="$currentLocale"
                    />
                @endforeach
                <div class="mb-4"></div>
            @else
                <x-mailbook::items
                    :mailable="$item"
                    :current="$current"
                    :display="$display"
                    :currentLocale="$currentLocale"
                />
            @endif
        @endforeach
    </div>
</div>
