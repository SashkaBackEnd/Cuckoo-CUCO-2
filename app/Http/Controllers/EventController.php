<?php

namespace App\Http\Controllers;

use App\Event;
use App\EventSmall;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\EntityManager;
use App\User;
use App\GuardedObject;
use App\Http\Resources\EventResource;

class EventController extends Controller
{
    //TODO наброски отчета
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $request->validate([
            'from' => 'nullable|integer',
            'to' => 'nullable|integer',
            'type' => ['required', 'integer', 'regex:/^1$|^2$|^3$/']
        ]);

        $from = $request->input('from')
            ? Carbon::createFromTimestamp($request->input('from'))
            : null;

        $to = $request->input('to')
            ? Carbon::createFromTimestamp($request->input('to'))
            : null;

        $event = Event::getEvent($request->input('type'), $from, $to);
        return response($event, 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    /**
     * @return JsonResponse
     */

    public function list(Request $request) {
        $events = Event::query();
        if ($request->has('manager_id')) {
            $entity_manager = EntityManager::where('user_id', $request->manager_id)->get();
            $temp = [];
            foreach ($entity_manager as $value) {
                $temp[] = $value->entity_id;
            }

            $guarded_objects = GuardedObject::whereIn('entity_id', $temp)->get();
            $temp = [];
            foreach ($guarded_objects as  $item) {
                $temp[] = $item->id;
            }
            $events->whereIn('guarded_object_id', $temp);
        }
        // Основные
        if ($request->has('entity_id')) {
            $guarded_objects = GuardedObject::where('entity_id', $request->entity_id)->get();
            $temp = [];
            foreach ($guarded_objects as  $item) {
                $temp[] = $item->id;
            }
            $events->whereIn('guarded_object_id', $temp);
        }
        if ($request->has('date_to')) {
            $events->where('created_at', '<=', $request->date_to);
        }
        if ($request->has('date_from')) {
            $events->where('created_at', '>=', $request->date_from);
        } else {
            $events->where('created_at', '>', Carbon::now()->subDays(31));
        }
        if ($request->has('status')) {
            $events->where('type', $request->status);
        }
        if ($request->has('security_guard_id')) {
            $events->where('security_guard_id', $request->security_guard_id);
        }
        if ($request->has('guarded_object_id')) {
            $events->where('guarded_object_id', $request->guarded_object_id);
        }
        // return $events->get();
        return $events->orderBy('created_at', 'desc')->get()->map->listInfo();
    }   
    // public function Event()
    // {
    //     return $this->hasMany('App\Event');
    // }
    public function shortIndex(Request $request)
    {
        $data = [];
        $posts = [];
        $user = User::where('id', $request->user()->id)->first();
        if ($user->role_type == 2) {
            $access = json_decode($user->access, true);
            if ($access['objects'] == '0') {
                return response()->json(
                    [
                        'message' => "Нет доступа"
                    ], 404
                );
            } 
            $entity_manager = EntityManager::where('user_id', $user->id)->get();
            foreach ($entity_manager as $value) {
                $data[] = $value->entity_id;
            }
            $guarded_objects = GuardedObject::whereIn('entity_id', $data)->get();
            foreach ($guarded_objects as $value) {
                $posts[] = $value->id;
            }
            if (empty($posts)) {
                return response([]);     
            }
        }


        //TODO заносить в кэш
        $countPostsWithoutGuards = DB::table('guarded_objects')
            ->selectRaw('COUNT(id) count_id')
            ->whereRaw(DB::raw("id NOT IN (SELECT guarded_object_id FROM work_shifts WHERE shift_status = 'process')"))
            ->whereNull('deleted_at')
            ->when(!empty($posts), function($q) use ($data) {
                return $q->whereIn('id', $data);
            })
            ->value('count_id');

        $fromDate = now()->subDay()->toDateTimeString();

        $countDialingErrors = DB::table('events')
            ->selectRaw('COUNT(id) count_id')
            ->where('created_at', '>=', $fromDate)
            ->whereIn('type', ['unknownPin', 'checkFailed', '1MissedCall', '2MissedCall'])
            ->when(!empty($posts), function($q) use ($data) {
                return $q->whereIn('guarded_object_id', $data);
            })
            ->value('count_id');

        $countErrorsStartingJob = DB::table('events')
            ->selectRaw('COUNT(events.id) count_id')
            ->whereRaw("guarded_object_id IN (SELECT guarded_object_id FROM aster_calls WHERE call_date >= '$fromDate' AND direction = 'in')")
            ->where('created_at', '>=', $fromDate)
            ->whereIn('type', ['unknownPin', 'objectGuardMismatch'])
            ->when(!empty($posts), function($q) use ($data) {
                return $q->whereIn('guarded_object_id', $data);
            })
            ->value('count_id');

        $countErrorsFinishedJob = DB::table('events')
            ->selectRaw('COUNT(events.id) count_id')
            ->whereRaw("guarded_object_id IN (SELECT guarded_object_id FROM aster_calls WHERE call_date >= '$fromDate' AND direction = 'out')")
            ->where('created_at', '>=', $fromDate)
            ->whereIn('type', ['unknownPin', 'shortEndShiftTry', 'timeoutEndShift'])
            ->when(!empty($posts), function($q) use ($data) {
                return $q->whereIn('guarded_object_id', $data);
            })
            ->value('count_id');

        return response()->json([
            'countPostsWithoutGuards' => $countPostsWithoutGuards,
            'countDialingErrors' => $countDialingErrors,
            'countErrorsStartingJob' => $countErrorsStartingJob,
            'countErrorsFinishedJob' => $countErrorsFinishedJob
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
