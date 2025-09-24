<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Issuelog extends Model
{
    use HasFactory;
    public function task(): HasOne
    {
        return $this->HasOne(Issuetask::class, 'source_id')->where('type', 'issue-log');
    }

    public function issuetype():BelongsTo{
        return $this->belongsTo(Issuetype::class,'issuetype_id','id');
    }

    public function issuegroup():BelongsTo{
        return $this->belongsTo(Issuegroup::class,'issuegroup_id','id');
    }

    public function comments():HasMany{
        return $this->hasMany(Issuecomment::class,"issuelog_id","id");
    }
    public function user():BelongsTo{
        return $this->belongsTo(User::class,'user_id','id');
    }


    protected $casts = [
        'library' => AsCollection::class,
        'files'=>AsCollection::class,
    ];


}
