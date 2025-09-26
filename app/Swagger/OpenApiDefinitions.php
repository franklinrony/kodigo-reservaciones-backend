<?php

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