<?php

namespace App\Http\Controllers;

use App\GuardedObject;
use App\Http\Requests\WorkTimetableDates\WorkTimetableDateCreateRequest;
use App\WorkTimetableDates;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class WorkTimetableDateController extends Controller
{
    /**
     * @param WorkTimetableDateCreateRequest $request
     * @param GuardedObject $guardedObject
     * @return JsonResponse
     */
    public function store(WorkTimetableDateCreateRequest $request, GuardedObject $guardedObject): JsonResponse
    {
        DB::transaction(function () use ($request, $guardedObject) {
            $day = Carbon::createFromTimestamp($request->input('day'))->toDateString();
            $workTimetableDates = WorkTimetableDates::create([
                'day' => $day,
                'salary' => $request->input('salary'),
                'guarded_objects_id' => $guardedObject->id
            ]);

            $data = $this->handleWorkHoursData($workTimetableDates, $request->input('times'));
            DB::table('work_hours')->insert($data);
        });


        return response()->json(null, 204);
    }

    /**
     * @param WorkTimetableDateCreateRequest $request
     * @param WorkTimetableDates $workTimetableDates
     * @return JsonResponse
     */
    public function update(WorkTimetableDateCreateRequest $request, WorkTimetableDates $workTimetableDates): JsonResponse
    {
        DB::transaction(function () use ($workTimetableDates, $request) {
            $day = Carbon::createFromTimestamp($request->input('day'))->toDateString();
            $workTimetableDates->update([
                'day' => $day,
                'salary' => $request->input('salary')
            ]);

            $workTimetableDates->hours()->delete();
            $data = $this->handleWorkHoursData($workTimetableDates, $request->input('times'));
            DB::table('work_hours')->insert($data);
        });

        return response()->json(null, 204);
    }

    /**
     * @param WorkTimetableDates $workTimetableDates
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(WorkTimetableDates $workTimetableDates): JsonResponse
    {
        $workTimetableDates->delete();
        return response()->json(null, 204);
    }

    /**
     * @param WorkTimetableDates $workTimetableDates
     * @param array $times
     * @return array
     */
    private function handleWorkHoursData(WorkTimetableDates $workTimetableDates, array $times): array
    {
        $data = [];
        foreach ($times as $time) {
            try {
                $to = Carbon::createFromTimestamp($time['to'])->toTimeString();
                $from = Carbon::createFromTimestamp($time['from'])->toTimeString();
            } catch (Exception $exception) {
                throw new HttpException(400, 'Не переданы данные необходимые для построения графика.');
            }

            $data[] = [
                'to' => $to,
                'from' => $from,
                'work_timetables_date_id' => $workTimetableDates->id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        return $data;
    }
}
