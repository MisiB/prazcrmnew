<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Issuetask extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'user_id','id');
    }
    public function assignee(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'assigned_by','id');
    }
    public function issuelog(): BelongsTo
    {
        return $this->BelongsTo(Issuelog::class, 'source_id','id');
    }
    public function individualoutputbreakdown(){
        return $this->belongsTo(Individualoutputbreakdown::class);
    }


}
