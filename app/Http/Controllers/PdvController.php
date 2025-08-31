<?php

// app/Http/Controllers/PDVController.php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class PDVController extends Controller
{
    /**
     * Lista as vendas realizadas no PDV com paginação.
     */
    public function listSales(Request $request)
    {
        // Carrega vendas já com os relacionamentos principais
        $query = Sale::with(['items.product', 'user', 'customer'])
            ->orderBy('id', 'desc');

        // 🔹 Filtro por forma de pagamento
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // 🔹 Filtro por período (início/fim)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $sales = $query->paginate(10);

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