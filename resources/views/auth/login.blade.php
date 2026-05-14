@extends('layouts.guest')

@section('content')
<div class="card shadow-sm">
    <div class="card-body p-4">
        <h1 class="h4 text-center mb-3">AQUICOB</h1>
        <p class="text-muted text-center small mb-4">Sistema de ponto eletrónico — inicie sessão</p>
        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <div class="mb-3">
                <label for="cpf" class="form-label">CPF</label>
                <input type="text" name="cpf" id="cpf" value="{{ old('cpf') }}" class="form-control @error('cpf') is-invalid @enderror" required autofocus inputmode="numeric" autocomplete="username" placeholder="000.000.000-00" maxlength="14">
                @error('cpf')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" id="remember" value="1" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">Manter sessão</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js" referrerpolicy="no-referrer"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.jQuery && document.getElementById('cpf')) {
            window.jQuery('#cpf').mask('000.000.000-00', { clearIfNotMatch: true });
        }
    });
</script>
@endpush
