<?php

namespace App\Http\Controllers;

use App\ActionLog;
use App\Entity;
use App\Event;
use App\GuardedObject;
use App\WorkShift;
use App\Http\Requests\GuardedObjects\GuardedCreateRequest;
use App\Http\Requests\GuardedObjects\GuardedRequest;
use App\Http\Requests\GuardedObjects\GuardedUpdateRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\WorkTimetableDatesResource;
use App\Http\Resources\WorkTimetableResource;
use App\Rabbit;
use App\WorkTimetable;
use App\WorkTimetableDates;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GuardedObjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return response(GuardedObject::get()->map->listInfo(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param GuardedCreateRequest $request
     * @param Entity $entity
     * @return JsonResponse
     */
    public function store(GuardedCreateRequest $request, Entity $entity): JsonResponse
    {
        $go = GuardedObject::where('phone', $request->phone)->first();
        if ($go != null) {
            return response()->json([
                'message' => 'Такой телефон уже зарегистрирован в системе'
            ],400);
        }
        $guardedObject = DB::transaction(function () use ($request,$entity){
            $guardedObject = GuardedObject::create([
                'entity_id' => $entity->id,
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
            ]);

            $this->handleWorkTimetable($request, $guardedObject);

            return $guardedObject;
        });

        ActionLog::addToLog(sprintf('Добавил пост "%s"', $guardedObject->name));
        /*$socketMessage = [
            'fetchObjects' => '',
        ];
        $rabbit = new Rabbit();
        $rabbit->sendForSocket(json_encode($socketMessage));*/

        return response()->json($guardedObject->load(['workTimetable.hours', 'workTimetableDates.hours']), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param GuardedObject $guardedObject
     * @return JsonResponse
     */
    public function show(GuardedObject $guardedObject): JsonResponse
    {
        $guardedObject->load(['entity', 'workTimetable.hours', 'workTimetableDates.hours']);
        PostResource::addIsCentral(true);
        PostResource::addFullInfo(true);
        PostResource::workTimetableDateRelation(true);
        PostResource::workTimetableRelation(true);
        WorkTimetableResource::setHoursRelationships(true);
        WorkTimetableDatesResource::setHoursRelationships(true);
        return response()->json(new PostResource($guardedObject));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param GuardedUpdateRequest $request
     * @param Entity $entity
     * @param GuardedObject $guardedObject
     * @return Response
     */
    public function update(GuardedUpdateRequest $request, Entity $entity, GuardedObject $guardedObject)
    {
        $before = $guardedObject->toArray();

        $guardedObject = DB::transaction(function () use ($guardedObject, $request) {
            $guardedObject->update([
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
            ]);

            $this->handleWorkTimetable($request, $guardedObject);

            return $guardedObject;
        });

        $after = $guardedObject->toArray();
        // TODO изменить
        foreach ($before as $key => $value) {
            if (is_array($value)) {
                continue;
            }
            if ($after[$key] != $value) {
                $text = '';
                switch ($key) {
                    case 'name':
                        $text = 'Изменил название поста с %s на %s';
                        ActionLog::addToLog(sprintf($text, $before[$key], $after[$key]));
                        continue 2;
                    case 'phone':
                        $text = 'Изменил телефон поста "%s" с %s на %s';
                        break;
                    default:
                        break;
                }
                if ($text != '') {
                    ActionLog::addToLog(sprintf($text, $guardedObject->name, $before[$key], $after[$key]));
                }
            }
        }
        /*$socketMessage = [
            'fetchObjects' => '',
            'fetchObjectById' => $guardedObject->id,
        ];
        $rabbit = new Rabbit();
        $rabbit->sendForSocket(json_encode($socketMessage));*/
        return response(null, 204);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param GuardedObject $guardedObject
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(GuardedObject $guardedObject): JsonResponse
    {
        if (count($guardedObject->currentShifts()) !== 0) {
            throw new HttpException(403, 'Нельзя удалить - на объекте есть охранник.');
        }

        DB::transaction(function () use ($guardedObject) {
            $guardedObject->clearDialQueue();
            $guardedObject->forceDelete();  // TODO полностью удаляю, без софт делит
        });

        WorkShift::where('guarded_object_id', $guardedObject->id)->delete();

        Event::where('guarded_object_id', $guardedObject->id)->delete();

        ActionLog::addToLog("Удалил пост $guardedObject->name");
        /*$socketMessage = [
            'fetchObjects' => '',
        ];
        $rabbit = new Rabbit();
        $rabbit->sendForSocket(json_encode($socketMessage));*/
        return response()->json(null, 204);
    }

    public function stopSos($objectId)
    {
        $guardedObject = GuardedObject::findOrFail($objectId);
        if ($guardedObject->sos_status == 1) {
            $eventData = [
                'type' => 'sosEnd',
                'guarded_object_id' => $guardedObject->id,
                'security_guard_id' => 0,
            ];
            $socketMessage = [
                'fetchObjects' => '',
                'fetchObjectById' => $guardedObject->id,
            ];
            $guardedObject->sos_status = 0;
            $guardedObject->save();
            $event = Event::create($eventData);
            ActionLog::addToLog(sprintf('Снял тревогу с поста "%s"', $guardedObject->name), $event->id);
            /*$rabbit = new Rabbit();
            $rabbit->sendForSocket(json_encode($socketMessage));*/
            return response(null, 200);
        }
        return response('Already stopped', 200);
    }

    /**
     * @param GuardedObject $guardedObject
     * @return JsonResponse
     */
    public function showWorkTimetable(GuardedObject $guardedObject): JsonResponse
    {
        $guardedObject->load(['workTimetable.hours', 'workTimetableDates.hours']);
        $nonStandard = WorkTimetableDatesResource::collection($guardedObject->workTimetableDates);
        $standard = WorkTimetableResource::collection($guardedObject->workTimetable);

        return response()->json([
            'nonStandard' => $nonStandard,
            'standard' => $standard
        ]);
    }

    /**
     * @param GuardedCreateRequest $request
     * @param GuardedObject $guardedObject
     */
    private function handleWorkTimetable(GuardedRequest $request, GuardedObject $guardedObject)
    {
        $days = [
            WorkTimetable::MON => $request->input('Mon'),
            WorkTimetable::TUE => $request->input('Tue'),
            WorkTimetable::WED => $request->input('Wed'),
            WorkTimetable::THU => $request->input('Thu'),
            WorkTimetable::FRI => $request->input('Fri'),
            WorkTimetable::SAT => $request->input('Sat'),
            WorkTimetable::SUN => $request->input('Sun')
        ];

        DB::transaction(function () use ($days, $guardedObject, $request) {
            DB::table('work_timetables')
                ->where('guarded_objects_id', $guardedObject->id)
                ->delete();

            $workHours = [];
            //TODO n+1
            foreach ($days as $day => $data) {
                $workTimetable = WorkTimetable::create([
                    'day' => $day,
                    'salary' => $data['salary'] == '' ? 0 : $data['salary'],
                    'guarded_objects_id' => $guardedObject->id
                ]);

                if (!$data['times']) {
                    continue;
                }

                foreach ($data['times'] as $time) {
                    $workHours[] = [
                        'to' => Carbon::createFromTimestamp($time['to'])->toTimeString(),
                        'from' => Carbon::createFromTimestamp($time['from'])->toTimeString(),
                        'work_timetables_id' => $workTimetable->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }
            DB::table('work_hours')->insert($workHours);

            // NonStandard
            DB::table('work_timetable_dates')
              ->where('guarded_objects_id', $guardedObject->id)
              ->delete();
            foreach($request->input('nonStandardWork') as $key => $value){
            $workTimetableDate = WorkTimetableDates::create([
              'day' => Carbon::createFromTimestamp($value['day'])->toDateString(),
              'salary' => $value['salary'],
              'guarded_objects_id' => $guardedObject->id
            ]);

            if (!$value['times']) {
              continue;
            }

            foreach ($value['times'] as $time) {
              $workHoursDate[] = [
                'to' => Carbon::createFromTimestamp($time['to'])->toTimeString(),
                'from' => Carbon::createFromTimestamp($time['from'])->toTimeString(),
                'work_timetables_date_id' => $workTimetableDate->id,
                'created_at' => now(),
                'updated_at' => now()
              ];
            }
          }
            if(isset($workHoursDate)){
              DB::table('work_hours')->insert($workHoursDate);
            }
        });
    }

    /**
     * @param Entity $entity
     * @param GuardedObject $guardedObject
     * @return JsonResponse
     */
    public function check(Entity $entity, GuardedObject $guardedObject): JsonResponse
    {
        if ($entity->hasCentralPost()) {
            $guardedObject = $entity->centralPost;
        }

        DB::table('queued_calls')->insert([
            'call_date' => now()->addSeconds(5)->timestamp,
            'guarded_object_id' => $guardedObject->id,
            'created_at' => now(),
            'updated_at' => now(),
            'call_status' => 'custom'
        ]);

        return response()->json(null);
    }
}
