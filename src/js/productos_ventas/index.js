import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormProductos = document.getElementById('FormProductos');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnConfirmarStock = document.getElementById('BtnConfirmarStock');

const GuardarProducto = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormProductos, ['producto_id'])) {
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

    const body = new FormData(FormProductos);
    const url = '/app01_avpc/productos/guardarAPI';
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
            BuscarProductos();
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
        console.error('Error en GuardarProducto:', error);
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

const BuscarProductos = async () => {
    const url = '/app01_avpc/productos/buscarAPI';
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

const datatable = new DataTable('#TableProductos', {
    language: lenguaje,
    data: [],
    columns: [
        { title: 'No.', data: 'producto_id', render: (data, type, row, meta) => meta.row + 1 },
        { title: 'Producto', data: 'producto_nombre' },
        { 
            title: 'Precio', 
            data: 'producto_precio',
            render: (data) => `Q${parseFloat(data).toFixed(2)}`
        },
        { 
            title: 'Stock', 
            data: 'producto_stock',
            render: (data) => {
                let color = 'bg-success';
                if (data <= 5) color = 'bg-danger';
                else if (data <= 10) color = 'bg-warning';
                
                return `<span class="badge ${color} text-white">${data}</span>`;
            }
        },
        { title: 'Descripción', data: 'producto_descripcion' },
        {
            title: 'Acciones',
            data: 'producto_id',
            searchable: false,
            orderable: false,
            render: (data, type, row) => {
                return `
                    <div class='d-flex justify-content-center gap-1 flex-wrap'>
                        <button class='btn btn-warning btn-sm modificar mb-1' 
                            data-id="${data}" 
                            data-nombre="${row.producto_nombre}"  
                            data-precio="${row.producto_precio}"  
                            data-stock="${row.producto_stock}"  
                            data-descripcion="${row.producto_descripcion}">
                            <i class='bi bi-pencil-square'></i> Modificar
                        </button>
                        <button class='btn btn-info btn-sm agregar-stock mb-1' 
                            data-id="${data}"
                            data-nombre="${row.producto_nombre}"
                            data-stock="${row.producto_stock}">
                            <i class="bi bi-plus-circle"></i> Stock
                        </button>
                        <button class='btn btn-danger btn-sm eliminar mb-1' 
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

    document.getElementById('producto_id').value = datos.id;
    document.getElementById('producto_nombre').value = datos.nombre;
    document.getElementById('producto_precio').value = datos.precio;
    document.getElementById('producto_stock').value = datos.stock;
    document.getElementById('producto_descripcion').value = datos.descripcion;

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

const limpiarTodo = () => {
    FormProductos.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
}

const ModificarProducto = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormProductos, [''])) {
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

    const body = new FormData(FormProductos);
    const url = '/app01_avpc/productos/modificarAPI';
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
            BuscarProductos();
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
        console.error('Error en ModificarProducto:', error);
    }
    BtnModificar.disabled = false;
}

const EliminarProducto = async (event) => {
    const idProducto = event.currentTarget.dataset.id;

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "warning",
        title: "¿Desea eliminar este producto?",
        text: 'Esta acción no se puede deshacer',
        showConfirmButton: true,
        confirmButtonText: 'Sí, Eliminar',
        confirmButtonColor: '#d33',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        const url = `/app01_avpc/productos/eliminar?id=${idProducto}`;
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

                BuscarProductos();
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
            console.error('Error en EliminarProducto:', error);
        }
    }
}

const MostrarModalStock = (event) => {
    const datos = event.currentTarget.dataset;
    
    document.getElementById('stock_producto_id').value = datos.id;
    document.getElementById('producto_nombre_stock').value = datos.nombre;
    document.getElementById('stock_actual').value = datos.stock;
    document.getElementById('cantidad_agregar').value = '';

    const modal = new bootstrap.Modal(document.getElementById('ModalAgregarStock'));
    modal.show();
}

const AgregarStock = async () => {
    const productoId = document.getElementById('stock_producto_id').value;
    const cantidad = document.getElementById('cantidad_agregar').value;

    if (!cantidad || cantidad <= 0) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "Cantidad Requerida",
            text: "Debe ingresar una cantidad válida",
            showConfirmButton: true,
        });
        return;
    }

    BtnConfirmarStock.disabled = true;

    const body = new FormData();
    body.append('producto_id', productoId);
    body.append('cantidad', cantidad);

    const url = '/app01_avpc/productos/agregarStockAPI';
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

            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('ModalAgregarStock'));
            modal.hide();

            BuscarProductos();
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
        console.error('Error en AgregarStock:', error);
    }
    BtnConfirmarStock.disabled = false;
}

// Inicializar
BuscarProductos();

// Event Listeners
FormProductos.addEventListener('submit', GuardarProducto);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarProducto);
BtnConfirmarStock.addEventListener('click', AgregarStock);
datatable.on('click', '.eliminar', EliminarProducto);
datatable.on('click', '.modificar', llenarFormulario);
datatable.on('click', '.agregar-stock', MostrarModalStock);