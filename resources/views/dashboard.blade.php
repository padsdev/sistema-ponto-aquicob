@extends('layouts.app')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h1 class="h5 mb-0">Bater o ponto</h1>
    </div>
    <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success mb-3">{{ session('status') }}</div>
        @endif

        @if ($employees->isEmpty())
            <p class="text-muted mb-0">Não há funcionários cadastrados.@if ($selectEmployee) <a href="{{ route('employees.index') }}">Adicione funcionários</a> para poder registar o ponto.@endif</p>
        @else
            @php
                $nextLabels = $employees->mapWithKeys(fn ($e) => [$e->id => $e->nextClockingType()->label()])->all();
            @endphp
            <form method="POST" action="{{ route('clockings.store') }}" class="row g-3">
                @csrf
                @if ($selectEmployee)
                    <div class="col-md-6">
                        <label for="employee_id" class="form-label">Funcionário</label>
                        <select name="employee_id" id="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" @selected(old('employee_id', $employees->first()->id) == $employee->id)>
                                    {{ $employee->name }} — {{ $employee->cpf_formatted }} ({{ $employee->position }})
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                @else
                    @php $self = $employees->first(); @endphp
                    <div class="col-12">
                        <p class="form-label mb-1">Funcionário</p>
                        <p class="mb-0"><strong>{{ $self->name }}</strong> — {{ $self->cpf_formatted }}</p>
                        <p class="text-muted small mb-0">{{ $self->position }}</p>
                    </div>
                @endif
                <div class="col-12">
                    <label for="notes" class="form-label">Observação <span class="text-muted">(opcional)</span></label>
                    <textarea name="notes" id="notes" rows="2" class="form-control @error('notes') is-invalid @enderror" maxlength="2000" placeholder="Ex.: visita ao cliente, atraso justificado…">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary" id="clockSubmitBtn">
                        Registrar <span id="nextKindLabel">{{ $nextLabels[old('employee_id', $employees->first()->id)] ?? $employees->first()->nextClockingType()->label() }}</span>
                    </button>
                </div>
            </form>
            @if ($selectEmployee)
                <script type="application/json" id="next-labels-json">{!! json_encode($nextLabels, JSON_THROW_ON_ERROR) !!}</script>
            @endif
        @endif
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <h2 class="h6 mb-0">Últimas marcações</h2>
    </div>
    <div class="card-body p-0">
        @if ($recentClockings->isEmpty())
            <p class="text-muted p-3 mb-0">Ainda não existem registos de ponto.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Data</th>
                            <th>Hora</th>
                            @if ($selectEmployee)
                                <th>Funcionário</th>
                            @endif
                            <th>Tipo</th>
                            <th>Observação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentClockings as $clocking)
                            <tr>
                                <td>{{ $clocking->punched_at->timezone(config('app.timezone'))->format('d/m/Y') }}</td>
                                <td>{{ $clocking->punched_at->timezone(config('app.timezone'))->format('H:i') }}</td>
                                @if ($selectEmployee)
                                    <td>{{ $clocking->employee?->name }}</td>
                                @endif
                                <td>
                                    @if ($clocking->type === \App\Enums\ClockingType::Entrada)
                                        <span class="badge text-bg-success">Entrada</span>
                                    @else
                                        <span class="badge text-bg-secondary">Saída</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $clocking->notes ? \Illuminate\Support\Str::limit($clocking->notes, 80) : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
@if (! $employees->isEmpty() && $selectEmployee)
<script>
    (function () {
        const select = document.getElementById('employee_id');
        const label = document.getElementById('nextKindLabel');
        const raw = document.getElementById('next-labels-json');
        if (!select || !label || !raw) return;
        const map = JSON.parse(raw.textContent || '{}');
        function sync() {
            const id = String(select.value);
            label.textContent = map[id] || 'Entrada';
        }
        select.addEventListener('change', sync);
    })();
</script>
@endif
@endsection
