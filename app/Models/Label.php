<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Label extends Model
{
    protected $fillable = [
        'name',
        'color',
        'board_id',
    ];
    
    /**
     * Obtener el tablero al que pertenece esta etiqueta.
     */
    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }
    
    /**
     * Obtener las tarjetas que tienen esta etiqueta.
     */
    public function cards(): BelongsToMany
    {
        return $this->belongsToMany(Card::class, 'card_label')->withTimestamps();
    }
}
