<?php

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
 *     ),
 *     @OA\Property(
 *         property="board_list_id",
 *         type="integer",
 *         description="ID de la lista a la que pertenece"
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="ID del usuario que creó la tarjeta"
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
 *         property="labels",
 *         type="array",
 *         description="Etiquetas asignadas a la tarjeta",
 *         @OA\Items(
 *             ref="#/components/schemas/Label"
 *         )
 *     ),
 *     @OA\Property(
 *         property="comments",
 *         type="array",
 *         description="Comentarios en la tarjeta",
 *         @OA\Items(
 *             ref="#/components/schemas/Comment"
 *         )
 *     )
 * )
 */

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
 *     ),
 *     @OA\Property(
 *         property="board_id",
 *         type="integer",
 *         description="ID del tablero al que pertenece"
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
 *         property="card_id",
 *         type="integer",
 *         description="ID de la tarjeta a la que pertenece"
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
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Fecha de última actualización"
 *     ),
 *     @OA\Property(
 *         property="user",
 *         ref="#/components/schemas/User",
 *         description="Usuario que creó el comentario"
 *     )
 * )
 */