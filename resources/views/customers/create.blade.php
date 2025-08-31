<!-- resources/views/customers/create.blade.php -->

@extends('layouts.app')

@section('title', 'Cadastrar Cliente')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Cadastrar Cliente</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('customers.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="photo">Foto</label>
                                <input type="file" class="form-control" id="photo" name="photo">
                            </div>

                            <div class="form-group">
                                <label for="name">Nome</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="form-group">
                                <label for="contact">Contato</label>
                                <input type="text" class="form-control" id="contact" name="contact" required>
                            </div>

                            <div class="form-group">
                                <label for="rg">RG</label>
                                <input type="text" class="form-control" id="rg" name="rg" required>
                            </div>

                            <div class="form-group">
                                <label for="cpf">CPF</label>
                                <input type="text" class="form-control" id="cpf" name="cpf" required>
                            </div>

                            <div class="form-group">
                                <label for="birthdate">Data de Nascimento</label>
                                <input type="date" class="form-control" id="birthdate" name="birthdate" required>
                            </div>

                            <div class="form-group">
                                <label for="address">Endereço</label>
                                <textarea class="form-control" id="address" name="address" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Cadastrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection