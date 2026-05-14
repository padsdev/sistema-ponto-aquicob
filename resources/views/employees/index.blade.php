@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Gestão de Funcionários</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#employeeModal">
            <i class="fas fa-plus"></i> Novo Funcionário
        </button>
    </div>
    <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success mb-3">{{ session('status') }}</div>
        @endif
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="search" class="form-control" placeholder="Buscar por nome ou CPF...">
            </div>
        </div>

        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Cargo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="employeeTable">
                @foreach ($employees as $employee)
                <tr>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->cpf_formatted }}</td>
                    <td>{{ $employee->position }}</td>
                    <td>
                        <button
                            type="button"
                            class="btn btn-sm btn-warning btn-open-edit"
                            data-bs-toggle="modal"
                            data-bs-target="#editEmployeeModal"
                            data-employee-id="{{ $employee->id }}"
                            data-name="{{ e($employee->name) }}"
                            data-position="{{ e($employee->position) }}"
                            data-cpf-formatted="{{ e($employee->cpf_formatted) }}"
                            data-original-name="{{ e($employee->name) }}"
                        >Editar</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('employees.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="employeeModalLabel">Cadastrar Colaborador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome Completo</label>
                        <input type="text" name="name" value="{{ old('edit_employee_id') ? '' : old('name') }}" class="form-control @error('name') is-invalid @enderror" autocomplete="name">
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CPF</label>
                        <input type="text" name="cpf" id="cpfInput" value="{{ old('edit_employee_id') ? '' : (old('cpf') ? \App\Support\Cpf::formatMasked(old('cpf')) : '') }}" class="form-control @error('cpf') is-invalid @enderror" placeholder="000.000.000-00" inputmode="numeric" autocomplete="off" maxlength="14">
                        @error('cpf')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cargo/Função</label>
                        <input type="text" name="position" value="{{ old('edit_employee_id') ? '' : old('position') }}" class="form-control @error('position') is-invalid @enderror" autocomplete="organization-title">
                        @error('position')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editEmployeeModalLabel">Editar colaborador</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form id="editEmployeeForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="edit_employee_id" id="editEmployeeIdField" value="{{ old('edit_employee_id') }}">
                    <input type="hidden" name="edit_employee_original_name" id="editOriginalNameHidden" value="{{ old('edit_employee_original_name') }}">
                    <input type="hidden" name="edit_employee_cpf_formatted" id="editCpfFormattedHidden" value="{{ old('edit_employee_cpf_formatted') }}">
                    <div class="mb-3">
                        <label class="form-label">Nome completo</label>
                        <input type="text" name="name" id="editNameInput" value="{{ old('edit_employee_id') ? old('name') : '' }}" class="form-control @error('name') is-invalid @enderror" autocomplete="name">
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CPF</label>
                        <input type="text" class="form-control" id="editCpfDisplay" readonly tabindex="-1" aria-readonly="true">
                        <div class="form-text">O CPF não pode ser alterado por aqui.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cargo / função</label>
                        <input type="text" name="position" id="editPositionInput" value="{{ old('edit_employee_id') ? old('position') : '' }}" class="form-control @error('position') is-invalid @enderror" autocomplete="organization-title">
                        @error('position')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="modal-footer px-0 pb-0 border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Salvar alterações</button>
                    </div>
                </form>

                <hr class="my-4">

                <div class="border border-danger rounded p-3 bg-danger-subtle">
                    <h6 class="text-danger mb-2">Zona de perigo — excluir funcionário</h6>
                    <p class="small mb-2">Para habilitar o botão <strong>Excluir permanentemente</strong>, digite <strong>exatamente</strong> o nome completo atual deste colaborador (como cadastrado hoje):</p>
                    <p class="small mb-2"><span class="text-muted">Nome exigido:</span> <code id="editExpectedNameDisplay" class="user-select-all"></code></p>
                    <form id="deleteEmployeeForm" method="POST" action="" class="mt-3">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="edit_employee_id" id="deleteEditEmployeeIdField" value="{{ old('edit_employee_id') }}">
                        <input type="hidden" name="edit_employee_original_name" id="deleteOriginalNameHidden" value="{{ old('edit_employee_original_name') }}">
                        <input type="hidden" name="edit_employee_cpf_formatted" id="deleteCpfFormattedHidden" value="{{ old('edit_employee_cpf_formatted') }}">
                        <div class="mb-3">
                            <label class="form-label" for="deleteConfirmInput">Confirme o nome completo</label>
                            <input type="text" name="delete_confirmation" id="deleteConfirmInput" value="{{ old('delete_confirmation') }}" class="form-control @error('delete_confirmation') is-invalid @enderror" autocomplete="off" placeholder="Digite o nome completo">
                            @error('delete_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-danger" id="deleteEmployeeBtn" disabled>Excluir permanentemente</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js" referrerpolicy="no-referrer"></script>
<script>
    const employeesBaseUrl = @json(url('/employees'));

    function trimString(s) {
        return String(s).trim();
    }

    function syncDeleteButtonState() {
        const expected = window.__editEmployeeExpectedName || '';
        const typed = trimString(document.getElementById('deleteConfirmInput').value);
        const btn = document.getElementById('deleteEmployeeBtn');
        btn.disabled = typed !== trimString(expected);
    }

    $(document).ready(function () {
        $('#cpfInput').mask('000.000.000-00', { clearIfNotMatch: true });

        $("#search").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#employeeTable tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        document.getElementById('deleteConfirmInput').addEventListener('input', syncDeleteButtonState);

        document.querySelectorAll('.btn-open-edit').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-employee-id');
                const name = this.getAttribute('data-name') || '';
                const position = this.getAttribute('data-position') || '';
                const cpfFormatted = this.getAttribute('data-cpf-formatted') || '';
                const originalName = this.getAttribute('data-original-name') || '';

                document.getElementById('editEmployeeForm').action = employeesBaseUrl + '/' + id;
                document.getElementById('deleteEmployeeForm').action = employeesBaseUrl + '/' + id;
                document.getElementById('editEmployeeIdField').value = id;
                document.getElementById('editNameInput').value = name;
                document.getElementById('editPositionInput').value = position;
                document.getElementById('editCpfDisplay').value = cpfFormatted;
                document.getElementById('editExpectedNameDisplay').textContent = originalName;
                window.__editEmployeeExpectedName = originalName;
                document.getElementById('editOriginalNameHidden').value = originalName;
                document.getElementById('editCpfFormattedHidden').value = cpfFormatted;
                document.getElementById('deleteEditEmployeeIdField').value = id;
                document.getElementById('deleteOriginalNameHidden').value = originalName;
                document.getElementById('deleteCpfFormattedHidden').value = cpfFormatted;
                document.getElementById('deleteConfirmInput').value = '';
                syncDeleteButtonState();
            });
        });

        const editModalEl = document.getElementById('editEmployeeModal');
        if (editModalEl) {
            editModalEl.addEventListener('hidden.bs.modal', function () {
                document.getElementById('deleteConfirmInput').value = '';
                syncDeleteButtonState();
            });
        }
    });

    @if ($errors->any() && old('edit_employee_id'))
        document.addEventListener('DOMContentLoaded', function () {
            const id = @json(old('edit_employee_id'));
            document.getElementById('editEmployeeForm').action = employeesBaseUrl + '/' + id;
            document.getElementById('deleteEmployeeForm').action = employeesBaseUrl + '/' + id;
            document.getElementById('editCpfDisplay').value = @json(old('edit_employee_cpf_formatted', ''));
            const expected = @json(old('edit_employee_original_name', ''));
            document.getElementById('editExpectedNameDisplay').textContent = expected;
            window.__editEmployeeExpectedName = expected;
            syncDeleteButtonState();
            var editModal = new bootstrap.Modal(document.getElementById('editEmployeeModal'));
            editModal.show();
        });
    @elseif ($errors->hasAny(['name', 'cpf', 'position']) && ! old('edit_employee_id'))
        document.addEventListener('DOMContentLoaded', function () {
            var m = document.getElementById('employeeModal');
            if (m && typeof bootstrap !== 'undefined') {
                new bootstrap.Modal(m).show();
            }
        });
    @endif
</script>
@endsection
