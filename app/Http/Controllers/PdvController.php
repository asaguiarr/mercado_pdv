<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PdvController extends Controller
{
    /**
     * Página inicial do PDV → lista de vendas
     */
    public function index()
    {
        $sales = Sale::with(['customer', 'user'])->latest()->get();
        return view('pdv.sales', compact('sales'));
    }

    /**
     * Formulário para criar nova venda
     */
    public function create()
    {
        // Exemplo: você pode carregar clientes e produtos para o form
        $products = Product::all();
        return view('pdv.create', compact('products'));
    }

    /**
     * Processar uma venda (via POST)
     */
    public function processSale(Request $request)
    {
        $request->validate([
            'cart' => 'required|array|min:1',
            'payment_method' => 'required|string',
            'discount' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        DB::beginTransaction();

        try {
            // Criar a venda
            $sale = Sale::create([
                'user_id' => auth()->id(),
                'customer_id' => $request->customer_id,
                'payment_method' => $request->payment_method,
                'discount' => $request->discount,
                'total' => 0,
            ]);

            $total = 0;

            foreach ($request->cart as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Estoque insuficiente para o produto {$product->name}");
                }

                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'price'      => $product->price,
                    'subtotal'   => $subtotal,
                ]);

                // Atualiza estoque
                $product->decrement('stock', $item['quantity']);
            }

            $finalTotal = max($total - $request->discount, 0);
            $sale->update(['total' => $finalTotal]);

            DB::commit();

            return response()->json([
                'message' => 'Venda processada com sucesso!',
                'sale' => $sale->load(['items.product', 'customer', 'user']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Erro ao processar a venda',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Listagem de produtos para o PDV (ex: AJAX)
     */
    public function getProducts()
    {
        $products = Product::select('id', 'name', 'price', 'stock')->get();
        return response()->json($products);
    }

    /**
     * Detalhes de um produto específico (ex: AJAX)
     */
    public function getProduct($id)
    {
        $product = Product::select('id', 'name', 'price', 'stock')->findOrFail($id);
        return response()->json($product);
    }
}
// app/Http/Controllers/PdvController.php

public function processSale(Request $request)
{
    // ...

    $sale = Sale::create([
        'customer_id' => $request->customer_id,
        'payment_method' => $request->payment_method,
        'total' => $total,
    ]);

    // ...
}