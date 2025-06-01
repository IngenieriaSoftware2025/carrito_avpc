create database carrito

CREATE TABLE categorias (
    categoria_id SERIAL PRIMARY KEY,
    categoria_nombre VARCHAR(50) NOT NULL,
    categoria_situacion SMALLINT DEFAULT 1
);


CREATE TABLE productos (
    producto_id SERIAL PRIMARY KEY,
    producto_nombre VARCHAR(100) NOT NULL,
    producto_precio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    producto_stock INT NOT NULL DEFAULT 0,
    producto_categoria_id INT NOT NULL,
    producto_situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (producto_categoria_id) REFERENCES categorias(categoria_id)
);

CREATE TABLE clientes (
    cliente_id SERIAL PRIMARY KEY,
    cliente_nombre VARCHAR(100) NOT NULL,
    cliente_nit VARCHAR(20),
    cliente_direccion VARCHAR(200),
    cliente_telefono VARCHAR(15),
    cliente_situacion SMALLINT DEFAULT 1
);

CREATE TABLE facturas (
    factura_id SERIAL PRIMARY KEY,
    factura_numero VARCHAR(20) UNIQUE NOT NULL,
    factura_cliente_id INT NOT NULL,
    factura_fecha DATETIME YEAR TO MINUTE,
    factura_subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    factura_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    factura_situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (factura_cliente_id) REFERENCES clientes(cliente_id)
);

CREATE TABLE factura_detalle (
    detalle_id SERIAL PRIMARY KEY,
    detalle_factura_id INT NOT NULL,
    detalle_producto_id INT NOT NULL,
    detalle_cantidad INT NOT NULL,
    detalle_precio_unitario DECIMAL(10,2) NOT NULL,
    detalle_subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (detalle_factura_id) REFERENCES facturas(factura_id),
    FOREIGN KEY (detalle_producto_id) REFERENCES productos(producto_id)
);

INSERT INTO categorias (categoria_nombre) VALUES 
('Alimentos');
INSERT INTO categorias (categoria_nombre) VALUES 
('Lipmieza');
INSERT INTO categorias (categoria_nombre) VALUES 
('Hogar');
INSERT INTO categorias (categoria_nombre) VALUES 
('Tecnología');
INSERT INTO categorias (categoria_nombre) VALUES 
('ropa');


INSERT INTO productos (producto_nombre, producto_precio, producto_stock, producto_categoria_id) VALUES 
('Arroz 1lb', 5.50, 100, 1);
INSERT INTO productos (producto_nombre, producto_precio, producto_stock, producto_categoria_id) VALUES
('Frijol 1lb', 6.00, 80, 1);
INSERT INTO productos (producto_nombre, producto_precio, producto_stock, producto_categoria_id) VALUES
('Aceite de cocina', 15.75, 50, 1);
INSERT INTO productos (producto_nombre, producto_precio, producto_stock, producto_categoria_id) VALUES
('Detergente Ariel', 25.50, 30, 2);
INSERT INTO productos (producto_nombre, producto_precio, producto_stock, producto_categoria_id) VALUES
('Cloro', 8.25, 40, 2);
INSERT INTO productos (producto_nombre, producto_precio, producto_stock, producto_categoria_id) VALUES
('Escoba', 35.00, 20, 3);
INSERT INTO productos (producto_nombre, producto_precio, producto_stock, producto_categoria_id) VALUES
('Smartphone Samsung', 2500.00, 5, 4);
INSERT INTO productos (producto_nombre, producto_precio, producto_stock, producto_categoria_id) VALUES
('Camiseta básica', 75.00, 25, 5);


INSERT INTO clientes (cliente_nombre, cliente_nit, cliente_direccion, cliente_telefono) VALUES 
('Cliente Genérico', 'C/F', 'Ciudad de Guatemala', '2200-0000');
INSERT INTO clientes (cliente_nombre, cliente_nit, cliente_direccion, cliente_telefono) VALUES
('María García', '123456-7', 'Zona 1, Guatemala', '5555-1234');
INSERT INTO clientes (cliente_nombre, cliente_nit, cliente_direccion, cliente_telefono) VALUES
('Juan Pérez', '987654-3', 'Zona 10, Guatemala', '5555-5678');