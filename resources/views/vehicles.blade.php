@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h2>Vehículos</h2>
        <button id="btn-create" class="btn btn-primary">Nuevo Vehículo</button>
    </div>

    <table id="vehiclesTable" class="table table-striped">
        <thead>
            <tr>
                <th>Placa</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Año</th>
                <th>Cliente</th>
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
                <input type="hidden" name="id" id="vehId">

                <div class="modal-header">
                    <h5 id="modalTitle" class="modal-title">Nuevo Vehículo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <h5>Datos del Vehículo</h5>
                    <div class="mb-3"><label>Placa</label><input name="plate" class="form-control" required></div>
                    <div class="mb-3"><label>Marca</label><input name="brand" class="form-control" required></div>
                    <div class="mb-3"><label>Modelo</label><input name="model" class="form-control" required></div>
                    <div class="mb-3"><label>Año de fabricación</label><input name="year_of_manufacture" type="number" class="form-control" required></div>

                    <hr>

                    <h5>Datos del Cliente</h5>
                    <input type="hidden" name="customer_id" id="customer_id">

                    <div class="mb-3"><label>Nombre</label><input name="first_name" class="form-control" required></div>
                    <div class="mb-3"><label>Apellidos</label><input name="last_name" class="form-control" required></div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Tipo de Documento</label>
                            <select name="document_type" id="document_type" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                @foreach ($documents as $document)
                                <option value="{{ $document->id }}">
                                    {{ $document->short_name }}
                                </option>
                            @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Número de Documento</label>
                            <input name="document_number" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3" id="div_razon_social" style="display: none;">
                        <label>Razón Social</label>
                        <input name="business_name" id="business_name" class="form-control">
                    </div>
                    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
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

        const dataTable = new simpleDatatables.DataTable("#vehiclesTable", {
            searchable: true,
            fixedHeight: true,
            labels: {
                placeholder: "Buscar...",
                perPage: " registros por página",
                noRows: "No hay registros",
                info: "Mostrando {start} a {end} de {rows} registros"
            }
        });

        async function loadData() {
            const resp = await fetch("{{ route('vehicles.json') }}");
            const json = await resp.json();
            const allIndexes = dataTable.data.data.map((_, i) => i);
            dataTable.rows.remove(allIndexes);

            const rows = json.map(v => [
                v.plate,
                v.brand,
                v.model,
                v.year,
                v.customer.first_name + ' ' + v.customer.last_name,
                v.customer.document_number,
                v.customer.email,
                v.customer.phone,
                `<button class="btn btn-sm btn-warning edit" data-id="${v.id}">✏️</button>
                 <button class="btn btn-sm btn-danger delete" data-id="${v.id}">🗑️</button>`
            ]);
            dataTable.insert({ data: rows });
        }
        loadData();

        document.getElementById('btn-create').onclick = () => {
            form.reset();
            document.getElementById('vehId').value = '';
            document.getElementById('modalTitle').innerText = 'Nuevo Vehículo';
            modal.show();
        };

        document.querySelector('#vehiclesTable').addEventListener('click', async e => {
            if (!e.target.classList.contains('edit')) return;
            const id = e.target.dataset.id;
            const resp = await fetch(`/vehicle/data/${id}`);
            const data = await resp.json();
            for (let k in data) {
                const inp = document.querySelector(`[name=${k}]`);
                if (inp) inp.value = data[k];
            }
            if (data.customer) {
                document.getElementById('customer_id').value = data.customer.id;
                document.querySelector('[name="first_name"]').value = data.customer.first_name || '';
                document.querySelector('[name="last_name"]').value = data.customer.last_name || '';
                document.querySelector('[name="document_type"]').value = data.customer.document_id || '';
                document.querySelector('[name="document_number"]').value = data.customer.document_number || '';
                document.querySelector('[name="email"]').value = data.customer.email || '';
                document.querySelector('[name="phone"]').value = data.customer.phone || '';

                if (data.customer.business_name) {
                    document.getElementById('business_name').value = data.customer.business_name;
                    document.getElementById('div_razon_social').style.display = 'block';
                } else {
                    document.getElementById('business_name').value = '';
                    document.getElementById('div_razon_social').style.display = 'none';
                }
            }

            document.getElementById('vehId').value = id;
            document.getElementById('modalTitle').innerText = 'Editar Vehículo';
            modal.show();
        });

        document.getElementById('btn-save').addEventListener('click', async e => {
            e.preventDefault();
            const formData = new FormData(form);
            const id = formData.get('id');
            const url = id ? `/vehicle/update/${id}` : '/vehicle/create';
            const method = id ? 'POST' : 'POST';
            if (id) {
                formData.append('_method', 'PUT');
            }

            try {
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

                if (response.ok) {
                    modal.hide();
                    loadData();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: id ? 'Vehículo actualizado correctamente.' : 'Vehículo creado correctamente.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error(responseData.message || 'Ocurrió un error al guardar.');
                }
            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'No se pudo guardar el vehículo.'
                });
            }
        });

        document.querySelector('#vehiclesTable').addEventListener('click', e => {
            if (!e.target.classList.contains('delete')) return;
            const id = e.target.dataset.id;
            Swal.fire({
                title: '¿Eliminar vehículo?',
                text: "¡No podrás revertirlo!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(async res => {
                if (res.isConfirmed) {
                    try {
                        const response = await fetch(`/vehicle/destroy/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        if (response.ok) {
                            loadData();
                            Swal.fire({
                                icon: 'success',
                                title: '¡Eliminado!',
                                text: 'El vehículo ha sido eliminado.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            const result = await response.json();
                            throw new Error(result.message || 'No se pudo eliminar.');
                        }
                    } catch (error) {
                        console.error(error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'No se pudo eliminar el vehículo.'
                        });
                    }
                }
            });
        });

        form.addEventListener('submit', function(e){
            e.preventDefault();
        });

        document.getElementById('document_type').addEventListener('change', function() {
            const divRazon = document.getElementById('div_razon_social');
            if (this.value === '2') {
                divRazon.style.display = 'block';
                document.getElementById('business_name').required = true;
            } else {
                divRazon.style.display = 'none';
                document.getElementById('business_name').required = false;
                document.getElementById('business_name').value = '';
            }
        });

        document.querySelector('[name="document_number"]').addEventListener('blur', async function() {
            const tipo = document.querySelector('[name="document_type"]').value;
            const numero = this.value.trim();

            if (!tipo || !numero) return; // No buscar si falta datos

            try {
                const response = await fetch(`/customer/search?type=${tipo}&number=${numero}`);
                const result = await response.json();

                if (result.success) {
                    const cliente = result.data;
                    document.querySelector('[name="first_name"]').value = cliente.first_name;
                    document.querySelector('[name="last_name"]').value = cliente.last_name;
                    document.querySelector('[name="email"]').value = cliente.email;
                    document.querySelector('[name="phone"]').value = cliente.phone;
                    if (cliente.business_name) {
                        document.querySelector('[name="business_name"]').value = cliente.business_name;
                    }
                    document.getElementById('customer_id').value = cliente.id;
                    console.log('Cliente encontrado y cargado.');
                } else {
                    document.getElementById('customer_id').value = '';
                    console.log('Cliente no encontrado.');
                }
            } catch (error) {
                console.error('Error buscando cliente', error);
            }
        });
    });


</script>
@endsection
