# CRUD con AJAX

Práctica de un CRUD de productos con PHP, MySQL y Docker Compose. La interfaz usa `fetch()` para crear, editar y eliminar registros sin recargar la página. También permite subir y actualizar imágenes.

## Puesta en marcha

Hace falta tener Docker Desktop instalado. Desde la carpeta del proyecto, ejecuta:

```bash
docker compose up -d --build
```

Después, abre:

```text
http://localhost:8080
```

## Endpoints

Los archivos de `src/api/` se encargan de las operaciones:

```text
crear.php    crear un registro
listar.php   listar los registros
editar.php   actualizar un registro
eliminar.php eliminar un registro
```

Cada endpoint devuelve JSON. Tras una operación, JavaScript actualiza solo la tarjeta afectada en lugar de volver a cargar toda la página.

La base de datos se inicializa con 300 registros de prueba. La página los muestra en tres bloques: textos, números e imágenes. Los primeros 30 ya tienen una imagen local en `src/uploads/`; el resto usa una imagen por defecto.

## Detener el proyecto

```bash
docker compose down
```

Para borrar también el volumen de MySQL y volver a cargar los datos iniciales:

```bash
docker compose down -v
docker compose up -d --build
```
