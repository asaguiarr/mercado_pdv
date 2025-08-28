<?php
// app/Services/DashboardService.php

namespace App\Services;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Sale;

class DashboardService
{
    public function getDashboardData()
    {
        $today = now()->format('Y-m-d');
        
        $todaySales = Sale::whereDate('created_at', $today)->sum('total');
        $customersCount = Customer::count();
        $productsCount = Product::count();
        $pendingOrders = Order::where('status', '!=', 'done')->count();
        
        $lowStockProducts = Product::where('stock', '<=', 5)->get();
        
        return [
            'todaySales' => $todaySales,
            'customersCount' => $customersCount,
            'productsCount' => $productsCount,
            'pendingOrders' => $pendingOrders,
            'lowStockProducts' => $lowStockProducts,
        ];
    }
}