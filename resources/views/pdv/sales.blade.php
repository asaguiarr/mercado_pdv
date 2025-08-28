@extends('layouts.app')

@section('title', 'Vendas - PDV')

@section('content')
<div class="container mt-4">

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">📊 Vendas Realizadas</h5>
            <!-- Botão abrir modal -->
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#newSaleModal">
                <i class="bi bi-plus-circle"></i> Nova Venda
            </button>
        </div>

        <div class="card-body">
            @if($sales->count())
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Pagamento</th>
                            <th>Total</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                        <tr>
                            <td>{{ $sale->id }}</td>
                            <td>{{ $sale->customer->name ?? 'Consumidor' }}</td>
                            <td>{{ ucfirst($sale->payment_method) }}</td>
                            <td>R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                            <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('pdv.show', $sale->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Detalhes
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $sales->links() }}
            @else
                <p class="text-center">Nenhuma venda registrada ainda.</p>
            @endif
        </div>
    </div>
</div>

{{-- MODAL NOVA VENDA --}}
<div class="modal fade" id="newSaleModal" tabindex="-1" aria-labelledby="newSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('pdv.sale') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newSaleModalLabel">Nova Venda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    {{-- Seleção de produtos --}}
                    <div class="mb-3">
                        <label for="productSelect" class="form-label">Produto</label>
                        <select id="productSelect" class="form-select">
                            <option value="">Selecione um produto</option>
                            @foreach(\App\Models\Product::all() as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->sale_price }}">
                                    {{ $product->name }} - R$ {{ number_format($product->sale_price,2,',','.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Lista de itens da venda --}}
                    <table class="table table-bordered" id="saleItemsTable">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Preço</th>
                                <th>Quantidade</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    {{-- Total geral --}}
                    <div class="d-flex justify-content-end">
                        <h5>Total: R$ <span id="saleTotal">0,00</span></h5>
                    </div>

                    {{-- Forma de pagamento --}}
                    <div class="mt-3">
                        <label for="paymentMethod" class="form-label">Forma de Pagamento</label>
                        <select name="payment_method" id="paymentMethod" class="form-select" required>
                            <option value="">Selecione</option>
                            <option value="dinheiro">Dinheiro</option>
                            <option value="cartao">Cartão</option>
                            <option value="pix">PIX</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Finalizar Venda</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('productSelect');
    const saleItemsTable = document.querySelector('#saleItemsTable tbody');
    const saleTotalEl = document.getElementById('saleTotal');

    let saleTotal = 0;

    function updateTotal() {
        let total = 0;
        saleItemsTable.querySelectorAll('tr').forEach(tr => {
            total += parseFloat(tr.querySelector('.item-total').textContent);
        });
        saleTotal = total;
        saleTotalEl.textContent = total.toFixed(2).replace('.', ',');
    }

    productSelect.addEventListener('change', function() {
        const productId = this.value;
        if(!productId) return;

        const option = this.selectedOptions[0];
        const name = option.text.split(' - ')[0];
        const price = parseFloat(option.dataset.price);
        const quantity = 1;
        const total = price * quantity;

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${name}<input type="hidden" name="products[${productId}][id]" value="${productId}"></td>
            <td>${price.toFixed(2).replace('.', ',')}</td>
            <td><input type="number" name="products[${productId}][quantity]" value="${quantity}" min="1" class="form-control form-control-sm item-qty"></td>
            <td class="item-total">${total.toFixed(2)}</td>
            <td><button type="button" class="btn btn-sm btn-danger btn-remove">X</button></td>
        `;
        saleItemsTable.appendChild(tr);
        updateTotal();

        tr.querySelector('.item-qty').addEventListener('input', function() {
            const qty = parseInt(this.value) || 1;
            tr.querySelector('.item-total').textContent = (price * qty).toFixed(2);
            updateTotal();
        });

        tr.querySelector('.btn-remove').addEventListener('click', function() {
            tr.remove();
            updateTotal();
        });

        this.value = '';
    });
});
</script>
@endpush
