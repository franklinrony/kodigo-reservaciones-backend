# 📊 Estructura de la Base de Datos - Kodigo Kanban API

## 🏗️ Arquitectura General

La base de datos del sistema Kanban está diseñada siguiendo las mejores prácticas de Laravel y utiliza MySQL como motor de base de datos. La estructura está normalizada para evitar redundancia y mantener la integridad referencial.

## 📋 Tablas del Sistema

### 👤 1. Tabla `users` - Usuarios del Sistema

**Propósito**: Almacena la información de todos los usuarios registrados en el sistema.

| Campo | Tipo | Descripción | Restricciones |
|-------|------|-------------|---------------|
| `id` | BIGINT UNSIGNED | Identificador único del usuario | PRIMARY KEY, AUTO_INCREMENT |
| `name` | VARCHAR(255) | Nombre completo del usuario | NOT NULL |
| `email` | VARCHAR(255) | Correo electrónico único | UNIQUE, NOT NULL |
| `email_verified_at` | TIMESTAMP | Fecha de verificación del email | NULLABLE |
| `password` | VARCHAR(255) | Contraseña hasheada | NOT NULL |
| `remember_token` | VARCHAR(100) | Token para "recordar sesión" | NULLABLE |
| `created_at` | TIMESTAMP | Fecha de creación | NOT NULL |
| `updated_at` | TIMESTAMP | Fecha de última actualización | NOT NULL |

**Relaciones**:
- Uno a muchos con `boards` (como propietario)
- Uno a muchos con `cards` (como creador)
- Uno a muchos con `comments` (como autor)
- Muchos a muchos con `roles` (a través de `role_user`)
- Muchos a muchos con `boards` (a través de `board_user`)

---

### 🏷️ 2. Tabla `roles` - Roles del Sistema

**Propósito**: Define los diferentes roles disponibles en el sistema (admin, user, etc.).

| Campo | Tipo | Descripción | Restricciones |
|-------|------|-------------|---------------|
| `id` | BIGINT UNSIGNED | Identificador único del rol | PRIMARY KEY, AUTO_INCREMENT |
| `name` | VARCHAR(255) | Nombre del rol (admin, user) | UNIQUE, NOT NULL |
| `description` | VARCHAR(255) | Descripción del rol | NULLABLE |
| `created_at` | TIMESTAMP | Fecha de creación | NOT NULL |
| `updated_at` | TIMESTAMP | Fecha de última actualización | NOT NULL |

**Relaciones**:
- Muchos a muchos con `users` (a través de `role_user`)

---

### 🔗 3. Tabla `role_user` - Relación Usuario-Rol

**Propósito**: Tabla pivote que asigna roles específicos a usuarios.

| Campo | Tipo | Descripción | Restricciones |
|-------|------|-------------|---------------|
| `id` | BIGINT UNSIGNED | Identificador único de la asignación | PRIMARY KEY, AUTO_INCREMENT |
| `user_id` | BIGINT UNSIGNED | ID del usuario | FOREIGN KEY → users.id, CASCADE |
| `role_id` | BIGINT UNSIGNED | ID del rol | FOREIGN KEY → roles.id, CASCADE |
| `created_at` | TIMESTAMP | Fecha de creación | NOT NULL |
| `updated_at` | TIMESTAMP | Fecha de última actualización | NOT NULL |

**Restricciones únicas**:
- UNIQUE(`user_id`, `role_id`) - Un usuario no puede tener el mismo rol asignado múltiples veces

---

### 📋 4. Tabla `boards` - Tableros Kanban

**Propósito**: Representa los espacios de trabajo principales donde se organizan las tareas.

| Campo | Tipo | Descripción | Restricciones |
|-------|------|-------------|---------------|
| `id` | BIGINT UNSIGNED | Identificador único del tablero | PRIMARY KEY, AUTO_INCREMENT |
| `name` | VARCHAR(255) | Nombre del tablero | NOT NULL |
| `description` | TEXT | Descripción opcional del tablero | NULLABLE |
| `user_id` | BIGINT UNSIGNED | ID del propietario | FOREIGN KEY → users.id, CASCADE |
| `is_public` | BOOLEAN | Indica si es visible públicamente | DEFAULT FALSE |
| `background_color` | VARCHAR(255) | Color de fondo | DEFAULT '#f5f5f5' |
| `background_image` | VARCHAR(255) | URL de imagen de fondo | NULLABLE |
| `created_at` | TIMESTAMP | Fecha de creación | NOT NULL |
| `updated_at` | TIMESTAMP | Fecha de última actualización | NOT NULL |

**Relaciones**:
- Muchos a uno con `users` (propietario)
- Uno a muchos con `board_lists`
- Uno a muchos con `labels`
- Muchos a muchos con `users` (colaboradores, a través de `board_user`)

---

### 📝 5. Tabla `board_lists` - Listas/Columnas de Tableros

**Propósito**: Representa las columnas dentro de un tablero (ej: "Por hacer", "En progreso", "Terminado").

| Campo | Tipo | Descripción | Restricciones |
|-------|------|-------------|---------------|
| `id` | BIGINT UNSIGNED | Identificador único de la lista | PRIMARY KEY, AUTO_INCREMENT |
| `name` | VARCHAR(255) | Nombre de la lista | NOT NULL |
| `board_id` | BIGINT UNSIGNED | ID del tablero padre | FOREIGN KEY → boards.id, CASCADE |
| `position` | INT | Posición de ordenamiento | DEFAULT 0 |
| `is_archived` | BOOLEAN | Indica si está archivada | DEFAULT FALSE |
| `created_at` | TIMESTAMP | Fecha de creación | NOT NULL |
| `updated_at` | TIMESTAMP | Fecha de última actualización | NOT NULL |

**Relaciones**:
- Muchos a uno con `boards`
- Uno a muchos con `cards`

---

### 🎯 6. Tabla `cards` - Tarjetas/Tareas

**Propósito**: Representa las tareas individuales dentro de las listas.

| Campo | Tipo | Descripción | Restricciones |
|-------|------|-------------|---------------|
| `id` | BIGINT UNSIGNED | Identificador único de la tarjeta | PRIMARY KEY, AUTO_INCREMENT |
| `title` | VARCHAR(255) | Título de la tarea | NOT NULL |
| `description` | TEXT | Descripción detallada | NULLABLE |
| `board_list_id` | BIGINT UNSIGNED | ID de la lista contenedora | FOREIGN KEY → board_lists.id, CASCADE |
| `user_id` | BIGINT UNSIGNED | ID del creador | FOREIGN KEY → users.id, CASCADE |
| `assigned_user_id` | BIGINT UNSIGNED | ID del usuario asignado | FOREIGN KEY → users.id, SET NULL |
| `assigned_by` | BIGINT UNSIGNED | ID del usuario que asignó la tarea | FOREIGN KEY → users.id, SET NULL |
| `position` | INT | Posición dentro de la lista | DEFAULT 0 |
| `due_date` | DATETIME | Fecha límite de la tarea | NULLABLE |
| `progress_percentage` | INT | Porcentaje de avance de la tarea (0-100) | DEFAULT 0 |
| `is_completed` | BOOLEAN | Estado de completitud | DEFAULT FALSE |
| `is_archived` | BOOLEAN | Indica si está archivada | DEFAULT FALSE |
| `created_at` | TIMESTAMP | Fecha de creación | NOT NULL |
| `updated_at` | TIMESTAMP | Fecha de última actualización | NOT NULL |

**Relaciones**:
- Muchos a uno con `board_lists`
- Muchos a uno con `users` (creador)
- Muchos a uno con `users` (asignado, a través de `assigned_user_id`)
- Muchos a uno con `users` (asignador, a través de `assigned_by`)
- Uno a muchos con `comments`
- Muchos a muchos con `labels` (a través de `card_label`)

---

### 🏷️ 7. Tabla `labels` - Etiquetas de Clasificación

**Propósito**: Permite categorizar y etiquetar tarjetas con colores. Las etiquetas de prioridad (Bajo, Medio, Alto, Extremo) son globales (board_id = null), mientras que otras etiquetas pueden ser específicas por tablero.

| Campo | Tipo | Descripción | Restricciones |
|-------|------|-------------|---------------|
| `id` | BIGINT UNSIGNED | Identificador único de la etiqueta | PRIMARY KEY, AUTO_INCREMENT |
| `name` | VARCHAR(255) | Nombre de la etiqueta | NOT NULL |
| `color` | VARCHAR(255) | Color en formato hexadecimal | DEFAULT '#0079bf' |
| `board_id` | BIGINT UNSIGNED | ID del tablero al que pertenece (null para etiquetas globales como prioridades) | FOREIGN KEY → boards.id, NULLABLE, CASCADE |
| `created_at` | TIMESTAMP | Fecha de creación | NOT NULL |
| `updated_at` | TIMESTAMP | Fecha de última actualización | NOT NULL |

**Relaciones**:
- Muchos a uno con `boards`
- Muchos a muchos con `cards` (a través de `card_label`)

---

### 💬 8. Tabla `comments` - Comentarios en Tarjetas

**Propósito**: Almacena comentarios y discusiones relacionadas con las tarjetas.

| Campo | Tipo | Descripción | Restricciones |
|-------|------|-------------|---------------|
| `id` | BIGINT UNSIGNED | Identificador único del comentario | PRIMARY KEY, AUTO_INCREMENT |
| `content` | TEXT | Contenido del comentario | NOT NULL |
| `card_id` | BIGINT UNSIGNED | ID de la tarjeta relacionada | FOREIGN KEY → cards.id, CASCADE |
| `user_id` | BIGINT UNSIGNED | ID del autor del comentario | FOREIGN KEY → users.id, CASCADE |
| `created_at` | TIMESTAMP | Fecha de creación | NOT NULL |
| `updated_at` | TIMESTAMP | Fecha de última actualización | NOT NULL |

**Relaciones**:
- Muchos a uno con `cards`
- Muchos a uno con `users` (autor)

---

### 🔗 9. Tabla `card_label` - Relación Tarjeta-Etiqueta

**Propósito**: Tabla pivote que asocia etiquetas a tarjetas.

| Campo | Tipo | Descripción | Restricciones |
|-------|------|-------------|---------------|
| `id` | BIGINT UNSIGNED | Identificador único de la asociación | PRIMARY KEY, AUTO_INCREMENT |
| `card_id` | BIGINT UNSIGNED | ID de la tarjeta | FOREIGN KEY → cards.id, CASCADE |
| `label_id` | BIGINT UNSIGNED | ID de la etiqueta | FOREIGN KEY → labels.id, CASCADE |
| `created_at` | TIMESTAMP | Fecha de creación | NOT NULL |
| `updated_at` | TIMESTAMP | Fecha de última actualización | NOT NULL |

**Restricciones únicas**:
- UNIQUE(`card_id`, `label_id`) - Una tarjeta no puede tener la misma etiqueta asignada múltiples veces

---

### 👥 10. Tabla `board_user` - Colaboradores de Tableros

**Propósito**: Gestiona los colaboradores de un tablero con diferentes niveles de permisos.

| Campo | Tipo | Descripción | Restricciones |
|-------|------|-------------|---------------|
| `id` | BIGINT UNSIGNED | Identificador único de la colaboración | PRIMARY KEY, AUTO_INCREMENT |
| `board_id` | BIGINT UNSIGNED | ID del tablero | FOREIGN KEY → boards.id, CASCADE |
| `user_id` | BIGINT UNSIGNED | ID del colaborador | FOREIGN KEY → users.id, CASCADE |
| `role` | ENUM | Nivel de acceso (viewer, editor, admin) | DEFAULT 'viewer' |
| `created_at` | TIMESTAMP | Fecha de creación | NOT NULL |
| `updated_at` | TIMESTAMP | Fecha de última actualización | NOT NULL |

**Valores del campo `role`**:
- `viewer`: Solo puede ver el contenido
- `editor`: Puede editar tarjetas y listas
- `admin`: Tiene control total del tablero

**Restricciones únicas**:
- UNIQUE(`board_id`, `user_id`) - Un usuario no puede tener múltiples roles en el mismo tablero

---

## 🔧 Tablas del Sistema Laravel

### 🔑 11. Tabla `password_reset_tokens` - Tokens de Reset de Contraseña

**Propósito**: Almacena tokens temporales para recuperación de contraseñas.

| Campo | Tipo | Descripción | Restricciones |
|-------|------|-------------|---------------|
| `email` | VARCHAR(255) | Email del usuario | PRIMARY KEY |
| `token` | VARCHAR(255) | Token de recuperación | NOT NULL |
| `created_at` | TIMESTAMP | Fecha de creación | NULLABLE |

### 📊 12. Tabla `sessions` - Sesiones de Usuario

**Propósito**: Gestiona las sesiones activas de los usuarios.

| Campo | Tipo | Descripción | Restricciones |
|-------|------|-------------|---------------|
| `id` | VARCHAR(255) | ID único de la sesión | PRIMARY KEY |
| `user_id` | BIGINT UNSIGNED | ID del usuario | FOREIGN KEY → users.id, INDEX, NULLABLE |
| `ip_address` | VARCHAR(45) | Dirección IP del cliente | NULLABLE |
| `user_agent` | TEXT | Información del navegador | NULLABLE |
| `payload` | LONGTEXT | Datos serializados de la sesión | NOT NULL |
| `last_activity` | INT | Timestamp de última actividad | INDEX, NOT NULL |

### ⚡ 13. Tabla `cache` - Sistema de Cache

**Propósito**: Almacena datos en caché para mejorar el rendimiento.

| Campo | Tipo | Descripción | Restricciones |
|-------|------|-------------|---------------|
| `key` | VARCHAR(255) | Clave única del caché | PRIMARY KEY |
| `value` | MEDIUMTEXT | Valor almacenado | NOT NULL |
| `expiration` | INT | Timestamp de expiración | NOT NULL |

---

## 🔗 Diagrama de Relaciones

```
users (1) ──── (M) boards (1) ──── (M) board_lists (1) ──── (M) cards
   │                    │                      │
   │                    │                      │
   └──── (M) role_user (M) ──── roles          └──── (M) comments
                           │                           │
                           │                           │
                           └───────────────────────────┘

users (1) ──── (M) board_user (M) ──── boards
   │
   │
   └──── (M) comments

cards (M) ──── (M) card_label (M) ──── labels
```

---

## 🛡️ Restricciones de Integridad

- **Eliminación en cascada**: Cuando se elimina un usuario, tablero, lista, etc., todos los registros relacionados se eliminan automáticamente
- **Claves únicas**: Previenen duplicados en relaciones muchos a muchos
- **Claves foráneas**: Garantizan que las referencias sean válidas
- **Validaciones a nivel de aplicación**: Reglas de negocio adicionales implementadas en los controladores

---

## 📈 Consideraciones de Rendimiento

- **Índices automáticos**: Laravel crea índices en claves primarias y foráneas
- **Posicionamiento**: Las tablas con ordenamiento (board_lists, cards) usan campos `position` para mantener el orden
- **Cache**: Implementado para mejorar el rendimiento de consultas frecuentes
- **Paginación**: Recomendada para endpoints que retornan listas grandes

---

## 🔄 Migraciones y Versionado

Todas las tablas se crean y modifican a través de migraciones de Laravel, lo que permite:
- Control de versiones de la base de datos
- Rollback de cambios
- Sincronización entre entornos de desarrollo
- Documentación automática de cambios en el esquema

---

**[⬅️ Volver al README principal](../README.md)** | **[📖 Documentación de la API](../API_DOCUMENTATION.md)**