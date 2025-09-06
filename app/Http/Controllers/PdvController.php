<?php
// app/Http/Controllers/PdvController.php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\CashStatus;
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
        // Verificar se o caixa está aberto
        $cashStatus = CashStatus::where('user_id', auth()->id())->first();

        if (!$cashStatus || $cashStatus->status != 'open') {
            return redirect()->route('pdv.open-cash')->with('error', 'Caixa não está aberto');
        }

        $products = Product::all();
        $customers = Customer::all();
        return view('pdv.create', compact('products', 'customers'));
    }

    /**
     * Processar uma venda (via POST)
     */
    public function processSale(Request $request)
    {
        // Verificar se o caixa está aberto
        $cashStatus = CashStatus::where('user_id', auth()->id())->first();

        if (!$cashStatus || $cashStatus->status != 'open') {
            return response()->json([
                'error' => 'Caixa não está aberto',
            ], 403);
        }

        $request->validate([
            'cart' => 'required|array|min:1',
            'payment_method' => 'required|string|in:dinheiro,cartao,pix,misto',
            'discount' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        DB::beginTransaction();

        try {
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
     * Abrir o caixa (formulário)
     */
    public function openCash()
    {
        // Verificar se o caixa já está aberto
        $cashStatus = CashStatus::where('user_id', auth()->id())->first();

        if ($cashStatus && $cashStatus->status == 'open') {
            return redirect()->route('pdv.sales')->with('error', 'Caixa já está aberto');
        }

        return view('pdv.open-cash');
    }

    /**
     * Abrir o caixa (processamento)
     */
    public function openCashStore(Request $request)
    {
        // Abrir o caixa
        $cashStatus = new CashStatus();
        $cashStatus->user_id = auth()->id();
        $cashStatus->initial_balance = $request->input('initial_balance');
        $cashStatus->status = 'open';
        $cashStatus->save();

        return redirect()->route('pdv.sales')->with('success', 'Caixa aberto com sucesso');
    }

    // ...
}