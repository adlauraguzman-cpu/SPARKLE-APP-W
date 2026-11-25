# SPARKLE · Mi Espacio Vitamina

Aplicación web de bienestar olfativo: calcula fórmulas aromáticas personalizadas y seguras a partir de las esencias **ALPHA**, **HELIOS** y **JAZ**, permite programar rutinas y registrar en un diario cómo cambia el estado de ánimo. No es una tienda: no vende ni procesa pagos.

**Tecnologías:** Laravel 13 · PHP 8.4 · MySQL 8.0 (Lando) o SQLite · Blade, Tailwind CSS y Alpine.js (por CDN) · Ubuntu (WSL).

---

## 1. Problema y propuesta de valor

Quien usa aromas en casa suele elegirlos sin saber cuál conviene a su objetivo ni si es seguro ante alergias, mascotas o embarazo. SPARKLE guía esa decisión en tres pilares:

| Pilar | Qué hace la persona usuaria |
| :--- | :--- |
| **1. Conocerme** | Registra su perfil sensorial, de seguridad y sus espacios. |
| **2. Mi Fórmula** | Calcula una mezcla según objetivo, momento, intensidad, espacio y emoción. |
| **3. Mi Rutina y Mi Diario** | Programa rituales, sigue su racha y califica cada sesión. |

## 2. Roles del sistema

| Rol | Permisos |
| :--- | :--- |
| **Invitado** | Ve el inicio y el catálogo de aromas, y calcula fórmulas sin cuenta. No puede guardarlas. |
| **Usuario** | Completa su perfil, guarda fórmulas, crea rutinas, usa el reproductor de ritual y lleva su diario. |
| **Especialista en aromas** | Crea reglas de afinidad (peso de 0 a 3) y aprueba las de otro especialista (*Maker-Checker*: nadie aprueba su propia regla). Ve métricas agregadas. |
| **Administrador** | Gestiona roles y estado de los usuarios, revisa consentimientos y auditoría, y exporta datos de un usuario. No tiene acceso a los diarios privados. |

## 3. Motor de fórmulas

[`FormulaEngineService`](app/Services/FormulaEngineService.php) trabaja en cinco pasos:

1. **Seguridad:** descarta los aromas contraindicados por alergias, mascotas o embarazo y lactancia.
2. **Afinidad:** suma los pesos de las reglas publicadas (objetivo, espacio, momento y emoción).
3. **Ajuste personal:** considera las familias preferidas o evitadas y las calificaciones del diario.
4. **Proporciones:** descarta los aportes menores al 10 % y ajusta el total a 100 %.
5. **Ritual:** sugiere gotas, duración y pasos, con una explicación de la mezcla.

## 4. Estados de interfaz

El configurador, el diario y el panel del especialista muestran cuatro estados: **reposo**, **carga**, **éxito** y **error**. El configurador distingue además tres errores: *faltan respuestas*, *sin fórmula segura* y *falla de cálculo*.

## 5. Base de datos

```text
users ─┬─ consentimientos
       ├─ perfiles_sensoriales
       ├─ perfiles_seguridad
       ├─ espacios
       ├─ registros_animo
       ├─ sesiones ── formulas ── componentes_formula ── aromas ── reglas_afinidad
       ├─ rutinas ── recordatorios
       ├─ entradas_diario
       └─ registros_cambios (auditoría)
```

## 6. Diseño visual

| Elemento | Valor |
| :--- | :--- |
| Fondo claro | `#FAF8F5` |
| Ámbar vitamina | `#B88B57` |
| Botones principales | `#9B6F3E` |
| Verde salvia | `#5F8565` |
| Tipografía de texto | Plus Jakarta Sans |
| Tipografía de acento | Playfair Display |

El diseño es responsivo y en pantallas pequeñas usa un menú desplegable.

---

## 7. Ejecución con Lando (recomendada)

Requisitos: Ubuntu (WSL), Docker Desktop, Lando y conexión a internet.

**Primera vez** (después de clonar el repositorio):

```bash
cp .env.example .env
lando start
lando composer install
lando artisan key:generate
lando artisan migrate --seed
```

**Las veces siguientes:**

```bash
lando start
```

| Servicio | Dirección |
| :--- | :--- |
| Aplicación | http://sparkle-app-w.lndo.site |
| phpMyAdmin | http://pma.sparkle-app-w.lndo.site |

En Lando la aplicación usa MySQL (servidor `database`; usuario, contraseña y base: `laravel`). Esa configuración está en [`.lando.yml`](.lando.yml) y tiene prioridad sobre `.env`.

Para restaurar los datos de demostración (**borra lo registrado**):

```bash
lando artisan migrate:fresh --seed
```

### Solución de problemas

* **`is not connected to the network lando_bridge_network`:**
  ```bash
  docker network create lando_bridge_network
  docker network connect lando_bridge_network landoproxyhyperion5000gandalfedition_proxy_1
  lando restart
  ```
* **Mensajes `Healthcheck ... database FAILED` durante el arranque:** son normales mientras MySQL inicia.

## 8. Ejecución sin Lando (SQLite)

Requiere PHP 8.4.1 o superior y Composer.

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

Abre http://localhost:8000.

---

## 9. Cuentas de demostración

La barra **Modo demo**, en la parte superior, cambia de rol con un clic. También puedes iniciar sesión manualmente (contraseña de todas: `password`).

| Rol | Correo | Nombre |
| :--- | :--- | :--- |
| Usuario | `usuario@sparkle.com` | María Torres |
| Especialista (autor) | `especialista1@sparkle.com` | Dr. Carlos Especialista |
| Especialista (revisor) | `especialista2@sparkle.com` | Dra. Elena Revisor |
| Administrador | `admin@sparkle.com` | Laura Administradora |

## 10. Pruebas

```bash
lando artisan test    # o: php artisan test
```

Las pruebas usan una base SQLite en memoria, así que no modifican los datos de la aplicación.
