<div class="flex items-stretch">
    <div class="flex flex-col justify-center gap-2 px-5 py-3 border-r border-b border-gray-500 border-solid w-[350px]">
        @include('mailbook::logo')
    </div>
    <div class="px-5 py-3 flex flex-col flex-1 border-r border-b border-gray-500 border-solid">
        <div class="text-xs font-bold uppercase tracking-wider">
            Subject
        </div>
        <div class="text-xl">
            {{ $subject }}
        </div>
    </div>
</div>
