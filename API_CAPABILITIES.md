# Capacidades de la API Kanban

## 1. Autenticación y Gestión de Usuarios
- **Registro de usuarios**: Permite crear nuevas cuentas de usuario
- **Inicio de sesión**: Genera tokens JWT para autenticación
- **Cierre de sesión**: Invalida tokens activos
- **Perfil de usuario**: Permite consultar información del usuario actual

## 2. Gestión de Tableros (Boards)
- **Crear tableros**: Permite crear espacios de trabajo Kanban
- **Listar tableros**: Muestra todos los tableros accesibles para el usuario
- **Ver detalles**: Consulta información completa de un tablero específico
- **Actualizar tableros**: Modifica nombre, descripción u otras propiedades
- **Eliminar tableros**: Remueve tableros completos del sistema
- **Gestión de colaboradores**: Permite añadir o quitar usuarios de un tablero

## 3. Gestión de Listas (Lists)
- **Crear listas**: Añade nuevas columnas a un tablero (ej: "Por hacer", "En progreso", "Terminado")
- **Listar listas**: Muestra todas las listas de un tablero
- **Ver detalles**: Consulta información de una lista específica
- **Actualizar listas**: Modifica nombre, posición u otras propiedades
- **Eliminar listas**: Elimina columnas completas del tablero

## 4. Gestión de Tarjetas (Cards)
- **Crear tarjetas**: Añade nuevas tareas a las listas
- **Listar tarjetas**: Muestra todas las tarjetas de una lista
- **Ver detalles**: Consulta información completa de una tarjeta
- **Actualizar tarjetas**: Modifica título, descripción, fecha límite, posición, etc.
- **Eliminar tarjetas**: Remueve tareas de las listas
- **Asignar etiquetas**: Asocia etiquetas de colores a las tarjetas

## 5. Gestión de Etiquetas (Labels)
- **Crear etiquetas**: Define nuevas categorías con colores para clasificar tarjetas
- **Listar etiquetas**: Muestra todas las etiquetas disponibles en un tablero
- **Ver detalles**: Consulta información de una etiqueta específica
- **Actualizar etiquetas**: Modifica nombre, color u otras propiedades
- **Eliminar etiquetas**: Remueve etiquetas del sistema

## 6. Gestión de Comentarios (Comments)
- **Crear comentarios**: Añade notas o discusiones a las tarjetas
- **Listar comentarios**: Muestra todos los comentarios de una tarjeta
- **Ver detalles**: Consulta información completa de un comentario
- **Actualizar comentarios**: Modifica el contenido de un comentario existente
- **Eliminar comentarios**: Remueve comentarios de las tarjetas

## 7. Funcionalidades de Seguridad y Control de Acceso
- **Autenticación JWT**: Protección de endpoints mediante tokens
- **Control de roles**: Distinción entre usuarios normales y administradores
- **Validación de datos**: Verificación de entradas para evitar errores
- **Manejo de errores**: Respuestas JSON estructuradas para errores

## 8. Características Técnicas
- **Respuestas en formato JSON**: Todos los endpoints devuelven respuestas estructuradas
- **Códigos de estado HTTP**: Utiliza los códigos estándar (200, 201, 400, 401, 403, 404, 422, 500)
- **Versioning**: La API está versionada (v1) para facilitar futuras evoluciones
- **Middleware de JSON**: Fuerza respuestas en formato JSON para consistencia

## Referencias
Para una documentación más detallada de cada endpoint, consulta:
- [API_DOCUMENTATION.md](API_DOCUMENTATION.md): Visión general de la API
- [API_MANUAL.md](API_MANUAL.md): Documentación manual detallada con ejemplos