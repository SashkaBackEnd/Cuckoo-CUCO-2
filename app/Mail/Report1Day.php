<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Report1Day extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($fromDate, $toDate)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $fromDate = $this->fromDate;
        $toDate = $this->toDate;
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
        $this->subject = 'Отчет о работе Кукушки с  20:00 ' . $fromDateString . ' по 19:59 ' . $toDateString;
        return $this->view('mail/report', [
            'summary' => $summary,
            'report' => $report,
            'months' => $months,
            'fromDate' => $fromDateString,
            'toDate' => $toDateString,
            'toYear' => $toYear,
        ]);
    }
}
