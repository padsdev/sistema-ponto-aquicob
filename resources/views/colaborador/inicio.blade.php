@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <h1 class="h4 mb-3">Olá, {{ auth()->user()->name }}</h1>
        <p class="text-muted mb-0">Bem-vindo à sua área. O registo de ponto para colaboradores será disponibilizado em breve.</p>
    </div>
</div>
@endsection
