@extends('layouts.app')

@section('title', 'Relatório de Caixa')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-12">

            <div class="card shadow border-0 rounded-3">
                <div class="card-header bg-success text-white fw-bold d-flex justify-content-between">
                    <span><i class="bi bi-clipboard-data me-2"></i> Relatório de Caixa</span>
                    <span>Aberto em: {{ $cashStatus->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Data</th>
                                    <th>Cliente</th>
                                    <th>Itens</th>
                                    <th>Pagamento</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                    <tr>
                                        <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $sale->customer?->name ?? 'Não informado' }}</td>
                                        <td>
                                            <ul class="list-unstyled mb-0">
                                                @foreach($sale->items as $item)
                                                    <li>{{ $item->product->name }} (x{{ $item->quantity }})</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ ucfirst($sale->payment_method) }}</span>
                                        </td>
                                        <td class="fw-bold text-success">
                                            R$ {{ number_format($sale->total, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Nenhuma venda registrada neste caixa.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-light fw-bold">
                                    <td colspan="4" class="text-end">TOTAL:</td>
                                    <td class="text-success">R$ {{ number_format($total, 2, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
