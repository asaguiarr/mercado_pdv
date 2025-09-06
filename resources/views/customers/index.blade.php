// resources/views/customers/index.blade.php

@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-12">

            <div class="card shadow border-0 rounded-3">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-users me-2"></i> Clientes
                    </h4>
                    <div class="d-flex gap-2 align-items-center">

                        {{-- Dropdown de quantidade por página --}}
                        <form method="GET" action="{{ route('customers.index') }}" id="perPageForm">
                            <select name="per_page" class="form-select form-select-sm rounded-pill shadow-sm"
                                    onchange="document.getElementById('perPageForm').submit();">
                                @foreach([10, 25, 50, 100] as $size)
                                    <option value="{{ $size }}" 
                                        {{ request()->input('per_page', 10) == $size ? 'selected' : '' }}>
                                        {{ $size }} por página
                                    </option>
                                @endforeach
                            </select>
                            {{-- Mantém o termo de busca se existir --}}
                            <input type="hidden" name="search" value="{{ request()->input('search') }}">
                        </form>

                        <a href="{{ route('customers.create') }}" class="btn btn-sm btn-primary rounded-pill shadow-sm">
                            <i class="fas fa-user-plus me-1"></i> Novo Cliente
                        </a>

                        <form action="{{ route('customers.index') }}" method="GET" class="d-flex">
                            <input type="text" 
                                   name="search" 
                                   class="form-control form-control-sm rounded-pill me-2 shadow-sm" 
                                   placeholder="Buscar cliente..." 
                                   value="{{ request()->input('search') }}">
                            <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill shadow-sm">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Foto</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Contato</th>
                                    <th>RG</th>
                                    <th>CPF</th>
                                    <th>Nascimento</th>
                                    <th>Endereço</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="customerTableBody">
                                @forelse($customers as $customer)
                                    <tr>
                                        <td>
                                            <div class="avatar avatar-sm">
                                                @if($customer->photo)
                                                    <img src="{{ asset('storage/' . $customer->photo) }}" 
                                                         alt="Foto do Cliente" 
                                                         class="rounded-circle border shadow-sm" 
                                                         width="42" height="42">
                                                @else
                                                    <img src="{{ asset('default-avatar.png') }}" 
                                                         alt="Foto Padrão" 
                                                         class="rounded-circle border shadow-sm" 
                                                         width="42" height="42">
                                                @endif
                                            </div>
                                        </td>
                                        <td class="fw-semibold text-dark">{{ $customer->name }}</td>
                                        <td class="text-muted">{{ $customer->email }}</td>
                                        <td>{{ $customer->contact }}</td>
                                        <td><span class="badge bg-secondary bg-opacity-75">{{ $customer->rg }}</span></td>
                                        <td><span class="badge bg-dark bg-opacity-75">{{ $customer->cpf }}</span></td>
                                        <td>{{ \Carbon\Carbon::parse($customer->birthdate)->format('d/m/Y') }}</td>
                                        <td>{{ $customer->address }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('customers.edit', $customer->id) }}" 
                                                   class="btn btn-sm btn-warning rounded-pill shadow-sm" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <form action="{{ route('customers.destroy', $customer->id) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Tem certeza que deseja excluir este cliente?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-danger rounded-pill shadow-sm" 
                                                            title="Excluir">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            <i class="fas fa-info-circle me-2"></i> Nenhum cliente encontrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($customers instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="card-footer bg-white d-flex justify-content-center">
                        {{ $customers->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
