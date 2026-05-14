<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Sistema de Ponto AQUICOB — API REST',
    description: <<<'MARKDOWN'
REST API do **Sistema de Ponto Eletrónico** (AQUICOB).

### Autenticação
Os endpoints registados em `routes/api.php` estão **públicos** (sem token nem sessão) nesta versão de demonstração. **Em produção** deve proteger-se a API (por exemplo [Laravel Sanctum](https://laravel.com/docs/sanctum) ou um API gateway).

### Convenções
- Pedidos com corpo: `Content-Type: application/json`.
- Respostas de recurso ou lista seguem o envelope Laravel: `{"data": ...}`.
- Erros de validação: HTTP `422` com `message` e objeto `errors` por campo.

### Funcionários (`/api/employees`)
- Na **API**, ao criar um funcionário o papel é sempre **colaborador**; o campo `role` no JSON é **proibido** (`422` se enviado).
- Na **atualização** pela API, `role` também é proibido.
- O CPF é armazenado com 11 dígitos; na API pode enviar-se com ou sem máscara; nas respostas devolve-se mascarado.
MARKDOWN,
)]
#[OA\Server(
    url: '/',
    description: 'URL base da aplicação. Os paths abaixo incluem o prefixo `/api` (Laravel `routes/api.php`).'
)]
#[OA\ExternalDocumentation(
    description: 'Interface Swagger UI (explorar e testar a API no browser)',
    url: '/api/documentation'
)]
abstract class Controller
{
    //
}
