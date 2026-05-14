<?php

namespace Database\Seeders;

use App\Enums\EmployeeRole;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DevLoginEmployeesSeeder extends Seeder
{
    public function run(): void
    {
        $adminPassword = 'AdminAquicob!2026';
        $colaboradorPassword = 'ColaboradorAquicob!2026';

        $admin = Employee::query()->updateOrCreate(
            ['cpf' => '11144477735'],
            [
                'name' => 'Administrador Sistema',
                'position' => 'Administrador TI',
                'password' => $adminPassword,
            ]
        );
        $admin->forceFill(['role' => EmployeeRole::Admin])->save();

        $colaborador = Employee::query()->updateOrCreate(
            ['cpf' => '52998224725'],
            [
                'name' => 'Colaborador Exemplo',
                'position' => 'Atendente',
                'password' => $colaboradorPassword,
            ]
        );
        $colaborador->forceFill(['role' => EmployeeRole::Colaborador])->save();

        $markdown = <<<'MD'
# Credenciais de desenvolvimento — Sistema de Ponto AQUICOB

Este ficheiro é gerado pelo seeder `DevLoginEmployeesSeeder`. **Não use estas credenciais em produção.**

| Perfil | CPF (apenas dígitos no login) | Senha |
|--------|--------------------------------|-------|
| Administrador | 11144477735 | AdminAquicob!2026 |
| Colaborador | 52998224725 | ColaboradorAquicob!2026 |

No ecrã de login pode usar o CPF com máscara (`111.444.777-35`); o sistema normaliza para 11 dígitos.

- **Administrador:** painel «Bater o Ponto», «Relatório de Ponto» e gestão de funcionários.
- **Colaborador:** página inicial (registo de ponto em desenvolvimento).

**Segurança:** altere as senhas após o primeiro login. O perfil (`admin` / `colaborador`) **não** é aceite pela API em criação ou edição — novos registos via API são sempre colaboradores; apenas a área web de administrador pode definir o perfil. O ficheiro `CREDENCIAIS-DEV.md` está no `.gitignore` para reduzir commits acidentais.
MD;

        File::put(base_path('CREDENCIAIS-DEV.md'), $markdown);
    }
}
