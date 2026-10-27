@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h3 class="fw-bold mb-4 text-center text-primary">Criar Conta de Paciente</h3>
                
                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nome Completo</label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">CPF</label>
                            <input type="text" name="cpf" class="form-control" placeholder="000.000.000-00" required value="{{ old('cpf') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Telefone</label>
                            <input type="text" name="phone" class="form-control" placeholder="(00) 00000-0000" required value="{{ old('phone') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Senha</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2">Cadastrar</button>
                </form>

                <div class="text-center mt-3">
                    <small class="text-muted">Já tem uma conta? <a href="{{ route('login') }}">Entrar</a></small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection