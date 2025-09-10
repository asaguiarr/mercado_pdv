

@extends('layouts.app')

@section('title', 'Vendas - PDV')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">📊 Vendas Realizadas</h5>
            <a href="{{ route('pdv.sales.create') }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle"></i> Nova Venda
            </a>
        </div>

        <div class="card-body">
            <form action="{{ route('pdv.sales') }}" method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por cliente ou ID" value="{{ request()->input('search') }}">
                    <button type="submit" class="btn btn-outline-secondary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

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
            @else
                <p class="text-muted">Nenhuma venda encontrada.</p>
            @endif
        </div>
    </div>
</div>
@endsection