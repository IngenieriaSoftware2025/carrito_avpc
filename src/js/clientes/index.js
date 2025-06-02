import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormClientes = document.getElementById('FormClientes');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');

const GuardarCliente = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormClientes, ['cliente_id'])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe completar todos los campos obligatorios",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    const body = new FormData(FormClientes);
    const url = '/app01_avpc/clientes/guardarAPI';
    const config = {
        method: 'POST',
        body
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarClientes();
        } else {
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }
    } catch (error) {
        console.error('Error en GuardarCliente:', error);
        Swal.fire({
            position: "center",
            icon: "error",
            title: "Error",
            text: "Ocurrió un error inesperado",
            showConfirmButton: true,
        });
    }
    BtnGuardar.disabled = false;
}

const BuscarClientes = async () => {
    const url = '/app01_avpc/clientes/buscarAPI';
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        if (codigo == 1) {
            datatable.clear().draw();
            datatable.rows.add(data).draw();
        } else {
            await Swal.fire({
                position: "center",
                icon: "info",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }
    } catch (error) {
        console.log(error);
    }
}

const datatable = new DataTable('#TableClientes', {
    language: lenguaje,
    data: [],
    columns: [
        { title: 'No.', data: 'cliente_id', render: (data, type, row, meta) => meta.row + 1 },
        { title: 'Nombre', data: 'cliente_nombre' },
        { title: 'Apellido', data: 'cliente_apellido' },
        { title: 'NIT', data: 'cliente_nit' },
        { title: 'Email', data: 'cliente_email' },
        { title: 'Teléfono', data: 'cliente_telefono' },
        {
            title: 'Acciones',
            data: 'cliente_id',
            searchable: false,
            orderable: false,
            render: (data, type, row) => {
                return `
                    <div class='d-flex justify-content-center gap-1'>
                        <button class='btn btn-warning btn-sm modificar' 
                            data-id="${data}" 
                            data-nombre="${row.cliente_nombre}"  
                            data-apellido="${row.cliente_apellido}"  
                            data-nit="${row.cliente_nit}"  
                            data-email="${row.cliente_email}"
                            data-telefono="${row.cliente_telefono}"
                            data-direccion="${row.cliente_direccion}">
                            <i class='bi bi-pencil-square'></i> Modificar
                        </button>
                        <button class='btn btn-danger btn-sm eliminar' 
                            data-id="${data}">
                            <i class="bi bi-trash3"></i> Eliminar
                        </button>
                    </div>`;
            }
        }
    ]
});

const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset;

    document.getElementById('cliente_id').value = datos.id;
    document.getElementById('cliente_nombre').value = datos.nombre;
    document.getElementById('cliente_apellido').value = datos.apellido;
    document.getElementById('cliente_nit').value = datos.nit;
    document.getElementById('cliente_email').value = datos.email;
    document.getElementById('cliente_telefono').value = datos.telefono;
    document.getElementById('cliente_direccion').value = datos.direccion;

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

const limpiarTodo = () => {
    FormClientes.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
}

const ModificarCliente = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormClientes, [''])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe completar todos los campos obligatorios",
            showConfirmButton: true,
        });
        BtnModificar.disabled = false;
        return;
    }

    const body = new FormData(FormClientes);
    const url = '/app01_avpc/clientes/modificarAPI';
    const config = {
        method: 'POST',
        body
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarClientes();
        } else {
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }
    } catch (error) {
        console.error('Error en ModificarCliente:', error);
    }
    BtnModificar.disabled = false;
}

const EliminarCliente = async (event) => {
    const idCliente = event.currentTarget.dataset.id;

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "warning",
        title: "¿Desea eliminar este cliente?",
        text: 'Esta acción no se puede deshacer',
        showConfirmButton: true,
        confirmButtonText: 'Sí, Eliminar',
        confirmButtonColor: '#d33',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        const url = `/app01_avpc/clientes/eliminar?id=${idCliente}`;
        const config = {
            method: 'GET'
        }

        try {
            const consulta = await fetch(url, config);
            const respuesta = await consulta.json();
            const { codigo, mensaje } = respuesta;

            if (codigo == 1) {
                await Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Éxito",
                    text: mensaje,
                    showConfirmButton: true,
                });

                BuscarClientes();
            } else {
                await Swal.fire({
                    position: "center",
                    icon: "error",
                    title: "Error",
                    text: mensaje,
                    showConfirmButton: true,
                });
            }
        } catch (error) {
            console.error('Error en EliminarCliente:', error);
        }
    }
}

// Inicializar
BuscarClientes();

// Event Listeners
FormClientes.addEventListener('submit', GuardarCliente);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarCliente);
datatable.on('click', '.eliminar', EliminarCliente);
datatable.on('click', '.modificar', llenarFormulario);