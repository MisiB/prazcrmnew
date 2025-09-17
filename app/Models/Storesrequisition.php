<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Storesrequisition extends Model
{
    public function hod():BelongsTo
    {
        return $this->belongsTo(Hodstoresrequisitionapproval::class, 'storesrequisition_uuid', 'storesrequisition_uuid');
    }
    public function adminissuer():BelongsTo
    {
        return $this->belongsTo(Issuerstoresrequisitionapproval::class, 'storesrequisition_uuid', 'storesrequisition_uuid');
    }
    public function receiver():BelongsTo
    {
        return $this->belongsTo(Receiverstoresrequisitionapproval::class, 'storesrequisition_uuid', 'storesrequisition_uuid');
    }
    public function initiator():BelongsTo
    {
        return $this->belongsTo(User::class, 'initiator_id', 'id');
    }
}
 