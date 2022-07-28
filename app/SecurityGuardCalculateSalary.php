<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class SecurityGuardCalculateSalary
{
    /**
     * @var SecurityGuard
     */
    protected $securityGuard;

    public function __construct(SecurityGuard $securityGuard)
    {
        $this->securityGuard = $securityGuard;
    }

    /**
     * Получить рабочие смены всех гвардейцев за период
     *
     * @param Carbon|null $from
     * @param Carbon|null $to
     * @return Collection
     */
    public static function getAllWorkShifts(Carbon $from = null, Carbon $to = null): Collection
    {
        return WorkShift::query()
            ->whereIn('shift_status', ['done', 'process'])
            ->when($from, function ($q) use ($from) {
                return $q->where('start_time', '>=', $from);
            })->when($to, function ($q) use ($to) {
                return $q->where('end_time', '<=', $to);
            })->get();
    }

    /**
     * @param Carbon|null $from
     * @param Carbon|null $to
     * @return Collection
     */
    public function getWorkDoneShifts(Carbon $from = null, Carbon $to = null): Collection
    {
        return WorkShift::query()
            ->where('security_guard_id', $this->securityGuard->id)
            ->where('shift_status', 'done')
            ->when($from, function ($q) use ($from) {
                return $q->where('start_time', '>=', $from);
            })->when($to, function ($q) use ($to) {
                return $q->where('end_time', '<=', $to);
            })->get();
    }

    /**
     * @return Collection
     */
    public function getWorkProcessShifts(): Collection
    {
        return WorkShift::query()
            ->where('security_guard_id', $this->securityGuard->id)
            ->where('shift_status', 'process')
            ->get();
    }

    public function calculateProcessSalary(): ?SecurityGuardSalary
    {
        $workShift = $this->getWorkProcessShifts()->load([
            'guardedObject.workTimetable.hours',
            'guardedObject.workTimetableDates.hours',
            'securityGuard'
        ])->first();

        if (!$workShift) {
            return null;
        }

        $securityGuard = $workShift->securityGuard;
        $post = $workShift->guardedObject;

        if (!$post) {
            return null;
        }
        $shiftStart = Carbon::createFromDate($workShift->start_time);

        $salaryObject = new SecurityGuardSalary(
            $post,
            $securityGuard,
            $workShift->shift_status,
            $shiftStart
        );

        $startDay = $shiftStart->day;
        $nowDay = now()->day;

        for (; $startDay <= $nowDay; $startDay++) {
            $workDay = now()->subDays($nowDay - $startDay);
            $dayName = WorkTimetable::getByDayOfWeek($workDay->dayOfWeekIso);
            $shiftTimetable =
                $post->workTimetableDates->where('day', $workDay->toDateString())->first()
                ?? $post->workTimetable->where('day', $dayName)->first();

            if (!$shiftTimetable) {
                continue;
            }

            $hours = $shiftTimetable->hours;
            $halfHour = 30;
            foreach ($hours as $hour) {
                $from = $hour->from->setDateFrom($workDay);
                $to = $hour->to->setDateFrom($workDay);

                //todo сделать рефакторинг
                if ($shiftStart->toDateString() === $workDay->toDateString()) {
                    if ($shiftStart->greaterThan($to)) {
                        continue;
                    }

                    $start = $shiftStart->lessThan($from)
                        ? $from
                        : $shiftStart;

                    $end = now()->lessThan($to)
                        ? now()
                        : $to;

                    $salaryObject->salary += intdiv($start->diffInMinutes($end), $halfHour) * ($shiftTimetable->salary / 2);
                } elseif ($workDay->toDateString() === now()->toDateString()) {
                    $end = now()->greaterThan($to)
                        ? $to
                        : now();
                    $salaryObject->salary += intdiv($from->diffInMinutes($end), $halfHour) * ($shiftTimetable->salary / 2);
                } else {
                    $salaryObject->salary +=  intdiv($from->diffInMinutes($to), $halfHour) * ($shiftTimetable->salary / 2);
                }
            }
        }

        return $salaryObject;
    }

    public function calculateDoneSalary(): ?SecurityGuardSalary
    {
        $workShift = $this->getWorkProcessShifts()->load([
            'guardedObject.workTimetable.hours',
            'guardedObject.workTimetableDates.hours',
            'securityGuard'
        ])->first();

        if (!$workShift) {
            return null;
        }

        $securityGuard = $workShift->securityGuard;
        $post = $workShift->guardedObject;

        if (!$post) {
            return null;
        }
        $shiftStart = Carbon::createFromDate($workShift->start_time);

        $salaryObject = new SecurityGuardSalary(
            $post,
            $securityGuard,
            $workShift->shift_status,
            $shiftStart
        );

        $startDay = $shiftStart->day;
        $nowDay = now()->day;

        for (; $startDay <= $nowDay; $startDay++) {
            $workDay = now()->subDays($nowDay - $startDay);
            $dayName = WorkTimetable::getByDayOfWeek($workDay->dayOfWeekIso);
            $shiftTimetable =
                $post->workTimetableDates->where('day', $workDay->toDateString())->first()
                ?? $post->workTimetable->where('day', $dayName)->first();

            if (!$shiftTimetable) {
                continue;
            }

            $hours = $shiftTimetable->hours;
            foreach ($hours as $hour) {
                $from = $hour->from->setDateFrom($workDay);
                $to = $hour->to->setDateFrom($workDay);

                //todo сделать рефакторинг
                if ($shiftStart->toDateString() === $workDay->toDateString()) {
                    if ($shiftStart->greaterThan($to)) {
                        continue;
                    }

                    $start = $shiftStart->lessThan($from)
                        ? $from
                        : $shiftStart;

                    $end = now()->lessThan($to)
                        ? now()
                        : $to;

                    $salaryObject->salary += $start->diffInMinutes($end) * ($shiftTimetable->salary / 60);

                } elseif ($workDay->toDateString() === now()->toDateString()) {
                    $end = now()->greaterThan($to)
                        ? $to
                        : now();
                    $salaryObject->salary += $from->diffInMinutes($end) * ($shiftTimetable->salary/ 60);
                } else {
                    $salaryObject->salary += $from->diffInMinutes($to) * ($shiftTimetable->salary/ 60);
                }
            }
        }
        // $salaryObject->salary = 100;
        return $salaryObject;
    }
}
