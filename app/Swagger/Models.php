<?php

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
 *     )
 * )
 */

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
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="ID del propietario del tablero"
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
 *         property="lists",
 *         type="array",
 *         description="Listas del tablero",
 *         @OA\Items(
 *             ref="#/components/schemas/BoardList"
 *         )
 *     ),
 *     @OA\Property(
 *         property="collaborators",
 *         type="array",
 *         description="Colaboradores del tablero",
 *         @OA\Items(
 *             ref="#/components/schemas/User"
 *         )
 *     )
 * )
 */

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
 *         property="board_id",
 *         type="integer",
 *         description="ID del tablero al que pertenece"
 *     ),
 *     @OA\Property(
 *         property="position",
 *         type="integer",
 *         description="Posición de la lista en el tablero"
 *     ),
 *     @OA\Property(
 *         property="is_archived",
 *         type="boolean",
 *         description="Indica si la lista está archivada"
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
 *         property="cards",
 *         type="array",
 *         description="Tarjetas de la lista",
 *         @OA\Items(
 *             ref="#/components/schemas/Card"
 *         )
 *     )
 * )
 */