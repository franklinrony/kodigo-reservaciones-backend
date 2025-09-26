<?php

namespace App\Swagger;

/**
 * @OA\Info(
 *     title="Kodigo Kanban API",
 *     version="1.0",
 *     description="API Backend para la aplicación Kanban",
 *     @OA\Contact(
 *         email="admin@kodigo.com",
 *         name="Equipo de desarrollo"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * ),
 * 
 * @OA\Server(
 *     url="/api/v1",
 *     description="API V1"
 * ),
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * ),
 * 
 * @OA\Tag(
 *     name="Auth",
 *     description="Endpoints de autenticación y registro"
 * ),
 * @OA\Tag(
 *     name="Boards",
 *     description="Operaciones con tableros"
 * ),
 * @OA\Tag(
 *     name="Lists",
 *     description="Operaciones con listas dentro de tableros"
 * ),
 * @OA\Tag(
 *     name="Cards",
 *     description="Operaciones con tarjetas"
 * ),
 * @OA\Tag(
 *     name="Labels",
 *     description="Operaciones con etiquetas"
 * ),
 * @OA\Tag(
 *     name="Comments",
 *     description="Operaciones con comentarios en tarjetas"
 * ),
 * @OA\Tag(
 *     name="Admin",
 *     description="Operaciones de administración"
 * )
 */
class OpenAPI {}


/**
 * @OA\Schema(
 *     schema="Error",
 *     title="Error",
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         description="Mensaje de error"
 *     ),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         description="Detalles específicos del error"
 *     )
 * )
 */
class ErrorSchema {}

/**
 * @OA\Schema(
 *     schema="User",
 *     title="User",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID del usuario"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nombre del usuario"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="Correo electrónico del usuario"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Fecha de creación"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Fecha de última actualización"
 *     ),
 *     @OA\Property(
 *         property="roles",
 *         type="array",
 *         description="Roles asignados al usuario",
 *         @OA\Items(
 *             ref="#/components/schemas/Role"
 *         )
 *     )
 * )
 */
class UserSchema {}

/**
 * @OA\Schema(
 *     schema="Role",
 *     title="Role",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID del rol"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nombre del rol (admin, user, etc.)"
 *     )
 * )
 */
class RoleSchema {}

/**
 * @OA\Schema(
 *     schema="Board",
 *     title="Board",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID del tablero"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nombre del tablero"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Descripción del tablero"
 *     ),
 *     @OA\Property(
 *         property="is_archived",
 *         type="boolean",
 *         description="Indica si el tablero está archivado"
 *     )
 * )
 */
class BoardSchema {}

/**
 * @OA\Schema(
 *     schema="BoardList",
 *     title="BoardList",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID de la lista"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nombre de la lista"
 *     ),
 *     @OA\Property(
 *         property="position",
 *         type="integer",
 *         description="Posición de la lista"
 *     )
 * )
 */
class BoardListSchema {}

/**
 * @OA\Schema(
 *     schema="Card",
 *     title="Card",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID de la tarjeta"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Título de la tarjeta"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         nullable=true,
 *         description="Descripción de la tarjeta"
 *     ),
 *     @OA\Property(
 *         property="position",
 *         type="integer",
 *         description="Posición de la tarjeta en la lista"
 *     ),
 *     @OA\Property(
 *         property="due_date",
 *         type="string",
 *         format="date-time",
 *         nullable=true,
 *         description="Fecha de vencimiento"
 *     )
 * )
 */
class CardSchema {}

/**
 * @OA\Schema(
 *     schema="Label",
 *     title="Label",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID de la etiqueta"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nombre de la etiqueta"
 *     ),
 *     @OA\Property(
 *         property="color",
 *         type="string",
 *         description="Código de color de la etiqueta (hex)"
 *     )
 * )
 */
class LabelSchema {}

/**
 * @OA\Schema(
 *     schema="Comment",
 *     title="Comment",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID del comentario"
 *     ),
 *     @OA\Property(
 *         property="content",
 *         type="string",
 *         description="Contenido del comentario"
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="ID del usuario que creó el comentario"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Fecha de creación"
 *     )
 * )
 */
class CommentSchema {}