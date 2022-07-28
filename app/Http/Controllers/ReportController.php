<?php

namespace App\Http\Controllers;

use App\WorkShift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;


class ReportController extends Controller
{
    protected $customRules = [
        'fromDate' => 'required|date_format:Y-m-d\TH:i',
        'toDate' => 'required|date_format:Y-m-d\TH:i',
//        'email' => 'email',
    ];

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function objects(Request $request, $fromDate, $toDate)
    {
        $request->request->add(['fromDate' => $fromDate]);
        $request->request->add(['toDate' => $toDate]);
        $fromDate = date('Y-m-d\TH:i',$fromDate);
        $toDate = date('Y-m-d\TH:i',$toDate);
        // $validatedData = $request->validate($this->customRules);
        $report = WorkShift::getObjectsReport($fromDate, $toDate);
        
        return response($report, 200);
    }
    public function managers(Request $request, $fromDate, $toDate)
    {
        $request->request->add(['fromDate' => $fromDate]);
        $request->request->add(['toDate' => $toDate]);
        $fromDate = date('Y-m-d\TH:i',$fromDate);
        $toDate = date('Y-m-d\TH:i',$toDate);
        // $validatedData = $request->validate($this->customRules);
        $report = WorkShift::getManagersReport($fromDate, $toDate);
        
        return response()->json($report);
    }
    public function guards(Request $request, $fromDate, $toDate)
    {
        $request->request->add(['fromDate' => $fromDate]);
        $request->request->add(['toDate' => $toDate]);
        $fromDate = date('Y-m-d\TH:i',$fromDate);
        $toDate = date('Y-m-d\TH:i',$toDate);
        // $validatedData = $request->validate($this->customRules);
        $report = WorkShift::getGuardsReport($fromDate, $toDate);
  
        return response($report, 200);
    }


    public function excel(Request $request, $fromDate, $toDate, $type)
    {
        $request->request->add(['fromDate' => $fromDate]);
        $request->request->add(['toDate' => $toDate]);
        $fromDate = date('Y-m-d\TH:i',$fromDate);
        $toDate = date('Y-m-d\TH:i',$toDate);
        if ($type == "guards") {
            $report = WorkShift::getGuardsReportExcel($fromDate, $toDate);
        }
        if ($type == "objects") {
            $report = WorkShift::getObjectsReportExcel($fromDate, $toDate);
        }
        if ($type == "managers") {
            $report = WorkShift::getManagersReportExcel($fromDate, $toDate);
        }

        return (new ReportExport($report))->download('report.xlsx');
    }

    public function objectsEmail(Request $request, $fromDate, $toDate, $email)
    {
        $request->request->add(['fromDate' => $fromDate]);
        $request->request->add(['toDate' => $toDate]);
        $request->request->add(['email' => $email]);
        $validatedData = $request->validate($this->customRules);
        \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\Report1Day($fromDate, $toDate));
        $report = \App\WorkShift::getObjectsReport($fromDate, $toDate);
        $summary = [
            'objects' => count($report),
            'shifts' => 0,
            'hours' => 0,
            'calls' => 0,
            'errors' => 0,
        ];
        foreach ($report as $objectReport) {
            $summary['shifts'] += $objectReport['totalDoneShifts'];
            $summary['hours'] += $objectReport['totalWorkHours'];
            $summary['calls'] += $objectReport['totalCalls'];
            $summary['errors'] += $objectReport['totalEmergencyCases'];
        }
        $months = [
            '01' => 'янв',
            '02' => 'фев',
            '03' => 'мар',
            '04' => 'апр',
            '05' => 'мая',
            '06' => 'июн',
            '07' => 'июл',
            '08' => 'авг',
            '09' => 'сен',
            '10' => 'окт',
            '11' => 'ноя',
            '12' => 'дек',
        ];
        $fromDateString = date('d', strtotime($fromDate)) . ' ' . $months[date('m', strtotime($fromDate))];
        $toDateString = date('d', strtotime($toDate)) . ' ' . $months[date('m', strtotime($toDate))];
        $toYear = date('Y', strtotime($toDate));
        return response(view('mail/report', [
            'summary' => $summary,
            'report' => $report,
            'months' => $months,
            'fromDate' => $fromDateString,
            'toDate' => $toDateString,
            'toYear' => $toYear,
        ]), 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="report.html"',
        ]);
    }
}
