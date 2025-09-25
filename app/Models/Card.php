<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Card extends Model
{
    protected $fillable = [
        'title',
        'description',
        'board_list_id',
        'user_id',
        'position',
        'due_date',
        'is_completed',
        'is_archived',
    ];
    
    protected $casts = [
        'due_date' => 'date',
        'is_completed' => 'boolean',
        'is_archived' => 'boolean',
    ];
    
    /**
     * Obtener la lista a la que pertenece esta tarjeta.
     */
    public function boardList(): BelongsTo
    {
        return $this->belongsTo(BoardList::class);
    }
    
    /**
     * Obtener el usuario que creó esta tarjeta.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Obtener los comentarios de esta tarjeta.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
    
    /**
     * Obtener las etiquetas de esta tarjeta.
     */
    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'card_label')->withTimestamps();
    }
}
