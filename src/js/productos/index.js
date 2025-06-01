import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormProductos = document.getElementById('FormProductos');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');

const GuardarProducto = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormProductos, ['producto_id'])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe de validar todos los campos",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    const body = new FormData(FormProductos);
    const url = '/carrito_avpc/productos/guardarAPI';
    const config = {
        method: 'POST',
        body
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos

        console.log('Respuesta del servidor:', datos);

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
            text: "Ocurrió un error",
            showConfirmButton: true,
        });
    }
    BtnGuardar.disabled = false;
}

const BuscarProductos = async () => {
    const url = '/carrito_avpc/productos/buscarAPI';
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
                icon: "info",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }
    } catch (error) {
        console.log(error)
    }
}

const datatable = new DataTable('#TableProductos', {
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
    order: [[3, 'asc'], [1, 'asc']],
    columns: [
        { title: 'No.', data: 'producto_id', render: (data, type, row, meta) => meta.row + 1 },
        { title: 'Producto', data: 'producto_nombre' },
        { 
            title: 'Precio', 
            data: 'producto_precio',
            render: (data) => `Q${parseFloat(data).toFixed(2)}`
        },
        { title: 'Stock', data: 'producto_stock' },
        {
            title: 'Categoría', 
            data: 'categoria_nombre', 
            render: (data) => {
                let color = 'bg-secondary';
                if (data === 'Alimentos') color = 'bg-success';
                if (data === 'Limpieza') color = 'bg-info';
                if (data === 'Hogar') color = 'bg-warning';
                if (data === 'Tecnología') color = 'bg-primary';
                if (data === 'Ropa') color = 'bg-dark';
                return `<span class="badge ${color} text-white">${data}</span>`;
            }
        },
        {
            title: 'Estado Stock',
            data: 'producto_stock',
            render: (data) => {
                if (data == 0) {
                    return '<span class="badge bg-danger">SIN STOCK</span>';
                } else if (data <= 5) {
                    return '<span class="badge bg-warning">STOCK BAJO</span>';
                } else {
                    return '<span class="badge bg-success">DISPONIBLE</span>';
                }
            }
        },
        {
            title: 'Acciones',
            data: 'producto_id',
            searchable: false,
            orderable: false,
            width: '20%',
            render: (data, type, row, meta) => {
                let botones = `
                    <div class='d-flex justify-content-center'>
                        <button class='btn btn-warning btn-sm modificar mx-1' 
                            data-id="${data}" 
                            data-nombre="${row.producto_nombre}"  
                            data-precio="${row.producto_precio}"  
                            data-stock="${row.producto_stock}"  
                            data-categoria="${row.producto_categoria_id}">
                            <i class='bi bi-pencil-square'></i>Modificar
                        </button>`;

                // Solo mostrar eliminar si no tiene stock
                if (row.producto_stock == 0) {
                    botones += `
                        <button class='btn btn-danger btn-sm eliminar mx-1' 
                            data-id="${data}">
                            <i class="bi bi-trash3"></i> Eliminar
                        </button>`;
                } else {
                    botones += `
                        <button class='btn btn-secondary btn-sm' disabled title="No se puede eliminar con stock">
                            <i class="bi bi-trash3"></i> Eliminar
                        </button>`;
                }

                botones += `</div>`;
                return botones;
            }
        }
    ],
});

const CargarCategorias = async () => {
    const url = '/carrito_avpc/productos/categoriasAPI';
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, data } = datos

        if (codigo == 1) {
            const select = document.getElementById('producto_categoria_id');
            select.innerHTML = '<option value="">Seleccione categoría</option>';

            data.forEach(categoria => {
                const option = document.createElement('option');
                option.value = categoria.categoria_id;
                option.textContent = categoria.categoria_nombre;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error en CargarCategorias:', error);
    }
}

const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset

    document.getElementById('producto_id').value = datos.id
    document.getElementById('producto_nombre').value = datos.nombre
    document.getElementById('producto_precio').value = datos.precio
    document.getElementById('producto_stock').value = datos.stock
    document.getElementById('producto_categoria_id').value = datos.categoria

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    })
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
            text: "Debe de validar todos los campos",
            showConfirmButton: true,
        });
        BtnModificar.disabled = false;
        return;
    }

    const body = new FormData(FormProductos);
    const url = '/carrito_avpc/productos/modificarAPI';
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

const EliminarProducto = async (e) => {
    const idProducto = e.currentTarget.dataset.id

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
        const url = `/carrito_avpc/productos/eliminar?id=${idProducto}`;
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

CargarCategorias();
BuscarProductos();
datatable.on('click', '.eliminar', EliminarProducto);
datatable.on('click', '.modificar', llenarFormulario);
FormProductos.addEventListener('submit', GuardarProducto);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarProducto);