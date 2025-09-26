<?php

namespace App\Swagger\Routes;

class BoardRoutes
{
    /**
     * @OA\Get(
     *     path="/boards",
     *     summary="Obtener todos los tableros",
     *     description="Devuelve una lista de todos los tableros a los que tiene acceso el usuario",
     *     tags={"Boards"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tableros",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tableros recuperados con éxito"),
     *             @OA\Property(
     *                 property="boards",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Board")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function index() {}
    
    /**
     * @OA\Post(
     *     path="/boards",
     *     summary="Crear un nuevo tablero",
     *     description="Crea un nuevo tablero para el usuario autenticado",
     *     tags={"Boards"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Proyecto Web"),
     *             @OA\Property(property="description", type="string", example="Tablero para gestionar el proyecto web")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tablero creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tablero creado con éxito"),
     *             @OA\Property(property="board", ref="#/components/schemas/Board")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function store() {}
    
    /**
     * @OA\Get(
     *     path="/boards/{boardId}",
     *     summary="Obtener un tablero específico",
     *     description="Devuelve los datos de un tablero específico y sus listas",
     *     tags={"Boards"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="ID del tablero",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Datos del tablero",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tablero recuperado con éxito"),
     *             @OA\Property(property="board", ref="#/components/schemas/Board")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tablero no encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function show() {}
}