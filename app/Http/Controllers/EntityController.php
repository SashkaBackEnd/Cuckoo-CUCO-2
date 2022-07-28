<?php

namespace App\Http\Controllers;

use App\ActionLog;
use App\Entity;
use App\User;
use App\Event;
use App\EntityManager;
use App\QueuedCall;
use App\Exports\Entity\EntityExport;
use App\Exports\Entity\EntitiesExport;
use App\GuardedObject;
use App\Http\Requests\Entity\EntityCreateRequest;
use App\Http\Requests\Entity\EntityUpdateRequest;
use App\Http\Resources\EntityResource;
use App\Http\Resources\PostResource;
use App\Imports\Entity\EntityImport;
use App\Imports\Entity\EntitiesImport;
use App\SecurityGuardEventCalculate;
use App\WorkShift;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Log;

class EntityController extends Controller
{
    /**
     * @param int $entityId
     * @param array $customers
     * @return array
     */
    private function handleCustomers(int $entityId, array $customers): array
    {
        $data = [];
        foreach ($customers as $customer) {
            try {
                $contact = $customer['contact'];
                $name = $customer['name'];
            } catch (Exception $ex) {
                throw new HttpException(400, "Переданы не корректные данные для создания контактного лица");
            }

            $data[] = [
                'name' => $name,
                'contact' => $contact,
                'entity_id' => $entityId
            ];
        }
        return $data;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'integer|required',
            'search' => 'nullable|max:255|min:0|string'
        ]);
        $data = [];
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
            if (empty($data)) {
                return response([]);     
            }
        }

        $entities = Entity::withCount('managers')
            ->when($request->input('search'), function ($q) use ($request) {
                return $q->where('name', 'like', "%{$request->input('search')}%")
                    ->orWhere('customer_name', 'like', "%{$request->input('search')}%")
                    ->orWhere('address', 'like', "%{$request->input('search')}%");
            })
            ->when(!empty($data), function($q) use ($data) {
                return $q->whereIn('id', $data);
            })
            ->orderByDesc('id')
            ->limit(Entity::INFINITY_SCROLL_PER_PAGE * $request->input('page'))
            ->get();

        EntityResource::setPostsRelationships(true);
        return response()->json(EntityResource::collection($entities));
    }

    public function main(Request $request): JsonResponse
    {

        // $request->validate([
        //     'page' => 'integer|required',
        //     'search' => 'nullable|max:255|min:0|string'
        // ]);

        $entities = Entity::query()
            ->when($request->input('search'), function ($q) use ($request) {
                return $q->where('name', 'like', "%{$request->input('search')}%")
                    ->orWhere('customer_name', 'like', "%{$request->input('search')}%")
                    ->orWhere('address', 'like', "%{$request->input('search')}%");
            })
            ->orderByDesc('id')
            ->limit(Entity::INFINITY_SCROLL_PER_PAGE * $request->input('page'))
            ->get();

        EntityResource::setPostsRelationships(true);
        $entities = EntityResource::collection($entities);
        return response()->json($entities);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EntityCreateRequest $request
     * @return JsonResponse
     */
    public function store(EntityCreateRequest $request): JsonResponse
    {
        if (Entity::where('id', $request->input('id'))->first() != null) {
            return response()->json([
                'message' => 'Объект с таким id уже есть в системе.'
            ],400);
        }
        $entity = DB::transaction(function () use ($request) {
            $entity = Entity::create([
                'id' => $request->input('id'),
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'comment' => $request->input('comment'),
                'address' => $request->input('address'),
                'customer_name' => $request->input('customerName'),
                'service_phone' => $request->input('servicePhone'),
                'dialing_status' => $request->input('dialingStatus'),
                'quantity_calls' => $request->input('quantityCalls'),
                'central_guarded_objects_id' => $request->input('centralPostId'),
                'max_duration_work' => $request->input('maxDurationWork'),
                'call_back_quantity' => $request->input('callBackQuantity'),
                'call_to' => Carbon::createFromTimestamp($request->input('callTo')),
                'call_from' => Carbon::createFromTimestamp($request->input('callFrom'))
            ]);

            if ($request->input('centralPostId')) {
                DB::table('guarded_objects')
                    ->where('id', $request->input('centralPostId'))
                    ->update([
                        'entity_id' => $request->input('id'),
                        'updated_at' => now()
                    ]);
            }

            $customers = $this->handleCustomers($request->input('id'), $request->input('customers'));
            DB::table('entity_customers')->insert($customers);
            return $entity;
        });

        return response()->json(new EntityResource($entity), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Entity $entity
     * @return JsonResponse
     */
    public function show(Request $request, Entity $entity): JsonResponse
    {
        $centralRelation = true;
        $postsRelation = true;
        $customersRelation = true;
        if ($request->has('centralPostObj')) {
            $centralRelation = $request->input('centralPostObj') === 'true';
        }
        if ($request->has('posts')) {
            $postsRelation = $request->input('posts') === 'true';
        }
        if ($request->has('customers')) {
            $customersRelation = $request->input('customers') === 'true';
        }

        EntityResource::setCentralPostRelationships($centralRelation);
        EntityResource::setPostsRelationships($postsRelation);
        EntityResource::setCustomersRelationships($customersRelation);
        PostResource::workTimetableRelation(true);
        PostResource::workTimetableDateRelation(true);
        if (!$entity->hasCentralPost()) {
            PostResource::addFullInfo(true);
        } else {
            PostResource::addIsCentral(true);
        }

        //TODO убрать ленивое получение отношений и задействовать тогда, когда это необходимо
        return response()->json(new EntityResource($entity->load([
            'customers',
            'centralPost',
            'posts.workTimetable.hours',
            'posts.workTimetableDates.hours',
            'posts.workShift.securityGuard'
        ])));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EntityUpdateRequest $request
     * @param Entity $entity
     * @return JsonResponse
     */
    public function update(EntityUpdateRequest $request, Entity $entity)
    {
        $user = User::where('id', $request->user()->id)->first();
        if ($user->role_type == 2) {
            $access = json_decode($user->access, true);
            if ($access['objects'] != 'edit') {
                return response()->json(
                    [
                        'message' => "Нет доступа"
                    ], 404
                );
            }
            $entity_manager = EntityManager::where('user_id', $user->id)->where('entity_id', $request->input('id'))->first();
            if ($entity_manager == null) {
                return response()->json(
                    [
                        'message' => "Нет доступа"
                    ], 404
                );
            }
        }
        $guardObjects = GuardedObject::where('entity_id',$request->id)->get();
        foreach ($guardObjects as $value) {
            QueuedCall::where('guarded_object_id', $value->id)->where('call_status', 'queued')->delete();
        }
        GuardedObject::where('entity_id', $request->id)->update([
            'queue_end_time' => null
        ]);

        if ($request->input('id') != $entity->id) {
            $newEntity = $this->updateEntityId($request, $entity);
            return response()->json(new EntityResource($newEntity));
        }

        DB::transaction(function () use ($entity, $request) {
            $entity->update([
                'id' => $request->input('id'),
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'comment' => $request->input('comment'),
                'address' => $request->input('address'),
                'customer_name' => $request->input('customerName'),
                'service_phone' => $request->input('servicePhone'),
                'dialing_status' => $request->input('dialingStatus'),
                'quantity_calls' => $request->input('quantityCalls'),
                'central_guarded_objects_id' => $request->input('centralPostId'),
                'max_duration_work' => $request->input('maxDurationWork'),
                'call_back_quantity' => $request->input('callBackQuantity'),
                'call_to' => Carbon::createFromTimestamp($request->input('callTo')),
                'call_from' => Carbon::createFromTimestamp($request->input('callFrom'))
            ]);

            if ($request->input('centralPostId') && is_array($request->input('centralPost'))) {
                DB::table('guarded_objects')
                    ->where('id', $request->input('centralPostId'))
                    ->update([
                        'entity_id' => $request->input('id'),
                        'updated_at' => now()
                    ]);
            }

            $customers = $this->handleCustomers($entity->id, $request->input('customers'));
            $entity->customers()->delete();
            DB::table('entity_customers')->insert($customers);
        });

        EntityResource::setCentralPostRelationships(true);
        EntityResource::setPostsRelationships(true);
        EntityResource::setCustomersRelationships(true);
        return response()->json(new EntityResource($entity));
    }

    private function updateEntityId(EntityUpdateRequest $request, Entity $entity)
    {

        $centralPost = $request->input('centralPost');
        $centralPostId = $request->input('centralPostId');

        DB::transaction(function () use ($entity, $request) {
            $entity->update([
                'id' => $request->input('id'),
                'central_guarded_objects_id' => null,
            ]);

        });

        DB::transaction(function () use ($entity, $request, $centralPostId ) {
            if (is_array($request->input('posts'))) {
                DB::table('guarded_objects')
                    ->where('entity_id', $request->input('id'))
                    ->update([
                        'entity_id' => $request->input('id'),
                        'updated_at' => now()
                    ]);
            }

            $entity->update([
                'id' => $request->input('id'),
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'comment' => $request->input('comment'),
                'address' => $request->input('address'),
                'customer_name' => $request->input('customerName'),
                'service_phone' => $request->input('servicePhone'),
                'dialing_status' => $request->input('dialingStatus'),
                'quantity_calls' => $request->input('quantityCalls'),
                'central_guarded_objects_id' => $centralPostId,
                'max_duration_work' => $request->input('maxDurationWork'),
                'call_back_quantity' => $request->input('callBackQuantity'),
                'call_to' => Carbon::createFromTimestamp($request->input('callTo')),
                'call_from' => Carbon::createFromTimestamp($request->input('callFrom'))
            ]);

            $customers = $this->handleCustomers($entity->id, $request->input('customers'));
            $entity->customers()->delete();
            DB::table('entity_customers')->insert($customers);
        });
        EntityResource::setCentralPostRelationships(true);
        EntityResource::setPostsRelationships(true);
        EntityResource::setCustomersRelationships(true);
        return $entity;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Entity $entity
     * @return Response
     * @throws Exception
     */
    public function destroy(Entity $entity): Response
    {
        $guardObjects = GuardedObject::where('entity_id', $entity->id)->get();
        foreach ($guardObjects as $guardObject) {
          WorkShift::where('guarded_object_id', $guardObject->id)->forceDelete();
          Event::where('guarded_object_id', $guardObject->id)->forceDelete();
          GuardedObject::where('id', $guardObject->id)->delete();
        }
        $entity->delete();
        return response('', 204);
    }

    /**
     * @param Entity $entity
     * @return Response|BinaryFileResponse
     */
    public function export(Entity $entity)
    {
        $entities = Entity::all();
        return (new EntitiesExport(
            $entities->load('posts.workTimetable.hours', 'posts.entity')
        ))->download('entity.xls');
        // return (new EntityExport(
        //     $entity->load('posts.workTimetable.hours', 'posts.entity')
        // ))->download('entity.xls');
    }

    public function exportAll()
    {
        $entities = Entity::all();
        return (new EntitiesExport(
            $entities->load('posts.workTimetable.hours', 'posts.entity')
        ))->download('entity.xls');
    }

    /**
     * @param Request $request
     * @param Entity $entity
     * @return JsonResponse
     */
    public function import(Request $request, Entity $entity)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        if (!$request->file('file')->isValid()) {
            throw new HttpException(404, 'Невалидный файл.');
        }

        Excel::import(new EntitiesImport(), $request->file('file'));
        return response()->json(null, 204);
    }

    /**
     * @param Request $request
     * @param Entity $entity
     * @return JsonResponse
     */
    public function setDialingStatus(Request $request, Entity $entity)
    {
        $request->validate([
            'set' => 'required|boolean'
        ]);

        $entity->update([
            'dialing_status' => (int)$request->input('set')
        ]);

        return response()->json(true, 200);
    }
}
