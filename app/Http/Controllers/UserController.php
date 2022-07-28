<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserListResource;
use App\User;
use App\ActionLog;
use App\SMS\SMS;
use App\EntityManager;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(UserResource::collection(User::query()
            ->orderByDesc('id')->get()));
    }

    public function permission(Request $request) {

 
        return response([
        	'status' => User::where('id', $request->user_id)->update([
            				'access' => $request->json()->all()
        				])
        ]);
  
    }

    /**
     * Display a list of managers
     *
     * @return JsonResponse
     */
    public function managersList(Request $request): JsonResponse
    {
        $user = User::where('id', $request->user()->id)->first();
        if ($user->role_type == 2) {
            $access = json_decode($user->access, true);
            if ($access['managers'] == '0') {
                return response()->json(
                    [
                        'message' => "Нет доступа"
                    ], 404
                );
            } 
        }
        return response()->json(UserListResource::collection(User::query()
            ->orderByDesc('id')->where('role_type',2)->get()));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param UserCreateRequest $request
     * @return JsonResponse
     */
    public function store(UserCreateRequest $request): JsonResponse
    {
        $user = User::where('id', $request->user()->id)->first();
        if ($user->role_type == 2) {
            $access = json_decode($user->access, true);
            if ($access['managers'] != 'edit') {
                return response()->json(
                    [
                        'message' => "Нет доступа"
                    ], 404
                );
            } 
        }
        $user = DB::transaction(function () use ($request) {
            $role = $request->input('roleType');
            $user = User::query()->create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'role_type' => $role,
                'password' => Hash::make($request->input('password'))
            ]);

            if ($role === User::ROLE_MANAGER && $request->has('entities')) {
                $data = [];
                foreach ($request->input('entities') as $entityId) {
                    $data[] = [
                        'entity_id' => $entityId,
                        'user_id' => $user->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                DB::table('entity_manager')->insert($data);
            }

            return $user;
        });

        return response()->json(new UserResource($user), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        return response()->json(new UserResource($user));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserUpdateRequest $request
     * @param User $user
     * @return JsonResponse
     */
    // public function update(UserUpdateRequest $request, User $user): JsonResponse
    // {
    //     DB::transaction(function () use ($request, $user) {
    //         $newRoleType = $request->input('roleType');
    //         $user->update([
    //             'name' => $request->input('name'),
    //             'surname' => $request->input('surname'),
    //             'patronymic' => $request->input('patronymic'),
    //             'email' => $request->input('email'),
    //             'phone' => $request->input('phone'),
    //             'role_type' => $newRoleType,
    //             'password' => Hash::make($request->input('password'))
    //         ]);

    //         $user->entities->delete();

    //         if ($newRoleType === User::ROLE_MANAGER && $request->has('entities')) {
    //             $data = [];
    //             foreach ($request->input('entities') as $entityId) {
    //                 $data[] = [
    //                     'entity_id' => $entityId,
    //                     'user_id' => $user->id,
    //                     'created_at' => now(),
    //                     'updated_at' => now()
    //                 ];
    //             }
    //             DB::table('entity_manager')->insert($data);
    //         }
    //     });


    //     return response()->json(new UserResource($user));
    // }
    public function update(Request $request): JsonResponse {
        $user = User::where('id', $request->user()->id)->first();
        if ($user->role_type == 2) {
            $access = json_decode($user->access, true);
            if ($access['managers'] != 'edit') {
                return response()->json(
                    [
                        'message' => "Нет доступа"
                    ], 404
                );
            } 
        }
        if ($request->has('password') && $request->password != '' && $request->password != null) {
        	User::where('id', $request->user)->update([
		   		'password' => Hash::make($request->input('password'))
			]);
        }

    	return response()->json([
    		'status' => User::where('id', $request->user)->update([
                'name' => $request->input('name'),
                'surname' => $request->input('surname'),
                'patronymic' => $request->input('patronymic'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone')
            ])
    	]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(User $user): JsonResponse
    {
        $user->forceDelete();
        return response()->json(null, 204);
    }


    /**
     * Deactivate the manager user.
     *
     * @param User $user
     * @return JsonResponse
     * @throws Exception
     */
    public function deactivate(Request $request): JsonResponse
    {
        $user = User::where('id', $request->user()->id)->first();
        if ($user->role_type == 2) {
            $access = json_decode($user->access, true);
            if ($access['managers'] != 'edit') {
                return response()->json(
                    [
                        'message' => "Нет доступа"
                    ], 404
                );
            } 
        }
        $user = User::where('id', $request->user_id)->first();
        if ($user->role_type == User::ROLE_MANAGER) {
            EntityManager::where('user_id', $request->user_id)->delete();
            ActionLog::addToLog(sprintf('Менеджер '.$user->name.' заблокирован по причине: ', $request->message));
            $user->update([
                'block' => true
            ]);
        }
        return response()->json(['status' => 'ok']);
    }
    /**
     * Activate the manager user.
     *
     * @param User $user
     * @return JsonResponse
     * @throws Exception
     */
    public function activate(Request $request): JsonResponse
    {
        $user = User::where('id', $request->user()->id)->first();
        if ($user->role_type == 2) {
            $access = json_decode($user->access, true);
            if ($access['managers'] != 'edit') {
                return response()->json(
                    [
                        'message' => "Нет доступа"
                    ], 404
                );
            } 
        }
        $user = User::where('id', $request->user_id)->first();
        if ($user->role_type == User::ROLE_MANAGER) {
            ActionLog::addToLog(sprintf('Менеджер '.$user->name.' разблокирован по причине: ', $request->message));
            $user->update([
                'block' => false
            ]);
        }
        return response()->json(['status' => 'ok']);
    }

    public function addEntities(Request $request) {
        if ($request->has('entity_id') && $request->has('user_id')) {
            $entity_manager = EntityManager::where('entity_id', $request->entity_id)->where('user_id',$request->user_id)->first();
            if ($entity_manager != null) {
                return response([
                    'status' => 'Already exist'
                ],409);
            }
            return EntityManager::create([
                'entity_id' => $request->entity_id,
                'user_id' => $request->user_id
            ]);
        }
        return response(404);
    }

    public function removeEntities(Request $request) {
        if ($request->has('entity_id') && $request->has('user_id')) {
            $entity_manager = EntityManager::where('entity_id', $request->entity_id)->where('user_id',$request->user_id)->first();
            if ($entity_manager == null) {
                return response(404);
            }
            return response([
                'status' => EntityManager::where('entity_id', $request->entity_id)->where('user_id',$request->user_id)->delete()
            ]); 
        }
        return response(404);
    }
}
