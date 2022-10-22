<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $subject }} - Mailbook</title>
    <style>{{ $style }}</style>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>✉️</text></svg>">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-900 text-white h-screen w-screen flex flex-col">
@include('mailbook::navigation')
<div class="flex flex-grow flex-shrink items-stretch overflow-hidden">
    @include('mailbook::list')
    @include('mailbook::display')
    @include('mailbook::details')
</div>
</body>
</html>

