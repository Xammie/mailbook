<div class="flex flex-col flex-1 bg-gray-900">
    <div class="text-black flex-1 flex justify-center bg-[#090816]">
        <simple-shadow-root @class([
                    'w-full h-full bg-white',
                    'max-w-md' => $display === 'phone',
                    'max-w-3xl' => $display === 'tablet',
                ])
                content="{{ base64_encode($content) }}">
        </simple-shadow-root>
    </div>
</div>
