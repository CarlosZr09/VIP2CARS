@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h2>Clientes</h2>
        <button id="btn-create" class="btn btn-primary">Nuevo Cliente</button>
    </div>

    <table id="customersTable" class="table table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Documento</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <div class="modal fade" id="modalForm" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="formData" class="modal-content">
                <input type="hidden" name="id" id="custId">

                <div class="modal-header">
                    <h5 id="modalTitle" class="modal-title">Nuevo Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3"><label>Nombre</label><input name="first_name" class="form-control" required></div>
                    <div class="mb-3"><label>Apellidos</label><input name="last_name" class="form-control" required></div>

                    <div class="mb-3">
                        <label>Tipo de Documento</label>
                        <select name="document_id" id="documentSelect" class="form-select" required>
                            <option value="">– Selecciona –</option>
                            @foreach ($documents as $document)
                                <option value="{{ $document->id }}">
                                    {{ $document->short_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id="businessWrapper" style="display: none;">
                        <label>Razón Social</label>
                        <input type="text" name="business_name" id="businessInput" class="form-control">
                    </div>

                    <div class="mb-3"><label>Número de Documento</label><input name="document_number" class="form-control"
                            required></div>
                    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control"
                            required></div>
                    <div class="mb-3"><label>Teléfono</label><input name="phone" class="form-control" required></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="btn-save" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>

        document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('formData');
                const modal = new bootstrap.Modal(document.getElementById('modalForm'));
                const docSelect = document.getElementById('documentSelect');
                const businessWrapper = document.getElementById('businessWrapper');
                const dataTable = new simpleDatatables.DataTable("#customersTable", {
                        searchable: true,
                        fixedHeight: true,
                        labels: {
                        placeholder: "Buscar...", // :contentReference[oaicite:2]{index=2}
                        perPage: " registros por página",
                        noRows: "No hay registros",
                        info: "Mostrando {start} a {end} de {rows} registros"
                        }
                });

                async function loadData() {
                        const resp = await fetch("{{ route('customers.json') }}");
                        const json = await resp.json();
                        const allIndexes = dataTable.data.data.map((_, i) => i);
                        dataTable.rows.remove(allIndexes);

                        const rows = json.map(c => [
                        c.first_name,
                        c.last_name,
                        c.document_number,
                        c.email,
                        c.phone,
                        `<button class="btn btn-sm btn-warning edit" data-id="${c.id}">✏️</button>
                                <button class="btn btn-sm btn-danger delete" data-id="${c.id}">🗑️</button>`
                        ]);
                        dataTable.insert({
                        data: rows
                        });
                }
                loadData();

                function toggleBusiness() {
                        businessWrapper.style.display = (docSelect.value === '2') ? 'block' : 'none';
                }
                docSelect.addEventListener('change', toggleBusiness);

                document.getElementById('btn-create').onclick = () => {
                        document.getElementById('formData').reset();
                        document.getElementById('custId').value = '';
                        document.getElementById('modalTitle').innerText = 'Nuevo Cliente';
                        toggleBusiness();
                        modal.show();
                };

                document.querySelector('#customersTable').addEventListener('click', async e => {
                        if (!e.target.classList.contains('edit')) return;
                        const id = e.target.dataset.id;
                        const resp = await fetch(`/customer/data/${id}`);
                        const data = await resp.json();
                        for (let k in data) {
                        const inp = document.querySelector(`[name=${k}]`);
                        if (inp) inp.value = data[k];
                        }
                        docSelect.value = data.document_id;
                        toggleBusiness();
                        document.getElementById('custId').value = id;
                        document.getElementById('modalTitle').innerText = 'Editar Cliente';
                        modal.show();
                });

                document.getElementById('btn-save').addEventListener('click', async e => {
                        e.preventDefault();
                        const form = document.getElementById('formData');
                        const id = form.querySelector('[name=id]').value;
                        const url = id ? `/customer/update/${id}` : '/customer/create';
                        const method = id ? 'POST' : 'POST';
                        const formData = new FormData(form);
                        if (id) {
                                formData.append('_method', 'PUT');
                        }

                        const response = await fetch(url, {
                                method: method,
                                headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                },
                                body: formData,
                                credentials: 'same-origin'
                        });

                        const responseData = await response.json();
                        console.log(responseData);

                        modal.hide();
                        loadData();
                });

                document.querySelector('#customersTable').addEventListener('click', e => {
                        if (!e.target.classList.contains('delete')) return;
                        const id = e.target.dataset.id;
                        Swal.fire({
                        title: '¿Eliminar cliente?',
                        text: "¡No podrás revertirlo!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                        }).then(async res => {
                        if (res.isConfirmed) {
                                await fetch(`/customer/destroy/${id}`, {
                                method: 'DELETE',
                                headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                                });
                                loadData();
                                Swal.fire('Eliminado', 'El cliente ha sido eliminado.', 'success');
                        }
                        });
                });

                form.addEventListener('submit', function(e){
                        e.preventDefault();
                });
        });
    </script>
@endsection
