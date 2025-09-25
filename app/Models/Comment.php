<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $fillable = [
        'content',
        'card_id',
        'user_id',
    ];
    
    /**
     * Obtener la tarjeta a la que pertenece este comentario.
     */
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
    
    /**
     * Obtener el usuario que creó este comentario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
