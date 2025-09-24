<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Issuetype extends Model
{
    use HasFactory;
     
    public function issues(): HasMany
    {
        return $this->HasMany(Issuelog::class, 'Issuetype_id');
    }
}
