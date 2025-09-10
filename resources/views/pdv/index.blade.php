@extends('layouts.app')

@section('title', 'PDV')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="mb-4">Caixa Livre</h1>

            <form action="{{ route('pdv.sale') }}" method="POST">
                @csrf

                {{-- Cliente (opcional) --}}
                <div class="mb-3">
                    <label for="customer_id" class="form-label">Cliente</label>
                    <select name="customer_id" id="customer_id" class="form-select">
                        <option value="">Consumidor</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Produtos --}}
                <h5>Produtos</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Produto</th>
                                <th>Preço</th>
                                <th>Quantidade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>R$ {{ number_format($product->price, 2, ',', '.') }}</td>
                                <td>
                                    <input type="number" name="cart[{{ $product->id }}][quantity]" min="0" value="0" class="form-control form-control-sm">
                                    <input type="hidden" name="cart[{{ $product->id }}][product_id]" value="{{ $product->id }}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Forma de pagamento --}}
                <div class="mb-3">
                    <label for="payment_method" class="form-label">Forma de Pagamento</label>
                    <select name="payment_method" id="payment_method" class="form-select" required>
                        <option value="dinheiro">Dinheiro</option>
                        <option value="cartao">Cartão</option>
                        <option value="pix">Pix</option>
                    </select>
                </div>

                {{-- Desconto --}}
                <div class="mb-3">
                    <label for="discount" class="form-label">Desconto (R$)</label>
                    <input type="number" name="discount" id="discount" class="form-control" step="0.01" value="0" min="0">
                </div>

                <button type="submit" class="btn btn-primary">Finalizar Venda</button>
                <a href="{{ route('pdv.sales') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection