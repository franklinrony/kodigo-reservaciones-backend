# 🚀 Kodigo Kanban API - Backend

[![Laravel Version](https://img.shields.io/badge/Laravel-12-red.svg)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://mysql.com)
[![JWT](https://img.shields.io/badge/JWT-Auth-green.svg)](https://jwt.io)
[![Swagger](https://img.shields.io/badge/Swagger-OpenAPI-yellow.svg)](https://swagger.io)

> **Proyecto Final del Bootcamp Fullstack Jr FSJ28 - Kodigo**
>
> Una API REST completa para gestión de tableros Kanban desarrollada con Laravel 12

## 📋 Tabla de Contenidos

- [🚀 Descripción del Proyecto](#-descripción-del-proyecto)
- [✨ Características Principales](#-características-principales)
- [🛠️ Tecnologías Utilizadas](#️-tecnologías-utilizadas)
- [🏗️ Arquitectura del Sistema](#️-arquitectura-del-sistema)
- [📦 Instalación y Configuración](#-instalación-y-configuración)
- [🚀 Levantar el Proyecto](#-levantar-el-proyecto)
- [📚 Documentación de la API](#-documentación-de-la-api)
- [🧪 Testing y Validación](#-testing-y-validación)
- [📊 Estructura de Base de Datos](#-estructura-de-base-de-datos)
- [🔒 Sistema de Autenticación](#-sistema-de-autenticación)
- [👥 Control de Acceso y Roles](#-control-de-acceso-y-roles)
- [📋 Funcionalidades Técnicas](#-funcionalidades-técnicas)
- [🔧 Comandos Útiles](#-comandos-útiles)
- [📈 Despliegue en Producción](#-despliegue-en-producción)
- [👨‍💻 Autor](#-autor)
- [📄 Licencia](#-licencia)

---

## 🚀 Descripción del Proyecto

**Kodigo Kanban API** es una aplicación backend RESTful completa que implementa un sistema de gestión de tableros Kanban. Desarrollada como proyecto final del bootcamp Fullstack Jr FSJ28 de Kodigo, esta API permite crear, gestionar y colaborar en tableros Kanban con funcionalidades avanzadas de organización de tareas.

### 🎯 Objetivo

Crear una API robusta y escalable que sirva como backend para aplicaciones de gestión de proyectos, permitiendo a los equipos organizar sus tareas de manera visual y eficiente mediante el método Kanban.

---

## ✨ Características Principales

### 📋 Gestión de Tableros
- ✅ Crear tableros personalizados con colores e imágenes de fondo
- ✅ Gestión de colaboradores con diferentes niveles de acceso
- ✅ Tableros públicos y privados
- ✅ Dashboard administrativo para supervisión

### 📝 Organización de Tareas
- ✅ Listas/columnas personalizables (Por hacer, En progreso, Terminado, etc.)
- ✅ Tarjetas con títulos, descripciones y fechas límite
- ✅ Asignación de usuarios responsables a tareas
- ✅ Seguimiento de progreso porcentual con auto-completado automático
- ✅ Sistema de etiquetas con colores para categorización (incluyendo prioridades globales)
- ✅ Reordenamiento intuitivo de listas y tarjetas

### 💬 Colaboración
- ✅ Comentarios en tarjetas para discusiones
- ✅ Sistema de roles y permisos granular
- ✅ Notificaciones y seguimiento de actividad
- ✅ Control de versiones y auditoría

### 🔒 Seguridad
- ✅ Autenticación JWT con refresh tokens
- ✅ Control de acceso basado en roles (User, Admin)
- ✅ Validación completa de datos
- ✅ Protección contra ataques comunes

---

## 🛠️ Tecnologías Utilizadas

### Backend Framework
- **Laravel 12** - Framework PHP moderno y robusto
- **PHP 8.2+** - Lenguaje de programación principal

### Base de Datos
- **MySQL 8.0+** - Sistema de gestión de base de datos relacional
- **Laravel Eloquent ORM** - Mapeo objeto-relacional

### Autenticación y Seguridad
- **JWT (JSON Web Tokens)** - Autenticación stateless
- **Laravel Sanctum** - Sistema de autenticación API
- **bcrypt** - Hashing de contraseñas

### API y Documentación
- **RESTful API** - Arquitectura de servicios web
- **Swagger/OpenAPI** - Documentación interactiva
- **L5-Swagger** - Generación automática de documentación

### Desarrollo y Testing
- **Composer** - Gestión de dependencias PHP
- **PHPUnit** - Framework de testing
- **Laravel Dusk** - Testing de navegador (opcional)

### Servidor Web
- **Apache/Nginx** - Servidores web para producción
- **Laravel Artisan** - Servidor de desarrollo

---

## 🏗️ Arquitectura del Sistema

```
📁 Kodigo Kanban API
├── 🎯 API RESTful (Laravel 12)
├── 🔐 Autenticación JWT
├── 📊 Base de Datos MySQL
├── 📚 Documentación Swagger
└── 🧪 Testing Suite

📋 Entidades Principales:
├── 👤 Users (Usuarios)
├── 📋 Boards (Tableros)
├── 📝 BoardLists (Listas)
├── 🎯 Cards (Tarjetas)
├── 🏷️ Labels (Etiquetas)
├── 💬 Comments (Comentarios)
└── 👥 Roles (Sistema de roles)
```

### 🏛️ Patrón Arquitectural

- **MVC (Model-View-Controller)** - Patrón de diseño principal
- **Repository Pattern** - Abstracción de la capa de datos
- **Service Layer** - Lógica de negocio centralizada
- **Middleware** - Control de acceso y validaciones
- **Resource Classes** - Transformación de respuestas API

---

## 📦 Instalación y Configuración

### 📋 Prerrequisitos

Antes de comenzar, asegúrate de tener instalado:

- **PHP 8.2 o superior**
- **Composer** (Gestor de dependencias PHP)
- **MySQL 8.0 o superior**
- **Git** (Para clonar el repositorio)
- **Node.js y NPM** (Opcional, para assets frontend si los hay)

### 🔄 Pasos de Instalación

#### 1. 📥 Clonar el Repositorio

```bash
# Clonar el proyecto desde GitHub
git clone https://github.com/tu-usuario/kodigo-kanban-backend.git
cd kodigo-kanban-backend
```

#### 2. 📦 Instalar Dependencias PHP

```bash
# Instalar todas las dependencias del proyecto
composer install
```

#### 3. 🔧 Configurar Variables de Entorno

```bash
# Copiar el archivo de configuración de ejemplo
cp .env.example .env
```

Editar el archivo `.env` con tus configuraciones:

```env
# Configuración de la aplicación
APP_NAME="Kodigo Kanban API"
APP_ENV=local
APP_KEY=base64:tu_clave_aqui
APP_DEBUG=true
APP_URL=http://localhost:8000

# Configuración de base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kanban_api
DB_USERNAME=tu_usuario_mysql
DB_PASSWORD=tu_password_mysql

# Configuración JWT
JWT_SECRET=tu_clave_secreta_jwt_muy_larga_y_segura
JWT_ALGO=HS256
JWT_TTL=60

# Configuración de correo (opcional)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

#### 4. 🔑 Generar Clave de Aplicación

```bash
# Generar una clave única para la aplicación
php artisan key:generate
```

#### 5. 🗄️ Configurar Base de Datos

```bash
# Crear la base de datos en MySQL
mysql -u root -p
CREATE DATABASE kanban_api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

#### 6. 🏗️ Ejecutar Migraciones y Seeders

```bash
# Ejecutar migraciones para crear las tablas
php artisan migrate

# Ejecutar seeders para poblar datos iniciales
php artisan db:seed

# O ejecutar ambos comandos juntos
php artisan migrate:fresh --seed
```

#### 7. 📚 Generar Documentación API (Opcional)

```bash
# Generar documentación Swagger
php artisan l5-swagger:generate
```

---

## 🚀 Levantar el Proyecto

### 🏠 Servidor de Desarrollo

#### Opción 1: Usando Artisan (Recomendado para desarrollo)

```bash
# Levantar el servidor en el puerto 8000
php artisan serve

# O especificar host y puerto
php artisan serve --host=127.0.0.1 --port=8000
```

El servidor estará disponible en: `http://localhost:8000`

#### Opción 2: Usando Docker (Si tienes Docker configurado)

```bash
# Si tienes un Dockerfile configurado
docker-compose up -d
```

### 🏭 Servidor de Producción (Apache/Nginx)

#### Configuración Apache

Crear un Virtual Host en Apache:

```apache
<VirtualHost *:80>
    ServerName kanban-api.tu-dominio.com
    DocumentRoot /var/www/kodigo-kanban-backend/public

    <Directory /var/www/kodigo-kanban-backend/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/kanban-api-error.log
    CustomLog ${APACHE_LOG_DIR}/kanban-api-access.log combined
</VirtualHost>
```

#### Configuración Nginx

```nginx
server {
    listen 80;
    server_name kanban-api.tu-dominio.com;
    root /var/www/kodigo-kanban-backend/public;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    index index.php index.html index.htm;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Comandos de Despliegue

```bash
# Cambiar permisos de storage y bootstrap/cache
sudo chown -R www-data:www-data /var/www/kodigo-kanban-backend
sudo chown -R www-data:www-data /var/www/kodigo-kanban-backend/storage
sudo chown -R www-data:www-data /var/www/kodigo-kanban-backend/bootstrap/cache

# Limpiar y optimizar la aplicación
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Reiniciar servicios
sudo systemctl restart apache2  # Para Apache
sudo systemctl restart nginx    # Para Nginx
sudo systemctl restart php8.2-fpm
```

---

## 📚 Documentación de la API

### 🌐 Acceder a la Documentación

Una vez que el servidor esté ejecutándose, puedes acceder a:

- **📖 Documentación Swagger UI**: `http://localhost:8000/api/documentation`
- **📋 JSON de la API**: `http://localhost:8000/docs`
- **📚 Documentación Detallada**: Ver [API_DOCUMENTATION.md](./API_DOCUMENTATION.md)

### 🔧 Endpoints Principales

#### Sistema
```http
GET /api/v1/health    # Health check de la aplicación
```

#### Autenticación
```http
POST /api/auth/register   # Registro de usuario
POST /api/auth/login      # Inicio de sesión
GET  /api/auth/me         # Información del usuario actual
POST /api/auth/logout     # Cierre de sesión
POST /api/auth/refresh    # Refresco de token
```

#### Tableros
```http
GET    /api/v1/boards                    # Listar tableros
POST   /api/v1/boards                    # Crear tablero
GET    /api/v1/boards/{id}               # Ver tablero
PUT    /api/v1/boards/{id}               # Actualizar tablero
DELETE /api/v1/boards/{id}               # Eliminar tablero
POST   /api/v1/boards/{id}/collaborators # Agregar colaborador
```

#### Listas y Tarjetas
```http
GET    /api/v1/boards/{boardId}/lists    # Listar listas
POST   /api/v1/boards/{boardId}/lists    # Crear lista
GET    /api/v1/lists/{listId}/cards      # Listar tarjetas
POST   /api/v1/lists/{listId}/cards      # Crear tarjeta
PUT    /api/v1/cards/{id}                # Actualizar tarjeta
```

### 🧪 Testing con Postman

#### Importar Colección
1. Abrir Postman
2. Importar `postman/postman_collection_reordered.json`
3. Importar `postman/postman_environment.json`
4. Seleccionar "Kanban API Environment"

#### Flujo de Testing
1. **Registro/Login** → Obtener token JWT
2. **Crear Tablero** → Crear un espacio de trabajo
3. **Crear Listas** → Agregar columnas al tablero
4. **Crear Tarjetas** → Agregar tareas a las listas
5. **Gestionar Etiquetas** → Crear categorías para las tarjetas
6. **Agregar Comentarios** → Comentar en las tarjetas

---

## 🧪 Testing y Validación

### Ejecutar Tests

```bash
# Ejecutar todos los tests
php artisan test

# Ejecutar tests específicos
php artisan test --filter=AuthControllerTest

# Ejecutar tests con cobertura
php artisan test --coverage
```

### Testing con Postman CLI

```bash
# Instalar Postman CLI si no lo tienes
npm install -g @postman/cli

# Ejecutar colección completa
postman collection run postman/postman_collection_reordered.json \
  --environment postman/postman_environment.json \
  --reporters cli,json \
  --reporter-json-export test-results.json
```

### Validación de Endpoints

```bash
# Verificar rutas registradas
php artisan route:list

# Verificar estado de la aplicación
php artisan tinker
>>> app()->version()
>>> config('app.name')
```

---

## 📊 Estructura de Base de Datos

Para información detallada sobre la estructura de la base de datos, consulta:

📋 **[DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md)** - Documentación completa de tablas, campos y relaciones

### 🗄️ Diagrama de Entidades

```
Users (1) ──── (M) Boards (1) ──── (M) BoardLists (1) ──── (M) Cards
   │                    │                      │
   │                    │                      │
   └──── (M) Role_User (M) ──── Roles          └──── (M) Comments

Cards (M) ──── (M) Card_Label (M) ──── Labels
Users (M) ──── (M) Board_User (M) ──── Boards
```

### 📊 Tablas Principales

- **`users`** - Información de usuarios
- **`boards`** - Tableros Kanban
- **`board_lists`** - Listas/columnas dentro de tableros
- **`cards`** - Tarjetas/tareas
- **`labels`** - Etiquetas para categorizar tarjetas
- **`comments`** - Comentarios en tarjetas
- **`roles`** - Sistema de roles y permisos

---

## 🔒 Sistema de Autenticación

### JWT (JSON Web Tokens)

La API utiliza autenticación JWT para proteger los endpoints:

```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "bearer",
  "expires_in": 3600
}
```

### Headers Requeridos

```http
Authorization: Bearer {tu_token_jwt_aqui}
Content-Type: application/json
```

### Gestión de Tokens

- **TTL**: 60 minutos por defecto
- **Refresh**: Endpoint para renovar tokens expirados
- **Logout**: Invalida tokens activos

---

## 👥 Control de Acceso y Roles

### 🏷️ Roles del Sistema

#### 👤 Usuario Regular (`user`)
- ✅ Crear y gestionar sus propios tableros
- ✅ Gestionar tarjetas en tableros donde es colaborador
- ✅ Crear comentarios en tarjetas

#### 👥 Colaborador de Tablero
- **`viewer`**: Solo lectura
- **`editor`**: Lectura y edición
- **`admin`**: Control total del tablero

#### 🛡️ Administrador del Sistema (`admin`)
- ✅ Acceso a dashboard administrativo
- ✅ Gestionar todos los recursos del sistema
- ✅ Supervisar actividad de usuarios

### 🔐 Permisos por Endpoint

| Endpoint | Usuario Regular | Colaborador | Admin Sistema |
|----------|----------------|-------------|---------------|
| `GET /boards` | ✅ Propios | ✅ Compartidos | ✅ Todos |
| `POST /boards` | ✅ | ❌ | ✅ |
| `PUT /boards/{id}` | ✅ Propietario | ✅ Admin tablero | ✅ |
| `DELETE /boards/{id}` | ✅ Propietario | ✅ Admin tablero | ✅ |

---

## 📋 Funcionalidades Técnicas

### ✅ Características Implementadas

- **🏗️ Arquitectura RESTful** - Endpoints bien diseñados siguiendo estándares REST
- **🔄 Versionado de API** - Sistema v1 preparado para futuras evoluciones
- **📄 Paginación Automática** - Para endpoints que retornan listas grandes
- **🔍 Validación Completa** - Validaciones a nivel de controlador y modelo
- **⚡ Optimización de Consultas** - Eager loading y consultas optimizadas
- **🛡️ Middleware de Seguridad** - Protección contra ataques comunes
- **📊 Logging y Monitoreo** - Sistema de logs para debugging y auditoría
- **🔄 Cache** - Implementación de cache para mejorar rendimiento
- **📱 API Responsiva** - Respuestas JSON consistentes y bien estructuradas

### 🛠️ Middlewares Utilizados

- **`auth:api`** - Autenticación JWT
- **`auth.role`** - Control de roles personalizado
- **`throttle`** - Limitación de tasa de requests
- **`json`** - Forzar respuestas JSON

### 📈 Rendimiento

- **Consultas Optimizadas** - Uso de índices y eager loading
- **Cache de Configuración** - Configuraciones en caché para producción
- **Compresión de Respuestas** - Gzip automático
- **Pool de Conexiones DB** - Conexiones persistentes a MySQL

---

## 🔧 Comandos Útiles

### 🗄️ Base de Datos

```bash
# Limpiar caché de configuración
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Recrear base de datos con seeders
php artisan migrate:fresh --seed

# Crear nueva migración
php artisan make:migration create_example_table

# Crear seeder
php artisan make:seeder ExampleSeeder
```

### 📚 Documentación

```bash
# Generar documentación Swagger
php artisan l5-swagger:generate

# Publicar assets de Swagger
php artisan l5-swagger:publish
```

### 🧪 Testing

```bash
# Crear test
php artisan make:test AuthControllerTest

# Ejecutar tests con cobertura
php artisan test --coverage --min=80
```

### 🚀 Despliegue

```bash
# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Limpiar cachés de optimización
php artisan optimize:clear
```

---

## 📈 Despliegue en Producción

### 🌐 Requisitos del Servidor

- **PHP 8.2+** con extensiones requeridas
- **MySQL 8.0+** o MariaDB 10.5+
- **Composer** para gestión de dependencias
- **SSL Certificate** para HTTPS
- **Servidor Web** (Apache/Nginx) con PHP-FPM

### 🚀 Checklist de Despliegue

#### 1. 📦 Preparar el Código
```bash
# Clonar en servidor de producción
git clone https://github.com/franklinrony/kodigo-reservaciones-backend 
cd kanban-api

# Instalar dependencias
composer install --optimize-autoloader --no-dev
```

#### 2. 🔧 Configurar Entorno
```bash
# Copiar configuración de producción
cp .env.example .env.production
# Editar .env.production con valores de producción

# Generar clave de aplicación
php artisan key:generate
```

#### 3. 🗄️ Configurar Base de Datos
```bash
# Ejecutar migraciones
php artisan migrate --force

# Ejecutar seeders (solo en primera instalación)
php artisan db:seed --force
```

#### 4. 🔒 Configurar Permisos
```bash
# Asignar permisos correctos
sudo chown -R www-data:www-data /var/www/kanban-api
sudo chmod -R 755 /var/www/kanban-api
sudo chmod -R 775 /var/www/kanban-api/storage
sudo chmod -R 775 /var/www/kanban-api/bootstrap/cache
```

#### 5. ⚡ Optimizar Rendimiento
```bash
# Cache de configuraciones
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Generar documentación (opcional)
php artisan l5-swagger:generate
```

#### 6. 🌐 Configurar Servidor Web

**Apache**:
```bash
sudo a2ensite kanban-api.conf
sudo systemctl restart apache2
```

**Nginx**:
```bash
sudo ln -s /etc/nginx/sites-available/kanban-api /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### 7. 🔐 Configurar SSL (Recomendado)
```bash
# Usando Let's Encrypt
sudo certbot --nginx -d kanban-api.tu-dominio.com
```

#### 8. 📊 Monitoreo y Logs
```bash
# Ver logs de Laravel
tail -f storage/logs/laravel.log

# Ver logs de PHP
tail -f /var/log/php8.2-fpm.log

# Ver logs del servidor web
tail -f /var/log/apache2/error.log  # Apache
tail -f /var/log/nginx/error.log    # Nginx
```

### 🔄 Estrategia de Despliegue

#### Zero-Downtime Deployment
```bash
# Usando Deployer o similar
dep deploy production

# O manualmente con Git
git pull origin main
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Health Checks
```bash
# Endpoint de health check
curl -f http://localhost:8000/api/v1/health

# Verificar base de datos
php artisan tinker --execute="DB::connection()->getPdo()"
```

### 👥 Usuarios de Prueba

Después de ejecutar los seeders (`php artisan db:seed`), estarán disponibles los siguientes usuarios de prueba:

| Email | Password | Rol | Nombre |
|-------|----------|-----|--------|
| `admin@kodigo.com` | `password` | Admin | Administrador Kodigo |
| `test@example.com` | `password` | User | Usuario de Prueba |
| `maria@example.com` | `password` | User | María González |
| `carlos@example.com` | `password` | User | Carlos Rodríguez |
| `ana@example.com` | `password` | User | Ana López |
| `pedro@example.com` | `password` | User | Pedro Martínez |
| `laura@example.com` | `password` | User | Laura Sánchez |

**Nota**: Todos los usuarios tienen la contraseña `password`. Se recomienda cambiar las contraseñas en un entorno de producción.

---

## 👨‍💻 Autor

**Proyecto desarrollado por:** Franklin Rony Cortez Barrera

**Bootcamp:** Fullstack Jr FSJ28 - Kodigo

**Fecha:** Septiembre 2025

### 📞 Contacto

- **Email:** tu-email@ejemplo.com
- **GitHub:** [https://github.com/tu-usuario](https://github.com/tu-usuario)
- **LinkedIn:** [Tu perfil de LinkedIn](https://linkedin.com/in/tu-perfil)

---

## 📄 Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para más detalles.

---

## 🙏 Agradecimientos

- **Kodigo** por el bootcamp Fullstack Jr FSJ28
- **Laravel Community** por el excelente framework
- **Open Source Community** por las herramientas utilizadas

---

## Database Schema

### Cards Table
- `user_id`: The user who created the card.
- `assigned_user_id`: The user assigned to execute the card.
- `assigned_by`: The user who assigned the card (new field). This tracks who delegated the task, which may differ from the creator in collaborative boards.

## API Endpoints

### Create Card
- **Method**: POST `/api/cards`
- **Body**: Include `assigned_by` (optional, integer, user ID who assigned the task).
- **Response**: Card object with `assigned_by` field.

### Update Card
- **Method**: PUT `/api/cards/{id}`
- **Body**: Include `assigned_by` (optional, integer, user ID who assigned the task).
- **Response**: Updated card object with `assigned_by` field.

