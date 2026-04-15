# NATADINATTA

Sistema web de facturación, ventas, clientes, cartera e inventario desarrollado en Laravel para la asignatura de Ingeniería de Software.

## Descripción

NATADINATTA permite:

- iniciar sesión con usuarios internos
- registrar clientes
- crear pedidos de venta
- confirmar pedidos como facturas
- registrar, editar y eliminar abonos
- administrar inventario básico
- consultar historial por cliente
- revisar cuentas por cobrar
- exportar reportes y cartera

El sistema está pensado para una empresa única y trabaja con moneda `COP`.

## Módulos principales

### 1. Autenticación y usuarios

- inicio de sesión con correo y contraseña
- roles `admin` y `empleado`
- administración de usuarios por parte del administrador

### 2. Clientes

- registro y edición de clientes
- datos de contacto, ciudad y dirección
- historial de compras, facturas, abonos y saldo pendiente

### 3. Ventas y facturación

- creación de pedidos
- selección manual o desde inventario
- confirmación como factura
- cancelación controlada
- vista imprimible y PDF de factura

### 4. Abonos

- registro de múltiples abonos por factura
- edición y eliminación controlada
- recálculo automático de saldo pendiente

### 5. Inventario

- productos con nombre, SKU, precio y stock
- descuento automático de stock al confirmar factura
- reposición al cancelar o eliminar ventas confirmadas

### 6. Reportes y cartera

- reportes de ventas por período
- productos más vendidos
- cuentas por cobrar globales
- exportación a Excel y PDF

## Flujo principal

1. El usuario inicia sesión.
2. Registra o selecciona un cliente.
3. Crea un pedido con productos.
4. Confirma el pedido como factura.
5. Registra uno o varios abonos.
6. Consulta saldo, historial y reportes.

## Roles

### Administrador

- administra usuarios
- administra inventario
- configura la empresa
- consulta reportes y cuentas por cobrar
- puede cancelar ventas
- puede eliminar clientes, productos, facturas y ventas

### Empleado

- registra clientes
- crea y confirma sus propios pedidos
- registra abonos
- solo consulta sus propias ventas
- no puede cancelar ventas ni eliminar clientes

## Política operativa

- `Cancelar` conserva el registro histórico de la venta y, si aplica, marca la factura como cancelada.
- `Eliminar factura` quita la factura y sus abonos, pero conserva el pedido para poder rehacerlo.
- `Eliminar venta` borra el pedido, la factura asociada y los abonos relacionados de la base de datos.
- `Eliminar producto` solo está permitido cuando ese producto no ha sido usado en ventas previas.

Esta política busca equilibrar flexibilidad administrativa con protección del historial.

## Tecnologías

- PHP 8.4
- Laravel 13
- Blade
- Bootstrap 5
- MySQL / MariaDB

## Base de datos

El proyecto está configurado para usar:

- conexión: `mysql`
- base de datos: `inventario-nadinatta`

## Instalación rápida

1. Configurar la base de datos en `.env`.
2. Crear la base `inventario-nadinatta`.
3. Ejecutar migraciones y seeders:

```powershell
C:\Users\jarav\.config\herd\bin\php84\php.exe artisan migrate:fresh --seed
```

4. Levantar el servidor:

```powershell
C:\Users\jarav\.config\herd\bin\php84\php.exe artisan serve
```

## Acceso de prueba

- Administrador: `admin@natadinatta.com`
- Empleado: `empleado@natadinatta.com`
- Contraseña: `password`

## Estado actual

El sistema ya incluye:

- autenticación
- roles
- clientes
- ventas
- facturación
- abonos
- inventario
- historial por cliente
- cuentas por cobrar
- reportes
- exportaciones

## Pruebas

Ejecutar:

```powershell
C:\Users\jarav\.config\herd\bin\php84\php.exe artisan test
```

## Autoría

Proyecto académico de Ingeniería de Software adaptado e implementado sobre Laravel para NATADINATTA.
