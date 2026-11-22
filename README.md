# SPARKLE · Aplicación Web de Aromas de Autor & Bienestar

Aplicación web desarrollada en **Laravel 12 / PHP 8.5** sobre **Ubuntu (WSL)** con soporte y gestión de contenedores y bases de datos a través de **Lando**.

Diseñada y construida siguiendo estrictamente la arquitectura de negocio, roles, flujos y estados de interfaz definidos en los documentos técnicos de SPARKLE.

---

## 🏛️ 1. Arquitectura y Roles del Sistema

| Rol | Objetivo | Acceso y Permisos |
| :--- | :--- | :--- |
| **Visitante sin registro** | Explorar la marca, entender el kit y decidir si compra. | Inicio, Catálogo, Ficha de producto, Carrito (invitado), Guía de aromas interactiva, Suscripción a la comunidad y Solicitud de aviso si un producto está agotado. *Restricción: No puede pagar sin identificarse.* |
| **Cliente registrado** | Comprar fácil, repetir su compra y acumular beneficios. | Todo lo del visitante + Checkout y pago, Mis pedidos y seguimiento en vivo, Mis puntos y recompensas (Loyalty Club), Recompra de Refills, Repetición de pedidos y Solicitud de cambio/garantía. |
| **Colaborador de operación** *(Fase 2)* | Preparar y despachar pedidos sin ver información comercial sensible. | Panel operativo, Inventario por producto, Pedidos (cambiar a «Enviado» registrando transportadora y guía). *Restricción: Oculta precios, costos y totales financieros. Solo ve nombre y dirección de entrega.* |
| **Administrador** *(Dueña de la marca)* | Operar la tienda, inventario correcto, catálogo vigente y fidelización. | Panel de control completo con KPIs, Inventario con umbrales mínimos y alertas, Catálogo y precios (CRUD), Gestión de pedidos y anulación con auditoría, Moderación de reseñas, Gestión de usuarios y roles. |

---

## ⚡ 2. Módulos y Estados de Interfaz Implementados

### ✦ Checkout (4 Estados y Variantes de Error)
* **1 · Reposo (Idle):** Formulario listo con datos del cliente precargados (nombre, WhatsApp, dirección principal), cálculo de envío y puntos a acumular (`+[X] puntos`). Foco automático en el primer campo vacío.
* **2 · Espera (Loading):** Barra de progreso superior animada, campos bloqueados y botón con spinner de carga: *"Procesando tu pago… No cierres esta ventana"*.
* **3 · Éxito (Success):** Alerta verde con check, número de pedido `SPK-YYYY-XXXX`, resumen de ítems, puntos pendientes de acreditar y botones hacia *"Ver mi pedido"* o *"Seguir explorando"*.
* **4 · Error:** Banner rojo con conteo de errores, campos afectados resaltados en rojo con mensajes específicos (*"Falta tu dirección"*, *"Faltan dígitos (10)"*), conservación de datos escritos y foco al primer error.
* **Variante Pago Rechazado:** Aviso de no cobro bancario, stock reservado y botones de reintento o cambio de medio de pago.
* **Variante Stock Insuficiente:** Aviso de cambio de disponibilidad con retorno al carrito.

### ✦ Ajustar Stock de Inventario (4 Estados)
* **1 · Reposo:** Vista de disponibles, reservados, umbral mínimo y estado (`Stock bajo` vs `Stock al día`). Formulario con tipo (entrada, merma, ajuste), cantidad y motivo.
* **2 · Espera:** Botón con spinner *"Guardando…"*.
* **3 · Éxito:** Banner verde de confirmación con delta de unidades (`Pasó de X a Y unidades`) y **notificación automática a los clientes suscritos en lista de espera**.
* **4 · Error:** Validación de cantidad mayor a cero y motivo obligatorio.

### ✦ Flujo de Fidelización y Entrega
1. La compra genera un movimiento de puntos en estado **Pendiente**.
2. Cuando el Administrador o Colaborador despacha y marca el pedido como **Entregado**, el sistema:
   - Cambia el movimiento de puntos a **Acreditado**.
   - Suma los puntos al saldo disponible del cliente.
   - Habilita la solicitud de **Reseña de producto** y el acceso directo a **Recompra de Refill**.

---

## 🚀 3. Instrucciones de Ejecución

### Opción A: Ejecución Directa en WSL (Ubuntu)
La base de datos SQLite ya se encuentra migrada y sembrada con datos de demostración:

```bash
# Iniciar servidor local
php artisan serve
```
Abre en tu navegador: [http://localhost:8000](http://localhost:8000)

### Opción B: Ejecución con Lando (MySQL & phpMyAdmin)
El proyecto incluye el archivo `.lando.yml` configurado con la receta oficial `laravel`, base de datos MySQL 8.0, Redis y el servicio gráfico de **phpMyAdmin**:

```bash
# Iniciar contenedores con Lando
lando start

# Ejecutar migraciones en MySQL de Lando
lando artisan migrate:fresh --seed
```

* **URL de la Aplicación en Lando:** `http://sparkle-app-w.lndo.site` (o por proxy localhost)
* **Manejo de Base de Datos (phpMyAdmin):** `http://localhost:8082` o `http://pma.sparkle-app-w.lndo.site`
  * Servidor: `database`
  * Usuario: `laravel`
  * Contraseña: `laravel`
  * Base de datos: `laravel`

---

## 🔑 4. Cuentas de Acceso de Demostración

La aplicación incluye un **Barra de Acceso Rápido Demo** en la parte superior que permite cambiar de rol con un solo clic. También puedes iniciar sesión manualmente con:

| Rol | Correo | Contraseña | Perfil |
| :--- | :--- | :--- | :--- |
| **Cliente** | `cliente@sparkle.com` | `password` | María Torres (150 pts acumulados, 1 pedido entregado y 1 en proceso) |
| **Colaborador** | `operaciones@sparkle.com` | `password` | Carlos Operaciones (Vista operativa de pedidos e inventario) |
| **Administrador** | `admin@sparkle.com` | `password` | Laura Guzmán (Control total, reportes, precios y usuarios) |

---

## 🧪 5. Pruebas Automatizadas

El proyecto incluye una suite completa de pruebas unitarias y de integración que validan los flujos:

```bash
php artisan test
```

* 10 tests pasados al 100% (32 aserciones).
* Formato de código verificado con Laravel Pint (`vendor/bin/pint`).
