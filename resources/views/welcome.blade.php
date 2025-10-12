<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitorbizz - Manufacturing Management for SMEs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-blue-600">Monitorbizz</h1>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600">Login</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Get Started</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-5xl font-bold mb-6">Still writing job details on paper? It's 2025.</h1>
            <p class="text-xl mb-8 max-w-3xl mx-auto">Monitorbizz helps small manufacturers replace clipboards, notebooks, and messy Excel sheets — with a simple app that fits on your phone.</p>
            <a href="{{ route('register') }}" class="bg-white text-blue-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition">Start Your Free Workshop</a>
        </div>
    </section>

    <!-- Features -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">What Every Shop Needs</h2>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-blue-600 text-3xl mb-4"><i class="fas fa-boxes"></i></div>
                    <h3 class="text-xl font-semibold mb-2">Items & Products</h3>
                    <p class="text-gray-600">List your raw materials, finished goods, spare parts</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-blue-600 text-3xl mb-4"><i class="fas fa-shopping-cart"></i></div>
                    <h3 class="text-xl font-semibold mb-2">Purchase Orders</h3>
                    <p class="text-gray-600">Order steel, wood, paint, screws digitally</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-blue-600 text-3xl mb-4"><i class="fas fa-file-invoice"></i></div>
                    <h3 class="text-xl font-semibold mb-2">Invoices & Quotes</h3>
                    <p class="text-gray-600">Bill customers, send professional quotes</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-blue-600 text-3xl mb-4"><i class="fas fa-cogs"></i></div>
                    <h3 class="text-xl font-semibold mb-2">Machines</h3>
                    <p class="text-gray-600">Register every machine: CNC, lathe, welding setup</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-blue-600 text-3xl mb-4"><i class="fas fa-clipboard-list"></i></div>
                    <h3 class="text-xl font-semibold mb-2">Work Orders</h3>
                    <p class="text-gray-600">Digital job cards. No more lost paper slips</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-blue-600 text-3xl mb-4"><i class="fas fa-chart-line"></i></div>
                    <h3 class="text-xl font-semibold mb-2">Dashboard</h3>
                    <p class="text-gray-600">See sales, stock, pending work at a glance</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="bg-blue-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold mb-4">Ready to digitize your workshop?</h2>
            <p class="text-xl mb-8">Get your own workspace: yourworkshop.monitorbizz.com</p>
            <a href="{{ route('register') }}" class="bg-white text-blue-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition">Start Free Trial</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; 2025 Monitorbizz. Built for workshops, not offices.</p>
        </div>
    </footer>
</body>
</html>