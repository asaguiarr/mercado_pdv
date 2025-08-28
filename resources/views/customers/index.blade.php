<!-- resources/views/customers/index.blade.php -->

@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold">👥 Clientes</h4>
                    <a href="{{ route('customers.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus me-1"></i> Novo Cliente
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Foto</th>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Contato</th>
                                    <th scope="col">RG</th>
                                    <th scope="col">CPF</th>
                                    <th scope="col">Nascimento</th>
                                    <th scope="col">Endereço</th>
                                    <th scope="col" class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                    <tr>
                                        <td>
                                            @if($customer->photo)
                                                <img src="{{ asset('storage/' . $customer->photo) }}" 
                                                     alt="Foto do Cliente" 
                                                     class="rounded-circle shadow-sm" 
                                                     width="50" height="50">
                                            @else
                                                <img src="{{ asset('default-avatar.png') }}" 
                                                     alt="Foto Padrão" 
                                                     class="rounded-circle shadow-sm" 
                                                     width="50" height="50">
                                            @endif
                                        </td>
                                        <td class="fw-semibold">{{ $customer->name }}</td>
                                        <td>{{ $customer->email }}</td>
                                        <td>{{ $customer->contact }}</td>
                                        <td><span class="badge bg-secondary">{{ $customer->rg }}</span></td>
                                        <td><span class="badge bg-dark">{{ $customer->cpf }}</span></td>
                                        <td>{{ \Carbon\Carbon::parse($customer->birthdate)->format('d/m/Y') }}</td>
                                        <td>{{ $customer->address }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('customers.edit', $customer->id) }}" 
                                                   class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <form action="{{ route('customers.destroy', $customer->id) }}" 
                                                      method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este cliente?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            Nenhum cliente cadastrado ainda.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection
