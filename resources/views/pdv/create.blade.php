// resources/views/pdv/create.blade.php

@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Nova Venda</h1>

    <div class="row">
        <div class="col-md-6">
            {{-- Cliente (opcional) --}}
            <div class="mb-3">
                <label for="customer_id" class="form-label">Cliente</label>
                <select id="customer_id" class="form-select">
                    <option value="">Consumidor</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Produtos --}}
            <h5>Produtos</h5>
            <div class="form-group">
                <label for="product_id">Produto</label>
                <select id="product_id" class="form-select">
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} (R$ {{ number_format($product->price, 2, ',', '.') }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="quantity">Quantidade</label>
                <input type="number" id="quantity" class="form-control" value="1">
            </div>

            <button id="add-to-cart" class="btn btn-primary">Adicionar ao Carrinho</button>

            <table id="cart-table" class="table table-striped">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço</th>
                        <th>Total</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Os itens do carrinho serão exibidos aqui -->
                </tbody>
            </table>
        </div>

        <div class="col-md-6">
            {{-- Forma de pagamento --}}
            <div class="mb-3">
                <label for="payment_method" class="form-label">Forma de Pagamento</label>
                <select id="payment_method" class="form-select" required>
                    <option value="dinheiro">Dinheiro</option>
                    <option value="cartao">Cartão</option>
                    <option value="pix">Pix</option>
                </select>
            </div>

            {{-- Desconto --}}
            <div class="mb-3">
                <label for="discount" class="form-label">Desconto (R$)</label>
                <input type="number" id="discount" class="form-control" step="0.01" value="0" min="0">
            </div>

            <button id="finalize-sale" class="btn btn-primary">Finalizar Venda</button>
            <a href="{{ route('pdv.sales') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </div>
</div>
@endsection