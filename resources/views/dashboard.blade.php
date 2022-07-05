<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $subject }} - Mailbook</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>✉️</text></svg>">
</head>
<body class="bg-gray-900 text-white min-h-screen w-screen overflow-hidden flex flex-col">
@include('mailbook::navigation')
<div class="flex flex-1">
    @include('mailbook::list')
    @include('mailbook::display')
</div>
</body>
</html>

