<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Model
{
    use HasApiTokens;
    public function onlinepayments():HasMany{
        return $this->hasMany(Onlinepayment::class);
    }

    public function tenders():HasMany{
        return $this->hasMany(Tender::class);
    }
    public function banktransactions():HasMany{
        return $this->hasMany(Banktransaction::class);
    }
    public function epayments():HasMany{
        return $this->hasMany(Epayment::class);
    }

    public function suspenses():HasMany{
        return $this->hasMany(Suspense::class);
    }
    
}
