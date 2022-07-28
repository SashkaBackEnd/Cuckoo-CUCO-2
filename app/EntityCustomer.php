<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EntityCustomer extends Model
{
    protected $fillable = [
        'name',
        'entity_id',
        'contact'
    ];
}
