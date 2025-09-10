@extends('layouts.app')

@section('title', 'Fechar Caixa')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">

            <div class="card shadow border-0 rounded-3">
                <div class="card-header bg-danger text-white fw-bold">
                    <i class="bi bi-cash me-2"></i> Fechar Caixa
                </div>
                <div class="card-body">
                    <p><strong>Aberto em:</strong> {{ $cashStatus->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Saldo Inicial:</strong> R$ {{ number_format($cashStatus->initial_balance, 2, ',', '.') }}</p>

                    <form action="{{ route('pdv.close-cash.store') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100 fw-bold rounded-pill">
                            <i class="bi bi-box-arrow-right me-2"></i> Fechar Caixa
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
