import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormClientes = document.getElementById('FormClientes');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const InputClienteTelefono = document.getElementById('cliente_telefono');
const InputClienteNit = document.getElementById('cliente_nit');
const InputClienteCorreo = document.getElementById('cliente_correo');

const ValidarTelefono = () => {
    const telefono = InputClienteTelefono.value;

    if (telefono.length < 1) {
        InputClienteTelefono.classList.remove('is-valid', 'is-invalid');
    } else {
        if (telefono.length != 8) {
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Teléfono inválido",
                text: "El teléfono debe tener exactamente 8 dígitos",
                showConfirmButton: true,
            });

            InputClienteTelefono.classList.remove('is-valid');
            InputClienteTelefono.classList.add('is-invalid');
        } else {
            InputClienteTelefono.classList.remove('is-invalid');
            InputClienteTelefono.classList.add('is-valid');
        }
    }
}

const ValidarCorreo = () => {
    const correo = InputClienteCorreo.value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (correo.length < 1) {
        InputClienteCorreo.classList.remove('is-valid', 'is-invalid');
    } else {
        if (!emailRegex.test(correo)) {
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Correo inválido",
                text: "Por favor ingrese un correo electrónico válido",
                showConfirmButton: true,
            });

            InputClienteCorreo.classList.remove('is-valid');
            InputClienteCorreo.classList.add('is-invalid');
        } else {
            InputClienteCorreo.classList.remove('is-invalid');
            InputClienteCorreo.classList.add('is-valid');
        }
    }
}

const GuardarCliente = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormClientes, ['cliente_id'])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe validar todos los campos",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    const body = new FormData(FormClientes);

    const url = '/carrito_avpc/clientes/guardarAPI';
    const config = {
        method: 'POST',
        body
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos

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
        console.log(error)
    }
    BtnGuardar.disabled = false;
}

const BuscarClientes = async () => {
    const url = '/carrito_avpc/clientes/buscarAPI';
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos

        if (codigo == 1) {
            datatable.clear().draw();
            datatable.rows.add(data).draw();
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
        console.log(error)
    }
}

const datatable = new DataTable('#TableClientes', {
    dom: `
        <"row mt-3 justify-content-between" 
            <"col" l> 
            <"col" B> 
            <"col-3" f>
        >
        t
        <"row mt-3 justify-content-between" 
            <"col-md-3 d-flex align-items-center" i> 
            <"col-md-8 d-flex justify-content-end" p>
        >
    `,
    language: lenguaje,
    data: [],
    columns: [
        {
            title: 'No.',
            data: 'cliente_id',
            width: '5%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { 
            title: 'Nombres', 
            data: 'cliente_nombres',
            width: '15%'
        },
        { 
            title: 'Apellidos', 
            data: 'cliente_apellidos',
            width: '15%'
        },
        { 
            title: 'Correo', 
            data: 'cliente_correo',
            width: '20%'
        },
        { 
            title: 'Teléfono', 
            data: 'cliente_telefono',
            width: '10%'
        },
        { 
            title: 'NIT', 
            data: 'cliente_nit',
            width: '10%'
        },
        { 
            title: 'Fecha', 
            data: 'cliente_fecha',
            width: '10%',
            render: (data, type, row) => {
                const fecha = new Date(data);
                return fecha.toLocaleDateString('es-GT');
            }
        },
        {
            title: 'Estado',
            data: 'cliente_estado',
            width: '8%',
            render: (data, type, row) => {
                if (data == "A") {
                    return '<span class="badge bg-success">ACTIVO</span>';
                } else {
                    return '<span class="badge bg-danger">INACTIVO</span>';
                }
            }
        },
        {
            title: 'Acciones',
            data: 'cliente_id',
            width: '12%',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                 <div class='d-flex justify-content-center'>
                     <button class='btn btn-warning btn-sm modificar mx-1' 
                         data-id="${data}" 
                         data-nombres="${row.cliente_nombres}"  
                         data-apellidos="${row.cliente_apellidos}"  
                         data-nit="${row.cliente_nit}"  
                         data-telefono="${row.cliente_telefono}"  
                         data-correo="${row.cliente_correo}"  
                         data-estado="${row.cliente_estado}"  
                         data-fecha="${row.cliente_fecha}"  
                         title="Modificar cliente">
                         <i class='bi bi-pencil-square me-1'></i> Modificar
                     </button>
                     <button class='btn btn-danger btn-sm eliminar mx-1' 
                         data-id="${data}"
                         title="Eliminar cliente">
                        <i class="bi bi-x-circle me-1"></i>Eliminar
                     </button>
                 </div>`;
            }
        }
    ]
});

const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset

    document.getElementById('cliente_id').value = datos.id
    document.getElementById('cliente_nombres').value = datos.nombres
    document.getElementById('cliente_apellidos').value = datos.apellidos
    document.getElementById('cliente_nit').value = datos.nit
    document.getElementById('cliente_telefono').value = datos.telefono
    document.getElementById('cliente_correo').value = datos.correo
    document.getElementById('cliente_estado').value = datos.estado
    document.getElementById('cliente_fecha').value = datos.fecha

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
    
    const inputs = FormClientes.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.classList.remove('is-valid', 'is-invalid');
    });
}

const ModificarCliente = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormClientes, [''])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe validar todos los campos",
            showConfirmButton: true,
        });
        BtnModificar.disabled = false;
        return;
    }

    const body = new FormData(FormClientes);

    const url = '/carrito_avpc/clientes/modificarAPI';
    const config = {
        method: 'POST',
        body
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos

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
        console.log(error)
    }
    BtnModificar.disabled = false;
}

const EliminarCliente = async (e) => {
    const idCliente = e.currentTarget.dataset.id

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "question",
        title: "¿Desea eliminar este cliente?",
        text: 'Esta acción no se puede deshacer',
        showConfirmButton: true,
        confirmButtonText: 'Sí, eliminar',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        const url = `/carrito_avpc/clientes/eliminar?id=${idCliente}`;
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
            console.log(error)
        }
    }
}


BuscarClientes();

datatable.on('click', '.eliminar', EliminarCliente);
datatable.on('click', '.modificar', llenarFormulario);
FormClientes.addEventListener('submit', GuardarCliente);
InputClienteTelefono.addEventListener('change', ValidarTelefono);
InputClienteCorreo.addEventListener('change', ValidarCorreo);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarCliente);