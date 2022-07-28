<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SecurityGuardEventCalculate implements EventInterface
{
    /**
     * @param Carbon|null $from
     * @param Carbon|null $to
     * @return Collection
     */
    public function getEventStat(Carbon $from = null, Carbon $to = null): Collection
    {
        $from = $from ? $from->toDateTimeString() : null;
        $to = $to ? $to->toDateTimeString() : null;

        return collect(DB::select("
            WITH count_events as (SELECT count(e.id)
                                   OVER (PARTITION BY e.type, e.security_guard_id, e.guarded_object_id) count_events,
                             sg.id                                                 security_guards_id,
                             e.guarded_object_id,
                             e.type
                      FROM events e
                               JOIN security_guards sg ON e.security_guard_id = sg.id
                      WHERE IF('$from' <> '', e.created_at >= '$from',
                               e.created_at is not null)
                        AND IF('$to' <> '', e.created_at <= '$to',
                               e.created_at is not null))

            SELECT *
            FROM count_events
            GROUP BY security_guards_id, guarded_object_id, type, count_events
        "));
    }

    public function generateEvent(Carbon $from = null, Carbon $to = null): Collection
    {
        $securityEvents = $this->getEventStat($from, $to);

        $securityGuards = SecurityGuard::query()
            ->whereIn('id', data_get($securityEvents, '*.security_guards_id'))
            ->get();

        $posts = GuardedObject::query()
            ->with(['entity', 'workShift'])
            ->whereIn('id', data_get($securityEvents, '*.guarded_object_id'))
            ->get();

        $result = collect();
        foreach ($securityGuards as $securityGuard) {
            $securityGuardEvent = new SecurityGuardEventStruct($securityGuard);

            $events = $securityEvents->where('security_guards_id', $securityGuard->id);

            $securityGuardEvent->callCount = $events->pluck('count_events')->sum();

            $errorsEvents = $events->whereIn('type', Event::getErrorsType());
            $securityGuardEvent->errorsCount = $errorsEvents->pluck('count_events')->sum();

            $callErrorsEvents = $events->whereIn('type', Event::getCallInErrorsType());
            $securityGuardEvent->callInErrors = $callErrorsEvents->pluck('count_events')->sum();

            $startWorkErrorsEvents = $events->whereIn('type', [
                Event::UNKNOWN_PIN,
                Event::OBJECT_GUARD_MISMATCH
            ]);
            $securityGuardEvent->startShiftErrors = $startWorkErrorsEvents->pluck('count_events')->sum();
            $securityGuardEvent->reStartShiftErrors = $startWorkErrorsEvents->pluck('count_events')->sum();

            $overtimeShiftErrorsEvents = $events->where('type', Event::SHIFT_TIME_EXCEEDED);
            $securityGuardEvent->overtimeShiftErrors = $overtimeShiftErrorsEvents->pluck('count_events')->sum();

            $postsWhereWork = $posts->whereIn('id', data_get($events, '*.guarded_object_id'));
            foreach ($postsWhereWork as $post) {
                $shifts = $post->workShift
                    ->where('security_guard_id', $securityGuard->id)
                    ->where('shift_status', 'done')
                    ->when($from, function ($q) use ($from){
                        return $q->where('created_at', '>=', $from);
                    })
                    ->when($to, function ($q) use ($to) {
                        return $q->where('created_at', '<=', $to);
                    });

                $postEventStruct = new PostEventStruct($post, $shifts);

                $postEventStruct->callCount = $events->where('guarded_object_id', $post->id)
                    ->pluck('count_events')->sum();

                $postEventStruct->errorsCount = $errorsEvents->where('guarded_object_id', $post->id)
                    ->pluck('count_events')->sum();

                $postEventStruct->callInErrors = $callErrorsEvents->where('guarded_object_id', $post->id)
                    ->pluck('count_events')->sum();

                $startPostErrors = $startWorkErrorsEvents->where('guarded_object_id', $post->id)
                    ->pluck('count_events')->sum();
                $postEventStruct->startShiftErrors = $startPostErrors;
                $postEventStruct->reStartShiftErrors = $startPostErrors;

                $postEventStruct->overtimeShiftErrors = $overtimeShiftErrorsEvents->where('guarded_object_id', $post->id)
                    ->pluck('count_events')->sum();

                $securityGuardEvent->insertPost($postEventStruct);
            }

            $result[] = $securityGuardEvent;
        }
        return $result;
    }
}
