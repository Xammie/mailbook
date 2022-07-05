<div class="flex flex-col gap-2 p-5 border-r border-gray-500 border-solid w-[350px]">
    <div class="flex flex-col gap-1">
        @foreach($mailables as $mailable)
            @if($mailable->class === $current->class)
                <!-- Current: "bg-gray-100 text-gray-900", Default: "text-gray-600 hover:bg-gray-50 hover:text-gray-900" -->
                <a href="{{ route('mailbook.dashboard', ['selected' => $mailable->class]) }}"
                   class="bg-gray-100 text-gray-900 group flex items-center px-2 py-2 text-base font-medium rounded-md">
                    {{ $mailable->name() }}
                </a>
            @else
                <a href="{{ route('mailbook.dashboard', ['selected' => $mailable->class]) }}"
                   class="text-white hover:bg-gray-100 hover:text-gray-900 group flex items-center px-2 py-2 text-base font-medium rounded-md">
                    {{ $mailable->name() }}
                </a>
            @endif
        @endforeach
    </div>
</div>
