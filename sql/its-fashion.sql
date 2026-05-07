

CREATE DATABASE IF NOT EXISTS `its-fashion`;
USE `its-fashion`;

-- TABLA ROL
CREATE TABLE IF NOT EXISTS rol (
id_rol INT AUTO_INCREMENT PRIMARY KEY,
descripcion VARCHAR(50)
);

-- Insertar roles por defecto
INSERT IGNORE INTO rol (id_rol, descripcion) VALUES (1,'Administrador'),(2,'Empleado'),(3,'Cliente');

-- TABLA USUARIO
CREATE TABLE IF NOT EXISTS usuario (
id_usuario INT AUTO_INCREMENT PRIMARY KEY,
nombre VARCHAR(100) NOT NULL,
apellido VARCHAR(100) NOT NULL,
username VARCHAR(50) UNIQUE NOT NULL,
telefono VARCHAR(15) NOT NULL,
password VARCHAR(255) NOT NULL,
email VARCHAR(100) UNIQUE NOT NULL,
id_rol INT NOT NULL,
estado ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (id_rol) REFERENCES rol(id_rol)
);


-- TABLA PROVEEDOR
CREATE TABLE proveedor (
id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
nombre VARCHAR(100) NOT NULL,
contacto VARCHAR(100) NOT NULL,
telefono VARCHAR(15) NOT NULL,
email VARCHAR(100),
direccion VARCHAR(200),
documento VARCHAR(20) UNIQUE NOT NULL
);

-- TABLA CATEGORIA
CREATE TABLE categoria (
id_categoria INT AUTO_INCREMENT PRIMARY KEY,
nombre VARCHAR(50) NOT NULL,
descripcion TEXT
);

-- TABLA PRODUCTOS
CREATE TABLE productos (
id_producto INT AUTO_INCREMENT PRIMARY KEY,
nombre VARCHAR(100) NOT NULL,
descripcion TEXT,
precio_venta DECIMAL(10,2) NOT NULL,
precio_compra DECIMAL(10,2) NOT NULL,
stock INT NOT NULL,
stock_minimo INT,
talla VARCHAR(10) NOT NULL,
color VARCHAR(30) NOT NULL,
estado ENUM('Activo','Inactivo'),
id_categoria INT,
FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria)
);

-- TABLA COMPRAS
CREATE TABLE compras (
id_compra INT AUTO_INCREMENT PRIMARY KEY,
fecha DATETIME,
total DECIMAL(10,2) NOT NULL,
id_proveedor INT,
id_usuario INT,
FOREIGN KEY (id_proveedor) REFERENCES proveedor(id_proveedor),
FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

-- TABLA DETALLE COMPRA
CREATE TABLE detallecompra (
id_detalle INT AUTO_INCREMENT PRIMARY KEY,
id_compra INT,
id_producto INT,
cantidad INT NOT NULL,
precio_unitario DECIMAL(10,2) NOT NULL,
subtotal DECIMAL(10,2) NOT NULL,
FOREIGN KEY (id_compra) REFERENCES compras(id_compra),
FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

-- TABLA VENTA
CREATE TABLE venta (
id_venta INT AUTO_INCREMENT PRIMARY KEY,
fecha DATETIME,
total DECIMAL(10,2) NOT NULL,
id_cliente INT,
metodo_pago ENUM('Efectivo','Transferencia bancaria'),
estado ENUM('Completada','Cancelada'),
id_usuario INT,
FOREIGN KEY (id_cliente) REFERENCES usuario(id_usuario),
FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

-- TABLA DETALLE VENTA
CREATE TABLE detalle_venta (
id_detalle INT AUTO_INCREMENT PRIMARY KEY,
id_venta INT,
id_producto INT,
cantidad INT NOT NULL,
precio_unitario DECIMAL(10,2) NOT NULL,
FOREIGN KEY (id_venta) REFERENCES venta(id_venta),
FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

-- TABLA DEVOLUCIONES
CREATE TABLE devoluciones (
id_devolucion INT AUTO_INCREMENT PRIMARY KEY,
id_venta INT,
fecha DATETIME,
motivo TEXT,
total_devolucion DECIMAL(10,2) NOT NULL,
id_usuario INT,
estado ENUM('Pendiente','Aceptada','Rechazada') NOT NULL DEFAULT 'Pendiente',
fecha_resolucion DATETIME NULL,
id_admin INT NULL,
FOREIGN KEY (id_venta) REFERENCES venta(id_venta),
FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
FOREIGN KEY (id_admin) REFERENCES usuario(id_usuario)
);


-- TABLA DETALLE DEVOLUCIONA
CREATE TABLE detalledevolucion (
id_detalle INT AUTO_INCREMENT PRIMARY KEY,
id_devolucion INT,
id_producto INT,
cantidad INT NOT NULL,
precio_unitario DECIMAL(10,2) NOT NULL,
FOREIGN KEY (id_devolucion) REFERENCES devoluciones(id_devolucion),
FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

-- TABLA INVENTARIO
CREATE TABLE inventario (
id_inventario INT AUTO_INCREMENT PRIMARY KEY,
fecha_registro DATETIME,
stock_disponible INT NOT NULL,
tipo_movimiento ENUM('Entrada','Salida','Ajuste'),
id_producto INT,
FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

-- TABLA CAJA
CREATE TABLE caja (
id_caja INT AUTO_INCREMENT PRIMARY KEY,
total_ingresos DECIMAL(10,2),
total_egresos DECIMAL(10,2),
fecha_apertura DATETIME,
fecha_cierre DATETIME,
saldo_inicial DECIMAL(10,2) NOT NULL,
saldo_final DECIMAL(10,2),
diferencia DECIMAL(10,2),
justificacion TEXT,
estado ENUM('Abierta','Cerrada'),
id_usuario INT,
FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

-- TABLA MOVIMIENTOS CAJA
CREATE TABLE movimientos_caja (
id_movimiento INT AUTO_INCREMENT PRIMARY KEY,
id_caja INT,
tipo ENUM('Ingreso','Egreso'),
monto DECIMAL(10,2) NOT NULL,
concepto VARCHAR(200),
fecha DATETIME,
FOREIGN KEY (id_caja) REFERENCES caja(id_caja)
);
