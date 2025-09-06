<?php

// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request)
    {
        try {
            $data = $this->dashboardService->getDashboardData();
            $data['cards'] = [
                [
                    'label' => 'Vendas Hoje',
                    'value' => $data['todaySales'],
                    'color' => 'success',
                    'icon' => 'fas fa-dollar-sign'
                ],
                [
                    'label' => 'Clientes Cadastrados',
                    'value' => $data['customersCount'],
                    'color' => 'primary',
                    'icon' => 'fas fa-users'
                ],
                [
                    'label' => 'Produtos em Estoque',
                    'value' => $data['productsCount'],
                    'color' => 'warning',
                    'icon' => 'fas fa-box-open'
                ],
                [
                    'label' => 'Pedidos Pendentes',
                    'value' => $data['pendingOrders'],
                    'color' => 'danger',
                    'icon' => 'fas fa-truck'
                ],
            ];
            return view('dashboard', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar dados do dashboard');
        }
    }
}