<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Tailwind CSS via CDN (remove if you are compiling assets locally) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>


    <!-- Place for additional page-specific styles/scripts -->
    @stack('head')
</head>
<body class="bg-gray-100 min-h-screen antialiased leading-none">
    <nav class="bg-white shadow mb-8 py-4">
        <div class="container mx-auto px-4">
            <span class="text-lg font-semibold">{{ config('app.name', 'Laravel') }}</span>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>

    <!-- Page-specific scripts -->
    @stack('scripts')
</body>
</html>