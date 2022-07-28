<?php

namespace App\Http\Controllers;

use App\ActionLog;
use App\Event;
use App\User;
use App\Exports\SecurityGuard\SecurityGuardExport;
use App\GuardedObject;
use App\Http\Requests\SecurityGuard\SecurityGuardCreateRequest;
use App\Http\Resources\SecurityGuardResource;
use App\Imports\SecurityGuard\SecurityGuardImport;
use App\Rabbit;
use App\SecurityGuard;
use App\SecurityGuardCalculateSalary;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SecurityGuardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = User::where('id', $request->user()->id)->first();
        if ($user->role_type == 2) {
            $access = json_decode($user->access, true);
            if ($access['workers'] == '0') {
                return response()->json(
                    [
                        'message' => "Нет доступа"
                    ], 404
                );
            } 
        }
        SecurityGuardResource::addLastCheck(true);
        SecurityGuardResource::addCurrentShift(true);
        SecurityGuardResource::addLog(true);
        return response()->json(SecurityGuardResource::collection(
            SecurityGuard::query()
                ->orderByDesc('id')
                ->get()
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SecurityGuardCreateRequest $request
     * @return JsonResponse
     */
    public function store(SecurityGuardCreateRequest $request): JsonResponse
    {
        $user = User::where('id', $request->user()->id)->first();
        if ($user->role_type == 2) {
            $access = json_decode($user->access, true);
            if ($access['workers'] != 'edit') {
                return response()->json(
                    [
                        'message' => "Нет доступа"
                    ], 404
                );
            } 
        }
        $securityGuard = DB::transaction(function () use ($request): SecurityGuard {
            $securityGuardId = SecurityGuard::getNextId();
            $birthDate = Carbon::createFromTimestamp($request->input('birthDate'))->toDateString();
            $licenseToDate = Carbon::createFromTimestamp($request->input('licenseToDate'))->toDateString();
            if ($request->input('workType') == null) {
                $work_type = SecurityGuard::WORK_TYPE_WATCH;
            } else {
                $work_type = $request->input('workType');
            }
            $data = [
                'id' => $securityGuardId,
                'pin' => SecurityGuard::calculatePin($securityGuardId),
                'name' => $request->input('name'),
                'surname' => $request->input('surname'),
                'patronymic' => $request->input('patronymic'),
                'birth_date' => $birthDate,
                'phone' => $request->input('phone'),
                'license' => $request->input('license') === 1,
                'comment' => $request->input('comment'),
                'license_rank' => $request->input('licenseRank'),
                'knew_about_us' => $request->input('knewAboutUs'),
                'left_things' => $request->input('leftThings'),
                'driving_license' => $request->input('drivingLicense') === 1,
                'car' => $request->input('car'),
                'medical_book' => $request->input('medicalBook'),
                'gun' => $request->input('gun'),
                'debts' => $request->input('debts'),
                'work_type' => $work_type,
                'status' => $request->input('status'),
                'license_to_date' => $licenseToDate,
            ];

            return SecurityGuard::create($data);
        });

        ActionLog::addToLog(sprintf('Добавил охранника "%s"', $securityGuard->fullName));
        $socketMessage = [
            'fetchGuards' => '',
        ];
        /*$rabbit = new Rabbit();
        $rabbit->sendForSocket(json_encode($socketMessage));*/
        return response()->json(new SecurityGuardResource($securityGuard), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param SecurityGuard $securityGuard
     * @return JsonResponse
     */
    public function show(SecurityGuard $securityGuard)
    {
        SecurityGuardResource::addLastCheck(true);
        SecurityGuardResource::addCurrentShift(true);
        SecurityGuardResource::addLog(true);
        return response()->json(new SecurityGuardResource($securityGuard));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SecurityGuardCreateRequest $request
     * @param SecurityGuard $securityGuard
     * @return JsonResponse
     */
    public function update(SecurityGuardCreateRequest $request, SecurityGuard $securityGuard): JsonResponse
    {
        $user = User::where('id', $request->user()->id)->first();
        if ($user->role_type == 2) {
            $access = json_decode($user->access, true);
            if ($access['workers'] != 'edit') {
                return response()->json(
                    [
                        'message' => "Нет доступа"
                    ], 404
                );
            } 
        }
        $birthDate = Carbon::createFromTimestamp($request->input('birthDate'))->toDateString();
        $licenseToDate = Carbon::createFromTimestamp($request->input('licenseToDate'))->toDateString();

        $data = [
            'name' => $request->input('name'),
            'surname' => $request->input('surname'),
            'patronymic' => $request->input('patronymic'),
            'birth_date' => $birthDate,
            'phone' => $request->input('phone'),
            'license' => $request->input('license') === 1,
            'comment' => $request->input('comment'),
            'license_rank' => $request->input('licenseRank'),
            'knew_about_us' => $request->input('knewAboutUs'),
            'left_things' => $request->input('leftThings'),
            'driving_license' => $request->input('drivingLicense') === 1,
            'car' => $request->input('car'),
            'medical_book' => $request->input('medicalBook'),
            'gun' => $request->input('gun'),
            'debts' => $request->input('debts'),
            'work_type' => $request->input('workType'),
            'status' => $request->input('status'),
            'license_to_date' => $licenseToDate,
        ];

        $before = $securityGuard->toArray();

        $securityGuard->update($data);

        $after = $securityGuard->toArray();

        // TODO доработать логирование
        foreach ($before as $key => $value) {
            if (is_array($value)) {
                continue;
            }
            if ($after[$key] != $value) {
                $text = '';
                switch ($key) {
                    case 'surname':
                        $text = 'Изменил фамилию охранника с %s на %s (pin: %d)';
                        ActionLog::addToLog(sprintf($text, $before[$key], $after[$key], $securityGuard->pin));
                        continue 2;
                    case 'name':
                        $text = 'Изменил имя охранника с %s на %s (pin: %d)';
                        ActionLog::addToLog(sprintf($text, $before[$key], $after[$key], $securityGuard->pin));
                        continue 2;
                    case 'patronymic':
                        $text = 'Изменил отчество охранника с %s на %s (pin: %d)';
                        ActionLog::addToLog(sprintf($text, $before[$key], $after[$key], $securityGuard->pin));
                        continue 2;
                    case 'birth_date':
                        $text = 'Изменил дату рождения охранника %s с %s на %s';
                        break;
                    case 'phone':
                        $text = 'Изменил телефон охранника %s с %s на %s';
                        break;
                    case 'license':
                        if ($after[$key] == 1) {
                            $text = 'Отметил наличие УЛЧО у охранника %s';
                        } else {
                            $text = 'Снял отметку о наличии УЛЧО у охранника %s';
                        }
                        ActionLog::addToLog(sprintf($text, $securityGuard->fullName));
                        continue 2;
                    case 'comment':
                        $text = 'Изменил комментарий охранника %s с %s на %s';
                        break;
                    default:
                        break;
                }
                if ($text != '') {
                    ActionLog::addToLog(sprintf($text, $securityGuard->fullName, $before[$key], $after[$key]));
                }
            }
        }
        $socketMessage = [
            'fetchGuards' => '',
            'fetchGuardById' => [$securityGuard->id],
        ];
        /*$rabbit = new Rabbit();
        $rabbit->sendForSocket(json_encode($socketMessage));*/
        return response()->json(new SecurityGuardResource($securityGuard), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param SecurityGuard $securityGuard
     * @return Response
     * @throws Exception
     */
    public function destroy(SecurityGuard $securityGuard): Response
    {
        if (is_null($securityGuard->currentShift())) {
            if ($securityGuard->delete()) {
                ActionLog::addToLog(sprintf('Удалил охранника "%s"', $securityGuard->fullName));
                $socketMessage = [
                    'fetchGuards' => '',
                ];
                /*$rabbit = new Rabbit();
                $rabbit->sendForSocket(json_encode($socketMessage));*/
                return response(null, 204);
            }
        } else {
            return response('Нельзя удалить - охранник сейчас на объекте', 403);
        }
        return response('Something is going wrong', 404);
    }

    public function deactivate($guardId)
    {
        $securityGuard = SecurityGuard::findOrFail($guardId);
        if (is_null($securityGuard->currentShift())) {
            $securityGuard->active = 0;
            $securityGuard->save();
        
            $eventData = [
                'type' => 'guardDeactivate',
                'guarded_object_id' => 0,
                'security_guard_id' => $guardId,
            ];
            $event = Event::create($eventData);
            ActionLog::addToLog(sprintf('Сделал охранника %s неактивным', $securityGuard->fullName), $event->id);
            $socketMessage = [
                'fetchGuards' => '',
                'fetchGuardById' => [$guardId],
            ];
            /*$rabbit = new Rabbit();
            $rabbit->sendForSocket(json_encode($socketMessage));*/
            return response(null, 200);
        } else {
            return response('Нельзя деактивировать - охранник сейчас на объекте', 403);
        }
    }

    public function activate($guardId)
    {
        $securityGuard = SecurityGuard::findOrFail($guardId);
        $securityGuard->active = 1;
        $securityGuard->save();
        $eventData = [
            'type' => 'guardActivate',
            'guarded_object_id' => 0,
            'security_guard_id' => $guardId,
        ];
        $event =Event::create($eventData);
        ActionLog::addToLog(sprintf('Сделал охранника %s активным', $securityGuard->fullName), $event->id);
        $socketMessage = [
            'fetchGuards' => '',
            'fetchGuardById' => [$guardId],
        ];
        /*$rabbit = new Rabbit();
        $rabbit->sendForSocket(json_encode($socketMessage));*/
        return response(null, 200);
    }

    /**
     * @return Response|BinaryFileResponse
     */
    public function export()
    {
        return (new SecurityGuardExport())->download('securityGuard.xlsx');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        if (!$request->file('file')->isValid()) {
            throw new HttpException(404, 'Невалидный файл.');
        }
        // $filepathsource = $request->file('file');
        // $filepathdes = 'import/workers.xlsx';
        // $spreadsheet = IOFactory::load($filepathsource);
        // $writer = new Xlsx($spreadsheet);
        // $writer->save($filepathdes);
        Excel::import(new SecurityGuardImport(), $request->file('file'));
        // File::delete($filepathdes);
        return response()->json(null, 204);
    }

    /**
     * @param GuardedObject $guardedObject
     * @param SecurityGuard $securityGuard
     * @return JsonResponse
     */
    public function endShift(GuardedObject $guardedObject, SecurityGuard $securityGuard): JsonResponse
    {
        $workShift = $securityGuard->workShift()
            ->where('guarded_object_id', $guardedObject->id)
            ->where('shift_status', 'process')
            ->first();

        if (!$workShift) {
            throw new HttpException(404, 'Нет смены');
        }

        $salary = (new SecurityGuardCalculateSalary($securityGuard))->calculateProcessSalary()->salary;

        $workShift->update([
            'shift_status' => 'done',
            'end_time' => now(),
            'salary' => $salary ?? 0
        ]);

        return response()->json(null);
    }
}
