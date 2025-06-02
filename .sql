
create database perez

CREATE TABLE clientes (
    cliente_id SERIAL PRIMARY KEY,
    cliente_nombre VARCHAR(100) NOT NULL,
    cliente_apellido VARCHAR(100) NOT NULL,
    cliente_nit VARCHAR(20) UNIQUE,
    cliente_email VARCHAR(100),
    cliente_telefono VARCHAR(20),
    cliente_direccion TEXT,
    cliente_situacion SMALLINT DEFAULT 1
);


CREATE TABLE productos (
    producto_id SERIAL PRIMARY KEY,
    producto_nombre VARCHAR(100) NOT NULL,
    producto_precio DECIMAL(10,2) NOT NULL,
    producto_stock INT NOT NULL DEFAULT 0,
    producto_descripcion TEXT,
    producto_situacion SMALLINT DEFAULT 1
);


CREATE TABLE ventas (
    venta_id SERIAL PRIMARY KEY,
    venta_cliente_id INT NOT NULL,
    venta_fecha DATETIME year to minute,
    venta_total DECIMAL(10,2) NOT NULL DEFAULT 0,
    venta_situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (venta_cliente_id) REFERENCES clientes(cliente_id)
);


CREATE TABLE detalle_ventas (
    detalle_id SERIAL PRIMARY KEY,
    detalle_venta_id INT NOT NULL,
    detalle_producto_id INT NOT NULL,
    detalle_cantidad INT NOT NULL,
    detalle_precio_unitario DECIMAL(10,2) NOT NULL,
    detalle_subtotal DECIMAL(10,2) NOT NULL,
    detalle_situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (detalle_venta_id) REFERENCES ventas(venta_id),
    FOREIGN KEY (detalle_producto_id) REFERENCES productos(producto_id)
);



--- Insertar clientes de prueba
INSERT INTO clientes (cliente_nombre, cliente_apellido, cliente_nit, cliente_email) VALUES 
('Juan', 'Pérez', '12345678-9', 'juan@email.com');
INSERT INTO clientes (cliente_nombre, cliente_apellido, cliente_nit, cliente_email) VALUES 
('María', 'García', '98765432-1', 'maria@email.com');
INSERT INTO clientes (cliente_nombre, cliente_apellido, cliente_nit, cliente_email) VALUES 
('Carlos', 'López', 'CF', 'carlos@email.com');

--- Insertar productos de prueba
INSERT INTO productos (producto_nombre, producto_precio, producto_stock) VALUES 
('Laptop HP', 8500.00, 10);
INSERT INTO productos (producto_nombre, producto_precio, producto_stock) VALUES 
('Mouse Inalámbrico', 150.00, 25);
INSERT INTO productos (producto_nombre, producto_precio, producto_stock) VALUES 
('Teclado Mecánico', 350.00, 15);
INSERT INTO productos (producto_nombre, producto_precio, producto_stock) VALUES 
('Monitor 24"', 1200.00, 8);