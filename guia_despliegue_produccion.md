# 🚀 Guía Maestra de Despliegue en Producción (Docker)

Esta guía detalla el proceso exacto para subir la aplicación **Incidencias Modern** a un servidor de producción, asegurando que el motor de tiempo real (Reverb), el chat y el modo mantenimiento funcionen a la perfección desde cualquier dispositivo móvil o computadora.

---

## 📂 1. Preparación de Archivos y Permisos (Host Linux)

Antes de levantar los contenedores, el sistema operativo del servidor debe dar permiso a Docker para escribir en las carpetas de Laravel.

### Permisos Críticos
Ejecuta estos comandos en la carpeta raíz de tu proyecto en el servidor:

```bash
# Cambiar el dueño al usuario del servidor web (www-data usualmente en contenedores PHP)
sudo chown -R 33:33 storage bootstrap/cache

# Asegurar permisos de ejecución y escritura para Laravel
sudo chmod -R 775 storage bootstrap/cache
```
> [!IMPORTANT]
> El ID `33` corresponde al usuario `www-data` dentro del contenedor de PHP oficial. Esto evita errores de "Permission Denied" al generar logs o caché.

---

## ⚙️ 2. Configuración del Entorno (`.env`)

Crea o edita tu archivo `.env` en el servidor con estos valores de producción:

```env
APP_NAME="Control de Incidencias"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://TU_IP_O_DOMINIO:8190

# Reverb (TIEMPO REAL) - ¡MUY IMPORTANTE!
# Cambia TU_IP_O_DOMINIO por la dirección pública de tu servidor
REVERB_HOST="TU_IP_O_DOMINIO"
VITE_REVERB_HOST="${REVERB_HOST}"
REVERB_PORT=8080
REVERB_SCHEME=http

# Vite (Configuración para el cliente/navegador)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

# Base de Datos (Asegúrate que coincidan con docker-compose.yml)
DB_HOST=db
DB_DATABASE=sistemas
DB_USERNAME=rishar
DB_PASSWORD=una_clave_segura_aqui
```

---

## 🐳 3. Ajuste de Docker para Estabilidad

Para que Reverb se inicie automáticamente siempre, ajustaremos el proceso de PHP.

### Paso A: Crear el Script de Inicio (Entrypoint)
Crea un archivo en `docker/php/entrypoint.sh`:

```bash
#!/bin/bash
set -e

# Limpiar y optimizar caché para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Iniciar Reverb en segundo plano (Host 0.0.0.0 para que escuche fuera del contenedor)
php artisan reverb:start --host=0.0.0.0 --port=8080 &

# Iniciar el proceso principal (PHP-FPM)
exec php-fpm
```

### Paso B: Actualizar el Dockerfile
Asegúrate de que tu `docker/php/Dockerfile` incluya estas líneas al final:

```dockerfile
# Copiar el script de inicio
COPY docker/php/entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

# Usar el script como punto de entrada
ENTRYPOINT ["entrypoint.sh"]
```

---

## 🏗️ 4. Lanzamiento y Construcción de Activos

Sigue este orden exacto en tu terminal del servidor:

1.  **Levantar Contenedores:**
    ```bash
    docker-compose up -d --build
    ```

2.  **Instalar Librerías (PHP):**
    ```bash
    docker exec modern_app composer install --optimize-autoloader --no-dev
    ```

3.  **Compilar el Chat y Estilos (JS/Vite):**
    Este paso es vital para que el código del navegador sepa a qué IP pública conectarse.
    ```bash
    docker exec modern_app npm install
    docker exec modern_app npm run build
    ```

4.  **Migrar Base de Datos:**
    ```bash
    docker exec modern_app php artisan migrate --force
    ```

---

## 🛠️ 5. Resolución de Errores Comunes (Troubleshooting)

### ❌ Error: "Failed to connect to Reverb (cURL error 7)"
*   **Causa:** El servidor de Laravel trata de avisar a Reverb (puerto 8080) pero el puerto está bloqueado o Reverb no inició.
*   **Solución:** Revisa que el puerto **8080** esté abierto en el Firewall de tu servidor (Cloud, VPS o Router).
*   **Comando de verificación:** `docker exec modern_app ps aux | grep reverb` (Debería aparecer un proceso corriendo).

### ❌ Error: "Chat dice Desconectado en móviles"
*   **Causa:** El `VITE_REVERB_HOST` en el `.env` dice `localhost` en lugar de la IP real del servidor.
*   **Solución:** Cambia la IP en el `.env` y vuelve a correr `docker exec modern_app npm run build`.

### ❌ Error: "Z-index / Barra de progreso Vuejs no desaparece"
*   **Causa:** Una actualización de presencia falló o el caché de la vista es viejo.
*   **Solución:** Limpia el caché con `docker exec modern_app php artisan view:clear`.

### ❌ Error: "Database connection refused"
*   **Causa:** El contenedor de la DB (`db`) no ha terminado de iniciar cuando Laravel intenta conectar.
*   **Solución:** Espera 10 segundos y corre `docker exec modern_app php artisan migrate --force`.

---

## 🔒 6. Seguridad (Opcional pero Recomendado)

Si vas a usar **HTTPS (SSL)**:
1.  Usa un proxy inverso como **Nginx Proxy Manager**.
2.  Cambia en tu `.env`: `REVERB_SCHEME=https` y `VITE_REVERB_SCHEME=https`.
3.  El proxy debe apuntar al puerto **8190** para la web y al **8080** para el tráfico de WebSockets.

---
> **Tip Pro:** Cada vez que hagas un cambio en el `.env` relacionado con Reverb o la URL, **obligatoriamente** debes correr `npm run build` para que los cambios lleguen a los navegadores de tus usuarios.

correr en phpmyadmin
en la tabla empleados:
-- Acelera la búsqueda por nombre completo (el orden importa)
ALTER TABLE employees ADD INDEX idx_full_name (name, father_lastname, mother_lastname);

-- Acelera el filtro de empleados activos (usado en casi todas tus listas)
ALTER TABLE employees ADD INDEX idx_active_status (active);

-- Acelera la carga de departamentos (usado para verificar permisos en Manager.php)
ALTER TABLE employees ADD INDEX idx_department (deparment_id);

y en la tabla incidencias
ALTER TABLE incidencias ADD INDEX idx_employee (employee_id);
ALTER TABLE incidencias ADD INDEX idx_token (token);
ALTER TABLE incidencias ADD INDEX idx_created (created_at);
ALTER TABLE incidencias ADD INDEX idx_deleted_created (deleted_at, created_at);