@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow-lg rounded-lg border-l-4 border-blue-500">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-boxes text-blue-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-500">Materials</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['materials'] ?? 0 }}</p>
                            <p class="text-xs text-green-600"><i class="fas fa-arrow-up"></i> Active items</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-lg rounded-lg border-l-4 border-green-500">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-truck text-green-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-500">Vendors</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['vendors'] ?? 0 }}</p>
                            <p class="text-xs text-blue-600"><i class="fas fa-handshake"></i> Partners</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-lg rounded-lg border-l-4 border-yellow-500">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-yellow-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-500">Purchase Orders</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['purchase_orders'] ?? 0 }}</p>
                            <p class="text-xs text-orange-600"><i class="fas fa-clock"></i> This month</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-lg rounded-lg border-l-4 border-purple-500">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-cogs text-purple-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-500">Machines</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['machines'] ?? 0 }}</p>
                            <p class="text-xs text-green-600"><i class="fas fa-check-circle"></i> Operational</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                <span class="text-sm text-gray-500">Start here</span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('materials.create') }}" class="group flex flex-col items-center p-4 border-2 border-dashed border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-all">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-blue-200">
                        <i class="fas fa-plus text-blue-600"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-blue-700">Add Material</span>
                </a>
                <a href="{{ route('vendors.create') }}" class="group flex flex-col items-center p-4 border-2 border-dashed border-gray-200 rounded-lg hover:border-green-300 hover:bg-green-50 transition-all">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-green-200">
                        <i class="fas fa-plus text-green-600"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-green-700">Add Vendor</span>
                </a>
                <a href="{{ route('purchase-orders.create') }}" class="group flex flex-col items-center p-4 border-2 border-dashed border-gray-200 rounded-lg hover:border-yellow-300 hover:bg-yellow-50 transition-all">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-yellow-200">
                        <i class="fas fa-shopping-cart text-yellow-600"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-yellow-700">New Order</span>
                </a>
                <a href="{{ route('machines.create') }}" class="group flex flex-col items-center p-4 border-2 border-dashed border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition-all">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-purple-200">
                        <i class="fas fa-cogs text-purple-600"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-purple-700">Add Machine</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Getting Started -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Getting Started</h3>
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Setup</span>
                </div>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-green-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Workshop Created</p>
                            <p class="text-xs text-gray-500">Your {{ auth()->user()->business->name }} workspace is ready!</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 text-xs font-bold">2</span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Add Materials & Vendors</p>
                            <p class="text-xs text-gray-500">Start by adding your raw materials and supplier details</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                <span class="text-gray-600 text-xs font-bold">3</span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Register Machines</p>
                            <p class="text-xs text-gray-500">Add your CNC, lathe, welding equipment</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Workshop Info -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Workshop Details</h3>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Active</span>
                </div>
                @if(auth()->user()->business)
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Business Name:</span>
                        <span class="text-sm font-medium text-gray-900">{{ auth()->user()->business->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Workspace URL:</span>
                        <span class="text-sm font-medium text-blue-600">{{ auth()->user()->business->subdomain }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Plan:</span>
                        <span class="text-sm font-medium text-green-600">{{ ucfirst(auth()->user()->business->subscription_plan ?? 'Free') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Owner:</span>
                        <span class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Manufacturing Focus Message -->
        <div class="mt-8 bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-industry text-blue-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-lg font-semibold text-gray-900">Built for Makers, Not Offices</h4>
                    <p class="text-sm text-gray-600 mt-1">Track every job, machine hour, and material gram. No more guessing where your costs go.</p>
                </div>
            </div>
        </div>
    </div>
@endsection