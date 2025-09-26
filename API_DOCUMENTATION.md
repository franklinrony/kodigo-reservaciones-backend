# 📚 Documentación de la API - Kodigo Kanban

## 🚀 Introducción

La API de Kodigo Kanban es una interfaz RESTful completa para gestionar tableros Kanban. Está construida con Laravel 12 y utiliza autenticación JWT para proteger los endpoints.

**URL Base**: `http://localhost:8000/api/v1`

## 🔐 Autenticación

Todos los endpoints requieren autenticación JWT excepto los de registro y login.

### Headers Requeridos
```
Authorization: Bearer {token_jwt}
Content-Type: application/json
```

---

## 👤 Endpoints de Autenticación

### 📝 Registro de Usuario
```http
POST /api/auth/register
```

**Body**:
```json
{
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Respuesta Exitosa (201)**:
```json
{
  "message": "Usuario registrado exitosamente",
  "user": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "juan@example.com"
  },
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "bearer",
  "expires_in": 3600
}
```

### 🔑 Inicio de Sesión
```http
POST /api/auth/login
```

**Body**:
```json
{
  "email": "juan@example.com",
  "password": "password123"
}
```

**Respuesta Exitosa (200)**:
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "bearer",
  "expires_in": 3600
}
```

### 👤 Información del Usuario Actual
```http
GET /api/auth/me
```

**Respuesta Exitosa (200)**:
```json
{
  "id": 1,
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "roles": ["user"],
  "created_at": "2025-09-26T10:00:00.000000Z"
}
```

### 🚪 Cierre de Sesión
```http
POST /api/auth/logout
```

**Respuesta Exitosa (200)**:
```json
{
  "message": "Sesión cerrada exitosamente"
}
```

### 🔄 Refrescar Token
```http
POST /api/auth/refresh
```

**Respuesta Exitosa (200)**:
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "bearer",
  "expires_in": 3600
}
```

---

## 📋 Endpoints de Tableros (Boards)

### 📋 Listar Tableros
```http
GET /api/v1/boards
```

**Respuesta Exitosa (200)**:
```json
[
  {
    "id": 1,
    "name": "Proyecto Web",
    "description": "Desarrollo de aplicación web",
    "user_id": 1,
    "is_public": false,
    "background_color": "#f5f5f5",
    "created_at": "2025-09-26T10:00:00.000000Z",
    "updated_at": "2025-09-26T10:00:00.000000Z",
    "owner": {
      "id": 1,
      "name": "Juan Pérez",
      "email": "juan@example.com"
    },
    "collaborators_count": 2
  }
]
```

### ➕ Crear Tablero
```http
POST /api/v1/boards
```

**Body**:
```json
{
  "name": "Nuevo Proyecto",
  "description": "Descripción del proyecto",
  "is_public": false,
  "background_color": "#0079bf"
}
```

**Respuesta Exitosa (201)**:
```json
{
  "id": 1,
  "name": "Nuevo Proyecto",
  "description": "Descripción del proyecto",
  "user_id": 1,
  "is_public": false,
  "background_color": "#0079bf",
  "created_at": "2025-09-26T10:00:00.000000Z",
  "updated_at": "2025-09-26T10:00:00.000000Z"
}
```

### 👁️ Ver Tablero Detallado
```http
GET /api/v1/boards/{boardId}
```

**Respuesta Exitosa (200)**:
```json
{
  "id": 1,
  "name": "Proyecto Web",
  "description": "Desarrollo de aplicación web",
  "user_id": 1,
  "is_public": false,
  "background_color": "#f5f5f5",
  "created_at": "2025-09-26T10:00:00.000000Z",
  "updated_at": "2025-09-26T10:00:00.000000Z",
  "owner": {
    "id": 1,
    "name": "Juan Pérez"
  },
  "lists": [
    {
      "id": 1,
      "name": "Por hacer",
      "position": 0,
      "cards_count": 3
    }
  ],
  "labels": [
    {
      "id": 1,
      "name": "Urgente",
      "color": "#ff0000"
    }
  ],
  "collaborators": [
    {
      "id": 2,
      "name": "María García",
      "role": "editor"
    }
  ]
}
```

### ✏️ Actualizar Tablero
```http
PUT /api/v1/boards/{boardId}
```

**Body**:
```json
{
  "name": "Proyecto Web Actualizado",
  "description": "Nueva descripción",
  "is_public": true
}
```

### 🗑️ Eliminar Tablero
```http
DELETE /api/v1/boards/{boardId}
```

**Respuesta Exitosa (200)**:
```json
{
  "message": "Tablero eliminado exitosamente"
}
```

### 👥 Agregar Colaborador
```http
POST /api/v1/boards/{boardId}/collaborators
```

**Body**:
```json
{
  "user_id": 2,
  "role": "editor"
}
```

### 👥 Eliminar Colaborador
```http
DELETE /api/v1/boards/{boardId}/collaborators/{userId}
```

---

## 📝 Endpoints de Listas (Lists)

### 📝 Listar Listas de un Tablero
```http
GET /api/v1/boards/{boardId}/lists
```

**Respuesta Exitosa (200)**:
```json
[
  {
    "id": 1,
    "name": "Por hacer",
    "board_id": 1,
    "position": 0,
    "is_archived": false,
    "created_at": "2025-09-26T10:00:00.000000Z",
    "cards_count": 5
  }
]
```

### ➕ Crear Lista
```http
POST /api/v1/boards/{boardId}/lists
```

**Body**:
```json
{
  "name": "En progreso"
}
```

### 👁️ Ver Lista Detallada
```http
GET /api/v1/boards/{boardId}/lists/{id}
```

### ✏️ Actualizar Lista
```http
PUT /api/v1/boards/{boardId}/lists/{id}
```

**Body**:
```json
{
  "name": "Lista Actualizada",
  "position": 1
}
```

### 🗑️ Eliminar Lista
```http
DELETE /api/v1/boards/{boardId}/lists/{id}
```

---

## 🎯 Endpoints de Tarjetas (Cards)

### 🎯 Listar Tarjetas de una Lista
```http
GET /api/v1/lists/{listId}/cards
```

**Respuesta Exitosa (200)**:
```json
[
  {
    "id": 1,
    "title": "Implementar login",
    "description": "Crear sistema de autenticación",
    "board_list_id": 1,
    "user_id": 1,
    "position": 0,
    "due_date": "2025-10-01",
    "is_completed": false,
    "is_archived": false,
    "created_at": "2025-09-26T10:00:00.000000Z",
    "updated_at": "2025-09-26T10:00:00.000000Z",
    "creator": {
      "id": 1,
      "name": "Juan Pérez"
    },
    "labels": [
      {
        "id": 1,
        "name": "Urgente",
        "color": "#ff0000"
      }
    ],
    "comments_count": 2
  }
]
```

### ➕ Crear Tarjeta
```http
POST /api/v1/lists/{listId}/cards
```

**Body**:
```json
{
  "title": "Nueva tarea",
  "description": "Descripción de la tarea",
  "due_date": "2025-10-15",
  "label_ids": [1, 2]
}
```

### 👁️ Ver Tarjeta Detallada
```http
GET /api/v1/cards/{id}
```

**Respuesta Exitosa (200)**:
```json
{
  "id": 1,
  "title": "Implementar login",
  "description": "Crear sistema de autenticación",
  "board_list_id": 1,
  "user_id": 1,
  "position": 0,
  "due_date": "2025-10-01",
  "is_completed": false,
  "is_archived": false,
  "created_at": "2025-09-26T10:00:00.000000Z",
  "updated_at": "2025-09-26T10:00:00.000000Z",
  "creator": {
    "id": 1,
    "name": "Juan Pérez"
  },
  "list": {
    "id": 1,
    "name": "Por hacer"
  },
  "board": {
    "id": 1,
    "name": "Proyecto Web"
  },
  "labels": [
    {
      "id": 1,
      "name": "Urgente",
      "color": "#ff0000"
    }
  ],
  "comments": [
    {
      "id": 1,
      "content": "Esta tarea es crítica",
      "user_id": 1,
      "created_at": "2025-09-26T11:00:00.000000Z",
      "author": {
        "id": 1,
        "name": "Juan Pérez"
      }
    }
  ]
}
```

### ✏️ Actualizar Tarjeta
```http
PUT /api/v1/cards/{id}
```

**Body**:
```json
{
  "title": "Tarea actualizada",
  "description": "Nueva descripción",
  "board_list_id": 2,
  "position": 1,
  "is_completed": true,
  "label_ids": [1, 3]
}
```

### 🗑️ Eliminar Tarjeta
```http
DELETE /api/v1/cards/{id}
```

---

## 🏷️ Endpoints de Etiquetas (Labels)

### 🏷️ Listar Etiquetas de un Tablero
```http
GET /api/v1/boards/{boardId}/labels
```

**Respuesta Exitosa (200)**:
```json
[
  {
    "id": 1,
    "name": "Urgente",
    "color": "#ff0000",
    "board_id": 1,
    "created_at": "2025-09-26T10:00:00.000000Z"
  }
]
```

### ➕ Crear Etiqueta
```http
POST /api/v1/boards/{boardId}/labels
```

**Body**:
```json
{
  "name": "Importante",
  "color": "#ffa500"
}
```

### 👁️ Ver Etiqueta
```http
GET /api/v1/labels/{id}
```

### ✏️ Actualizar Etiqueta
```http
PUT /api/v1/labels/{id}
```

**Body**:
```json
{
  "name": "Muy Importante",
  "color": "#ff4500"
}
```

### 🗑️ Eliminar Etiqueta
```http
DELETE /api/v1/labels/{id}
```

---

## 💬 Endpoints de Comentarios (Comments)

### 💬 Listar Comentarios de una Tarjeta
```http
GET /api/v1/cards/{cardId}/comments
```

**Respuesta Exitosa (200)**:
```json
[
  {
    "id": 1,
    "content": "Esta tarea requiere atención inmediata",
    "card_id": 1,
    "user_id": 1,
    "created_at": "2025-09-26T11:00:00.000000Z",
    "updated_at": "2025-09-26T11:00:00.000000Z",
    "author": {
      "id": 1,
      "name": "Juan Pérez"
    }
  }
]
```

### ➕ Crear Comentario
```http
POST /api/v1/cards/{cardId}/comments
```

**Body**:
```json
{
  "content": "Nuevo comentario sobre esta tarea"
}
```

### 👁️ Ver Comentario
```http
GET /api/v1/comments/{id}
```

### ✏️ Actualizar Comentario
```http
PUT /api/v1/comments/{id}
```

**Body**:
```json
{
  "content": "Comentario actualizado"
}
```

### 🗑️ Eliminar Comentario
```http
DELETE /api/v1/comments/{id}
```

---

## 👑 Endpoints de Administración (Admin)

### 📊 Dashboard Administrativo
```http
GET /api/v1/admin/dashboard
```

**Respuesta Exitosa (200)**:
```json
{
  "total_users": 25,
  "total_boards": 15,
  "total_cards": 120,
  "users_by_role": {
    "admin": 2,
    "user": 23
  },
  "recent_activity": [
    {
      "type": "board_created",
      "user": "Juan Pérez",
      "timestamp": "2025-09-26T12:00:00.000000Z"
    }
  ]
}
```

---

## 📋 Códigos de Estado HTTP

| Código | Descripción |
|--------|-------------|
| 200 | OK - Operación exitosa |
| 201 | Created - Recurso creado exitosamente |
| 204 | No Content - Operación exitosa sin contenido de respuesta |
| 400 | Bad Request - Datos inválidos |
| 401 | Unauthorized - Token inválido o expirado |
| 403 | Forbidden - Permisos insuficientes |
| 404 | Not Found - Recurso no encontrado |
| 422 | Unprocessable Entity - Error de validación |
| 500 | Internal Server Error - Error del servidor |

---

## ⚠️ Manejo de Errores

### Estructura de Error General
```json
{
  "message": "Descripción del error",
  "errors": {
    "campo": ["Mensaje de error específico"]
  }
}
```

### Ejemplo de Error de Validación
```json
{
  "message": "Los datos proporcionados no son válidos",
  "errors": {
    "name": ["El campo nombre es obligatorio"],
    "email": ["El formato del email no es válido"]
  }
}
```

### Error de Autenticación
```json
{
  "message": "Token no válido"
}
```

### Error de Autorización
```json
{
  "message": "No tienes permisos para realizar esta acción"
}
```

---

## 🔒 Control de Acceso

### Niveles de Permiso

#### 👤 Usuario Regular
- ✅ Crear y gestionar sus propios tableros
- ✅ Gestionar tarjetas en tableros donde es colaborador
- ✅ Crear comentarios en tarjetas

#### 👥 Colaborador (Viewer)
- ✅ Ver tableros y contenido
- ❌ Modificar contenido

#### 👥 Colaborador (Editor)
- ✅ Ver y editar contenido
- ✅ Crear, actualizar y eliminar tarjetas
- ✅ Gestionar etiquetas y comentarios
- ❌ Eliminar tablero
- ❌ Gestionar colaboradores

#### 👑 Administrador de Tablero
- ✅ Todos los permisos de editor
- ✅ Gestionar colaboradores
- ✅ Eliminar tablero

#### 🛡️ Administrador del Sistema
- ✅ Acceso a dashboard administrativo
- ✅ Gestionar todos los recursos del sistema

---

## 📊 Paginación

Para endpoints que retornan listas grandes, se implementa paginación automática:

```json
{
  "data": [...],
  "links": {
    "first": "http://localhost:8000/api/v1/boards?page=1",
    "last": "http://localhost:8000/api/v1/boards?page=5",
    "prev": null,
    "next": "http://localhost:8000/api/v1/boards?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 15,
    "to": 15,
    "total": 75
  }
}
```

---

## 🔄 Versionado de API

La API utiliza versionado en la URL para mantener compatibilidad:

- **v1**: Versión actual (estable)
- Futuras versiones mantendrán compatibilidad hacia atrás

---

**[⬅️ Volver al README principal](../README.md)** | **[📊 Estructura de Base de Datos](../DATABASE_SCHEMA.md)**