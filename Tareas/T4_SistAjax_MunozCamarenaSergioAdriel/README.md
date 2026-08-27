# CRUD AJAX con Docker

Práctica de CRUD sencillo usando:

- HTML/CSS/JavaScript
- AJAX mediante `fetch()`
- PHP 8.3
- MySQL 8.4
- Docker Compose
- Subida y actualización de imágenes
- 250 registros iniciales

## 1. Requisitos

Tener instalado:

- Docker Desktop
- Docker Compose (incluido en Docker Desktop)

## 2. Levantar el proyecto

Desde esta carpeta:

```bash
docker compose up -d --build
```

Abrir:

```text
http://localhost:8080
```

## 3. Qué demuestra

### Create
El formulario manda los datos con `fetch()` a:

```text
api/crear.php
```

La respuesta es JSON y JavaScript agrega únicamente la nueva tarjeta.

### Read
Al cargar la página:

```text
api/listar.php
```

consulta MySQL y devuelve todos los registros.

### Update
Al editar:

```text
api/editar.php
```

actualiza MySQL y JavaScript reemplaza únicamente la tarjeta modificada.

### Delete
Al eliminar:

```text
api/eliminar.php
```

borra el registro y JavaScript elimina únicamente esa tarjeta del DOM.

## 4. La parte importante de AJAX

No se hace:

```text
Editar -> recargar index.php
```

Sino:

```text
Editar
   ↓
JavaScript fetch()
   ↓
editar.php
   ↓
MySQL
   ↓
JSON
   ↓
JavaScript
   ↓
actualiza SOLO la tarjeta
```

Eso es justamente la idea del ejercicio: modificar información sin recargar toda la página.

## 5. Detener

```bash
docker compose down
```

## 6. Borrar también la base de datos

Si quieres empezar desde cero:

```bash
docker compose down -v
docker compose up -d --build
```

El volumen `mysql_data` contiene la base de datos. Al usar `-v` se elimina y `init.sql` vuelve a crear los 250 registros.
