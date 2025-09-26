# Documentación API Kanban (Manual)

## Introducción

Este documento proporciona una documentación manual de la API RESTful para el sistema Kanban. Debido a problemas con la generación automática de Swagger, esta guía manual es la referencia principal.

## Autenticación

La API utiliza autenticación basada en tokens JWT (JSON Web Token).

### Obtener un Token

Para obtener un token, debes enviar una solicitud POST a `/api/auth/login` con tus credenciales:

```json
{
  "email": "usuario@ejemplo.com",
  "password": "contraseña"
}
```

La respuesta incluirá un token JWT que debes incluir en las cabeceras de todas tus solicitudes posteriores.

### Uso del Token

Para cada solicitud a un endpoint protegido, incluye el token en el encabezado `Authorization`:

```
Authorization: Bearer {tu_token_jwt}
```

## Notas Importantes para Solicitudes API

1. **Cabeceras requeridas**: Siempre incluye estos encabezados:
   ```
   Content-Type: application/json
   Accept: application/json
   Authorization: Bearer {tu_token_jwt}
   ```

2. **El encabezado `Accept: application/json` es crucial**: Si no se incluye, las respuestas de error podrían devolverse como HTML en lugar de JSON.

## Endpoints Principales

### Autenticación

- **POST /api/auth/register**: Registrar nuevo usuario
- **POST /api/auth/login**: Iniciar sesión y obtener token
- **POST /api/auth/logout**: Cerrar sesión (invalidar token)
- **GET /api/user/profile**: Obtener perfil del usuario actual

### Tableros (Boards)

- **GET /api/v1/boards**: Listar todos los tableros
- **POST /api/v1/boards**: Crear nuevo tablero
- **GET /api/v1/boards/{id}**: Obtener tablero específico
- **PUT /api/v1/boards/{id}**: Actualizar tablero
- **DELETE /api/v1/boards/{id}**: Eliminar tablero

### Listas (Lists)

- **GET /api/v1/boards/{boardId}/lists**: Listar listas de un tablero
- **POST /api/v1/boards/{boardId}/lists**: Crear nueva lista
- **GET /api/v1/boards/{boardId}/lists/{id}**: Obtener lista específica
- **PUT /api/v1/boards/{boardId}/lists/{id}**: Actualizar lista
- **DELETE /api/v1/boards/{boardId}/lists/{id}**: Eliminar lista

### Tarjetas (Cards)

- **GET /api/v1/lists/{listId}/cards**: Listar tarjetas de una lista
- **POST /api/v1/lists/{listId}/cards**: Crear nueva tarjeta
  - **Parámetros requeridos**: `title` (string)
  - **Parámetros opcionales**: `description` (string), `due_date` (date), `position` (integer), `label_ids` (array)
  - **Ejemplo**: 
    ```json
    {
      "title": "Mi tarea",
      "description": "Descripción de la tarea",
      "due_date": "2025-10-15"
    }
    ```
- **GET /api/v1/cards/{id}**: Obtener tarjeta específica
- **PUT /api/v1/cards/{id}**: Actualizar tarjeta
- **DELETE /api/v1/cards/{id}**: Eliminar tarjeta

### Etiquetas (Labels)

- **GET /api/v1/boards/{boardId}/labels**: Listar etiquetas de un tablero
- **POST /api/v1/boards/{boardId}/labels**: Crear nueva etiqueta
- **GET /api/v1/labels/{id}**: Obtener etiqueta específica
- **PUT /api/v1/labels/{id}**: Actualizar etiqueta
- **DELETE /api/v1/labels/{id}**: Eliminar etiqueta

### Comentarios (Comments)

- **GET /api/v1/cards/{cardId}/comments**: Listar comentarios de una tarjeta
- **POST /api/v1/cards/{cardId}/comments**: Crear nuevo comentario
- **GET /api/v1/comments/{id}**: Obtener comentario específico
- **PUT /api/v1/comments/{id}**: Actualizar comentario
- **DELETE /api/v1/comments/{id}**: Eliminar comentario

## Manejo de Errores

La API devuelve códigos de estado HTTP estándar:

- **200 OK**: Operación exitosa
- **201 Created**: Recurso creado con éxito
- **400 Bad Request**: Solicitud incorrecta
- **401 Unauthorized**: No autenticado
- **403 Forbidden**: No autorizado para este recurso
- **404 Not Found**: Recurso no encontrado
- **422 Unprocessable Entity**: Error de validación
- **500 Server Error**: Error interno del servidor

Las respuestas de error tienen este formato:

```json
{
  "message": "Mensaje de error",
  "errors": {
    "campo1": ["Error en campo1"],
    "campo2": ["Error en campo2"]
  }
}
```

## Consejos para Resolución de Problemas

1. **Si recibes HTML en lugar de JSON**: Verifica que estás incluyendo el encabezado `Accept: application/json`
2. **Errores de autenticación**: Asegúrate de que el token JWT es válido y no ha expirado
3. **Errores 422**: Revisa los campos requeridos y sus formatos