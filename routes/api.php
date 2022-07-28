<?php

use App\AsterDialer;
use App\Http\Controllers\ApiTokenController;
use App\PhoneCheck;
use App\Rabbit;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PAMI\Message\Action\OriginateAction;
use Illuminate\Support\Facades\Mail;
use App\Mail\Password;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->post('/user', function (Request $request) {
    return $request->user();
});
Route::get('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');
    if (Auth::attempt($credentials)) {
        $tokenController = new ApiTokenController();
        $token = $tokenController->update($request);
        return response(json_encode(['userId' => $request->user()->id, 'apiToken' => $token]), 200);
    } else {
        return response(json_encode(['error' => 'Wrong username or password', 'error_code' => 1]), 401);
    }
});

Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');
    if (Auth::attempt($credentials)) {
        $tokenController = new ApiTokenController();
        $token = $tokenController->update($request);
        if (User::where('id', $request->user()->id)->first()->block) {
            return response([
                'error' => 'Менеджер заблокирован',
                'error_code' => 2
            ], 401);
        }
        return response(json_encode(['userId' => $request->user()->id, 'apiToken' => $token]), 200);
    } else {
        return response(json_encode(['error' => 'Wrong username or password', 'error_code' => 1]), 401);
    }
})->name('login');

Route::post('/createUser', function (Request $request) {
    $apiToken = Str::random(60);
    $password = $request->pass;
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($password),
        'api_token' => $apiToken,
        'role' => 'admin',
        'block' => 0
    ]);
    return json_encode(['userId' => $user->id, 'email' => $request->email, 'password' => $password, 'apiToken' => $apiToken]);
});



Route::post('/createManager', function (Request $request) {
    $user = User::where('email', $request->email)->where('deleted_at', null)->first();
    if ($user != null) {
        return response([
            'message' => 'Такой email уже зарегистрирован в системе'
        ], 400); 
    }
    $user = User::where('phone', $request->phone)->where('deleted_at', null)->first();
    if ($user != null) {
        return response([
            'message' => 'Такой телефон уже зарегистрирован в системе'
        ], 400); 
    }
    $access = [
        'workers' => 0,
        'managers' => 0,
        'objects' => 0,
        'reports' => 0,
        'logs' => 0,
    ];
    $apiToken = Str::random(60);
    $password = substr(str_shuffle(strtolower(sha1(rand() . time() . "Password"))),0, 8);
    $user = User::create([
        'name' => $request->name,
        'surname' => $request->surname,
        'patronymic' => $request->patronymic,
        'email' => $request->email,
        'phone' => $request->phone,
        'password' => Hash::make($password),
        'api_token' => $apiToken,
        'role' => 'manager',
        'role_type' => 2,
        'block' => 0,
        'access' => json_encode($access)
    ]);
    Mail::to($request->email)->send(new Password($request->name,$password));
    return response()->json([]);
});

Route::resource('/settings', 'SettingsController', ['only' => [
    'index', 'store'
]]);

Route::post('/shifts/start', 'WorkShiftController@startShift');
Route::post('/shifts/end', 'WorkShiftController@endShift');

Route::get('/objects/workTimetable/{guardedObject}', 'GuardedObjectController@showWorkTimetable');
Route::post('/objects/{objectId}/check', 'PhoneCheckController@check');
Route::post('/objects/{objectId}/sos/stop', 'GuardedObjectController@stopSos');

Route::get('/reports/objects/{fromDate}/{toDate}', 'ReportController@objects');
Route::get('/reports/managers/{fromDate}/{toDate}', 'ReportController@managers');
Route::get('/reports/objects/{fromDate}/{toDate}/email/{email}', 'ReportController@objectsEmail');
Route::get('/reports/guards/{fromDate}/{toDate}', 'ReportController@guards');
Route::get('/reports/excel/{fromDate}/{toDate}/{type}', 'ReportController@excel');

Route::get('/log', 'ActionLogController@getAll');
Route::get('/log/offset/{offset}', 'ActionLogController@getFor24HoursWithOffset');

Route::middleware(['auth:api'])->group(function () {
    Route::prefix('entities')->group(function () {
        Route::get('/', 'EntityController@index');
        Route::get('/main', 'EntityController@main');
        Route::post('/', 'EntityController@store');
        Route::get('/{entity}', 'EntityController@show');
        Route::put('/{entity}', 'EntityController@update');
        Route::delete('/{entity}', 'EntityController@destroy');
        Route::get('/{entity}/export', 'EntityController@export');
        Route::get('/export-all', 'EntityController@exportAll');
        Route::put('/{entity}/set-dialing-status', 'EntityController@setDialingStatus');
        Route::post('/{entity}/import', 'EntityController@import');

        Route::get('/posts/{guardedObject}', 'GuardedObjectController@show');
        Route::post('/{entity}/posts', 'GuardedObjectController@store');
        Route::put('/{entity}/posts/{guardedObject}', 'GuardedObjectController@update');
        Route::get('/{entity}/posts/{guardedObject}/check', 'GuardedObjectController@check');
        Route::delete('/posts/{guardedObject}', 'GuardedObjectController@destroy');
        Route::put('/posts/{guardedObject}/{securityGuard}/end-shift', 'SecurityGuardController@endShift');
    });

    Route::prefix('workers')->group(function () {
        Route::get('/', 'SecurityGuardController@index');
        Route::post('/', 'SecurityGuardController@store');
        Route::get('/export', 'SecurityGuardController@export');
        Route::get('/{securityGuard}', 'SecurityGuardController@show');
        Route::put('/{securityGuard}', 'SecurityGuardController@update');
        Route::delete('/{securityGuard}', 'SecurityGuardController@destroy');
        Route::post('/import', 'SecurityGuardController@import');
    });

    Route::put('/work-timetable/{guardedObject}', 'WorkTimetableController@update');

    Route::prefix('work-timetable-date')->group(function () {
        Route::post('/{guardedObject}', 'WorkTimetableDateController@store');
        Route::put('/{workTimetableDate}', 'WorkTimetableDateController@update');
        Route::delete('/{workTimetableDate}', 'WorkTimetableDateController@destroy');
    });

    Route::prefix('events')->group(function () {
        Route::get('/', 'EventController@index');
        Route::get('/list', 'EventController@list');
        Route::get('/short', 'EventController@shortIndex');
    });
    Route::post('/manager/entities', 'UserController@addEntities');
    Route::post('/manager/entities/delete', 'UserController@removeEntities');

    
    Route::prefix('users')->group(function () {
        Route::get('/', 'UserController@index');
        Route::get('/managers/list', 'UserController@managersList');
        Route::post('/', 'UserController@store');
        Route::get('/{user}', 'UserController@show');
        Route::put('/{user}', 'UserController@update');
        Route::delete('/{user}', 'UserController@destroy');
        Route::post('/deactivate/{user_id}', 'UserController@deactivate');
        Route::post('/activate/{user_id}', 'UserController@activate');
        Route::post('/permission/{user_id}', 'UserController@permission');
    });

    Route::prefix('logs')->group(function () {
        Route::get('/', 'ActionLogController@index');
    });
});


Route::get('/checkcalls', function () {
    PhoneCheck::checkAllCalls();
});

Route::get('/socketmessage', function (Request $request) {
    /*$rabbit = new Rabbit();
    $rabbit->sendForSocket($request->text);*/
    return response($request->text, 200);
});

Route::get('/dump', function (Request $request) {
    $dialer = new AsterDialer();
    $dialer->dialByQueueId($request->id);
    return response($request->id, 200);
});
Route::get('/maketest', function () {

    $pamiClientOptions = array(
        'host' => env('ASTER_AMI_HOST'),
        'scheme' => 'tcp://',
        'port' => env('ASTER_AMI_PORT'),
        'username' => env('ASTER_AMI_USER'),
        'secret' => env('ASTER_AMI_SECRET'),
        'connect_timeout' => 10000,
        'read_timeout' => 10000
    );
    $pamiClient = new PAMI\Client\Impl\ClientImpl($pamiClientOptions);
    $objectPhone = '79162887840';
// Open the connection
    $pamiClient->open();
    $originate = new OriginateAction('SIP/' . $objectPhone . '@rtk');

    $originate->setCallerId($objectPhone);
    $originate->setContext('pinout');
    $originate->setExtension('s');
    $originate->setTimeout(15000);
    $originate->setPriority(1);
    $originate->setAsync(false);

    $message = $pamiClient->send($originate);
// Close the connection
    $pamiClient->close();

});
