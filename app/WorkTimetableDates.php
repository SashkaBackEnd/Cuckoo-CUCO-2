<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

// Нестандартный график работы на конкретное число
class WorkTimetableDates extends Model
{
    protected $fillable = [
      'day',
      'salary',
      'guarded_objects_id'
    ];

    /**
     * @param $value
     * @return Carbon
     */
    public function getDayAttribute($value): Carbon
    {
        return Carbon::createFromDate($value);
    }

    public function hours()
    {
        return $this->hasMany(WorkHours::class, 'work_timetables_date_id', 'id')
            ->orderBy('from');
    }
}
