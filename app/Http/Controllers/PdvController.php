<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;

public function processSale(Request $request)
{
    // 🔹 Validação inicial dos dados da requisição
    $request->validate([
        'cart' => 'required|array|min:1',       // carrinho precisa ter pelo menos 1 item
        'payment_method' => 'required|string', // ex: dinheiro, pix, cartão
        'discount' => 'required|numeric|min:0',
        'customer_id' => 'nullable|exists:customers,id'
    ]);

    DB::beginTransaction(); // 🔒 Inicia a transação para consistência

    try {
        // 🔹 Criação da venda inicial
        $sale = Sale::create([
            'user_id' => auth()->id(),              // usuário logado
            'customer_id' => $request->customer_id, // cliente pode ser opcional
            'payment_method' => $request->payment_method,
            'discount' => $request->discount,
            'total' => 0 // será atualizado depois
        ]);

        $total = 0;

        // 🔹 Itera sobre o carrinho de compras
        foreach ($request->cart as $item) {
            $product = Product::findOrFail($item['product_id']);

            // ✅ Valida estoque
            if ($product->stock < $item['quantity']) {
                throw new \Exception("Estoque insuficiente para o produto {$product->name}");
            }

            // calcula subtotal
            $subtotal = $product->price * $item['quantity'];
            $total += $subtotal;

            // 🔹 Cria o item da venda
            $sale->items()->create([
                'product_id' => $product->id,
                'quantity'   => $item['quantity'],
                'price'      => $product->price,
                'subtotal'   => $subtotal,
            ]);

            // 🔹 Atualiza o estoque do produto
            $product->decrement('stock', $item['quantity']);
        }

        // 🔹 Aplica desconto e atualiza o total da venda
        $finalTotal = max($total - $request->discount, 0); // nunca pode ser negativo
        $sale->update(['total' => $finalTotal]);

        DB::commit(); // ✅ Confirma a transação

        // 🔹 Retorna a venda processada com detalhes
        return response()->json([
            'message' => 'Venda processada com sucesso!',
            'sale' => $sale->load(['items.product', 'customer', 'user'])
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack(); // ❌ Cancela tudo se der erro
        return response()->json([
            'error' => 'Erro ao processar a venda',
            'details' => $e->getMessage()
        ], 500);
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