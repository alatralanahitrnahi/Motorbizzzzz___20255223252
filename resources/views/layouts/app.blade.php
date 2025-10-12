<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Monitorbizz</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="hidden md:flex md:w-64 md:flex-col">
            <div class="flex flex-col flex-grow pt-5 overflow-y-auto bg-white border-r">
                <div class="flex items-center flex-shrink-0 px-4">
                    <h1 class="text-xl font-bold text-blue-600">Monitorbizz</h1>
                </div>
                
                @if(auth()->user()->business)
                <div class="px-4 mt-4 pb-4 border-b">
                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->business->name }}</p>
                    <p class="text-xs text-gray-500">{{ auth()->user()->business->subdomain }}</p>
                </div>
                @endif

                <nav class="mt-5 flex-1 px-2 space-y-1">
                    <a href="{{ route('dashboard') }}" class="@if(request()->routeIs('dashboard')) bg-blue-100 text-blue-700 @else text-gray-600 hover:bg-gray-50 @endif group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                    </a>
                    
                    @can('view-materials')
                    <a href="{{ route('materials.index') }}" class="@if(request()->routeIs('materials.*')) bg-blue-100 text-blue-700 @else text-gray-600 hover:bg-gray-50 @endif group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-boxes mr-3"></i> Materials
                    </a>
                    @endcan
                    
                    @can('view-vendors')
                    <a href="{{ route('vendors.index') }}" class="@if(request()->routeIs('vendors.*')) bg-blue-100 text-blue-700 @else text-gray-600 hover:bg-gray-50 @endif group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-truck mr-3"></i> Vendors
                    </a>
                    @endcan
                    
                    @can('view-purchase-orders')
                    <a href="{{ route('purchase-orders.index') }}" class="@if(request()->routeIs('purchase-orders.*')) bg-blue-100 text-blue-700 @else text-gray-600 hover:bg-gray-50 @endif group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-shopping-cart mr-3"></i> Purchase Orders
                    </a>
                    @endcan
                    
                    <a href="{{ route('machines.index') }}" class="@if(request()->routeIs('machines.*')) bg-blue-100 text-blue-700 @else text-gray-600 hover:bg-gray-50 @endif group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-cogs mr-3"></i> Machines
                    </a>
                    
                    <a href="{{ route('work-orders.index') }}" class="@if(request()->routeIs('work-orders.*')) bg-blue-100 text-blue-700 @else text-gray-600 hover:bg-gray-50 @endif group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-clipboard-list mr-3"></i> Work Orders
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <header class="bg-white shadow-sm border-b">
                <div class="flex items-center justify-between px-4 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h2>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-700">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto">
                <div class="py-6">
                    @if(session('success'))
                        <div class="mx-4 mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>