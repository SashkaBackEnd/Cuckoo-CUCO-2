<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EntityManager extends Model
{
    protected $table = 'entity_manager';
    protected $fillable = ['user_id','entity_id'];
}
