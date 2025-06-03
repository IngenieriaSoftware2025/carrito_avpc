import { Modal } from "bootstrap";
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormVentas = document.getElementById('FormVentas');
const selectCliente = document.getElementById('venta_cliente_id');
const BtnCargarProductos = document.getElementById('BtnCargarProductos');
const BtnGuardarVenta = document.getElementById('BtnGuardarVenta');
const BtnModificarVenta = document.getElementById('BtnModificarVenta');
const BtnLimpiarVenta = document.getElementById('BtnLimpiarVenta');
const seccionProductos = document.getElementById('seccionProductos');
const seccionCarrito = document.getElementById('seccionCarrito');
const productosDisponibles = document.getElementById('productosDisponibles');
const carritoItems = document.getElementById('carritoItems');
const totalVenta = document.getElementById('totalVenta');

let carrito = [];
let productos = [];

const CargarClientes = async () => {
    try {
        const respuesta = await fetch('/carrito_avpc/ventas/clientes');
        const datos = await respuesta.json();
        
        if (datos.codigo == 1) {
            selectCliente.innerHTML = '<option value="">SELECCIONE UN CLIENTE</option>';
            
            datos.data.forEach(cliente => {
                selectCliente.innerHTML += `
                    <option value="${cliente.cliente_id}">
                        ${cliente.cliente_nombres} ${cliente.cliente_apellidos}
                    </option>
                `;
            });
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "No se pudieron cargar los clientes"
        });
    }
};

const CargarProductos = async () => {
    if (!selectCliente.value) {
        Swal.fire({
            icon: "warning",
            title: "Cliente requerido",
            text: "Debe seleccionar un cliente primero"
        });
        return;
    }

    try {
        const respuesta = await fetch('/carrito_avpc/productos/disponibles');
        const datos = await respuesta.json();
        
        if (datos.codigo == 1) {
            productos = datos.data;
            MostrarProductos();
        } else {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: datos.mensaje || "Error al cargar productos"
            });
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "No se pudieron cargar los productos"
        });
    }
};

const MostrarProductos = () => {
    productosDisponibles.innerHTML = '';
    
    if (!productos || productos.length === 0) {
        productosDisponibles.innerHTML = '<tr><td colspan="7" class="text-center">No hay productos disponibles</td></tr>';
        return;
    }
    
    productos.forEach(producto => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>
                <input type="checkbox" class="form-check-input producto-check" 
                       data-id="${producto.producto_id}">
            </td>
            <td>${producto.producto_nombre}</td>
            <td>Q. ${parseFloat(producto.producto_precio).toFixed(2)}</td>
            <td>${producto.producto_cantidad}</td>
            <td>
                <input type="number" class="form-control form-control-sm cantidad-input" 
                       data-id="${producto.producto_id}" 
                       min="1" max="${producto.producto_cantidad}" 
                       value="1" disabled>
            </td>
            <td>${producto.producto_descripcion || 'Sin descripción'}</td>
            <td>
                <button type="button" class="btn btn-sm btn-primary agregar-btn" 
                        data-id="${producto.producto_id}" disabled>
                    Agregar
                </button>
            </td>
        `;
        productosDisponibles.appendChild(fila);
    });

    seccionProductos.style.display = 'block';
    AgregarEventosProductos();
};

const AgregarEventosProductos = () => {
    document.querySelectorAll('.producto-check').forEach(check => {
        check.addEventListener('change', function() {
            const id = this.dataset.id;
            const cantidadInput = document.querySelector(`[data-id="${id}"].cantidad-input`);
            const agregarBtn = document.querySelector(`[data-id="${id}"].agregar-btn`);
            
            if (this.checked) {
                cantidadInput.disabled = false;
                agregarBtn.disabled = false;
            } else {
                cantidadInput.disabled = true;
                agregarBtn.disabled = true;
            }
        });
    });

    document.querySelectorAll('.agregar-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            AgregarAlCarrito(id);
        });
    });
};

const AgregarAlCarrito = (productoId) => {
    const producto = productos.find(p => p.producto_id == productoId);
    const cantidad = parseInt(document.querySelector(`[data-id="${productoId}"].cantidad-input`).value);
    
    if (cantidad > producto.producto_cantidad) {
        Swal.fire({
            icon: "error",
            title: "Stock insuficiente",
            text: `Solo hay ${producto.producto_cantidad} unidades disponibles`
        });
        return;
    }

    const existe = carrito.findIndex(item => item.producto_id == productoId);
    
    if (existe !== -1) {
        carrito[existe].cantidad = cantidad;
        carrito[existe].subtotal = cantidad * producto.producto_precio;
    } else {
        carrito.push({
            producto_id: productoId,
            nombre: producto.producto_nombre,
            descripcion: producto.producto_descripcion,
            precio: producto.producto_precio,
            cantidad: cantidad,
            subtotal: cantidad * producto.producto_precio
        });
    }

    ActualizarCarrito();
    
    if (!document.getElementById('venta_id').value) {
        BtnGuardarVenta.style.display = 'inline-block';
    }
    
    document.querySelector(`[data-id="${productoId}"].producto-check`).checked = false;
    document.querySelector(`[data-id="${productoId}"].cantidad-input`).disabled = true;
    document.querySelector(`[data-id="${productoId}"].agregar-btn`).disabled = true;
};

const ActualizarCarrito = () => {
    carritoItems.innerHTML = '';
    let total = 0;

    carrito.forEach((item, index) => {
        total += item.subtotal;
        
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>
                <strong>${item.nombre}</strong><br>
                <small class="text-muted">${item.descripcion || 'Sin descripción'}</small>
            </td>
            <td>Q. ${parseFloat(item.precio).toFixed(2)}</td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       value="${item.cantidad}" min="1" 
                       onchange="CambiarCantidad(${index}, this.value)">
            </td>
            <td>Q. ${parseFloat(item.subtotal).toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" 
                        onclick="QuitarDelCarrito(${index})">
                    Quitar
                </button>
            </td>
        `;
        carritoItems.appendChild(fila);
    });

    totalVenta.textContent = `Q. ${total.toFixed(2)}`;

    if (carrito.length > 0) {
        seccionCarrito.style.display = 'block';
    } else {
        seccionCarrito.style.display = 'none';
        BtnGuardarVenta.style.display = 'none';
    }
};

window.CambiarCantidad = (index, nuevaCantidad) => {
    const item = carrito[index];
    const producto = productos.find(p => p.producto_id == item.producto_id);
    
    if (nuevaCantidad > producto.producto_cantidad) {
        Swal.fire({
            icon: "error",
            title: "Stock insuficiente",
            text: `Solo hay ${producto.producto_cantidad} unidades disponibles`
        });
        ActualizarCarrito();
        return;
    }

    carrito[index].cantidad = parseInt(nuevaCantidad);
    carrito[index].subtotal = parseInt(nuevaCantidad) * item.precio;
    ActualizarCarrito();
};

window.QuitarDelCarrito = (index) => {
    carrito.splice(index, 1);
    ActualizarCarrito();
};

const GuardarVenta = async (event) => {
    event.preventDefault();
    BtnGuardarVenta.disabled = true;

    if (!selectCliente.value) {
        Swal.fire({
            icon: "warning",
            title: "Cliente requerido",
            text: "Debe seleccionar un cliente"
        });
        BtnGuardarVenta.disabled = false;
        return;
    }

    if (carrito.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Carrito vacío",
            text: "Debe agregar al menos un producto"
        });
        BtnGuardarVenta.disabled = false;
        return;
    }

    const formData = new FormData();
    formData.append('venta_cliente_id', selectCliente.value);
    formData.append('productos', JSON.stringify(carrito));

    try {
        const respuesta = await fetch('/carrito_avpc/ventas/guardarAPI', {
            method: 'POST',
            body: formData
        });
        
        const datos = await respuesta.json();

        if (datos.codigo == 1) {
            await Swal.fire({
                icon: "success",
                title: "Éxito",
                text: datos.mensaje
            });

            LimpiarTodo();
            BuscarVentas();
        } else {
            await Swal.fire({
                icon: "error",
                title: "Error",
                text: datos.mensaje
            });
        }
    } catch (error) {
        await Swal.fire({
            icon: "error",
            title: "Error",
            text: "Error de conexión"
        });
    }
    
    BtnGuardarVenta.disabled = false;
};

const BuscarVentas = async () => {
    try {
        const respuesta = await fetch('/carrito_avpc/ventas/buscarAPI');
        const datos = await respuesta.json();

        if (datos.codigo == 1) {
            datatable.clear();
            datatable.rows.add(datos.data);
            datatable.draw(false);
        }
    } catch (error) {
        console.error('Error:', error);
    }
};

const datatable = new DataTable('#TableVentas', {
    language: lenguaje,
    data: [],
    columns: [
        {
            title: 'No.',
            data: 'venta_id',
            render: (data, type, row, meta) => meta.row + 1
        },
        { 
            title: 'Cliente', 
            data: 'cliente_nombres',
            render: (data, type, row) => {
                return `${row.cliente_nombres} ${row.cliente_apellidos}`;
            }
        },
        { 
            title: 'Fecha', 
            data: 'venta_fecha',
            render: (data, type, row) => {
                const fecha = new Date(data);
                return fecha.toLocaleString('es-GT');
            }
        },
        { 
            title: 'Subtotal', 
            data: 'venta_subtotal',
            render: (data, type, row) => {
                return `Q. ${parseFloat(data).toFixed(2)}`;
            }
        },
        { 
            title: 'Total', 
            data: 'venta_total',
            render: (data, type, row) => {
                return `Q. ${parseFloat(data).toFixed(2)}`;
            }
        },
       
    ]
});

window.VerDetalle = async (ventaId) => {
    try {
        const respuesta = await fetch(`/carrito_avpc/ventas/detalle?id=${ventaId}`);
        const datos = await respuesta.json();

        if (datos.codigo == 1) {
            const venta = datos.venta;
            const detalles = datos.detalles;

            let contenido = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Cliente:</strong> ${venta.cliente_nombres} ${venta.cliente_apellidos}
                    </div>
                    <div class="col-md-6">
                        <strong>Fecha:</strong> ${new Date(venta.venta_fecha).toLocaleString('es-GT')}
                    </div>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            detalles.forEach(detalle => {
                contenido += `
                    <tr>
                        <td>
                            <strong>${detalle.producto_nombre}</strong><br>
                            <small class="text-muted">${detalle.producto_descripcion || 'Sin descripción'}</small>
                        </td>
                        <td>${detalle.detalle_cantidad}</td>
                        <td>Q. ${parseFloat(detalle.detalle_precio_unitario).toFixed(2)}</td>
                        <td>Q. ${parseFloat(detalle.detalle_subtotal).toFixed(2)}</td>
                    </tr>
                `;
            });

            contenido += `
                    </tbody>
                    <tfoot>
                        <tr class="table-info">
                            <td colspan="3"><strong>TOTAL:</strong></td>
                            <td><strong>Q. ${parseFloat(venta.venta_total).toFixed(2)}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            `;

            document.getElementById('contenidoDetalleVenta').innerHTML = contenido;
            
            const modal = new Modal(document.getElementById('modalDetalleVenta'));
            modal.show();
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "No se pudo obtener el detalle"
        });
    }
};


CargarClientes();
BuscarVentas(); 


BtnCargarProductos.addEventListener('click', CargarProductos);
FormVentas.addEventListener('submit', GuardarVenta);
BtnLimpiarVenta.addEventListener('click', LimpiarTodo);