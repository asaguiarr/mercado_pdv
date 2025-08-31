<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'          => 'nullable|string|max:255',
            'name'          => 'required|string|max:255',
            'cost_price'    => 'required|numeric|min:0',
            'profit_margin' => 'required|numeric|min:0',
            'sale_price'    => 'nullable|numeric|min:0',
            'stock'         => 'required|integer|min:0',
        ]);

        // Se não foi enviado sale_price, calcula automaticamente
        if (empty($validated['sale_price'])) {
            $validated['sale_price'] = $validated['cost_price'] * (1 + $validated['profit_margin'] / 100);
        }

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produto adicionado com sucesso!');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'code'          => 'nullable|string|max:255',
            'name'          => 'required|string|max:255',
            'cost_price'    => 'required|numeric|min:0',
            'profit_margin' => 'required|numeric|min:0',
            'sale_price'    => 'nullable|numeric|min:0',
            'stock'         => 'required|integer|min:0',
        ]);

        if (empty($validated['sale_price'])) {
            $validated['sale_price'] = $validated['cost_price'] * (1 + $validated['profit_margin'] / 100);
        }

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produto excluído com sucesso!');
    }
}
