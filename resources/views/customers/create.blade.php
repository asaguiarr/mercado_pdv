<!-- resources/views/customers/create.blade.php -->

@extends('layouts.app')

@section('title', 'Cadastrar Cliente')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-primary text-white fw-semibold">
                    <i class="fas fa-user-plus me-2"></i> Cadastrar Cliente
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('customers.store') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf

                        <!-- Foto -->
                        <div class="mb-3">
                            <label for="photo" class="form-label fw-semibold">
                                <i class="fas fa-image me-1"></i> Foto
                            </label>
                            <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                        </div>

                        <div class="row g-3">
                            <!-- Nome -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Nome" required>
                                    <label for="name"><i class="fas fa-user me-1"></i> Nome</label>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                    <label for="email"><i class="fas fa-envelope me-1"></i> Email</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <!-- Contato -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="contact" name="contact" placeholder="Contato" required>
                                    <label for="contact"><i class="fas fa-phone me-1"></i> Contato</label>
                                </div>
                            </div>

                            <!-- RG -->
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="rg" name="rg" placeholder="RG" required>
                                    <label for="rg"><i class="fas fa-id-card me-1"></i> RG</label>
                                </div>
                            </div>

                            <!-- CPF -->
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="cpf" name="cpf" placeholder="CPF" required>
                                    <label for="cpf"><i class="fas fa-id-card-alt me-1"></i> CPF</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <!-- Data de Nascimento -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="birthdate" name="birthdate" placeholder="Data de Nascimento" required>
                                    <label for="birthdate"><i class="fas fa-calendar-alt me-1"></i> Data de Nascimento</label>
                                </div>
                            </div>

                            <!-- Endereço -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <textarea class="form-control" id="address" name="address" placeholder="Endereço" style="height: 100px" required></textarea>
                                    <label for="address"><i class="fas fa-map-marker-alt me-1"></i> Endereço</label>
                                </div>
                            </div>
                        </div>

                        <!-- Botão -->
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i> Salvar Cliente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
