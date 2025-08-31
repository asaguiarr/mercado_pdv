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
