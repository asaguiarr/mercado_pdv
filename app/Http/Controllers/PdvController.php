<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class PdvController extends Controller
{
    public function listSales()
    {
        $sales = Sale::all();
        return view('pdv.sales', compact('sales'));
    }

    /**
     * Exibe os detalhes de uma venda específica.
     */
    public function show($id)
    {
        $sale = Sale::with(['items.product', 'user', 'customer'])->findOrFail($id);
        return view('pdv.show', compact('sale'));
    }

    /**
     * Processa uma venda.
     */
    public function processSale(Request $request)
    {
        $request->validate([
            'cart' => 'required|array',
            'payment_method' => 'required|string',
            'discount' => 'required|numeric',
        ]);

        try {
            // Lógica para processar a venda
            // ...

            return response()->json(['message' => 'Venda processada com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao processar a venda'], 500);
        }
    }
}
