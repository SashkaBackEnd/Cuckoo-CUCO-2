<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>

<style>
    @media screen and (max-width: 576px) {
        table .block-top {
            display: block !important;
            width: 100% !important;
            border-right: 0 !important;
            border-left: 0 !important;
            border-bottom: 2px solid #F2F2F2 !important;
        }

        table .inline-first {
            display: inline-block !important;
            width: 100% !important;
            max-width: 65% !important;
            border-top: 0 !important;
        }

        table .inline-second {
            display: inline-block !important;
            border-right: 0 !important;
            border-top: 0 !important;
        }

        table .flex-wrap {
            display: flex !important;
            flex-wrap: wrap !important;
        }

        table .flex-wrap__item {
            width: 32% !important;
        }

        table .padding-right {
            padding-right: 0 !important;
        }

        table .display-none {
            display: none !important;
        }
    }
</style>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tbody>
    <tr>
        <td align="center" valign="top">
            <table max-width="980px" width="100%" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td style="font-weight: bold;font-size: 20px;background-color: #2F80ED; color: #ffffff; padding: 20px 35px;font-family: Helvetica">
                        Отчет о работе Кукушки
                    </td>
                </tr>
                <tr style="padding-bottom: 20px">
                    <td style="position: relative;font-weight: bold;font-size: 15px;padding: 14px 35px 11px;background-color: #FFFFFF;box-shadow: 0px 4px 5px rgba(0, 0, 0, 0.05);font-family: Helvetica">
                        ПОСТЫ / {{ mb_strtoupper($fromDate) }} - {{ mb_strtoupper($toDate) }} {{ $toYear }}
                    </td>
                </tr>
                <tr>
                    <td style="padding-left:15px; padding-right:15px; background-color:#F5F5F5;">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tbody>
                            <tr>
                                <td style="padding-left: 20px; padding-top: 20px; padding-bottom: 6px;font-weight: bold; font-size: 15px;font-family: Helvetica">
                                    Подзаголовок
                                </td>
                            </tr>
                            <tr class="flex-wrap">
                                <td class="flex-wrap__item" style="padding-right: 10px; padding-bottom: 20px;">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tbody>
                                        <tr>
                                            <td style="padding:13px 20px;border: 2px solid #DCDCDC;border-radius: 10px;font-size: 16px">
                                                Постов:
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tbody>
                                                    <tr>
                                                        <td style="font-weight: bold; font-size: 18px;padding-top: 4px">
                                                            {{ $summary['objects'] }}
                                                        </td>
                                                        <td style="padding-top: 4px;text-align: right;"><img
                                                                style="vertical-align: middle;"
                                                                src="{{ $message->embed(public_path() . '/images/email-img/img-mail-point.png') }}"
                                                                alt="post"></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td class="flex-wrap__item" style="padding-right: 10px;padding-bottom: 20px;">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tbody>
                                        <tr>
                                            <td style="padding:13px 20px;border: 2px solid #DCDCDC;border-radius: 10px;font-size: 16px">
                                                Смен:
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tbody>
                                                    <tr>
                                                        <td style="font-weight: bold; font-size: 18px;padding-top: 4px">
                                                            {{ $summary['shifts'] }}
                                                        </td>
                                                        <td style="padding-top: 4px;text-align: right;"><img
                                                                style="vertical-align: middle;"
                                                                src="{{ $message->embed(public_path() . '/images/email-img/img-mail-shift.png') }}"
                                                                alt="shifts"></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td class="flex-wrap__item padding-right"
                                    style="padding-right: 10px;padding-bottom: 20px;">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tbody>
                                        <tr>
                                            <td style="padding:13px 20px;border: 2px solid #DCDCDC;border-radius: 10px;font-size: 16px">
                                                Часы:
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tbody>
                                                    <tr>
                                                        <td style="font-weight: bold; font-size: 18px;padding-top: 4px">
                                                            {{ $summary['hours'] }}
                                                        </td>
                                                        <td style="padding-top: 4px;text-align: right;"><img
                                                                style="vertical-align: middle;"
                                                                src="{{ $message->embed(public_path() . '/images/email-img/img-mail-clock.png') }}"
                                                                alt="clock"></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td class="flex-wrap__item" style="padding-right: 10px;padding-bottom: 20px;">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tbody>
                                        <tr>
                                            <td style="padding:13px 20px;border: 2px solid #DCDCDC;border-radius: 10px;font-size: 16px">
                                                Звонков:
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tbody>
                                                    <tr>
                                                        <td style="font-weight: bold; font-size: 18px;padding-top: 4px">
                                                            {{ $summary['calls'] }}
                                                        </td>
                                                        <td style="padding-top: 4px;text-align: right;"><img
                                                                style="vertical-align: middle;"
                                                                src="{{ $message->embed(public_path() . '/images/email-img/img-mail-calls.png') }}"
                                                                alt="calls"></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td class="flex-wrap__item" style="padding-right: 10px;padding-bottom: 20px;">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tbody>
                                        <tr>
                                            <td style="padding:13px 20px;border: 2px solid #DCDCDC;border-radius: 10px;font-size: 16px">
                                                Ошибок:
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tbody>
                                                    <tr>
                                                        <td style="font-weight: bold; font-size: 18px;padding-top: 4px">
                                                            {{ $summary['errors'] }}
                                                        </td>
                                                        <td style="padding-top: 4px;text-align: right;"><img
                                                                style="vertical-align: middle;"
                                                                src="{{ $message->embed(public_path() . '/images/email-img/img-mail-errors.png') }}"
                                                                alt="errors"></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td class="flex-wrap__item" style="padding-bottom: 20px;">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tbody>
                                        <tr>
                                            <td style="padding:13px 20px;border: 2px solid #DCDCDC;border-radius: 10px;font-size: 16px">
                                                Зарплата:
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tbody>
                                                    <tr>
                                                        <td style="padding-top: 4px;font-weight: bold; font-size: 18px">
{{--                                                            {{ $summary['salary'] }}₽--}}
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr class="display-none">
                    <td style="padding-left:15px; padding-right:15px; background-color:#F5F5F5;">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tbody>
                            <tr>
                                <td style="padding-left: 20px; padding-bottom: 7px;font-weight: bold; font-size: 15px;font-family: Helvetica">
                                    Ошибки
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 16px;color: #8c8c8c; padding-bottom: 18px;font-family: Helvetica">
                                    <img style="padding-right: 8px;vertical-align: middle"
                                         src="{{ $message->embed(public_path() . '/images/email-img/img-mail-dialing.png') }}"
                                         alt="dialing">При дозвоне
                                </td>
                                <td style="font-size: 16px;color: #8c8c8c; padding-bottom: 18px;font-family: Helvetica">
                                    <img style="padding-right: 8px;vertical-align: middle"
                                         src="{{ $message->embed(public_path() . '/images/email-img/img-mail-shiftChange.png') }}"
                                         alt="shift-change">входе
                                    пересменки
                                </td>
                                <td style="font-size: 16px;color: #8c8c8c; padding-bottom: 18px;font-family: Helvetica">
                                    <img style="padding-right: 8px;vertical-align: middle"
                                         src="{{ $message->embed(public_path() . '/images/email-img/img-mail-guardMismatch.png') }}"
                                         alt="guard-mismatch">попытки повторного заступления
                                </td>
                                <td style="font-size: 16px;color: #8c8c8c; padding-bottom: 18px;font-family: Helvetica">
                                    <img style="padding-right: 8px;vertical-align: middle"
                                         src="{{ $message->embed(public_path() . '/images/email-img/img-mail-timeExceed.png') }}"
                                         alt="time-exceed">Превышение
                                    времени смены
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                @foreach($report as $objectReport)
                    <tr>
                        <td style="padding-left:15px; padding-right:15px; background-color:#F5F5F5;padding-bottom: 10px;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tbody>
                                <tr style="background-color: #FFFFFF;box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);">
                                    <td class="block-top"
                                        style="padding: 19px 20px 13px; font-size: 18px; font-weight: bold;border-right: 2px solid #F2F2F2;font-family: Helvetica;width: 50%">
                                        <img style="margin-right: 6px;vertical-align: middle;"
                                             src="{{ $message->embed(public_path() . '/images/email-img/img-mail-post.png') }}"
                                             alt="post">{{$objectReport['name']}}
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tbody>
                                            <tr>
                                                <td style="padding-top: 16px;font-size: 16px; color: #8c8c8c;font-family: Helvetica">
                                                    Смен: <span
                                                        style="font-weight: bold;color: #000000;">{{$objectReport['totalDoneShifts']}}</span>
                                                </td>
                                                <td style="padding-top: 16px;font-size: 16px; color: #8c8c8c;font-family: Helvetica">
                                                    Часы: <span
                                                        style="font-weight: bold;color: #000000;">{{$objectReport['totalWorkHours']}}</span>
                                                </td>
                                                <td style="padding-top: 16px;font-size: 16px; color: #8c8c8c;font-family: Helvetica">
                                                    {{ $fromDate }} - {{ $toDate }}
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td class="inline-first"
                                        style="padding: 18px 20px 16px;border-right: 2px solid #F2F2F2;width: 35%">
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tbody>
                                            <tr>
                                                <td style="color: #77BA2F;font-size: 16px;font-family: Helvetica">
                                                    Звонков: <span style="font-weight: bold">{{$objectReport['totalCalls']}}</span>
                                                </td>
                                                <td style="color: #FF6663;font-size: 16px;font-family: Helvetica">
                                                    Ошибок: <span
                                                        style="font-weight: bold">{{$objectReport['totalEmergencyCases']}}</span>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tbody>
                                            <tr>
                                                <td style="font-size: 16px;padding-top: 16px;font-family: Helvetica">
                                                    <img style="padding-right: 8px;vertical-align: middle;"
                                                         src="{{ $message->embed( public_path() . '/images/email-img/img-mail-dialing.png') }}"
                                                         alt="dialing"><span
                                                        style="font-weight: bold">{{ $objectReport['caseMissed'] }}</span>
                                                </td>
                                                <td style="font-size: 16px;padding-top: 16px;font-family: Helvetica">
                                                    <img style="padding-right: 8px;vertical-align: middle;"
                                                         src="{{ $message->embed(public_path() . '/images/email-img/img-mail-shiftChange.png') }}"
                                                         alt="shift-change"><span
                                                        style="font-weight: bold">{{ $objectReport['caseShiftChange'] }}</span>
                                                </td>
                                                <td style="font-size: 16px;padding-top: 16px;font-family: Helvetica">
                                                    <img style="padding-right: 8px;vertical-align: middle;"
                                                         src="{{ $message->embed(public_path() . '/images/email-img/img-mail-guardMismatch.png') }}"
                                                         alt="guard-mismatch"><span
                                                        style="font-weight: bold">{{ $objectReport['caseObjectGuardMismatch'] }}</span>
                                                </td>
                                                <td style="font-size: 16px;padding-top: 16px;font-family: Helvetica">
                                                    <img style="padding-right: 8px;vertical-align: middle;"
                                                         src="{{ $message->embed(public_path() . '/images/email-img/img-mail-timeExceed.png') }}"
                                                         alt="time-exceed"><span
                                                        style="font-weight: bold">{{ $objectReport['caseShiftTimeExceed'] }}</span>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td class="inline-second"
                                        style="padding: 20px 16px 16px;vertical-align: bottom;font-family: Helvetica">
{{--                                        <span style="font-weight: bold">{{ $objectReport['totalSalary'] }}₽</span>--}}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            @foreach($objectReport['shifts'] as $objectShift)
                                @if ($objectShift['totalEmergencyCases'] == 0)
                                    @continue
                                @endif
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tbody>
                                    <tr style="background-color: #F5F5F5;">
                                        <td class="block-top"
                                            style="padding: 19px 20px 13px; font-size: 18px; font-weight: bold;border:2px solid #DCDCDC;width: 50%;font-family: Helvetica">
                                            <img style="margin-right: 6px;vertical-align: middle;"
                                                 src="{{ $message->embed(public_path() . '/images/email-img/img-mail-guard.png') }}"
                                                 alt="">{{ $objectShift['shortName'] }}
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tbody>
                                                <tr>
                                                    <td style="padding-top: 16px;font-size: 16px; color: #8c8c8c;font-family: Helvetica">
                                                        Смен: <span
                                                            style="font-weight: bold;color: #000000;">{{ $objectShift['totalDoneShifts'] }}</span>
                                                    </td>
                                                    <td style="padding-top: 16px;font-size: 16px; color: #8c8c8c;font-family: Helvetica">
                                                        Часы: <span
                                                            style="font-weight: bold;color: #000000;">{{ $objectShift['totalWorkHours'] }}</span>
                                                    </td>
                                                    <td style="padding-top: 16px;font-size: 16px; color: #8c8c8c;font-family: Helvetica">
                                                        {{ date('d', $objectShift['startTime']) }} {{ $months[date('m', $objectShift['startTime'])] }} {{date('H:i', $objectShift['startTime'])}}
                                                        - {{ date('d', $objectShift['endTime']) }} {{ $months[date('m', $objectShift['endTime'])] }} {{date('H:i', $objectShift['endTime'])}}
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td class="inline-first"
                                            style="padding: 18px 20px 16px;border:2px solid #DCDCDC; border-left: 0;width: 35%;">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tbody>
                                                <tr>
                                                    <td style="color: #77BA2F;font-size: 16px;font-family: Helvetica">
                                                        Звонков: <span
                                                            style="font-weight: bold">{{ $objectShift['totalCalls'] }}</span>
                                                    </td>
                                                    <td style="color: #FF6663;font-size: 16px;font-family: Helvetica">
                                                        Ошибок: <span
                                                            style="font-weight: bold">{{ $objectShift['totalEmergencyCases'] }}</span>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tbody>
                                                <tr>
                                                    <td style="font-size: 16px;padding-top: 16px;font-family: Helvetica">
                                                        <img style="padding-right: 8px;vertical-align: middle;"
                                                             src="{{ $message->embed(public_path() . '/images/email-img/img-mail-dialing.png') }}"
                                                             alt="dialing"><span
                                                            style="font-weight: bold">{{ $objectShift['caseMissed'] }}</span>
                                                    </td>
                                                    <td style="font-size: 16px;padding-top: 16px;font-family: Helvetica">
                                                        <img style="padding-right: 8px;vertical-align: middle;"
                                                             src="{{ $message->embed(public_path() . '/images/email-img/img-mail-shiftChange.png') }}"
                                                             alt="shift-change"><span
                                                            style="font-weight: bold">{{ $objectShift['caseShiftChange'] }}</span>
                                                    </td>
                                                    <td style="font-size: 16px;padding-top: 16px;font-family: Helvetica">
                                                        <img style="padding-right: 8px;vertical-align: middle;"
                                                             src="{{ $message->embed(public_path() . '/images/email-img/img-mail-guardMismatch.png') }}"
                                                             alt="guard-mismatch"><span
                                                            style="font-weight: bold">{{ $objectShift['caseObjectGuardMismatch'] }}</span>
                                                    </td>
                                                    <td style="font-size: 16px;padding-top: 16px;font-family: Helvetica">
                                                        <img style="padding-right: 8px;vertical-align: middle;"
                                                             src="{{ $message->embed(public_path() . '/images/email-img/img-mail-timeExceed.png') }}"
                                                             alt="time-exceed"><span
                                                            style="font-weight: bold">{{ $objectShift['caseShiftTimeExceed'] }}</span>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td class="inline-second"
                                            style="padding: 20px 16px 16px;vertical-align: bottom;border:2px solid #DCDCDC;border-left:0;font-family: Helvetica">
{{--                                            <span style="font-weight: bold">{{ $objectShift['totalSalary'] }}₽</span>--}}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>

</body>
</html>
