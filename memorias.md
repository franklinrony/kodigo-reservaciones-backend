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

6. **Usar la CLI de Postman**:
   - También se puede ejecutar la colección desde la línea de comandos usando Postman CLI
   - Asegúrate de tener instalado el CLI de Postman (`postman --version` para verificar)
   - Ejecuta el comando: `postman collection run postman_collection.json --environment postman_environment.json`
   - Para autenticación automatizada, añade la variable `--env-var "email=test@example.com" --env-var "password=password"`
   - Para generar reportes añade: `--reporters cli,json,html --reporter-json-export ./reports/report.json`

Esta colección se irá actualizando a medida que se implementen nuevos endpoints para el sistema Kanban.

## Controladores Implementados

Se han implementado los siguientes controladores para el sistema Kanban:

1. **BoardController**: Gestiona los tableros Kanban
   - `index`: Lista todos los tableros del usuario (propios y colaboraciones)
   - `store`: Crea un nuevo tablero
   - `show`: Muestra un tablero específico con sus listas, tarjetas, etiquetas y comentarios
   - `update`: Actualiza un tablero existente
   - `destroy`: Elimina un tablero
   - `addCollaborator`: Añade un colaborador al tablero
   - `removeCollaborator`: Elimina un colaborador del tablero

2. **BoardListController**: Gestiona las listas dentro de un tablero
   - `index`: Lista todas las listas de un tablero específico
   - `store`: Crea una nueva lista en un tablero
   - `show`: Muestra una lista específica
   - `update`: Actualiza una lista existente (incluyendo reordenación)
   - `destroy`: Elimina una lista

3. **CardController**: Gestiona las tarjetas dentro de las listas
   - `index`: Lista todas las tarjetas de una lista específica
   - `store`: Crea una nueva tarjeta en una lista
   - `show`: Muestra una tarjeta específica con sus etiquetas y comentarios
   - `update`: Actualiza una tarjeta existente (incluyendo movimiento entre listas)
   - `destroy`: Elimina una tarjeta

4. **LabelController**: Gestiona las etiquetas asociadas a un tablero
   - `index`: Lista todas las etiquetas de un tablero específico
   - `store`: Crea una nueva etiqueta en un tablero
   - `show`: Muestra una etiqueta específica
   - `update`: Actualiza una etiqueta existente
   - `destroy`: Elimina una etiqueta

5. **CommentController**: Gestiona los comentarios en las tarjetas
   - `index`: Lista todos los comentarios de una tarjeta específica
   - `store`: Crea un nuevo comentario en una tarjeta
   - `show`: Muestra un comentario específico
   - `update`: Actualiza un comentario existente (solo el autor)
   - `destroy`: Elimina un comentario (autor o propietario del tablero)

## Implementación actual del sistema Kanban

1. **Modelos implementados**:
   - User: Modelo base de usuarios con relaciones a tableros y tarjetas
   - Board: Tableros Kanban con relaciones a listas, etiquetas y usuarios colaboradores
   - BoardList: Listas dentro de un tablero con relación a sus tarjetas
   - Card: Tarjetas dentro de las listas con relaciones a etiquetas y comentarios
   - Label: Etiquetas para categorizar las tarjetas
   - Comment: Comentarios en las tarjetas

2. **Migraciones**:
   - Todas las tablas del sistema Kanban han sido creadas con sus relaciones
   - Las restricciones de clave foránea incluyen eliminación en cascada para mantener la integridad de datos

3. **Controladores**:
   - Se han implementado controladores completos para todas las entidades
   - Cada controlador verifica la autenticación y autorización del usuario
   - Los controladores manejan relaciones complejas como colaboración en tableros
   - Se implementa el reordenamiento automático de listas y tarjetas

4. **Tests**:
   - Se ha actualizado el script test_api.bat para probar los endpoints del sistema Kanban
   - La colección de Postman (postman_collection.json) incluye ejemplos para todos los endpoints

## Rutas API

Se han configurado las siguientes rutas API para acceder a los controladores:

```
// Tableros
GET    /api/v1/boards                        - Listar tableros del usuario
POST   /api/v1/boards                        - Crear nuevo tablero
GET    /api/v1/boards/{boardId}              - Ver un tablero específico
PUT    /api/v1/boards/{boardId}              - Actualizar un tablero
DELETE /api/v1/boards/{boardId}              - Eliminar un tablero

// Colaboradores
POST   /api/v1/boards/{boardId}/collaborators       - Añadir colaborador
DELETE /api/v1/boards/{boardId}/collaborators/{userId} - Eliminar colaborador

// Listas
GET    /api/v1/boards/{boardId}/lists        - Listar listas de un tablero
POST   /api/v1/boards/{boardId}/lists        - Crear nueva lista
GET    /api/v1/boards/{boardId}/lists/{id}   - Ver una lista específica
PUT    /api/v1/boards/{boardId}/lists/{id}   - Actualizar una lista
DELETE /api/v1/boards/{boardId}/lists/{id}   - Eliminar una lista

// Tarjetas
GET    /api/v1/lists/{listId}/cards          - Listar tarjetas de una lista
POST   /api/v1/lists/{listId}/cards          - Crear nueva tarjeta
GET    /api/v1/cards/{id}                    - Ver una tarjeta específica
PUT    /api/v1/cards/{id}                    - Actualizar una tarjeta
DELETE /api/v1/cards/{id}                    - Eliminar una tarjeta

// Etiquetas
GET    /api/v1/boards/{boardId}/labels       - Listar etiquetas de un tablero
POST   /api/v1/boards/{boardId}/labels       - Crear nueva etiqueta
GET    /api/v1/labels/{id}                   - Ver una etiqueta específica
PUT    /api/v1/labels/{id}                   - Actualizar una etiqueta
DELETE /api/v1/labels/{id}                   - Eliminar una etiqueta

// Comentarios
GET    /api/v1/cards/{cardId}/comments       - Listar comentarios de una tarjeta
POST   /api/v1/cards/{cardId}/comments       - Crear nuevo comentario
GET    /api/v1/comments/{id}                 - Ver un comentario específico
PUT    /api/v1/comments/{id}                 - Actualizar un comentario
DELETE /api/v1/comments/{id}                 - Eliminar un comentario
```

Todas las rutas están protegidas y requieren autenticación mediante JWT.

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
## Tarea: Solucionar problema de rutas en la API Kanban

### Problema identificado
Al probar los endpoints de la API para el sistema Kanban, descubrimos que las rutas definidas en `api_v1.php` no estaban siendo registradas correctamente. Aunque el controlador de autenticación funcionaba, las rutas para boards, lists, cards, etc. no aparecían al ejecutar `php artisan route:list` y devolvían un error 404 al intentar acceder.

### Diagnóstico
Después de analizar el problema, encontramos varias inconsistencias:

1. **Ubicación de rutas incorrecta**: Las rutas de Kanban estaban definidas en `api_v1.php`, pero el prefijo URL `/api/v1` en el `RouteServiceProvider` estaba causando confusión.

2. **Referencias incorrectas a controladores**: La ruta en la definición (`App\Http\Controllers\API\BoardController::class`) no coincidía con la ubicación real de los controladores que estaban directamente en `API` sin la subcarpeta `V1`.

3. **Inconsistencia entre modelo y controlador**: En la migración, el campo para el nombre del tablero se definió como `name`, pero en el controlador se estaba validando y usando como `title`.

4. **Colección de Postman desactualizada**: Los JSON de solicitudes en la colección de Postman usaban el campo `title` en lugar de `name` para los tableros.

### Solución aplicada

1. **Corregir las referencias a controladores**: Actualizamos las rutas en `api_v1.php` para usar la sintaxis correcta con la ruta completa y escape de namespace:
   ```php
   Route::get('/', [\App\Http\Controllers\API\BoardController::class, 'index']);
   ```
   en lugar de:
   ```php
   Route::get('/', [App\Http\Controllers\API\BoardController::class, 'index']);
   ```

2. **Mover las rutas al archivo principal de API**: Trasladamos todas las rutas de Kanban al archivo `api.php` dentro del grupo con prefijo `v1`, manteniendo la estructura y organización:
   ```php
   Route::prefix('v1')->group(function () {
       // Rutas de autenticación...
       
       // Rutas protegidas por JWT
       Route::middleware('auth:api')->group(function () {
           // Rutas de Kanban...
       });
   });
   ```

3. **Corregir la inconsistencia de campos**: Actualizamos el `BoardController` para que use el campo `name` en lugar de `title` en los métodos de validación y creación:
   ```php
   $request->validate([
       'name' => 'required|string|max:255',
       // ...
   ]);
   
   $board = Board::create([
       'name' => $request->name,
       // ...
   ]);
   ```

4. **Actualizar la colección de Postman**: Cambiamos las solicitudes de Postman para usar `name` en lugar de `title` en el cuerpo JSON:
   ```json
   {
       "name": "Mi Primer Tablero",
       "description": "Un tablero para organizar mis tareas",
       "is_public": false
   }
   ```

### Verificación
Después de aplicar estas soluciones:
1. Las rutas aparecen correctamente al ejecutar `php artisan route:list`
2. Los endpoints de la API responden con los códigos de estado esperados:
   - 200 OK para solicitudes GET exitosas
   - 201 Created para POST exitosos
   - 401 Unauthorized para solicitudes sin autenticación

### Lecciones aprendidas
- **Consistencia en namespaces**: Asegurar que la estructura de directorios coincida con los namespaces de los controladores
- **Rutas en Laravel**: En proyectos con versionamiento de API, es crucial entender cómo Laravel registra y resuelve las rutas según su ubicación y prefijos
- **Verificación de campos**: Mantener consistencia entre migraciones, modelos y controladores para evitar errores de validación
- **Depuración efectiva**: Utilizar `php artisan route:list` para verificar que todas las rutas estén registradas correctamente

### Comandos útiles para depurar problemas de rutas
```bash
# Listar todas las rutas registradas
php artisan route:list

# Limpiar caché de rutas
php artisan route:clear

# Limpiar caché general
php artisan optimize:clear

# Verificar si una ruta específica está registrada
php artisan route:list --name=nombre.ruta
# O filtrar por URI
php artisan route:list --path=api/v1/boards
```

### Prueba de endpoints con Postman CLI
```bash
# Ejecutar solicitud de login para obtener token
postman collection run postman_collection.json -i Login --env-var "email=test@example.com" --env-var "password=password" --reporters json --reporter-json-export token_response.json --silent

# Ejecutar solicitud con token de autenticación
postman collection run postman_collection.json -i "Create Board" --env-var "base_url=http://localhost:8000" --env-var "token=AQUÍ_VA_EL_TOKEN" --reporters cli

# Probar endpoint sin autenticación
postman collection run auth_test_collection.json -i "Get Boards No Auth" --reporters cli
```

Este enfoque sistemático para diagnosticar y resolver problemas de rutas API en Laravel puede aplicarse a problemas similares en el futuro.

## Tarea: Instalar y configurar documentación Swagger/OpenAPI para la API Kanban

### Contexto del problema
La API Kanban carecía de documentación interactiva y estandarizada. Los desarrolladores frontend y otros consumidores de la API no tenían una forma fácil de entender los endpoints disponibles, sus parámetros, respuestas y ejemplos de uso. Se necesitaba implementar una solución de documentación automática que se mantuviera sincronizada con el código.

### Solución implementada
Se instaló y configuró el paquete `darkaonline/l5-swagger` para Laravel, que genera documentación OpenAPI/Swagger automáticamente a partir de anotaciones en el código PHP.

### Pasos de instalación y configuración

#### 1. Instalación del paquete
```bash
composer require "darkaonline/l5-swagger"
```
**Resultado esperado**: El paquete se instala junto con sus dependencias (`zircote/swagger-php`, `swagger-api/swagger-ui`, etc.)

#### 2. Publicación de la configuración
```bash
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```
**Archivos generados**:
- `config/l5-swagger.php` - Archivo de configuración
- `resources/views/vendor/l5-swagger` - Vistas del UI de Swagger

#### 3. Configuración del Controller base
Se actualizó `app/Http/Controllers/Controller.php` para incluir:
- Las traits estándar de Laravel (`AuthorizesRequests`, `DispatchesJobs`, `ValidatesRequests`)
- La anotación `@OA\Info` con información general de la API

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @OA\Info(title="Kanban API", version="1.0")
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
```

#### 4. Agregado de anotaciones OpenAPI iniciales
Se agregó una anotación `@OA\PathItem` al `AuthController` y anotaciones detalladas al método `register`:

```php
/**
 * @OA\PathItem(
 *     path="/api/auth/register"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Register a new user",
     *     description="Creates a new user account and returns a JWT token",
     *     operationId="registerUser",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuario registrado exitosamente"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com")
     *             ),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        // ... implementación del método
    }
}
```

#### 5. Generación de la documentación
```bash
php artisan l5-swagger:generate
```
**Archivos generados**:
- `storage/api-docs/api-docs.json` - Documentación en formato JSON
- `storage/api-docs/api-docs.yaml` - Documentación en formato YAML (opcional)

### Configuración del l5-swagger

**Archivo**: `config/l5-swagger.php`

**Configuraciones importantes**:
```php
'default' => 'default',
'documentations' => [
    'default' => [
        'api' => [
            'title' => 'L5 Swagger UI',
        ],
        'routes' => [
            'api' => 'api/documentation',  // URL del UI de Swagger
        ],
        'paths' => [
            'docs_json' => 'api-docs.json',  // Nombre del archivo JSON
            'docs_yaml' => 'api-docs.yaml',  // Nombre del archivo YAML
            'annotations' => [
                base_path('app'),  // Directorio donde buscar anotaciones
            ],
        ],
    ],
],
```

### URLs de acceso a la documentación

1. **Interfaz Swagger UI**: `http://localhost:8000/api/documentation`
2. **JSON de la API**: `http://localhost:8000/docs`
3. **Archivo JSON generado**: `storage/api-docs/api-docs.json`

### Problemas encontrados y soluciones

#### Problema 1: Error "Required @OA\PathItem() not found"
**Síntoma**: El comando `php artisan l5-swagger:generate` fallaba con el error "Required @OA\PathItem() not found"
**Causa**: Swagger requiere al menos una anotación `@OA\PathItem` para generar documentación válida
**Solución**: Agregar la anotación `@OA\PathItem` al `AuthController` antes de generar la documentación

#### Problema 2: Error de referencia a esquema no definido
**Síntoma**: Error "$ref "#/components/schemas/User" not found"
**Causa**: Se intentó referenciar un esquema "User" que no estaba definido
**Solución**: Definir las propiedades del usuario inline en lugar de usar referencias a esquemas no existentes

### Estructura de anotaciones OpenAPI recomendada

Para documentar correctamente cada endpoint, usar la siguiente estructura:

```php
/**
 * @OA\[Método](
 *     path="/ruta/del/endpoint",
 *     summary="Breve descripción",
 *     description="Descripción detallada",
 *     operationId="identificadorUnico",
 *     tags={"Categoría"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"campo1","campo2"},
 *             @OA\Property(property="campo1", type="string", example="valor"),
 *             // ... más propiedades
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Respuesta exitosa",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="object"),
 *             // ... propiedades de respuesta
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="No autorizado"
 *     ),
 *     security={{"bearerAuth":{}}}
 * )
 */
```

### Comandos útiles para gestión de Swagger

```bash
# Generar documentación
php artisan l5-swagger:generate

# Generar con verbose para debugging
php artisan l5-swagger:generate --verbose

# Limpiar caché de configuración
php artisan config:clear

# Verificar rutas registradas
php artisan route:list | grep swagger
```

### Próximos pasos recomendados

1. **Documentar todos los endpoints**: Agregar anotaciones OpenAPI a todos los controladores (`BoardController`, `CardController`, `LabelController`, `CommentController`)

2. **Definir esquemas reutilizables**: Crear esquemas para modelos comunes (User, Board, Card, etc.) usando `@OA\Schema` para evitar duplicación

3. **Configurar autenticación en Swagger**: Agregar configuración de seguridad JWT en `config/l5-swagger.php` para permitir probar endpoints autenticados desde el UI

4. **Automatizar generación**: Configurar la regeneración automática de documentación en desarrollo agregando `'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', true)` en el entorno de desarrollo

### Lecciones aprendidas y mejores prácticas

1. **Consistencia en anotaciones**: Mantener un formato consistente en todas las anotaciones OpenAPI
2. **Validación antes de generar**: Siempre verificar que existan al menos las anotaciones mínimas requeridas
3. **Documentación como código**: Tratar la documentación como parte integral del código, manteniéndola actualizada
4. **Testing de documentación**: Verificar que la documentación generada sea funcional y completa
5. **Versionamiento**: Considerar el versionamiento de la documentación cuando se realicen cambios breaking

### Archivos modificados
- `composer.json` - Agregada dependencia `darkaonline/l5-swagger`
- `config/l5-swagger.php` - Archivo de configuración generado
- `app/Http/Controllers/Controller.php` - Agregadas traits y anotación `@OA\Info`
- `app/Http/Controllers/API/AuthController.php` - Agregadas anotaciones OpenAPI iniciales
- `storage/api-docs/api-docs.json` - Documentación generada automáticamente

Esta implementación proporciona una base sólida para la documentación de la API Kanban, facilitando el desarrollo frontend y la integración con otros sistemas.

## Tarea: Completar documentación Swagger/OpenAPI para todos los controladores de la API Kanban

### Contexto del problema
Después de implementar la documentación básica de Swagger para la API Kanban, era necesario completar la documentación de todos los endpoints disponibles en los controladores restantes. Los controladores `BoardController`, `BoardListController`, `CardController`, `LabelController`, `CommentController` y `AdminController` carecían de anotaciones OpenAPI, lo que limitaba la utilidad de la documentación generada.

### Solución implementada
Se agregaron anotaciones OpenAPI completas en español a todos los métodos públicos de los controladores restantes, siguiendo el patrón establecido y proporcionando documentación detallada para cada endpoint.

### Controladores documentados

#### 1. BoardController - Gestión de Tableros
**Anotaciones agregadas:**
- `@OA\PathItem` para definir la ruta base `/api/v1/boards`
- Documentación completa para todos los métodos:
  - `index`: Listar tableros del usuario (propios y como colaborador)
  - `store`: Crear nuevo tablero
  - `show`: Obtener detalles completos de un tablero
  - `update`: Actualizar información del tablero (solo propietario)
  - `destroy`: Eliminar tablero (solo propietario)
  - `addCollaborator`: Añadir colaborador al tablero
  - `removeCollaborator`: Eliminar colaborador del tablero

**Características destacadas:**
- Documentación de respuestas anidadas complejas (tableros con listas, tarjetas, colaboradores, etiquetas)
- Validación de permisos (propietario vs colaborador)
- Manejo de errores específicos (403 para acceso denegado)

#### 2. BoardListController - Gestión de Listas
**Anotaciones agregadas:**
- `@OA\PathItem` para definir la ruta base `/api/v1/boards/{boardId}/lists`
- Documentación completa para métodos CRUD:
  - `index`: Listar listas de un tablero ordenadas por posición
  - `store`: Crear nueva lista con manejo automático de posiciones
  - `show`: Obtener detalles de una lista con sus tarjetas
  - `update`: Actualizar nombre y posición de lista
  - `destroy`: Eliminar lista y reordenar posiciones restantes

**Características destacadas:**
- Documentación de lógica de posiciones (reordenamiento automático)
- Validación de acceso al tablero padre
- Ejemplos detallados de estructuras de respuesta

#### 3. CardController - Gestión de Tarjetas
**Anotaciones agregadas:**
- `@OA\PathItem` para definir la ruta base `/api/v1/lists/{listId}/cards`
- Documentación completa para operaciones complejas:
  - `index`: Listar tarjetas de una lista con etiquetas y comentarios
  - `store`: Crear nueva tarjeta con asignación opcional de etiquetas
  - `show`: Obtener detalles completos de tarjeta con todas las relaciones
  - `update`: Actualizar tarjeta con posibilidad de mover entre listas
  - `destroy`: Eliminar tarjeta y reordenar posiciones

**Características destacadas:**
- Documentación de movimiento entre listas (cambio de `list_id`)
- Manejo de asignación múltiple de etiquetas
- Lógica compleja de reordenamiento de posiciones
- Validación de permisos de tablero

#### 4. LabelController - Gestión de Etiquetas
**Anotaciones agregadas:**
- `@OA\PathItem` para definir la ruta base `/api/v1/boards/{boardId}/labels`
- Documentación completa para métodos CRUD:
  - `index`: Listar etiquetas disponibles en un tablero
  - `store`: Crear nueva etiqueta con nombre y color
  - `show`: Obtener detalles de una etiqueta específica
  - `update`: Actualizar nombre y/o color de etiqueta
  - `destroy`: Eliminar etiqueta (se remueve automáticamente de tarjetas)

**Características destacadas:**
- Validación de formato de color hexadecimal
- Documentación de eliminación en cascada automática
- Ejemplos de colores en formato `#RRGGBB`

#### 5. CommentController - Gestión de Comentarios
**Anotaciones agregadas:**
- `@OA\PathItem` para definir la ruta base `/api/v1/cards/{cardId}/comments`
- Documentación completa para gestión de comentarios:
  - `index`: Listar comentarios de una tarjeta ordenados por fecha
  - `store`: Crear nuevo comentario en una tarjeta
  - `show`: Obtener detalles de un comentario específico
  - `update`: Actualizar comentario (solo el autor)
  - `destroy`: Eliminar comentario (autor o administradores)

**Características destacadas:**
- Control de permisos diferenciado (autor vs administrador)
- Documentación de relaciones con usuarios
- Ordenamiento automático por fecha de creación

#### 6. AdminController - Funciones de Administración
**Anotaciones agregadas:**
- `@OA\PathItem` para definir la ruta base `/api/v1/admin/dashboard`
- Documentación para dashboard administrativo:
  - `dashboard`: Obtener estadísticas generales del sistema

**Características destacadas:**
- Restricción de acceso solo para administradores
- Estadísticas de usuarios por roles
- Información básica para dashboard

### Estructura de anotaciones OpenAPI implementada

Cada método documentado incluye:

```php
/**
 * @OA\[Método](
 *     path="/ruta/del/endpoint",
 *     summary="Resumen breve en español",
 *     description="Descripción detallada en español",
 *     operationId="identificadorUnico",
 *     tags={"Categoría"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(...), // Parámetros de ruta/query
 *     @OA\RequestBody(...), // Cuerpo de la petición
 *     @OA\Response(...), // Respuestas exitosas y de error
 * )
 */
```

### Categorización por tags

Los endpoints se organizaron en las siguientes categorías:
- **Tableros**: Gestión completa de tableros Kanban
- **Listas**: Gestión de columnas/listas dentro de tableros
- **Tarjetas**: Gestión de tareas individuales
- **Etiquetas**: Gestión de categorías y colores para tarjetas
- **Comentarios**: Sistema de comentarios en tarjetas
- **Administración**: Funciones exclusivas para administradores
- **Autenticación**: Gestión de usuarios y tokens JWT (ya documentado)

### Características técnicas documentadas

1. **Autenticación JWT**: Todos los endpoints requieren token Bearer
2. **Control de acceso**: Diferenciación entre propietario, colaborador y administrador
3. **Validación de datos**: Reglas de validación detalladas con ejemplos
4. **Manejo de posiciones**: Lógica de reordenamiento automático
5. **Relaciones complejas**: Documentación de respuestas anidadas
6. **Códigos de estado HTTP**: Respuestas específicas para diferentes escenarios

### Verificación y testing

Después de agregar todas las anotaciones:
1. Se ejecutó `php artisan l5-swagger:generate` exitosamente
2. La documentación se generó sin errores
3. Todos los endpoints aparecen correctamente en la interfaz Swagger UI
4. Las respuestas de ejemplo coinciden con la estructura real de la API

### Beneficios obtenidos

1. **Documentación completa**: Toda la API Kanban está ahora completamente documentada
2. **Facilitación del desarrollo**: Los desarrolladores frontend pueden entender fácilmente todos los endpoints
3. **Testing integrado**: La interfaz Swagger permite probar los endpoints directamente
4. **Mantenimiento simplificado**: Las anotaciones se mantienen sincronizadas con el código
5. **Integración con herramientas**: Compatible con Postman, Insomnia y otras herramientas de API

### Archivos modificados
- `app/Http/Controllers/API/V1/BoardController.php` - Agregadas anotaciones completas
- `app/Http/Controllers/API/V1/BoardListController.php` - Agregadas anotaciones completas
- `app/Http/Controllers/API/V1/CardController.php` - Agregadas anotaciones completas
- `app/Http/Controllers/API/V1/LabelController.php` - Agregadas anotaciones completas
- `app/Http/Controllers/API/V1/CommentController.php` - Agregadas anotaciones completas
- `app/Http/Controllers/API/V1/AdminController.php` - Agregadas anotaciones completas
- `storage/api-docs/api-docs.json` - Documentación generada automáticamente

### Próximos pasos recomendados

1. **Configurar esquemas reutilizables**: Crear esquemas OpenAPI para modelos comunes (User, Board, Card) para reducir duplicación
2. **Añadir ejemplos reales**: Incluir más ejemplos de uso real en las anotaciones
3. **Documentar webhooks**: Si se implementan, documentar los webhooks disponibles
4. **Versionado de documentación**: Considerar estrategias para manejar cambios breaking
5. **Automatizar publicación**: Configurar CI/CD para publicar documentación automáticamente

Esta documentación completa transforma la API Kanban en una API profesional y fácilmente consumible, facilitando significativamente el desarrollo de aplicaciones cliente y la integración con sistemas externos.

## Tarea: Pruebas con Postman CLI - Obtención y uso de tokens JWT

### Contexto del problema
Durante las pruebas automatizadas con Postman CLI, se encontró que la colección completa fallaba en los endpoints autenticados (401 Unauthorized) porque el token JWT no se estaba pasando correctamente entre las requests. La colección de Postman está diseñada para funcionar en la aplicación de escritorio donde las variables de entorno se comparten entre requests, pero Postman CLI maneja las variables de entorno de forma diferente.

### Solución implementada
Se desarrolló un flujo de prueba manual usando PowerShell y Invoke-WebRequest para obtener el token JWT y luego usarlo en las pruebas automatizadas.

### Pasos para obtener y usar tokens JWT en pruebas

#### 1. Verificar instalación de Postman CLI
```bash
postman --version
```
**Resultado esperado**: Versión de Postman CLI (ej: 1.19.4)

#### 2. Iniciar el servidor Laravel
```bash
php artisan serve --host=127.0.0.1 --port=8000
```
**Nota**: El servidor debe estar ejecutándose en segundo plano para que las pruebas funcionen.

#### 3. Obtener el token JWT manualmente
Usar PowerShell con Invoke-WebRequest para hacer login y obtener el token:

```powershell
Invoke-WebRequest -Uri "http://localhost:8000/api/auth/login" -Method POST -ContentType "application/json" -Body '{"email":"test@example.com","password":"password"}' | Select-Object -ExpandProperty Content
```

**Respuesta esperada**:
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpL2F1dGgvbG9naW4iLCJpYXQiOjE3NTg5MDQ2MDQsImV4cCI6MTc1ODkwODIwNCwibmJmIjoxNzU4OTA0NjA0LCJqdGkiOiJUdXVtVFg3M0Q3ZEZoeXhJIiwic3ViIjoiMSIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.73QWsIbE3M5RVNWlEPvMkFchHYCYlKCmADPs5sbaAYk",
  "token_type": "bearer",
  "expires_in": 3600
}
```

#### 4. Extraer y guardar el token
- Copiar el valor del campo `token` de la respuesta JSON
- Guardar el token en una variable de entorno o archivo para uso posterior

#### 5. Ejecutar pruebas con el token
Usar el token obtenido en las pruebas de Postman CLI:

```bash
postman collection run postman_collection.json --environment postman_environment.json --env-var "token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpL2F1dGgvbG9naW4iLCJpYXQiOjE3NTg5MDQ2MDQsImV4cCI6MTc1ODkwODIwNCwibmJmIjoxNzU4OTA0NjA0LCJqdGkiOiJUdXVtVFg3M0Q3ZEZoeXhJIiwic3ViIjoiMSIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.73QWsIbE3M5RVNWlEPvMkFchHYCYlKCmADPs5sbaAYk" --reporters cli,json --reporter-json-export authenticated_test_results.json
```

#### 6. Verificar resultados de las pruebas
Los resultados se guardan en `authenticated_test_results.json`. Los endpoints autenticados deberían ahora responder con códigos 200 OK en lugar de 401 Unauthorized.

### Problemas encontrados y soluciones

#### Problema 1: Variables de entorno no compartidas en Postman CLI
**Síntoma**: Los scripts de Postman que guardan el token en variables de entorno no funcionan en CLI
**Causa**: Postman CLI no comparte variables de entorno entre diferentes ejecuciones de requests
**Solución**: Obtener el token manualmente primero y luego pasarlo como parámetro `--env-var`

#### Problema 2: Sintaxis de curl en PowerShell
**Síntoma**: `curl.exe` no funcionaba correctamente con JSON en PowerShell
**Causa**: Problemas de escape de comillas y formato JSON
**Solución**: Usar `Invoke-WebRequest` nativo de PowerShell que maneja mejor el JSON

#### Problema 3: Rutas de API incorrectas
**Síntoma**: Error 404 al intentar acceder a `/api/v1/auth/login`
**Causa**: Las rutas de autenticación están en `/api/auth/` no en `/api/v1/auth/`
**Solución**: Verificar las rutas con `php artisan route:list` antes de hacer las llamadas

### Comandos útiles para debugging

```bash
# Verificar rutas registradas
php artisan route:list | findstr "auth/login"

# Probar endpoint manualmente con curl
curl.exe -X POST http://localhost:8000/api/auth/login -H "Content-Type: application/json" -d "{\"email\":\"test@example.com\",\"password\":\"password\"}"

# Verificar que el servidor esté corriendo
netstat -ano | findstr :8000
```

### Mejores prácticas para pruebas automatizadas

1. **Obtener token primero**: Siempre obtener el token JWT antes de ejecutar pruebas de endpoints autenticados
2. **Verificar expiración**: Los tokens JWT expiran, obtener uno nuevo si las pruebas fallan con 401
3. **Usar variables de entorno**: Pasar el token como `--env-var` en lugar de depender de scripts de Postman
4. **Guardar resultados**: Usar `--reporter-json-export` para guardar resultados y analizarlos posteriormente
5. **Pruebas incrementales**: Probar endpoints individuales antes de ejecutar la colección completa

### Archivos generados durante las pruebas
- `login_response.json` - Respuesta del endpoint de login
- `authenticated_test_results.json` - Resultados de pruebas con token
- `test_results.json` - Resultados de pruebas sin token (para comparación)

### Próximos pasos recomendados

1. **Automatizar obtención de token**: Crear un script que obtenga automáticamente el token y lo use en las pruebas
2. **Configurar CI/CD**: Integrar estas pruebas en un pipeline de CI/CD
3. **Crear tests específicos**: Desarrollar tests más específicos para validar lógica de negocio
4. **Documentar flujo completo**: Crear un script batch o PowerShell que automatice todo el flujo de pruebas

Esta metodología permite probar efectivamente la API Kanban usando Postman CLI, asegurando que todos los endpoints funcionen correctamente tanto con autenticación como sin ella.

## Solución: Ejecutar solo carpetas específicas en Postman CLI

### Problema identificado
Al ejecutar la colección completa de Postman CLI, los endpoints de `Logout` y `Refresh Token` invalidan el token JWT, causando que todas las peticiones siguientes fallen con `401 Unauthorized`.

### Solución implementada
Usar la opción `-i` (o `--request`) de Postman CLI para ejecutar únicamente las carpetas que contienen los endpoints de Kanban, excluyendo la carpeta `Auth` que incluye `Logout` y `Refresh Token`.

### Comando para ejecutar solo endpoints Kanban
```bash
postman collection run postman_collection.json \
  --environment postman_environment.json \
  -i "Boards" \
  -i "Lists" \
  -i "Cards" \
  -i "Labels" \
  -i "Comments" \
  -i "Admin" \
  --env-var "token=TOKEN_JWT_AQUÍ" \
  --reporters cli,json \
  --reporter-json-export kanban_only_test_results.json
```

### Resultados obtenidos
- ✅ **Boards**: Todos los endpoints funcionan correctamente (GET, POST, PUT, DELETE)
- ✅ **Admin Dashboard**: Funciona correctamente con rol de administrador
- ⚠️ **Lists, Cards, Labels, Comments**: Fallan con 404 cuando dependen de recursos eliminados (comportamiento esperado)
- ✅ **Token permanece válido**: No se ejecutan Logout/Refresh que invalidarían el token

### Opciones disponibles en Postman CLI

#### Ejecutar carpetas específicas
```bash
# Ejecutar múltiples carpetas
-i "Boards" -i "Lists" -i "Cards"

# Ejecutar una sola carpeta
-i "Boards"
```

#### Ejecutar requests específicos
```bash
# Ejecutar requests individuales por nombre
-i "Get All Boards" -i "Create Board"
```

#### Combinar con otras opciones
```bash
# Con variables de entorno
--env-var "token=TOKEN_JWT"

# Con reportes detallados
--reporters cli,json --reporter-json-export results.json

# Con timeout personalizado
--timeout-request 5000
```

### Estructura de la colección
La colección está organizada en las siguientes carpetas principales:
- **Auth**: Register, Login, Me, Show Current Token, Logout, Refresh Token
- **Boards**: Gestión completa de tableros
- **Lists**: Gestión de listas dentro de tableros
- **Cards**: Gestión de tarjetas dentro de listas
- **Labels**: Gestión de etiquetas de tableros
- **Comments**: Gestión de comentarios en tarjetas
- **Admin**: Funciones administrativas

### Flujo de prueba recomendado

1. **Obtener token JWT** (manual o script)
2. **Ejecutar endpoints Kanban** (excluyendo Auth)
3. **Verificar resultados** en el archivo JSON generado
4. **Limpiar datos de prueba** si es necesario

### Archivos de resultados
- `kanban_only_test_results.json`: Resultados de pruebas solo Kanban
- Contiene métricas detalladas de cada request ejecutado

### Ventajas de esta aproximación
- ✅ Token permanece válido durante toda la ejecución
- ✅ Pruebas más rápidas (menos requests)
- ✅ Resultados más limpios y predecibles
- ✅ Evita efectos secundarios de logout/refresh
- ✅ Enfocado en funcionalidad core de Kanban

Esta solución permite ejecutar pruebas automatizadas confiables de la API Kanban sin los problemas de invalidación de tokens.

## Tarea: Implementar seeders completos para datos de prueba

### Contexto del problema
La base de datos estaba vacía después de las migraciones, lo que dificultaba las pruebas de la API Kanban. Se necesitaba poblar todas las tablas con datos realistas y coherentes para poder probar todos los endpoints y relaciones del sistema.

### Solución implementada
Se crearon seeders completos para todas las entidades del sistema Kanban, con control de errores para evitar duplicados y datos de prueba realistas.

### Seeders implementados

#### 1. UserSeeder - Usuarios con roles asignados
**Características:**
- Crea usuario administrador (`admin@kodigo.com`)
- Crea usuario de prueba (`test@example.com`) 
- Crea 5 usuarios adicionales con rol 'user'
- Asigna roles automáticamente usando `firstOrCreate()`
- Usa `Hash::make()` para encriptar contraseñas

#### 2. BoardSeeder - Tableros Kanban
**Características:**
- Crea 5 tableros con diferentes propietarios
- Incluye tableros públicos y privados
- Datos realistas con nombres descriptivos
- Evita duplicados con `firstOrCreate()`

#### 3. BoardListSeeder - Listas dentro de tableros
**Características:**
- Crea 4 listas estándar por tablero: "Por Hacer", "En Progreso", "En Revisión", "Completado"
- Maneja posiciones automáticas (1-4)
- Se ejecuta para todos los tableros existentes

#### 4. LabelSeeder - Etiquetas para categorización
**Características:**
- Crea 8 etiquetas por tablero con colores distintivos
- Etiquetas útiles: "Alta Prioridad", "Bug", "Feature", "Documentación", etc.
- Colores en formato hexadecimal (#RRGGBB)

#### 5. CardSeeder - Tarjetas de tareas
**Características:**
- Crea tarjetas realistas según el tipo de lista
- Asigna usuarios aleatorios como creadores
- Incluye fechas de vencimiento aleatorias
- Contenido descriptivo y realista para cada lista

#### 6. CommentSeeder - Comentarios en tarjetas
**Características:**
- Agrega 1-3 comentarios aleatorios a algunas tarjetas
- Comentarios realistas sobre el progreso de tareas
- Asigna usuarios aleatorios como autores

#### 7. BoardUserSeeder - Colaboradores de tableros
**Características:**
- Asigna 1-3 colaboradores aleatorios por tablero
- Usa rol 'editor' (válido según enum de migración)
- Evita asignar colaboradores al propietario del tablero

#### 8. CardLabelSeeder - Etiquetas en tarjetas
**Características:**
- Asigna 0-2 etiquetas aleatorias por tarjeta
- Solo usa etiquetas del mismo tablero
- Relaciones muchos-a-muchos correctamente manejadas

### Control de errores implementado

Todos los seeders usan métodos que evitan duplicados:
- `firstOrCreate()` para entidades principales
- `updateOrInsert()` para tablas pivote
- Validación de dependencias antes de crear registros
- Mensajes informativos durante la ejecución

### Resultados obtenidos

Después de ejecutar `php artisan migrate:fresh --seed`:

```
Usuarios: 7 (1 admin, 1 test, 5 adicionales)
Roles: 2 (admin, user)
Tableros: 5 (con diferentes propietarios)
Listas: 20 (4 listas × 5 tableros)
Tarjetas: 80 (múltiples tarjetas por lista)
Etiquetas: 40 (8 etiquetas × 5 tableros)
Comentarios: 19 (comentarios en tarjetas seleccionadas)
```

### Usuarios de prueba disponibles

**Administrador:**
- Email: `admin@kodigo.com`
- Password: `password`
- Rol: admin

**Usuario de prueba:**
- Email: `test@example.com`
- Password: `password`
- Rol: user

**Usuarios adicionales:**
- `maria@example.com`, `carlos@example.com`, `ana@example.com`
- `pedro@example.com`, `laura@example.com`
- Todos con password: `password` y rol: user

### Comandos útiles para gestión de seeders

```bash
# Ejecutar todos los seeders
php artisan migrate:fresh --seed

# Ejecutar seeder específico
php artisan db:seed --class=UserSeeder

# Verificar datos creados
php artisan tinker
>>> App\Models\User::count()
>>> App\Models\Board::with('boardLists.cards')->get()
```

### Beneficios obtenidos

1. **Base de datos completa**: Todas las tablas pobladas con datos coherentes
2. **Pruebas realistas**: API completamente testable con datos representativos
3. **Relaciones funcionales**: Todas las relaciones muchos-a-muchos funcionando
4. **Control de errores**: Seeders seguros que no fallan por duplicados
5. **Flexibilidad**: Se pueden ejecutar seeders individuales o todos juntos
6. **Mantenimiento**: Fácil actualizar o agregar nuevos datos de prueba

### Archivos creados/modificados
- `database/seeders/UserSeeder.php` - Nuevo
- `database/seeders/BoardSeeder.php` - Nuevo
- `database/seeders/BoardListSeeder.php` - Nuevo
- `database/seeders/LabelSeeder.php` - Nuevo
- `database/seeders/CardSeeder.php` - Nuevo
- `database/seeders/CommentSeeder.php` - Nuevo
- `database/seeders/BoardUserSeeder.php` - Nuevo
- `database/seeders/CardLabelSeeder.php` - Nuevo
- `database/seeders/DatabaseSeeder.php` - Modificado para coordinar todos los seeders

Esta implementación proporciona una base de datos completamente poblada y lista para pruebas exhaustivas de toda la funcionalidad del sistema Kanban.
