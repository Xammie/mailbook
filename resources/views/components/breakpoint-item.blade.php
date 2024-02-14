@props(['label', 'type', 'selected'])

<div>
    <a href="{{ request()->fullUrlWithQuery(['display' => $type]) }}"
       @class([
           'flex items-center justify-center p-1 rounded-md transition-colors duration-100 text-white',
           'bg-[#829BBF]' => $selected === $type,
           'bg-[#677180] hover:bg-[#829BBF]' => $selected !== $type,
       ])
       aria-selected="{{ $selected === $type ? 'true' : 'false' }}"
       aria-label="{{ $label }}"
       title="{{ $label }}">
        {{ $slot }}
    </a>
</div>
