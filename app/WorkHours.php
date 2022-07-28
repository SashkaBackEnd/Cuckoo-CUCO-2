<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

//Часы работы
class WorkHours extends Model
{
    protected $fillable = ['to', 'from', 'work_timetables_id', 'work_timetables_date_id'];

    /**
     * time string to Carbon\Carbon
     *
     * @param $value
     * @return Carbon
     */
    public function getToAttribute($value): Carbon
    {
        return Carbon::createFromTimeString($value);
    }

    /**
     * time string to Carbon\Carbon
     *
     * @param $value
     * @return Carbon
     */
    public function getFromAttribute($value): Carbon
    {
        return Carbon::createFromTimeString($value);
    }
}
