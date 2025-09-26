<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *     title="Kodigo Kanban API",
 *     version="1.0",
 *     description="API para el sistema Kanban",
 *     @OA\Contact(
 *         email="contacto@kodigo.com",
 *         name="Soporte de API"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="/api/v1",
 *     description="API V1"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * 
 * @OA\Tag(
 *     name="Auth",
 *     description="Endpoints de autenticación"
 * )
 * @OA\Tag(
 *     name="Boards",
 *     description="Endpoints para tableros"
 * )
 * @OA\Tag(
 *     name="Lists",
 *     description="Endpoints para listas"
 * )
 * @OA\Tag(
 *     name="Cards",
 *     description="Endpoints para tarjetas"
 * )
 */
class SwaggerController extends Controller
{
    /**
     * Esto es solo un controlador para documentación Swagger
     */
    public function apiDocs()
    {
        return [];
    }
}