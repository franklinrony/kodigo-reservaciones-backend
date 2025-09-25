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

## Notas Importantes

- Los tokens JWT tienen una validez de 60 minutos.
- Para rutas protegidas, siempre incluir el token en la cabecera `Authorization: Bearer {token}`.
- El token se puede refrescar antes de que expire utilizando el endpoint `/api/v1/auth/refresh`.