@extends('layouts.app')

@section('title', 'Detalhes da Venda')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">🧾 Detalhes da Venda #{{ $sale->id }}</h5>
            <a href="{{ route('pdv.sales') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
        <div class="card-body">

            {{-- Informações principais --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <p><strong>Cliente:</strong> {{ $sale->customer->name ?? '---' }}</p>
                    <p><strong>Usuário:</strong> {{ $sale->user->name ?? '---' }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Forma de Pagamento:</strong> {{ ucfirst($sale->payment_method) }}</p>
                    <p><strong>Data:</strong> {{ $sale->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="col-md-4">
                    <h5 class="text-success">Total: R$ {{ number_format($sale->total, 2, ',', '.') }}</h5>
                </div>
            </div>

            {{-- Itens da venda --}}
            <h6 class="mb-3">Itens da Venda</h6>
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Preço Unitário</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $item)
                            <tr>
                                <td>{{ $item->product->name ?? 'Produto removido' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                                <td><strong>R$ {{ number_format($item->subtotal, 2, ',', '.') }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection
