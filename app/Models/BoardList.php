<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BoardList extends Model
{
    protected $fillable = [
        'name',
        'board_id',
        'position',
        'is_archived',
    ];
    
    /**
     * Obtener el tablero al que pertenece esta lista.
     */
    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }
    
    /**
     * Obtener las tarjetas de esta lista.
     */
    public function cards(): HasMany
    {
        return $this->hasMany(Card::class)->orderBy('position');
    }
}
