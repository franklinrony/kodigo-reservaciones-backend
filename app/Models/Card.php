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
        'assigned_user_id',
        'position',
        'due_date',
        'progress_percentage',
        'is_completed',
        'is_archived',
    ];
    
    protected $casts = [
        'due_date' => 'date',
        'progress_percentage' => 'integer',
        'is_completed' => 'boolean',
        'is_archived' => 'boolean',
    ];
    
    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Evento que se ejecuta antes de actualizar el modelo
        static::updating(function ($card) {
            // Si se está cambiando progress_percentage
            if ($card->isDirty('progress_percentage')) {
                $newProgress = $card->progress_percentage;
                
                // Si llega a 100, marcar como completada
                if ($newProgress >= 100) {
                    $card->is_completed = true;
                }
                // Si baja de 100, desmarcar como completada
                elseif ($newProgress < 100) {
                    $card->is_completed = false;
                }
            }
        });
    }
    
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
     * Obtener el usuario asignado a esta tarjeta.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
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
