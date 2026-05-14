<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Ponto - AQUICOB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <span class="navbar-brand">AQUICOB | Ponto Eletrônico</span>
            <div class="d-flex">
                <a href="{{ route('employees.index') }}" class="btn btn-outline-light me-2">Funcionários</a>
                <a href="#" class="btn btn-outline-light">Relatórios</a>
            </div>
        </div>
    </nav>

    <main class="container">
        @yield('content')
    </main>

    @if ($errors->any() || session('error'))
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080">
            <div id="globalErrorToast" class="toast align-items-center text-bg-danger border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        @if (session('error'))
                            <div class="fw-semibold mb-1">{{ session('error') }}</div>
                        @endif
                        @foreach ($errors->all() as $message)
                            <div>{{ $message }}</div>
                        @endforeach
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const el = document.getElementById('globalErrorToast');
                if (el && typeof bootstrap !== 'undefined') {
                    bootstrap.Toast.getOrCreateInstance(el, { autohide: true, delay: 10000 }).show();
                }
            });
        </script>
    @endif

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>