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
            return view('dashboard', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar dados do dashboard');
        }
    }
}