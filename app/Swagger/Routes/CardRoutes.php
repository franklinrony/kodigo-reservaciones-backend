<?php

namespace App\Swagger\Routes;

class CardRoutes
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
    public function index() {}
    
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
    public function store() {}
}