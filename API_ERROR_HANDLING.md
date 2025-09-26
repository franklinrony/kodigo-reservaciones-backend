# API Response and Error Handling Documentation

## Overview

This document explains how the API handles responses and errors following JSON:API standards.

## Standard Response Format

All API responses follow a consistent JSON format:

### Success Responses

```json
{
    "message": "Mensaje descriptivo del resultado",
    "data": {
        // Datos específicos de la respuesta
    }
}
```

### Error Responses

```json
{
    "message": "Mensaje de error descriptivo",
    "errors": {
        // Detalles específicos del error
    }
}
```

## HTTP Status Codes

The API uses appropriate HTTP status codes:

- **200 OK**: Operación exitosa (GET, PUT, PATCH)
- **201 Created**: Recurso creado exitosamente (POST)
- **204 No Content**: Operación exitosa sin contenido en respuesta (DELETE)
- **400 Bad Request**: Solicitud incorrecta
- **401 Unauthorized**: Autenticación requerida
- **403 Forbidden**: No autorizado para acceder al recurso
- **404 Not Found**: Recurso no encontrado
- **422 Unprocessable Entity**: Error de validación
- **500 Server Error**: Error interno del servidor

## Error Handling

El sistema maneja los siguientes tipos de errores:

1. **Errores de validación (422)**:
   ```json
   {
       "message": "Error de validación",
       "errors": {
           "campo1": ["El campo es requerido"],
           "campo2": ["Formato inválido"]
       }
   }
   ```

2. **Errores de autenticación (401)**:
   ```json
   {
       "message": "No autenticado",
       "errors": []
   }
   ```

3. **Errores de autorización (403)**:
   ```json
   {
       "message": "No autorizado para realizar esta acción",
       "errors": []
   }
   ```

4. **Errores de recurso no encontrado (404)**:
   ```json
   {
       "message": "El recurso solicitado no existe",
       "errors": []
   }
   ```

## Logging

Todos los errores son registrados en los logs de la aplicación con información detallada:
- Tipo de excepción
- Archivo y línea donde ocurrió el error
- Stack trace para depuración
- Información adicional relacionada con la solicitud

Los logs son almacenados en `storage/logs/laravel.log` en formato diario.