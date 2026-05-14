# Sistema de Ponto AQUICOB

Aplicação web em Laravel para **controle de ponto** (entrada/saída), **relatórios para administrador** e uma **API REST pública** de funcionários (demonstração: **sem autenticação** na API). Parte dos textos da interface está em **português europeu**; este README está em **português (Brasil)**.

## Requisitos

| Ferramenta | Observações |
|-------------|-------------|
| **PHP** | 8.3+ (veja `composer.json`; extensões: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`) |
| **Composer** | 2.x |
| **Node.js** | 20+ e **npm** (build com Vite / Tailwind) |
| **Banco de dados** | **MySQL** 8+ ou MariaDB (padrão no `.env.example`). SQLite é possível para desenvolvimento/testes se a extensão **`pdo_sqlite`** estiver habilitada. |

## Instalar dependências

```bash
composer install
npm ci
```

## Configurar o ambiente

```bash
cp .env.example .env
php artisan key:generate
```

Edite o `.env` e defina pelo menos:

- `APP_URL` — URL base da aplicação (usada pelo framework e pelo “Experimentar” do Swagger).
- `DB_*` — conexão com o banco (`DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

Variáveis opcionais relacionadas ao Swagger (comentários no final do `.env.example`):

- `L5_SWAGGER_GENERATE_ALWAYS` — defina como `true` em desenvolvimento local se quiser que o arquivo OpenAPI seja regerado a cada acesso à interface de documentação.
- `L5_SWAGGER_CONST_HOST` — sobrescreve o host usado na geração da documentação (o padrão vem de `APP_URL`).

## Migrações do banco

```bash
php artisan migrate
```

Se você já rodou migrações antigas e falta alguma coluna (por exemplo `clockings.notes`), execute `php artisan migrate` de novo para aplicar migrações novas.

## Popular dados de desenvolvimento

O seeder padrão cria duas contas de **funcionário** (`Employee`: admin + colaborador), com senhas hasheadas, e grava o arquivo **`CREDENCIAIS-DEV.md`** na raiz do projeto (esse arquivo está no **`.gitignore`**).

```bash
php artisan db:seed
```

Após o seed, abra `CREDENCIAIS-DEV.md` para ver **CPF e senha** (o login é **CPF + senha**, sem e-mail).

## Compilar o front-end

```bash
npm run build
```

Para desenvolvimento local com recarregamento automático:

```bash
npm run dev
```

Se o navegador mostrar erro de **manifesto do Vite**, rode `npm run build` ou mantenha `npm run dev` em execução.

## Executar a aplicação

```bash
php artisan serve
```

Acesse a `APP_URL` (por exemplo `http://127.0.0.1:8000`). Visitantes veem a tela de login; após o login, **administradores** vão ao painel (ponto da equipe); **colaboradores** vão para `/inicio` (ponto individual).

**Ambiente de desenvolvimento completo** (script Composer do `composer.json`):

```bash
composer run dev
```

Isso sobe o servidor HTTP, worker de filas, logs e o Vite ao mesmo tempo.

## Documentação OpenAPI / Swagger

A documentação interativa é servida pelo **L5-Swagger** (pacote `darkaonline/l5-swagger`).

- **Interface:** `{APP_URL}/api/documentation`  
  Exemplo: `http://127.0.0.1:8000/api/documentation`

Regenere o JSON da especificação depois de alterar atributos OpenAPI no PHP:

```bash
php artisan l5-swagger:generate
```

Os arquivos gerados ficam em `storage/api-docs/` (por exemplo `api-docs.json`). As anotações são varridas a partir de `app/` (veja `config/l5-swagger.php`).

**API documentada no momento:** `GET/POST /api/employees`, `GET/PUT/DELETE /api/employees/{employee}` — veja `App\Http\Controllers\EmployeeController` e os esquemas `#[OA\…]` nos *form requests* e em `EmployeeResource`.

## Testes automatizados

```bash
php artisan test
```

Saída compacta:

```bash
php artisan test --compact
```

Os testes usam o driver de banco configurado para o ambiente `testing` (muitas vezes SQLite em memória no `phpunit.xml`). Instale a extensão **PDO SQLite** se for usar essa configuração.

## Estrutura do projeto (resumo)

| Caminho | Função |
|---------|--------|
| `routes/web.php` | Interface web: login, ponto, relatório (admin), funcionários (admin). |
| `routes/api.php` | API REST (recurso de funcionários). |
| `app/Http/Controllers/` | *Controllers*; o `Controller` base concentra `Info` / `Server` / `ExternalDocumentation` do OpenAPI. |
| `database/migrations/` | Esquema do banco. |
| `database/seeders/DevLoginEmployeesSeeder.php` | Usuários de desenvolvimento + `CREDENCIAIS-DEV.md`. |

## Segurança (API)

As rotas `/api/*` **não** estão protegidas por Sanctum nem por sessão nesta demonstração. Trate-as como **somente desenvolvimento ou rede interna** até que você adicione autenticação e autorização antes de produção.

## Licença

O framework Laravel e este modelo de aplicação seguem a [licença MIT](https://opensource.org/licenses/MIT), quando aplicável.
