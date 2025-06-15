<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">
                    Dashboard
                </h1>
                <nav>
                    <a href="/weather" class="text-gray-700 hover:text-gray-900 px-3">Weather</a>
                    <a href="/myfavoritesubject" class="text-gray-700 hover:text-gray-900 px-3">My Pokemon</a>
                    <a href="/map" class="text-gray-700 hover:text-gray-900 px-3">Map</a>
                    <a href="/shop" class="text-gray-700 hover:text-gray-900 px-3">Shop</a>
                    <a href="/blog" class="text-gray-700 hover:text-gray-900 px-3">Blog</a>
                    
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-gray-900 px-3 py-2 leading-none">Logout</button>
                    </form>
                </nav>
            </div>
        </header>

        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h2 class="text-2xl font-bold mb-4">Welcome, {{ session('user_email') ?? 'Guest' }}!</h2>
                        <p class="text-gray-600">You are logged in as: <strong>{{ session('role') ?? 'unknown' }}</strong></p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
