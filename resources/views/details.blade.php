<div class="flex-col justify-between gap-2 w-[300px] overflow-y-auto overflow-x-hidden hidden xl:flex">
    <div class="flex flex-col p-4 divide-y divide-gray-600">
        @foreach($meta as $label => $values)
            <div class="flex flex-col py-2">
                <div class="text-xs font-bold tracking-wide uppercase">
                    {{ $label }}
                </div>
                    @if(is_array($values))
                    @foreach($values as $mail)
                        <div class="text-sm truncate" title="{{ $mail }}">
                            {{ $mail }}
                        </div>
                    @endforeach
                @else
                    <div class="text-sm">
                        {{ $values }}
                    </div>
                @endif
            </div>
        @endforeach
        @if(!empty($attachments))
            <div class="flex flex-col py-2 gap-2">
                <div class="text-xs font-bold tracking-wide uppercase">
                    Attachments
                </div>
                <div class="flex flex-row flex-wrap gap-2 items-start">
                    @foreach($attachments as $attachment)
                        <div
                            class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-blue-500/50 text-primary-foreground hover:bg-primary/80">
                            {{ $attachment }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        @if($size)
            <div class="flex flex-col py-2">
                <div class="text-xs font-bold tracking-wide uppercase">
                    Size
                </div>
                <div class="text-sm">
                    {{ $size }}
                </div>
            </div>
        @endif
    </div>
    @if(config('mailbook.show_credits'))
        <div class="text-gray-200 text-center text-xs p-2">
            Created with <a href="https://github.com/Xammie/mailbook" target="_blank"
                            class="text-white text-bold underline">mailbook</a>
        </div>
    @endif
</div>
