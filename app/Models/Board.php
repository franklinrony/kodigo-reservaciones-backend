<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Board extends Model
{
    protected $fillable = [
        'name',
        'description',
        'user_id',
        'is_public',
        'background_color',
        'background_image',
    ];
    
    /**
     * Obtener el usuario que es dueño del tablero.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Obtener las listas del tablero.
     */
    public function lists(): HasMany
    {
        return $this->hasMany(BoardList::class)->orderBy('position');
    }
    
    /**
     * Obtener las etiquetas del tablero.
     */
    public function labels(): HasMany
    {
        return $this->hasMany(Label::class);
    }
    
    /**
     * Obtener los colaboradores del tablero.
     */
    public function collaborators(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'board_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }
}
