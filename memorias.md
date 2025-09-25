## Tarea: Implementar gestión de roles de usuario

- Se crearon las migraciones para las tablas `roles` y la tabla pivote `role_user`.
- Se detectó un problema con la ejecución de migraciones debido a archivos antiguos con fechas inválidas y a la tabla `migrations` en la base de datos.
- Se renombraron los archivos de migración antiguos con fechas actuales.
- Se recomendó eliminar manualmente la tabla `migrations` en la base de datos MySQL para forzar la reejecución de todas las migraciones.
- Una vez eliminada la tabla, se debe ejecutar `php artisan migrate:fresh --seed` para crear todas las tablas y poblar los roles iniciales.
- Se actualizó el DatabaseSeeder para incluir el RoleSeeder y se ejecutaron las migraciones con el comando `php artisan migrate:fresh --seed`.
- Se verificó que las tablas se crearan correctamente y se crearon los roles 'admin' y 'user' mediante el seeder.
- Se verificó la estructura de las tablas mediante MCP:
  - La tabla `users` tiene todos los campos necesarios para la autenticación.
  - La tabla `roles` tiene campos `id`, `name` (unique), `description` (nullable) y timestamps.
  - La tabla `role_user` tiene la estructura correcta para una tabla pivote con campos `user_id` y `role_id`.
- Se asignó el rol 'admin' al usuario de prueba (test@example.com) mediante una inserción manual en la tabla `role_user`.

## Resumen de Avances del Proyecto

### Estructura del Proyecto
- Se ha inicializado correctamente un proyecto Laravel 12
- Se ha configurado la conexión a la base de datos MySQL
- Se han generado certificados autofirmados para JWT
- Se ha implementado una estructura avanzada de versionado de API

### Migraciones y Modelos
- Se han creado las migraciones para las tablas `users`, `roles` y `role_user`
- Se han ejecutado exitosamente las migraciones
- Se han creado los modelos `User` y `Role`

### Seeders
- Se ha implementado el `RoleSeeder` que crea roles 'admin' y 'user'
- Se ha actualizado el `DatabaseSeeder` para crear un usuario de prueba y llamar al `RoleSeeder`

### Estado Actual del Sistema
1. **Base de datos:**
   - Tablas creadas: `users`, `roles`, `role_user`, `migrations`, `password_reset_tokens`, `sessions`
   - Datos iniciales: roles ('admin', 'user'), usuario de prueba (test@example.com)
   - Relaciones: El usuario de prueba tiene asignado el rol de administrador

2. **Sistema de Autenticación:**
   - La estructura básica para la autenticación de usuarios está configurada
   - El sistema de roles está implementado con una relación muchos a muchos

3. **API:**
   - Se ha implementado una estructura de versionado que facilita la evolución de la API
   - Se han configurado las rutas básicas en `routes/api_v1.php`

### Próximos Pasos
- Desarrollar la funcionalidad core del tablero Kanban
- Implementar endpoints para la gestión de tableros y tarjetas
- Desarrollar la lógica de negocio para el seguimiento de tareas
- Implementar filtros y búsquedas para el tablero Kanban

## Tarea: Prueba de endpoints de autenticación y creación de documentación

- Se creó una colección de Postman (`postman_collection.json`) con todos los endpoints de la API
- Se desarrolló documentación detallada de la API (`API_DOCUMENTATION.md`) con información sobre:
  - Endpoints disponibles
  - Formato de peticiones y respuestas
  - Autenticación y autorización
  - Usuarios de prueba
- Se implementó un controlador para el dashboard de administrador (`AdminController`) para probar la protección basada en roles
- Se creó un script de prueba (`test_api.bat`) para verificar el funcionamiento de los endpoints mediante cURL
- Se verificó la estructura de la base de datos y los usuarios existentes usando el MCP de MySQL

El sistema de autenticación está completamente probado y documentado. La colección de Postman facilita las pruebas manuales, mientras que el script de batch permite verificar rápidamente la funcionalidad básica de la API.

## Tarea: Implementar autenticación JWT y controladores para usuarios

- Se configuró el sistema de autenticación usando JWT (JSON Web Tokens)
- Se consolidaron las configuraciones de JWT en el archivo `.env`, utilizando principalmente la clave secreta (`JWT_SECRET`) junto con el algoritmo (`JWT_ALGO=HS256`) y tiempo de vida del token (`JWT_TTL=60`)
- Se creó el `AuthController` en la estructura de API versionada (`app/Http/Controllers/API/V1/AuthController.php`)
- Se implementaron los métodos para registro, login, logout, y obtención de información del usuario
- Se corrigieron los métodos para adaptarlos a Laravel 12:
  - Se eliminó el uso de middleware en el constructor del controlador
  - Se reemplazó el método `load()` por `with()` para cargar relaciones
  - Se actualizó el método `refresh()` para usar `JWTAuth` directamente
  - Se corrigió la obtención del TTL desde la configuración
- Se actualizó el modelo `User` para implementar la interfaz `JWTSubject` y se definieron los métodos requeridos
- Se crearon métodos de ayuda en el modelo `User` para verificar roles (`hasRole`, `hasAnyRole`)
- Se definieron las relaciones entre los modelos `User` y `Role` usando `belongsToMany`
- Se implementó el middleware `CheckRole` para proteger rutas según el rol del usuario
- Se actualizó el archivo `bootstrap/app.php` para registrar el middleware con el alias `auth.role`
- Se configuraron las rutas en `routes/api_v1.php` con estructura versionada y agrupación lógica
- Se separaron las rutas públicas, protegidas para usuarios autenticados y protegidas para administradores
  - Se movió la protección al nivel de rutas en lugar de en el controlador
- Se configuró CORS (Cross-Origin Resource Sharing) en el `AppServiceProvider` para permitir peticiones desde otros dominios

La implementación de autenticación JWT está completa y lista para ser utilizada por el frontend. El sistema ahora soporta:
- Registro de nuevos usuarios (asignándoles automáticamente el rol 'user')
- Login con generación de token JWT
- Protección de rutas mediante autenticación
- Control de acceso basado en roles
- Endpoints para obtener información del usuario autenticado

## Tarea: Solucionar problema de autenticación JWT

- Se identificó un error relacionado con el parseo de las claves RSA en la configuración de JWT
- El error: "Could not create token: It was not possible to parse your key, reason: error:1E08010C:DECODER routines::unsupported"
- Se intentó generar un nuevo par de claves RSA pero persistía el problema de formato
- Se decidió cambiar la estrategia de cifrado de JWT de RSA256 (asimétrico) a HS256 (simétrico)
- Se actualizó el archivo `.env` para utilizar HS256 como algoritmo de JWT, eliminando las referencias a las claves pública/privada y utilizando únicamente una clave secreta compartida
- Se verificaron los endpoints de autenticación utilizando HTTPie desde la línea de comandos:
  - Login: `http POST http://localhost:8000/api/v1/auth/login email=test@example.com password=password`
  - Me (info usuario): `http GET http://localhost:8000/api/v1/auth/me "Authorization:Bearer {token}"`
- Se probó también el acceso al dashboard de administrador y funcionó correctamente
- Los problemas de tipo de datos en el TTL se resolvieron asegurando que el valor se convirtiera a entero antes de usarlo

El sistema de autenticación ahora funciona correctamente y genera tokens JWT válidos que pueden utilizarse para acceder a endpoints protegidos. La simplificación del algoritmo de JWT a HS256 mejoró la estabilidad y eliminó los problemas de formato de clave.

## Tarea: Implementar funcionalidad core de tableros Kanban

- Se crearon las migraciones para las entidades principales del sistema Kanban:
  - `boards` - Tableros
  - `board_lists` - Listas/Columnas dentro de un tablero
  - `cards` - Tarjetas que representan tareas
  - `labels` - Etiquetas para categorizar tarjetas
  - `comments` - Comentarios en las tarjetas
  - `card_label` - Tabla pivote para relación muchos a muchos entre tarjetas y etiquetas
  - `board_user` - Tabla pivote para colaboradores de tableros
- Se definieron las relaciones y restricciones adecuadas en las tablas:
  - Claves foráneas con eliminación en cascada
  - Índices únicos para evitar duplicados
  - Campos necesarios para la gestión de posición y estados
- Se implementaron los modelos correspondientes con sus relaciones:
  - `Board` - Con relaciones a usuario propietario, listas, etiquetas y colaboradores
  - `BoardList` - Con relaciones a tablero y tarjetas
  - `Card` - Con relaciones a lista, usuario, comentarios y etiquetas
  - `Label` - Con relaciones a tablero y tarjetas
  - `Comment` - Con relaciones a tarjeta y usuario
- Se actualizó el modelo `User` para incluir relaciones con:
  - Tableros propios
  - Tableros en los que colabora
  - Tarjetas creadas
  - Comentarios realizados
- Se definieron los atributos asignables (`fillable`) y conversiones de tipo (`casts`) en todos los modelos
- Se ejecutaron las migraciones y se verificó la creación correcta de las tablas en la base de datos

La estructura de datos para el sistema Kanban está completamente implementada, proporcionando una base sólida para desarrollar las funcionalidades de gestión de tableros, listas y tarjetas. La siguiente fase consistirá en desarrollar los controladores y endpoints de API para manipular estas entidades.

## Uso de Postman para probar la API

Para facilitar las pruebas de la API, se ha creado una colección de Postman con todos los endpoints disponibles. Para utilizarla:

1. **Importar la colección y el entorno**:
   - Abrir Postman
   - Importar el archivo `postman_collection.json` (Archivo > Importar)
   - Importar el archivo `postman_environment.json`
   - Seleccionar el entorno "Kanban API Environment" en el selector de entornos

2. **Configurar el entorno**:
   - El entorno ya tiene configurada la variable `base_url` como `http://localhost:8000`
   - La variable `token` se llenará automáticamente al hacer login

3. **Flujo de prueba recomendado**:
   - Ejecutar el endpoint "Auth > Register" para crear un nuevo usuario (si es necesario)
   - Ejecutar el endpoint "Auth > Login" para obtener un token JWT
   - El token se guarda automáticamente en la variable de entorno `token`
   - Usar los demás endpoints que requieren autenticación

4. **Ejecutar la colección completa**:
   - Se puede ejecutar la colección completa usando el Runner de Postman
   - Ir a la pestaña "Runner" y seleccionar la colección "Kodigo Kanban API"
   - Configurar el orden de ejecución según sea necesario

5. **Verificar respuestas**:
   - Todas las respuestas exitosas deben tener un código de estado 2xx
   - Las respuestas de error tendrán códigos 4xx o 5xx con mensajes descriptivos

Esta colección se irá actualizando a medida que se implementen nuevos endpoints para el sistema Kanban.

# Memorias de Desarrollo

## Tarea: Inicializar proyecto Laravel 12 y configurar base de datos

- Se revisaron los requerimientos y se generó un plan de trabajo detallado usando Taskmaster.
- Se creó el archivo PRD y se generaron automáticamente las tareas principales del proyecto.
- Se priorizó el versionamiento de la API como una de las primeras tareas tras la configuración base de Laravel.
- Se intentó crear el proyecto Laravel 12 en la carpeta de trabajo, pero hubo problemas de permisos.
- Se recomendó ejecutar el comando en una ruta con permisos o como administrador.
- Finalmente, se logró instalar Laravel 12 en la subcarpeta `kanban-api`.
- Se configuró la conexión a la base de datos MySQL en el archivo `.env` usando los valores: nombre de base de datos, usuario y contraseña como `kanban`.
- Se ejecutó el comando `php artisan migrate` y las migraciones se aplicaron correctamente, confirmando la conexión exitosa.
- Se generaron certificados autofirmados para JWT y se guardaron en la carpeta `certs` del proyecto (`certs/jwt.key` y `certs/jwt.crt`).
- Se agregaron las variables `JWT_PRIVATE_KEY_PATH` y `JWT_PUBLIC_CERT_PATH` en los archivos `.env` y `.env.example` para documentar la ruta de los certificados.
- Se implementó una estructura avanzada de versionado de API:
	- Se creó el archivo `routes/api_v1.php` para la versión 1 de la API.
	- El archivo `routes/api.php` quedó como entrypoint vacío para futuras versiones.
	- Se configuró el `RouteServiceProvider` para registrar cada versión de la API con su propio archivo y prefijo.
- Esto permite mantener el código de cada versión aislado y facilita la evolución de la API.
  
El siguiente paso es continuar con la siguiente tarea de Taskmaster.
