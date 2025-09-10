<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\CashStatus;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PdvController extends Controller
{
    /**
     * Página inicial do PDV → lista de vendas com paginação
     */
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'user']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $sales = $query->latest()->paginate(15);
        return view('pdv.sales', compact('sales'));
    }

    /**
     * Formulário para criar nova venda
     */
    public function create()
    {
        $cashStatus = CashStatus::where('user_id', auth()->id())->where('status', 'open')->first();

        if (!$cashStatus) {
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
        $cashStatus = CashStatus::where('user_id', auth()->id())->where('status', 'open')->first();

        if (!$cashStatus) {
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
                $product = Product::where('id', $item['product_id'])->lockForUpdate()->first();

                if (!$product) {
                    throw new \Exception("Produto não encontrado");
                }

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Estoque insuficiente para o produto {$product->name}");
                }

                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock', $item['quantity']);

                // Registrar movimentação de saída
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'saida',
                    'quantity' => $item['quantity'],
                    'reason' => 'Venda realizada',
                ]);
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
            Log::error('Erro ao processar venda', ['error' => $e->getMessage(), 'user_id' => auth()->id()]);
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
        $cashStatus = CashStatus::where('user_id', auth()->id())->where('status', 'open')->first();

        if ($cashStatus) {
            return redirect()->route('pdv.sales')->with('error', 'Caixa já está aberto');
        }

        return view('pdv.open-cash');
    }

    /**
     * Abrir o caixa (processamento)
     */
    public function openCashStore(Request $request)
    {
        $request->validate([
            'initial_balance' => 'required|numeric|min:0',
        ]);

        $cashStatus = CashStatus::firstOrNew(['user_id' => auth()->id(), 'status' => 'open']);
        $cashStatus->initial_balance = $request->input('initial_balance');
        $cashStatus->status = 'open';
        $cashStatus->save();

        return redirect()->route('pdv.sales')->with('success', 'Caixa aberto com sucesso');
    }

    /**
     * Listagem de produtos para o PDV (via AJAX / busca)
     */
    public function getProducts(Request $request)
    {
        $search = $request->input('search');

        $products = Product::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
        })->limit(50)->get();

        return response()->json($products);
    }

    /**
     * Buscar produto específico por ID
     */
    public function getProduct($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    /**
     * Detalhes da venda
     */
    public function show($id)
    {
        $sale = Sale::with(['items.product', 'customer', 'user'])->findOrFail($id);
        return view('pdv.show', compact('sale'));
    }

    /**
     * Fechar caixa (formulário)
     */
    public function closeCash()
    {
        $cashStatus = CashStatus::where('user_id', auth()->id())->where('status', 'open')->first();

        if (!$cashStatus) {
            return redirect()->route('pdv.sales')->with('error', 'Nenhum caixa aberto encontrado');
        }

        return view('pdv.close-cash', compact('cashStatus'));
    }

    /**
     * Fechar caixa (processamento)
     */
    public function closeCashStore(Request $request)
    {
        $request->validate([
            'closing_balance' => 'required|numeric|min:0',
        ]);

        $cashStatus = CashStatus::where('user_id', auth()->id())->where('status', 'open')->first();

        if (!$cashStatus) {
            return redirect()->route('pdv.sales')->with('error', 'Nenhum caixa aberto encontrado');
        }

        $cashStatus->status = 'closed';
        $cashStatus->closing_balance = $request->input('closing_balance');
        $cashStatus->save();

        return redirect()->route('pdv.sales')->with('success', 'Caixa fechado com sucesso');
    }

    /**
     * Relatório de vendas
     */
    public function report(Request $request)
    {
        $query = Sale::with(['customer', 'user']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . " 00:00:00",
                $request->end_date . " 23:59:59"
            ]);
        }

        $sales = $query->latest()->paginate(50);

        return view('pdv.report', compact('sales'));
    }
}
