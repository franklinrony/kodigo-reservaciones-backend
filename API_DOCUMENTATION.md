# API Documentation - Kodigo Kanban

## Información General

Base URL: `http://localhost:8000`  
Versión actual de la API: `v1`  
Formato de respuestas: `JSON`  
Autenticación: JWT (JSON Web Token)

## Requisitos para las peticiones

- Content-Type: application/json
- Accept: application/json
- Para rutas protegidas: Authorization: Bearer {token}

## Endpoints de Autenticación

### Registro de Usuario

- **URL**: `/api/v1/auth/register`
- **Método**: `POST`
- **Autenticación**: No requerida
- **Cuerpo de la petición**:
  ```json
  {
    "name": "Nombre Completo",
    "email": "correo@ejemplo.com",
    "password": "contraseña",
    "password_confirmation": "contraseña"
  }
  ```
- **Respuesta exitosa**:
  ```json
  {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
      "id": 2,
      "name": "Nombre Completo",
      "email": "correo@ejemplo.com",
      "roles": [{"id": 2, "name": "user", "description": null}]
    }
  }
  ```

### Inicio de Sesión

- **URL**: `/api/v1/auth/login`
- **Método**: `POST`
- **Autenticación**: No requerida
- **Cuerpo de la petición**:
  ```json
  {
    "email": "correo@ejemplo.com",
    "password": "contraseña"
  }
  ```
- **Respuesta exitosa**:
  ```json
  {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
      "id": 2,
      "name": "Nombre Completo",
      "email": "correo@ejemplo.com",
      "roles": [{"id": 2, "name": "user", "description": null}]
    }
  }
  ```
- **Respuesta de error** (401 Unauthorized):
  ```json
  {
    "error": "Unauthorized"
  }
  ```

### Cerrar Sesión

- **URL**: `/api/v1/auth/logout`
- **Método**: `POST`
- **Autenticación**: Requerida (Bearer Token)
- **Respuesta exitosa**:
  ```json
  {
    "message": "Successfully logged out"
  }
  ```

### Refrescar Token

- **URL**: `/api/v1/auth/refresh`
- **Método**: `POST`
- **Autenticación**: Requerida (Bearer Token)
- **Respuesta exitosa**:
  ```json
  {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
      "id": 2,
      "name": "Nombre Completo",
      "email": "correo@ejemplo.com"
    }
  }
  ```

### Obtener Información del Usuario

- **URL**: `/api/v1/auth/me`
- **Método**: `GET`
- **Autenticación**: Requerida (Bearer Token)
- **Respuesta exitosa**:
  ```json
  {
    "id": 2,
    "name": "Nombre Completo",
    "email": "correo@ejemplo.com",
    "email_verified_at": null,
    "created_at": "2025-09-25T10:30:45.000000Z",
    "updated_at": "2025-09-25T10:30:45.000000Z",
    "roles": [
      {
        "id": 2,
        "name": "user",
        "description": null,
        "pivot": {
          "user_id": 2,
          "role_id": 2
        }
      }
    ]
  }
  ```

## Endpoints de Usuario

### Perfil de Usuario

- **URL**: `/api/v1/user/profile`
- **Método**: `GET`
- **Autenticación**: Requerida (Bearer Token)
- **Respuesta exitosa**: Igual que `/api/v1/auth/me`

## Endpoints de Administrador

Todas las rutas bajo `/api/v1/admin/*` requieren que el usuario tenga el rol `admin`.

### Dashboard de Administrador

- **URL**: `/api/v1/admin/dashboard`
- **Método**: `GET`
- **Autenticación**: Requerida (Bearer Token + Rol Admin)
- **Respuesta de error** (403 Forbidden):
  ```json
  {
    "message": "You do not have the required permissions to access this resource."
  }
  ```

## Usuarios de Prueba

1. **Administrador**
   - Email: `test@example.com`
   - Password: `password`
   - Roles: `admin`

2. **Usuario Regular** (Crear mediante el endpoint de registro)
   - Email: `nuevo@ejemplo.com`
   - Password: `password123`
   - Roles: `user` (asignado automáticamente)

## Endpoints del Sistema Kanban

Todas las rutas requieren autenticación con JWT (Bearer Token).

### Tableros (Boards)

#### Listar tableros

- **URL**: `/api/v1/boards`
- **Método**: `GET`
- **Autenticación**: Requerida (Bearer Token)
- **Respuesta exitosa**:
  ```json
  {
    "status": "success",
    "message": "Boards retrieved successfully",
    "data": [
      {
        "id": 1,
        "title": "Proyecto Personal",
        "description": "Mi tablero personal de tareas",
        "is_public": false,
        "user_id": 2,
        "created_at": "2025-09-26T10:30:45.000000Z",
        "updated_at": "2025-09-26T10:30:45.000000Z",
        "lists": [...],
        "labels": [...]
      },
      {...}
    ]
  }
  ```

#### Crear tablero

- **URL**: `/api/v1/boards`
- **Método**: `POST`
- **Autenticación**: Requerida (Bearer Token)
- **Cuerpo de la petición**:
  ```json
  {
    "title": "Nuevo Tablero",
    "description": "Descripción del tablero",
    "is_public": false
  }
  ```
- **Respuesta exitosa** (201 Created):
  ```json
  {
    "status": "success",
    "message": "Board created successfully",
    "data": {
      "id": 3,
      "title": "Nuevo Tablero",
      "description": "Descripción del tablero",
      "is_public": false,
      "user_id": 2,
      "created_at": "2025-09-27T15:20:30.000000Z",
      "updated_at": "2025-09-27T15:20:30.000000Z"
    }
  }
  ```

#### Ver tablero

- **URL**: `/api/v1/boards/{boardId}`
- **Método**: `GET`
- **Autenticación**: Requerida (Bearer Token)
- **Respuesta exitosa**:
  ```json
  {
    "status": "success",
    "message": "Board retrieved successfully",
    "data": {
      "id": 1,
      "title": "Proyecto Personal",
      "description": "Mi tablero personal de tareas",
      "is_public": false,
      "user_id": 2,
      "created_at": "2025-09-26T10:30:45.000000Z",
      "updated_at": "2025-09-26T10:30:45.000000Z",
      "lists": [
        {
          "id": 1,
          "title": "Por hacer",
          "position": 0,
          "board_id": 1,
          "created_at": "2025-09-26T10:35:12.000000Z",
          "updated_at": "2025-09-26T10:35:12.000000Z",
          "cards": [...]
        },
        {...}
      ],
      "labels": [...]
    }
  }
  ```

#### Actualizar tablero

- **URL**: `/api/v1/boards/{boardId}`
- **Método**: `PUT`
- **Autenticación**: Requerida (Bearer Token)
- **Cuerpo de la petición**:
  ```json
  {
    "title": "Título Actualizado",
    "description": "Nueva descripción",
    "is_public": true
  }
  ```
- **Respuesta exitosa**:
  ```json
  {
    "status": "success",
    "message": "Board updated successfully",
    "data": {
      "id": 1,
      "title": "Título Actualizado",
      "description": "Nueva descripción",
      "is_public": true,
      "user_id": 2,
      "created_at": "2025-09-26T10:30:45.000000Z",
      "updated_at": "2025-09-27T16:45:22.000000Z"
    }
  }
  ```

#### Eliminar tablero

- **URL**: `/api/v1/boards/{boardId}`
- **Método**: `DELETE`
- **Autenticación**: Requerida (Bearer Token)
- **Respuesta exitosa**:
  ```json
  {
    "status": "success",
    "message": "Board deleted successfully"
  }
  ```

#### Añadir colaborador

- **URL**: `/api/v1/boards/{boardId}/collaborators`
- **Método**: `POST`
- **Autenticación**: Requerida (Bearer Token)
- **Cuerpo de la petición**:
  ```json
  {
    "user_id": 3
  }
  ```
- **Respuesta exitosa**:
  ```json
  {
    "status": "success",
    "message": "Collaborator added successfully"
  }
  ```

#### Eliminar colaborador

- **URL**: `/api/v1/boards/{boardId}/collaborators/{userId}`
- **Método**: `DELETE`
- **Autenticación**: Requerida (Bearer Token)
- **Respuesta exitosa**:
  ```json
  {
    "status": "success",
    "message": "Collaborator removed successfully"
  }
  ```

### Listas (Board Lists)

#### Listar listas de un tablero

- **URL**: `/api/v1/boards/{boardId}/lists`
- **Método**: `GET`
- **Autenticación**: Requerida (Bearer Token)
- **Respuesta exitosa**:
  ```json
  {
    "status": "success",
    "message": "Lists retrieved successfully",
    "data": [
      {
        "id": 1,
        "title": "Por hacer",
        "position": 0,
        "board_id": 1,
        "created_at": "2025-09-26T10:35:12.000000Z",
        "updated_at": "2025-09-26T10:35:12.000000Z",
        "cards": [...]
      },
      {...}
    ]
  }
  ```

#### Crear lista

- **URL**: `/api/v1/boards/{boardId}/lists`
- **Método**: `POST`
- **Autenticación**: Requerida (Bearer Token)
- **Cuerpo de la petición**:
  ```json
  {
    "title": "En progreso",
    "position": 1
  }
  ```
- **Respuesta exitosa** (201 Created):
  ```json
  {
    "status": "success",
    "message": "List created successfully",
    "data": {
      "id": 2,
      "title": "En progreso",
      "position": 1,
      "board_id": 1,
      "created_at": "2025-09-27T11:20:05.000000Z",
      "updated_at": "2025-09-27T11:20:05.000000Z"
    }
  }
  ```

### Tarjetas (Cards)

#### Listar tarjetas de una lista

- **URL**: `/api/v1/lists/{listId}/cards`
- **Método**: `GET`
- **Autenticación**: Requerida (Bearer Token)
- **Respuesta exitosa**:
  ```json
  {
    "status": "success",
    "message": "Cards retrieved successfully",
    "data": [
      {
        "id": 1,
        "title": "Implementar autenticación",
        "description": "Implementar sistema JWT",
        "due_date": "2025-10-05",
        "position": 0,
        "board_list_id": 1,
        "user_id": 2,
        "created_at": "2025-09-26T14:30:22.000000Z",
        "updated_at": "2025-09-26T14:30:22.000000Z",
        "labels": [...],
        "comments": [...]
      },
      {...}
    ]
  }
  ```

#### Crear tarjeta

- **URL**: `/api/v1/lists/{listId}/cards`
- **Método**: `POST`
- **Autenticación**: Requerida (Bearer Token)
- **Cuerpo de la petición**:
  ```json
  {
    "title": "Nueva tarea",
    "description": "Descripción de la tarea",
    "due_date": "2025-10-10",
    "label_ids": [1, 3]
  }
  ```
- **Respuesta exitosa** (201 Created):
  ```json
  {
    "status": "success",
    "message": "Card created successfully",
    "data": {
      "id": 5,
      "title": "Nueva tarea",
      "description": "Descripción de la tarea",
      "due_date": "2025-10-10",
      "position": 2,
      "board_list_id": 1,
      "user_id": 2,
      "created_at": "2025-09-27T17:10:45.000000Z",
      "updated_at": "2025-09-27T17:10:45.000000Z",
      "labels": [...]
    }
  }
  ```

### Etiquetas (Labels)

#### Listar etiquetas de un tablero

- **URL**: `/api/v1/boards/{boardId}/labels`
- **Método**: `GET`
- **Autenticación**: Requerida (Bearer Token)
- **Respuesta exitosa**:
  ```json
  {
    "status": "success",
    "message": "Labels retrieved successfully",
    "data": [
      {
        "id": 1,
        "name": "Urgente",
        "color": "#FF0000",
        "board_id": 1,
        "created_at": "2025-09-26T10:40:30.000000Z",
        "updated_at": "2025-09-26T10:40:30.000000Z"
      },
      {...}
    ]
  }
  ```

#### Crear etiqueta

- **URL**: `/api/v1/boards/{boardId}/labels`
- **Método**: `POST`
- **Autenticación**: Requerida (Bearer Token)
- **Cuerpo de la petición**:
  ```json
  {
    "name": "Importante",
    "color": "#FFA500"
  }
  ```
- **Respuesta exitosa** (201 Created):
  ```json
  {
    "status": "success",
    "message": "Label created successfully",
    "data": {
      "id": 4,
      "name": "Importante",
      "color": "#FFA500",
      "board_id": 1,
      "created_at": "2025-09-27T18:05:15.000000Z",
      "updated_at": "2025-09-27T18:05:15.000000Z"
    }
  }
  ```

### Comentarios (Comments)

#### Listar comentarios de una tarjeta

- **URL**: `/api/v1/cards/{cardId}/comments`
- **Método**: `GET`
- **Autenticación**: Requerida (Bearer Token)
- **Respuesta exitosa**:
  ```json
  {
    "status": "success",
    "message": "Comments retrieved successfully",
    "data": [
      {
        "id": 1,
        "content": "Este es un comentario de prueba",
        "edited": false,
        "card_id": 1,
        "user_id": 2,
        "created_at": "2025-09-27T09:15:30.000000Z",
        "updated_at": "2025-09-27T09:15:30.000000Z",
        "user": {
          "id": 2,
          "name": "Nombre Usuario",
          "email": "usuario@ejemplo.com"
        }
      },
      {...}
    ]
  }
  ```

#### Crear comentario

- **URL**: `/api/v1/cards/{cardId}/comments`
- **Método**: `POST`
- **Autenticación**: Requerida (Bearer Token)
- **Cuerpo de la petición**:
  ```json
  {
    "content": "Este es un nuevo comentario"
  }
  ```
- **Respuesta exitosa** (201 Created):
  ```json
  {
    "status": "success",
    "message": "Comment created successfully",
    "data": {
      "id": 5,
      "content": "Este es un nuevo comentario",
      "edited": false,
      "card_id": 1,
      "user_id": 2,
      "created_at": "2025-09-27T20:30:10.000000Z",
      "updated_at": "2025-09-27T20:30:10.000000Z",
      "user": {
        "id": 2,
        "name": "Nombre Usuario",
        "email": "usuario@ejemplo.com"
      }
    }
  }
  ```

## Notas Importantes

- Los tokens JWT tienen una validez de 60 minutos.
- Para rutas protegidas, siempre incluir el token en la cabecera `Authorization: Bearer {token}`.
- El token se puede refrescar antes de que expire utilizando el endpoint `/api/v1/auth/refresh`.
- Las etiquetas (labels) pertenecen a un tablero y pueden ser asignadas a cualquier tarjeta de ese tablero.
- Las tarjetas pueden moverse entre listas del mismo tablero mediante la actualización del campo `board_list_id`.
- Solo el propietario del tablero puede añadir o eliminar colaboradores.