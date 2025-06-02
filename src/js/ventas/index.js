import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormVentas = document.getElementById('FormVentas');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnAgregarProducto = document.getElementById('BtnAgregarProducto');
const TablaProductosSeleccionados = document.getElementById('TablaProductosSeleccionados').getElementsByTagName('tbody')[0];

// Array para manejar productos seleccionados
let productosSeleccionados = [];

const GuardarVenta = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    // Validar que hay un cliente seleccionado
    const clienteId = document.getElementById('venta_cliente_id').value;
    if (!clienteId) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "CLIENTE REQUERIDO",
            text: "Debe seleccionar un cliente",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    // Validar que hay productos seleccionados
    if (productosSeleccionados.length === 0) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "PRODUCTOS REQUERIDOS",
            text: "Debe seleccionar al menos un producto",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    const body = new FormData();
    body.append('venta_cliente_id', clienteId);
    body.append('productos', JSON.stringify(productosSeleccionados));

    const url = '/app01_avpc/ventas/guardarAPI';
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
            BuscarVentas();
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
        console.error('Error en GuardarVenta:', error);
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

const CargarClientes = async () => {
    const url = '/app01_avpc/ventas/clientesAPI';
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        if (codigo == 1) {
            const select = document.getElementById('venta_cliente_id');
            select.innerHTML = '<option value="">Seleccione un cliente</option>';

            data.forEach(cliente => {
                const option = document.createElement('option');
                option.value = cliente.cliente_id;
                option.textContent = `${cliente.cliente_nombre} ${cliente.cliente_apellido} - ${cliente.cliente_nit}`;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error en CargarClientes:', error);
    }
}

const CargarProductos = async () => {
    const url = '/app01_avpc/ventas/productosDisponiblesAPI';
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        if (codigo == 1) {
            const select = document.getElementById('producto_select');
            select.innerHTML = '<option value="">Seleccione un producto</option>';

            data.forEach(producto => {
                const option = document.createElement('option');
                option.value = producto.producto_id;
                option.dataset.precio = producto.producto_precio;
                option.dataset.stock = producto.producto_stock;
                option.dataset.nombre = producto.producto_nombre;
                option.textContent = `${producto.producto_nombre} - Q${producto.producto_precio} (Stock: ${producto.producto_stock})`;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error en CargarProductos:', error);
    }
}

const AgregarProducto = () => {
    const select = document.getElementById('producto_select');
    const cantidadInput = document.getElementById('cantidad_input');

    if (!select.value) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "Producto Requerido",
            text: "Debe seleccionar un producto",
            showConfirmButton: true,
        });
        return;
    }

    const cantidad = parseInt(cantidadInput.value);
    if (!cantidad || cantidad <= 0) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "Cantidad Inválida",
            text: "La cantidad debe ser mayor a 0",
            showConfirmButton: true,
        });
        return;
    }

    const selectedOption = select.options[select.selectedIndex];
    const productoId = select.value;
    const nombre = selectedOption.dataset.nombre;
    const precio = parseFloat(selectedOption.dataset.precio);
    const stock = parseInt(selectedOption.dataset.stock);

    // Verificar stock disponible
    if (cantidad > stock) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "Stock Insuficiente",
            text: `Solo hay ${stock} unidades disponibles`,
            showConfirmButton: true,
        });
        return;
    }

    // Verificar si el producto ya está en la lista
    const productoExistente = productosSeleccionados.find(p => p.producto_id == productoId);
    if (productoExistente) {
        const nuevaCantidad = productoExistente.cantidad + cantidad;
        if (nuevaCantidad > stock) {
            Swal.fire({
                position: "center",
                icon: "warning",
                title: "Stock Insuficiente",
                text: `Solo hay ${stock} unidades disponibles. Ya tiene ${productoExistente.cantidad} en el carrito`,
                showConfirmButton: true,
            });
            return;
        }
        productoExistente.cantidad = nuevaCantidad;
    } else {
        productosSeleccionados.push({
            producto_id: productoId,
            nombre: nombre,
            precio: precio,
            cantidad: cantidad
        });
    }

    ActualizarTablaProductos();
    select.value = '';
    cantidadInput.value = 1;
}

const ActualizarTablaProductos = () => {
    const tbody = TablaProductosSeleccionados;
    tbody.innerHTML = '';

    if (productosSeleccionados.length === 0) {
        tbody.innerHTML = '<tr id="filaVacia"><td colspan="5" class="text-center text-muted">No hay productos seleccionados</td></tr>';
        document.getElementById('totalVenta').textContent = '0.00';
        return;
    }

    let total = 0;

    productosSeleccionados.forEach((producto, index) => {
        const subtotal = producto.precio * producto.cantidad;
        total += subtotal;

        const fila = tbody.insertRow();
        fila.innerHTML = `
            <td>${producto.nombre}</td>
            <td>Q${producto.precio.toFixed(2)}</td>
            <td>
                <input type="number" class="form-control cantidad-input" 
                       value="${producto.cantidad}" 
                       min="1" 
                       data-index="${index}" 
                       style="width: 80px;">
            </td>
            <td>Q${subtotal.toFixed(2)}</td>
            <td>
                <button class="btn btn-danger btn-sm eliminar-producto" data-index="${index}">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
    });

    document.getElementById('totalVenta').textContent = total.toFixed(2);

    // Agregar event listeners para los inputs de cantidad
    document.querySelectorAll('.cantidad-input').forEach(input => {
        input.addEventListener('change', CambiarCantidad);
    });

    // Agregar event listeners para los botones de eliminar
    document.querySelectorAll('.eliminar-producto').forEach(btn => {
        btn.addEventListener('click', EliminarProducto);
    });
}

const CambiarCantidad = (event) => {
    const index = event.target.dataset.index;
    const nuevaCantidad = parseInt(event.target.value);

    if (nuevaCantidad <= 0) {
        event.target.value = productosSeleccionados[index].cantidad;
        return;
    }

    productosSeleccionados[index].cantidad = nuevaCantidad;
    ActualizarTablaProductos();
}

const EliminarProducto = (event) => {
    const index = event.target.closest('button').dataset.index;
    productosSeleccionados.splice(index, 1);
    ActualizarTablaProductos();
}

const limpiarTodo = () => {
    FormVentas.reset();
    productosSeleccionados = [];
    ActualizarTablaProductos();
    CargarProductos(); // Recargar productos para actualizar stock
}

// DataTable para ventas
const datatable = new DataTable('#TableVentas', {
    language: lenguaje,
    data: [],
    columns: [
        { title: 'No.', data: 'venta_id' },
        { title: 'Fecha', data: 'venta_fecha' },
        { 
            title: 'Cliente', 
            data: null,
            render: (data, type, row) => `${row.cliente_nombre} ${row.cliente_apellido}`
        },
        { title: 'NIT', data: 'cliente_nit' },
        { 
            title: 'Total', 
            data: 'venta_total',
            render: (data) => `Q${parseFloat(data).toFixed(2)}`
        },
        {
            title: 'Acciones',
            data: 'venta_id',
            searchable: false,
            orderable: false,
            render: (data, type, row) => {
                return `
                    <div class='d-flex justify-content-center gap-1'>
                        <button class='btn btn-info btn-sm ver-detalle' 
                            data-id="${data}">
                            <i class='bi bi-eye'></i> Ver
                        </button>
                        <button class='btn btn-danger btn-sm eliminar-venta' 
                            data-id="${data}">
                            <i class="bi bi-trash3"></i> Eliminar
                        </button>
                    </div>`;
            }
        }
    ]
});

const BuscarVentas = async () => {
    const url = '/app01_avpc/ventas/buscarAPI';
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
        }
    } catch (error) {
        console.log(error);
    }
}

const VerDetalle = async (event) => {
    const ventaId = event.currentTarget.dataset.id;
    
    const url = `/app01_avpc/ventas/detalleVentaAPI?id=${ventaId}`;
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, venta, detalles } = datos;

        if (codigo == 1) {
            let contenido = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Cliente:</strong> ${venta.cliente_nombre} ${venta.cliente_apellido}<br>
                        <strong>NIT:</strong> ${venta.cliente_nit}<br>
                        <strong>Fecha:</strong> ${venta.venta_fecha}
                    </div>
                    <div class="col-md-6">
                        <strong>Total:</strong> Q${parseFloat(venta.venta_total).toFixed(2)}
                    </div>
                </div>
                <hr>
                <h6>Productos:</h6>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            detalles.forEach(detalle => {
                contenido += `
                    <tr>
                        <td>${detalle.producto_nombre}</td>
                        <td>${detalle.detalle_cantidad}</td>
                        <td>Q${parseFloat(detalle.detalle_precio_unitario).toFixed(2)}</td>
                        <td>Q${parseFloat(detalle.detalle_subtotal).toFixed(2)}</td>
                    </tr>
                `;
            });

            contenido += `
                    </tbody>
                </table>
            `;

            document.getElementById('contenidoDetalle').innerHTML = contenido;
            
            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('ModalDetalleVenta'));
            modal.show();
        }
    } catch (error) {
        console.error('Error en VerDetalle:', error);
    }
}

const EliminarVenta = async (event) => {
    const ventaId = event.currentTarget.dataset.id;

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "warning",
        title: "¿Desea eliminar esta venta?",
        text: 'Esta acción devolverá el stock y no se puede deshacer',
        showConfirmButton: true,
        confirmButtonText: 'Sí, Eliminar',
        confirmButtonColor: '#d33',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        const url = `/app01_avpc/ventas/eliminar?id=${ventaId}`;
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

                BuscarVentas();
                CargarProductos(); // Recargar productos para actualizar stock
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
            console.error('Error en EliminarVenta:', error);
        }
    }
}

// Inicializar
CargarClientes();
CargarProductos();
BuscarVentas();

// Event Listeners
FormVentas.addEventListener('submit', GuardarVenta);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnAgregarProducto.addEventListener('click', AgregarProducto);
datatable.on('click', '.ver-detalle', VerDetalle);
datatable.on('click', '.eliminar-venta', EliminarVenta);