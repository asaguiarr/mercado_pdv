@extends('layouts.app')

@section('title', 'Painel de Controle')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2 fw-bold text-dark">📊 Painel de Controle</h1>
</div>

{{-- Cards Resumo --}}
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted small mb-1">Vendas Hoje</p>
                    <h3 class="fw-bold mb-0 text-success">
                        R$ {{ number_format($data['todaySales'] ?? 0, 2, ',', '.') }}
                    </h3>
                </div>
                <span class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                    <i class="fas fa-dollar-sign fs-3"></i>
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted small mb-1">Clientes Cadastrados</p>
                    <h3 class="fw-bold mb-0">{{ $data['customersCount'] }}</h3>
                </div>
                <span class="bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                    <i class="fas fa-users fs-3"></i>
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted small mb-1">Produtos em Estoque</p>
                    <h3 class="fw-bold mb-0">{{ $data['productsCount'] }}</h3>
                </div>
                <span class="bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                    <i class="fas fa-box-open fs-3"></i>
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted small mb-1">Pedidos Pendentes</p>
                    <h3 class="fw-bold mb-0">{{ $data['pendingOrders'] ?? 0 }}</h3>
                </div>
                <span class="bg-danger bg-opacity-10 text-danger rounded-circle p-3">
                    <i class="fas fa-truck fs-3"></i>
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Gráfico rápido (Exemplo com Chart.js) --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="fw-semibold mb-4">📈 Vendas da Semana</h5>
        <canvas id="salesChart" height="90"></canvas>
    </div>
</div>

{{-- Produtos em baixo estoque --}}
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="fw-semibold mb-4">⚠️ Produtos com Estoque Baixo</h5>
        
        @if($data['lowStockProducts']->isEmpty())
            <p class="text-muted text-center my-4">Nenhum produto com estoque baixo 🎉</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Produto</th>
                            <th>Estoque Atual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['lowStockProducts'] as $product)
                        <tr>
                            <td class="fw-medium">{{ $product->name }}</td>
                            <td>
                                <span class="badge bg-danger">
                                    {{ $product->stock }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Scripts para o gráfico --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($data['salesLabels'] ?? []),
            datasets: [{
                label: 'Vendas (R$)',
                data: @json($data['salesValues'] ?? []),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.2)',
                tension: 0.3,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '#0d6efd'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection
