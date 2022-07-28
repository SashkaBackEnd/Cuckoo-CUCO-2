<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QueuedCall extends Model
{
    protected $fillable = [
        'call_date', 'guarded_object_id', 'call_status',
    ];

    public function guardedObject()
    {
        return $this->belongsTo('App\GuardedObject');
    }
}
