// resources/views/pdv/open-cash.blade.php

@extends('layouts.app')

@section('title', 'Abrir Caixa')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="mb-4">Abrir Caixa</h1>

            <form action="{{ route('pdv.open-cash.store') }}" method="POST">
                @csrf

                {{-- Saldo inicial --}}
                <div class="mb-3">
                    <label for="initial_balance" class="form-label">Saldo Inicial (R$)</label>
                    <input type="number" name="initial_balance" id="initial_balance" class="form-control" step="0.01" value="0" min="0">
                </div>

                <button type="submit" class="btn btn-primary">Abrir Caixa</button>
                <a href="{{ route('pdv.sales') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection