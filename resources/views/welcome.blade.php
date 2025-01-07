<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Social Media Scheduler</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="font-sans antialiased bg-[url('../images/bg.webp')] bg-cover bg-center h-screen flex flex-col gap-7 items-center justify-center px-4">
    <main class="relative flex flex-col items-center justify-center gap-3 px-3 rounded-lg py-7 md:py-10 md:px-6">
        <img src="{{ Vite::asset('resources/images/main-bg.webp') }}" alt="Content BG"
            class="absolute top-0 left-0 w-full h-full pointer-events-none" />
        <h1 class="text-xl md:text-4xl font-bold text-center text-white !md:leading-[3rem]">Welcome to the AutoPost<br>
            to Social Media Web App!</h1>
        <a href="{{ route('filament.admin.auth.login') }}"
            class="px-4 py-2 text-sm text-white uppercase bg-blue-700 rounded-md hover:bg-blue-500 md:text-base">Get started</a>
    </main>
</body>

</html>
