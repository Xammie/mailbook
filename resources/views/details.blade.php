<div class="flex-col justify-between gap-2 w-[300px] overflow-y-hidden overflow-x-auto hidden xl:flex shadow-xl">
    <div class="flex flex-col p-4 divide-y divide-gray-500">
        @if($subject)
            <div class="flex flex-col py-2">
                <div class="text-xs font-bold tracking-wide uppercase">
                    Subject
                </div>
                <div class="text-sm">
                    {{ $subject }}
                </div>
            </div>
        @endif
        @if(!empty($from))
            <div class="flex flex-col py-2">
                <div class="text-xs font-bold tracking-wide uppercase">
                    From
                </div>
                @foreach($from as $mail)
                    <div class="text-sm truncate" title="{{ $mail }}">
                        {{ $mail }}
                    </div>
                @endforeach
            </div>
        @endif
        @if(!empty($to))
            <div class="flex flex-col py-2">
                <div class="text-xs font-bold tracking-wide uppercase">
                    To
                </div>
                @foreach($to as $mail)
                    <div class="text-sm truncate" title="{{ $mail }}">
                        {{ $mail }}
                    </div>
                @endforeach
            </div>
        @endif
        @if(!empty($cc))
            <div class="flex flex-col py-2">
                <div class="text-xs font-bold tracking-wide uppercase">
                    Cc
                </div>
                @foreach($cc as $mail)
                    <div class="text-sm truncate" title="{{ $mail }}">
                        {{ $mail }}
                    </div>
                @endforeach
            </div>
        @endif
        @if(!empty($bcc))
            <div class="flex flex-col py-2">
                <div class="text-xs font-bold tracking-wide uppercase">
                    Bcc
                </div>
                @foreach($bcc as $mail)
                    <div class="text-sm truncate" title="{{ $mail }}">
                        {{ $mail }}
                    </div>
                @endforeach
            </div>
        @endif
        @if($attachments->isNotEmpty())
            <div class="flex flex-col py-2 gap-2">
                <div class="text-xs font-bold tracking-wide uppercase">
                    Attachments
                </div>
                <div class="flex flex-col gap-2 items-start">
                    @foreach($attachments as $attachment)
                        <div
                            class="px-2 py-1 rounded-full shadow-lg bg-gray-600 flex gap-1 items-center text-sm truncate">
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
    <div class="text-gray-200 text-center text-xs p-2">
        Created with <a href="https://github.com/Xammie/mailbook" target="_blank" class="text-white text-bold underline">mailbook</a>
    </div>
</div>
