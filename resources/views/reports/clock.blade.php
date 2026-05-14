@extends('layouts.app')

@section('content')
<div class="card shadow-sm mb-3">
    <div class="card-header">
        <h1 class="h5 mb-0">Relatório de ponto</h1>
    </div>
    <div class="card-body">
        @if ($employees->isEmpty())
            <p class="text-muted mb-0">Não há funcionários cadastrados. <a href="{{ route('employees.index') }}">Adicione funcionários</a> para gerar relatórios.</p>
        @else
            <form method="GET" action="{{ route('reports.clock') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="employee_id" class="form-label">Funcionário</label>
                    <select name="employee_id" id="employee_id" class="form-select @error('employee_id') is-invalid @enderror">
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" @selected((string) $employeeIdValue === (string) $employee->id)>
                                {{ $employee->name }} — {{ $employee->cpf_formatted }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">De</label>
                    <input type="date" name="date_from" id="date_from" value="{{ $dateFromValue }}" class="form-control @error('date_from') is-invalid @enderror" required>
                    @error('date_from')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">Até</label>
                    <input type="date" name="date_to" id="date_to" value="{{ $dateToValue }}" class="form-control @error('date_to') is-invalid @enderror" required>
                    @error('date_to')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2">
                    <button type="submit" name="gerar" value="1" class="btn btn-primary w-100">Gerar</button>
                </div>
            </form>
            <p class="text-muted small mt-2 mb-0">Período máximo de 366 dias. As horas por dia somam apenas pares Entrada → Saída no mesmo dia civil.</p>
        @endif
    </div>
</div>

@if ($employees->isNotEmpty() && $selectedEmployee)
    <div class="card shadow-sm mb-3">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="fw-semibold">{{ $selectedEmployee->name }}</span>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('reports.clock.export.csv', ['employee_id' => $employeeIdValue, 'date_from' => $dateFromValue, 'date_to' => $dateToValue]) }}">Exportar CSV</a>
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('reports.clock.export.pdf', ['employee_id' => $employeeIdValue, 'date_from' => $dateFromValue, 'date_to' => $dateToValue]) }}" target="_blank" rel="noopener">Exportar PDF</a>
            </div>
        </div>
        <div class="card-body p-0">
            @if (count($reportRows) === 0)
                <p class="text-muted p-3 mb-0">Não existem marcações neste período.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Dia</th>
                                <th>Batidas (hh:mm)</th>
                                <th>Horas no dia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reportRows as $row)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($row['date'])->format('d/m/Y') }}</td>
                                    <td class="font-monospace small">{{ $row['punches_line'] }}</td>
                                    <td>{{ $row['hours_label'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            <div class="border-top px-3 py-2 bg-light">
                <strong>Total no período:</strong> {{ $totalPeriodLabel }}
            </div>
        </div>
    </div>
@endif
@endsection
