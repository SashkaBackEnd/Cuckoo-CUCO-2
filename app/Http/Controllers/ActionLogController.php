<?php

namespace App\Http\Controllers;

use App\ActionLog;
use App\Http\Resources\ActionLogResource;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActionLogController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'required|integer',
            'from' => 'nullable',
            'to' => 'nullable',
            'search' => 'nullable|string|max:255'
        ]);

        $quantityLogs = ActionLog::query()
            ->toBase()
            ->selectRaw('COUNT(id) as count_id')
            ->when($request->input('search'), function ($q) use ($request) {
                return $q->where('action_text', 'like', "%{$request->input('search')}%");
            })
            ->when($request->input('from'), function ($q) use ($request) {
                $from = Carbon::createFromTimestamp($request->input('from'));
                return $q->where('created_at', '>=', $from->toDateTimeString());
            })
            ->when($request->input('to'), function ($q) use ($request) {
                $to = Carbon::createFromTimestamp($request->input('to'));
                return $q->where('created_at', '<=', $to->toDateTimeString());
            })->value('count_id');

        $logs = ActionLog::query()
            ->with('user.entities')
            ->orderByDesc('id')
            ->when($request->input('search'), function ($q) use ($request) {
                return $q->where('action_text', 'like', "%{$request->input('search')}%");
            })
            ->when($request->input('from'), function ($q) use ($request) {
                $from = Carbon::createFromTimestamp($request->input('from'));
                return $q->where('created_at', '>=', $from->toDateTimeString());
            })
            ->when($request->input('to'), function ($q) use ($request) {
                $to = Carbon::createFromTimestamp($request->input('to'));
                return $q->where('created_at', '<=', $to->toDateTimeString());
            })
            ->limit(ActionLog::PAGINATE_LIMIT)
            ->offset(ActionLog::PAGINATE_LIMIT * $request->input('page'))
            ->get();

        ActionLogResource::addUser(true);
        return response()->json([
            'logs' => ActionLogResource::collection($logs),
            'quantity' => $quantityLogs
        ]);
    }

    protected $customRules = [
        'offset' => 'required|integer|min:0',
    ];

    public function getForLast24Hours()
    {
        $log = ActionLog::where('created_at', '>=', Carbon::now()->subDay()->toDateTimeString())->get()->map(function ($logEntry) {
            return $logEntry->listInfo();
        });
        return response($log, 200);
    }

    public function getFor24HoursWithOffset(Request $request, $offset)
    {
        $request->request->add(['offset' => $offset]);
        $validatedData = $request->validate($this->customRules);
        $log = ActionLog::where('created_at', '>=', Carbon::now()->subDays($offset + 1)->toDateTimeString())
            ->where('created_at', '<', Carbon::now()->subDays($offset)
                ->toDateTimeString())->get()->map(function ($logEntry) {
            return $logEntry->listInfo();
        });
        return response($log, 200);
    }

    public function getAll()
    {
        $log = ActionLog::orderBy('created_at','desc')->get()->map(function ($logEntry) {
            return $logEntry->listInfo();
        });
        return response($log, 200);
    }
}
