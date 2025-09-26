<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;

class SwaggerCardController extends Controller
{
    /**
     * @OA\Get(
     *     path="/lists/{listId}/cards",
     *     summary="Obtener tarjetas de una lista",
     *     description="Devuelve todas las tarjetas que pertenecen a una lista específica",
     *     tags={"Cards"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="listId",
     *         in="path",
     *         required=true,
     *         description="ID de la lista",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tarjetas",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tarjetas recuperadas con éxito"),
     *             @OA\Property(
     *                 property="cards",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Card")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Lista no encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    
    /**
     * @OA\Post(
     *     path="/lists/{listId}/cards",
     *     summary="Crear una nueva tarjeta",
     *     description="Crea una nueva tarjeta en una lista específica",
     *     tags={"Cards"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="listId",
     *         in="path",
     *         required=true,
     *         description="ID de la lista",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title"},
     *             @OA\Property(property="title", type="string", example="Implementar login"),
     *             @OA\Property(property="description", type="string", example="Implementar sistema de autenticación"),
     *             @OA\Property(property="position", type="integer", example=1),
     *             @OA\Property(property="due_date", type="string", format="date", example="2025-10-15"),
     *             @OA\Property(property="label_ids", type="array", @OA\Items(type="integer"), example={1, 2})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tarjeta creada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tarjeta creada con éxito"),
     *             @OA\Property(property="card", ref="#/components/schemas/Card")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Lista no encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    
    /**
     * @OA\Get(
     *     path="/cards/{id}",
     *     summary="Obtener una tarjeta específica",
     *     description="Devuelve los datos de una tarjeta específica",
     *     tags={"Cards"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la tarjeta",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Datos de la tarjeta",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tarjeta recuperada con éxito"),
     *             @OA\Property(property="card", ref="#/components/schemas/Card")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tarjeta no encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    
    /**
     * @OA\Put(
     *     path="/cards/{id}",
     *     summary="Actualizar una tarjeta",
     *     description="Actualiza los datos de una tarjeta específica",
     *     tags={"Cards"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la tarjeta",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Implementar login actualizado"),
     *             @OA\Property(property="description", type="string", example="Implementar sistema de autenticación actualizado"),
     *             @OA\Property(property="position", type="integer", example=2),
     *             @OA\Property(property="due_date", type="string", format="date", example="2025-10-20"),
     *             @OA\Property(property="list_id", type="integer", example=2),
     *             @OA\Property(property="label_ids", type="array", @OA\Items(type="integer"), example={2, 3})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tarjeta actualizada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tarjeta actualizada con éxito"),
     *             @OA\Property(property="card", ref="#/components/schemas/Card")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tarjeta no encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    
    /**
     * @OA\Delete(
     *     path="/cards/{id}",
     *     summary="Eliminar una tarjeta",
     *     description="Elimina una tarjeta específica",
     *     tags={"Cards"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la tarjeta",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tarjeta eliminada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tarjeta eliminada con éxito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tarjeta no encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
}