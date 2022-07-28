<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

// Стандартный график работы на каждый день недели
class WorkTimetable extends Model
{
    protected $fillable = ['day', 'salary', 'guarded_objects_id'];
    /**
     * Дни недели
     * @var string
     */
    public const MON = 'Mon';
    public const TUE = 'Tue';
    public const WED = 'Wed';
    public const THU = 'Thu';
    public const FRI = 'Fri';
    public const SAT = 'Sat';
    public const SUN = 'Sun';

    /**
     * Дни недели кириллицей
     * @var string
     */
    public const MON_RU = 'Пн';
    public const TUE_RU = 'Вт';
    public const WED_RU = 'Ср';
    public const THU_RU = 'Чт';
    public const FRI_RU = 'Пт';
    public const SAT_RU = 'Сб';
    public const SUN_RU = 'Вс';

    /**
     * @return HasMany
     */
    public function hours(): HasMany
    {
        return $this->hasMany(WorkHours::class, 'work_timetables_id', 'id')
            ->orderBy('from');
    }

    /**
     * @param string $day
     * @return bool
     */
    public static function validateDay(string $day): bool
    {
        switch ($day) {
            case self::MON:
            case self::TUE:
            case self::WED:
            case self::THU:
            case self::FRI:
            case self::SAT:
            case self::SUN:
                return true;
            default:
                return false;
        }
    }

    /**
     * @return Collection
     */
    public static function getDays(): Collection
    {
        return collect([self::MON, self::TUE, self::WED, self::THU, self::FRI, self::SAT, self::SUN]);
    }

    /**
     * @param string $day
     * @return string|null
     */
    public static function convertToRu(string $day): ?string
    {
        switch ($day) {
            case self::MON:
                return self::MON_RU;
            case self::TUE:
                return self::TUE_RU;
            case self::WED:
                return self::WED_RU;
            case self::THU:
                return self::THU_RU;
            case self::FRI:
                return self::FRI_RU;
            case self::SAT:
                return self::SAT_RU;
            case self::SUN:
                return self::SUN_RU;
            default:
                return null;
        }
    }

    public static function getByDayOfWeek(int $dayOfWeek): ?string
    {
        switch ($dayOfWeek) {
            case 1:
                return self::MON;
            case 2:
                return self::TUE;
            case 3:
                return self::WED;
            case 4:
                return self::THU;
            case 5:
                return self::FRI;
            case 6:
                return self::SAT;
            case 7:
                return self::SUN;
            default:
                return null;
        }
    }
}
