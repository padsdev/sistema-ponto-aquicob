<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de ponto — {{ $employee->name }}</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 1.5rem; color: #222; }
        h1 { font-size: 1.25rem; margin-bottom: 0.25rem; }
        .meta { color: #555; font-size: 0.9rem; margin-bottom: 1rem; }
        table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        th, td { border: 1px solid #ccc; padding: 0.4rem 0.5rem; text-align: left; }
        th { background: #f5f5f5; }
        tfoot td { font-weight: bold; background: #fafafa; }
        .hint { margin-top: 1rem; font-size: 0.8rem; color: #666; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <h1>Relatório de ponto</h1>
    <p class="meta">{{ $employee->name }} — Período: {{ $periodLabel }}</p>

    <table>
        <thead>
            <tr>
                <th>Dia</th>
                <th>Batidas (hh:mm)</th>
                <th>Horas no dia</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reportRows as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row['date'])->format('d/m/Y') }}</td>
                    <td style="font-family: ui-monospace, monospace;">{{ $row['punches_line'] }}</td>
                    <td>{{ $row['hours_label'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Não existem marcações neste período.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Total no período</td>
                <td>{{ $totalPeriodLabel }}</td>
            </tr>
        </tfoot>
    </table>

    <p class="hint no-print">
        <button type="button" style="padding: 0.35rem 0.75rem; cursor: pointer; border: 1px solid #888; border-radius: 4px; background: #f0f0f0;" onclick="window.print()">Imprimir / guardar como PDF</button>
        <span style="margin-left: 0.5rem;">Ou use Ctrl+P e escolha «Guardar como PDF».</span>
    </p>
</body>
</html>
