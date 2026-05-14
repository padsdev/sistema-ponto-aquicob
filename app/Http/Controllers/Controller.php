<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    description: 'Documentação do Teste Técnico de Sistema de Ponto Eletrônico',
    title: 'Sistema de Ponto - AQUICOB',
)]
#[OA\Contact(email: 'admin@exemplo.com')]
#[OA\Server(url: '/', description: 'Servidor atual (mesmo host da UI)')]
abstract class Controller
{
    //
}
