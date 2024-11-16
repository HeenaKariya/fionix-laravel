<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div>
        <img src="{{ asset('images/concord-laravel.jpg') }}" alt="{{ config('app.name') }}" class="fill-current text-gray-500" style="    width: 10rem;height: 7rem; ">
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="flex flex-col space-y-4"> 
                <a href="{{ route('login') }}" class="bg-gray-500 hover:bg-red-700 text-white font-bold py-2 px-4 mt-4 rounded text-center">  
                    Admin Login
                </a>
                <a href="{{ route('login') }}" class="bg-gray-500 hover:bg-red-700 text-white font-bold py-2 px-4 mt-4 rounded text-center">
                    Owner Login
                </a>
                <a href="{{ route('login') }}" class="bg-gray-500 hover:bg-red-700 text-white font-bold py-2 px-4 mt-4 rounded text-center">
                    Manager Login
                </a>
                <a href="{{ route('login') }}" class="bg-gray-500 hover:bg-red-700 text-white font-bold py-2 px-4 mt-4 rounded text-center">
                    Supervisor Login
                </a>
                <a href="{{ route('login') }}" class="bg-gray-500 hover:bg-red-700 text-white font-bold py-2 px-4 mt-4 rounded text-center">
                    Accounts Manager
                </a>
            </div>
        </div>
    </div>
</body>
</html>
