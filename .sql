create database bd_perez

CREATE TABLE clientes (
    cliente_id SERIAL PRIMARY KEY,
    cliente_nombres VARCHAR(255),
    cliente_apellidos VARCHAR(255),
    cliente_nit INT,
    cliente_telefono INT,
    cliente_correo VARCHAR(100),
    cliente_estado CHAR(1),
    cliente_fecha DATETIME YEAR TO MINUTE,
    cliente_situacion SMALLINT DEFAULT 1
);


CREATE TABLE productos (
    producto_id SERIAL PRIMARY KEY,
    producto_nombre VARCHAR(255),
    producto_descripcion VARCHAR(255),
    producto_precio DECIMAL(10,2),
    producto_cantidad INT,
    producto_situacion SMALLINT DEFAULT 1
);

create TABLE ventas (
    venta_id SERIAL PRIMARY KEY,
    venta_cliente_id INT,
    venta_fecha DATETIME YEAR TO MINUTE,
    venta_subtotal DECIMAL(10,2),
    venta_total DECIMAL(10,2),
    venta_situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (venta_cliente_id) REFERENCES clientes(cliente_id)
);


CREATE TABLE venta_detalles (
    detalle_id SERIAL PRIMARY KEY,
    detalle_venta_id INT,
    detalle_producto_id INT,
    detalle_cantidad INT,
    detalle_precio_unitario DECIMAL(10,2),
    detalle_subtotal DECIMAL(10,2),
    FOREIGN KEY (detalle_venta_id) REFERENCES ventas(venta_id),
    FOREIGN KEY (detalle_producto_id) REFERENCES productos(producto_id)
);
