import { Dropdown, Modal } from "bootstrap";
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

// Variables globales
let carrito = [];
let productosDisponibles = [];
let clientes = [];

// Elementos DOM
const clienteSelect = document.getElementById('cliente_id');
const totalGeneral = document.getElementById('total-general');
const itemsCount = document.getElementById('items-count');
const productosDisponiblesBody = document.getElementById('productos-disponibles');
const carritoItemsBody = document.getElementById('carrito-items');
const carritoVacio = document.getElementById('carrito-vacio');
const BtnProcesarVenta = document.getElementById('BtnProcesarVenta');
const BtnLimpiarCarrito = document.getElementById('BtnLimpiarCarrito');
const BtnVerFacturas = document.getElementById('BtnVerFacturas');
const SeccionFacturas = document.getElementById('SeccionFacturas');

// Cargar datos iniciales
const CargarClientes = async () => {
    try {
        const respuesta = await fetch('/carrito_avpc2/facturas/clientes');
        const datos = await respuesta.json();
        
        if (datos.codigo == 1) {
            clientes = datos.data;
            clienteSelect.innerHTML = '<option value="">Seleccione un cliente</option>';
            
            clientes.forEach(cliente => {
                const option = document.createElement('option');
                option.value = cliente.cliente_id;
                option.textContent = `${cliente.cliente_nombre} - ${cliente.cliente_nit}`;
                clienteSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar clientes:', error);
    }
}

const CargarProductosDisponibles = async () => {
    try {
        const respuesta = await fetch('/carrito_avpc2/facturas/productos-disponibles');
        const datos = await respuesta.json();
        
        if (datos.codigo == 1) {
            productosDisponibles = datos.data;
            mostrarProductosDisponibles();
        }
    } catch (error) {
        console.error('Error al cargar productos:', error);
    }
}

const mostrarProductosDisponibles = () => {
    productosDisponiblesBody.innerHTML = '';
    
    productosDisponibles.forEach(producto => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${producto.producto_nombre}</td>
            <td>Q${parseFloat(producto.producto_precio).toFixed(2)}</td>
            <td>
                <span class="badge ${producto.producto_stock <= 5 ? 'bg-warning' : 'bg-success'}">
                    ${producto.producto_stock}
                </span>
            </td>
            <td>
                <span class="badge bg-secondary">${producto.categoria_nombre}</span>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       id="cantidad-${producto.producto_id}" 
                       min="1" max="${producto.producto_stock}" value="1" style="width: 80px;">
            </td>
            <td>
                <button class="btn btn-primary btn-sm agregar-carrito" 
                        data-id="${producto.producto_id}">
                    <i class="bi bi-cart-plus"></i> Agregar
                </button>
            </td>
        `;
        productosDisponiblesBody.appendChild(row);
    });
}

const agregarAlCarrito = (productoId) => {
    const producto = productosDisponibles.find(p => p.producto_id == productoId);
    const cantidadInput = document.getElementById(`cantidad-${productoId}`);
    const cantidad = parseInt(cantidadInput.value);
    
    if (!producto || cantidad <= 0 || cantidad > producto.producto_stock) {
        Swal.fire({
            icon: 'error',
            title: 'Cantidad inválida',
            text: `La cantidad debe ser entre 1 y ${producto.producto_stock}`
        });
        return;
    }
    
    // Verificar si ya está en el carrito
    const itemExistente = carrito.find(item => item.producto_id == productoId);
    
    if (itemExistente) {
        const nuevaCantidad = itemExistente.cantidad + cantidad;
        if (nuevaCantidad > producto.producto_stock) {
            Swal.fire({
                icon: 'error',
                title: 'Stock insuficiente',
                text: `No puede agregar más de ${producto.producto_stock} unidades`
            });
            return;
        }
        itemExistente.cantidad = nuevaCantidad;
    } else {
        carrito.push({
            producto_id: producto.producto_id,
            producto_nombre: producto.producto_nombre,
            precio: parseFloat(producto.producto_precio),
            cantidad: cantidad,
            stock_disponible: producto.producto_stock
        });
    }
    
    cantidadInput.value = 1; // Reset cantidad
    actualizarCarrito();
}

const actualizarCarrito = () => {
    carritoItemsBody.innerHTML = '';
    
    if (carrito.length === 0) {
        carritoVacio.style.display = 'table-row';
        BtnProcesarVenta.disabled = true;
    } else {
        carritoVacio.style.display = 'none';
        BtnProcesarVenta.disabled = false;
        
        carrito.forEach((item, index) => {
            const subtotal = item.cantidad * item.precio;
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.producto_nombre}</td>
                <td>Q${item.precio.toFixed(2)}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-sm btn-outline-secondary me-2 btn-cantidad" 
                                data-index="${index}" data-accion="decrementar">-</button>
                        <span class="mx-2">${item.cantidad}</span>
                        <button class="btn btn-sm btn-outline-secondary ms-2 btn-cantidad" 
                                data-index="${index}" data-accion="incrementar">+</button>
                    </div>
                </td>
                <td class="fw-bold">Q${subtotal.toFixed(2)}</td>
                <td>
                    <button class="btn btn-danger btn-sm eliminar-item" data-index="${index}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            carritoItemsBody.appendChild(row);
        });
    }
    
    actualizarTotales();
}

const actualizarTotales = () => {
    const total = carrito.reduce((sum, item) => sum + (item.cantidad * item.precio), 0);
    const items = carrito.reduce((sum, item) => sum + item.cantidad, 0);
    
    totalGeneral.textContent = `Q${total.toFixed(2)}`;
    itemsCount.textContent = items;
}

const modificarCantidadCarrito = (index, accion) => {
    const item = carrito[index];
    
    if (accion === 'incrementar') {
        if (item.cantidad < item.stock_disponible) {
            item.cantidad++;
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Stock máximo',
                text: `No hay más stock disponible`
            });
            return;
        }
    } else if (accion === 'decrementar') {
        if (item.cantidad > 1) {
            item.cantidad--;
        } else {
            eliminarDelCarrito(index);
            return;
        }
    }
    
    actualizarCarrito();
}

const eliminarDelCarrito = (index) => {
    carrito.splice(index, 1);
    actualizarCarrito();
}

const limpiarCarrito = () => {
    carrito = [];
    actualizarCarrito();
}

const procesarVenta = async () => {
    if (!clienteSelect.value) {
        Swal.fire({
            icon: 'error',
            title: 'Cliente requerido',
            text: 'Debe seleccionar un cliente'
        });
        return;
    }
    
    if (carrito.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Carrito vacío',
            text: 'Debe agregar productos al carrito'
        });
        return;
    }
    
    BtnProcesarVenta.disabled = true;
    
    const formData = new FormData();
    formData.append('cliente_id', clienteSelect.value);
    formData.append('productos', JSON.stringify(carrito));
    
    try {
        const respuesta = await fetch('/carrito_avpc2/facturas/procesar-venta', {
            method: 'POST',
            body: formData
        });
        
        const datos = await respuesta.json();
        
        if (datos.codigo == 1) {
            await Swal.fire({
                icon: 'success',
                title: 'Venta procesada',
                text: `Factura ${datos.numero_factura} generada correctamente\nTotal: Q${datos.total.toFixed(2)}`,
                confirmButtonText: 'Aceptar'
            });
            
            // Limpiar carrito y recargar datos
            limpiarCarrito();
            clienteSelect.value = '';
            CargarProductosDisponibles(); // Actualizar stock
            CargarFacturas(); // Actualizar lista de facturas
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error en la venta',
                text: datos.mensaje
            });
        }
    } catch (error) {
        console.error('Error al procesar venta:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al procesar la venta'
        });
    }
    
    BtnProcesarVenta.disabled = false;
}

// DataTable para facturas
const datatableFacturas = new DataTable('#TableFacturas', {
    language: lenguaje,
    data: [],
    order: [[0, 'desc']],
    columns: [
        { title: 'No. Factura', data: 'factura_numero' },
        { title: 'Cliente', data: 'cliente_nombre' },
        { title: 'NIT', data: 'cliente_nit' },
        { 
            title: 'Fecha', 
            data: 'factura_fecha',
            render: (data) => {
                const fecha = new Date(data);
                return fecha.toLocaleDateString('es-GT');
            }
        },
        { 
            title: 'Total', 
            data: 'factura_total',
            render: (data) => `Q${parseFloat(data).toFixed(2)}`
        },
        {
            title: 'Acciones',
            data: 'factura_id',
            searchable: false,
            orderable: false,
            render: (data, type, row) => `
                <div class='d-flex justify-content-center'>
                    <button class='btn btn-info btn-sm ver-detalle mx-1' data-id="${data}">
                        <i class='bi bi-eye'></i> Ver
                    </button>
                    <button class='btn btn-danger btn-sm anular-factura mx-1' data-id="${data}">
                        <i class='bi bi-x-circle'></i> Anular
                    </button>
                </div>
            `
        }
    ]
});

const CargarFacturas = async () => {
    try {
        const respuesta = await fetch('/carrito_avpc2/facturas/buscarAPI');
        const datos = await respuesta.json();
        
        if (datos.codigo == 1) {
            datatableFacturas.clear().draw();
            datatableFacturas.rows.add(datos.data).draw();
        }
    } catch (error) {
        console.error('Error al cargar facturas:', error);
    }
}

const verDetalleFactura = async (facturaId) => {
    try {
        const respuesta = await fetch(`/carrito_avpc2/facturas/detalle?id=${facturaId}`);
        const datos = await respuesta.json();
        
        if (datos.codigo == 1) {
            mostrarModalDetalle(datos.factura, datos.detalle);
        }
    } catch (error) {
        console.error('Error al cargar detalle:', error);
    }
}

const mostrarModalDetalle = (factura, detalle) => {
    const contenido = document.getElementById('contenido-factura');
    
    let detalleHTML = `
        <div class="row mb-3">
            <div class="col-md-6">
                <h6><strong>Factura:</strong> ${factura.factura_numero}</h6>
                <p><strong>Cliente:</strong> ${factura.cliente_nombre}</p>
                <p><strong>NIT:</strong> ${factura.cliente_nit}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Fecha:</strong> ${new Date(factura.factura_fecha).toLocaleDateString('es-GT')}</p>
                <p><strong>Total:</strong> Q${parseFloat(factura.factura_total).toFixed(2)}</p>
            </div>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    detalle.forEach(item => {
        detalleHTML += `
            <tr>
                <td>${item.producto_nombre}</td>
                <td>Q${parseFloat(item.detalle_precio_unitario).toFixed(2)}</td>
                <td>${item.detalle_cantidad}</td>
                <td>Q${parseFloat(item.detalle_subtotal).toFixed(2)}</td>
            </tr>
        `;
    });
    
    detalleHTML += `
            </tbody>
        </table>
    `;
    
    contenido.innerHTML = detalleHTML;
    
    const modal = new Modal(document.getElementById('ModalDetalleFactura'));
    modal.show();
}

const anularFactura = async (facturaId) => {
    const confirmacion = await Swal.fire({
        title: '¿Anular factura?',
        text: 'Esta acción devolverá el stock y no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, anular',
        cancelButtonText: 'Cancelar'
    });
    
    if (confirmacion.isConfirmed) {
        try {
            const respuesta = await fetch(`/carrito_avpc2/facturas/anular?id=${facturaId}`);
            const datos = await respuesta.json();
            
            if (datos.codigo == 1) {
                Swal.fire({
                    icon: 'success',
                    title: 'Factura anulada',
                    text: datos.mensaje
                });
                CargarFacturas();
                CargarProductosDisponibles(); // Actualizar stock
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: datos.mensaje
                });
            }
        } catch (error) {
            console.error('Error al anular factura:', error);
        }
    }
}

// Event listeners
document.addEventListener('click', (e) => {
    if (e.target.closest('.agregar-carrito')) {
        const productoId = e.target.closest('.agregar-carrito').dataset.id;
        agregarAlCarrito(productoId);
    }
    
    if (e.target.closest('.btn-cantidad')) {
        const button = e.target.closest('.btn-cantidad');
        const index = parseInt(button.dataset.index);
        const accion = button.dataset.accion;
        modificarCantidadCarrito(index, accion);
    }
    
    if (e.target.closest('.eliminar-item')) {
        const index = parseInt(e.target.closest('.eliminar-item').dataset.index);
        eliminarDelCarrito(index);
    }
    
    if (e.target.closest('.ver-detalle')) {
        const facturaId = e.target.closest('.ver-detalle').dataset.id;
        verDetalleFactura(facturaId);
    }
    
    if (e.target.closest('.anular-factura')) {
        const facturaId = e.target.closest('.anular-factura').dataset.id;
        anularFactura(facturaId);
    }
});

BtnProcesarVenta.addEventListener('click', procesarVenta);
BtnLimpiarCarrito.addEventListener('click', limpiarCarrito);
BtnVerFacturas.addEventListener('click', () => {
    SeccionFacturas.style.display = SeccionFacturas.style.display === 'none' ? 'block' : 'none';
    if (SeccionFacturas.style.display === 'block') {
        CargarFacturas();
    }
});


CargarClientes();
CargarProductosDisponibles();