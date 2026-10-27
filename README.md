# 🏢 CondominioApp - Sistema Integral de Gestión Residencial

**CondominioApp** es una solución web empresarial completa, moderna y altamente optimizada para la administración y convivencia de condominios, edificios y conjuntos residenciales.

Desarrollado con **Laravel 12 / PHP 8.3+**, **PostgreSQL** (con estricta integridad referencial y foreign keys), **Blade Templates**, **Bootstrap 5.3**, **Chart.js** y almacenamiento público seguro mediante `storage:link`.

---

## 🚀 Características Principales y Módulos

### 1. 🛡️ Autenticación y Control de Acceso por Roles (Middleware)
- **Administrador:** Control total de finanzas, departamentos, residentes, expensas, áreas comunes y gastos.
- **Residente / Propietario:** Portal autoservicio con consulta de su unidad, estado de cuenta, historial de expensas, botón de pago con subida de comprobante y reserva de espacios comunes.
- **Acceso Rápido Demo en Login:** Botones de un solo clic para alternar entre perfil Administrador y Residente de pruebas.

### 2. 🚪 Módulo de Departamentos (Unidades Habitacionales)
- Campos: `id`, `numero_departamento`, `piso`, `bloque`, `propietario_id`.
- Validación estricta que previene duplicados del mismo número de unidad dentro del mismo bloque (`unique(['numero_departamento', 'bloque'])`).
- Integridad referencial en PostgreSQL: Protege contra borrados accidentales si existen expensas o habitantes vinculados.

### 3. 👥 Módulo de Residentes y Propietarios
- Campos: `id`, `name`, `email` (único), `ci` (Carnet de identidad único), `telefono`, `departamento_id`, `rol`, `activo`.
- Prevención estricta de duplicados mediante reglas de validación en backend e índices `UNIQUE` a nivel de base de datos.
- Expediente 360° del residente: Muestra su unidad habitacional, inmuebles en propiedad, historial de pagos y reservas.

### 4. 💳 Módulo Financiero (Expensas y Cuotas Mensuales)
- Campos: `id`, `departamento_id`, `mes`, `anio`, `monto`, `estado` (`pendiente`, `pagado`, `atrasado`), `fecha_pago`, `comprobante_imagen`, `metodo_pago`, `notas`.
- **Emisión Masiva de Expensas:** Genera con un solo clic las cuotas de todos los departamentos habitados para un mes y año determinado, omitiendo automáticamente las ya existentes.
- **Registro de Pagos y Comprobantes:** Los residentes pueden subir fotos o PDFs de sus recibos bancarios a la carpeta pública (`storage/comprobantes`), visualizables por la administración.
- **Badges de Color:** Estados visuales inmediatos (Verde = Pagado, Amarillo = Pendiente, Rojo = Atrasado/En mora).

### 5. 🏊 Módulo de Áreas Comunes y Reservas
- **Áreas Comunes:** `id`, `nombre`, `descripcion`, `imagen` (alojada en `storage/areas_comunes`), `capacidad`, `costo_reserva`, `normas`, `activo`.
- **Reservas:** `id`, `area_comun_id`, `residente_id`, `fecha_reserva`, `hora_inicio`, `hora_fin`, `estado` (`confirmada`, `cancelada`), `motivo`.
- **Algoritmo Anti-Solapamiento:** Valida matemáticamente que no existan reservas cruzadas en el mismo espacio y fecha:
  `(hora_inicio < nueva_hora_fin AND hora_fin > nueva_hora_inicio)`.

### 6. 📊 Módulo de Gastos y Egresos (Transparencia Financiera)
- Campos: `id`, `descripcion`, `monto`, `categoria` (mantenimiento, servicios, reparaciones, seguridad, limpieza, otros), `fecha`, `comprobante`, `registrado_por`.
- **Balance Financiero en Tiempo Real:** Total Recaudado por Expensas Pagadas vs. Total Egresos = Saldo Neto en Caja.
- Gráfico interactivo con **Chart.js** en el Dashboard con el flujo semestral de ingresos vs egresos.

---

## 🔑 Credenciales de Acceso (Datos Demo Seeders)

| Rol | Correo Electrónico | Contraseña | CI | Departamento |
|---|---|---|---|---|
| **Administrador** | `admin@condominio.com` | `admin123` | `1000001` | Administración |
| **Residente** | `carlos@condominio.com` | `residente123` | `7890123` | Bloque A - Piso 1 - Depto 101 |
| **Residente** | `maria@condominio.com` | `residente123` | `6543210` | Bloque A - Piso 1 - Depto 102 |
| **Residente** | `roberto@condominio.com` | `residente123` | `5432109` | Bloque A - Piso 2 - Depto 201 |

---

## 🛠️ Requisitos e Instalación

### Requisitos:
- **PHP:** 8.3 o superior (con extensiones `pdo_pgsql`, `pgsql`, `zip`, `mbstring`, `openssl`).
- **PostgreSQL:** Versión 14 a 18+.
- **Composer:** 2.x.

### Configuración de Base de Datos (`.env`):
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=condominio
DB_USERNAME=postgres
DB_PASSWORD=tu_password
```

### Comandos de Inicialización:
```bash
# 1. Enlazar disco de almacenamiento para comprobantes y fotos de áreas comunes
php artisan storage:link

# 2. Ejecutar migraciones e insertar datos demo completos
php artisan migrate:fresh --seed

# 3. Iniciar el servidor local
php artisan serve
```
El sistema estará accesible en: `http://127.0.0.1:8000`.

---

## 🧪 Pruebas Automatizadas
Se incluye una suite de pruebas Feature para verificar la integridad del sistema:
```bash
php artisan test --filter=CondominioSystemTest
```
Valida:
- Redirección de invitados al login.
- Acceso del administrador a todos los módulos.
- Bloqueo y redirección de residentes intentando acceder a rutas protegidas.
- Algoritmo de detección y prevención de solapamiento de horarios en reservas.
- Validación y prevención de duplicados de CI en residentes.
