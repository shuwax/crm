<?php

namespace App\Http\Controllers;

use App\AcceptedPayment;
use App\AcceptedPaymentUserStory;
use App\Agencies;
use App\ClientRouteInfo;
use App\Department_info;
use App\Department_types;
use App\Departments;
use App\DoublingQueryLogs;
use App\EmployeeOfTheWeek;
use App\EmployeeOfTheWeekRanking;
use App\JankyPenatlyProc;
use App\PaymentAgencyStory;
use App\PenaltyBonus;
use App\RecruitmentStory;
use App\ReportCampaign;
use App\Schedule;
use App\SuccessorHistory;
use App\SummaryPayment;
use App\User;
use App\UserEmploymentStatus;
use App\UserTypes;
use App\Utilities\Dates\MonthFourWeeksDivision;
use App\Utilities\DataProcessing\ConfirmationStatistics;
use App\Utilities\Reports\Report_data_methods\DataNewUsersRbhReport;
use App\Utilities\Salary\ProvisionLevels;
use App\Work_Hour;
use DateTime;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use App\ActivityRecorder;

class FinancesController extends Controller
{
    public $toSave = 0;
    private $acceptedPaymentForDepartment = null;

    /**
     * @return int
     */
    public function getToSave()
    {
        return $this->toSave;
    }


    /**
     * @param int $toSave
     */
    public function setToSave($toSave)
    {
        $this->toSave = $toSave;
    }


    public function viewPaymentGet()
    {
        $logged_user = Auth::user();
        return view('finances.viewPayment')->with('user', $logged_user);
    }
    public function viewPaymentCadreGet()
    {
        return view('finances.viewPaymentCadre');
    }

    private function monthPerRealWeekDivision($month, $year) {
        $dataStart = date('Y-m-' . '01', strtotime($year . '-' . $month)); //first day of given month
        $loopDate = $dataStart;
        $days_in_month = date('t', strtotime($year . '-' . $month));
        $firstWeekNumber = date('W', strtotime($year . '-' . $month . '-01')); //first week number of given month
        $unoWeekNumber = date('W', strtotime($year . '-' . $month . '-01')); //first week number of given month
        $monthNumber = date('m', strtotime($year . '-' . $month . '-01'));

        $loopDateMonthNumber = date('m', strtotime($loopDate));

        $weeks = [$firstWeekNumber]; //array of weeks number in this month

        for ($i = 1; $i <= $days_in_month; $i++) {
            $loop_day = ($i < 10) ? '0' . $i : $i;
            $loop_date = $year . '-' . $month . '-' . $loop_day;
            $loopDateWeekNumber = date('W', strtotime($loop_date));

            if($loopDateWeekNumber != $unoWeekNumber) {
                array_push($weeks, $loopDateWeekNumber);
                $unoWeekNumber = $loopDateWeekNumber;
            }
        }

        $weeksDivided = array();

        for($j = 0; $j < count($weeks); $j++) {
            $flag = 1;

            if($j == 0) {
            $from = date("Y-m-d", strtotime("{$year}-W{$weeks[$j]}-1")); //Returns the date of monday in week
            $to = date("Y-m-d", strtotime("{$year}-W{$weeks[$j]}-7"));   //Returns the date of sunday in week

                for($k = 1; $k <= 8; $k++) {
                    if($k == 1) {
                        $currentMonthNumber = date('m', strtotime($from));
                        if($monthNumber == $currentMonthNumber && $flag != 0) {
                            $firstDayNr = date("N", strtotime("{$year}-W{$weeks[$j]}-1"));
                            $lastDayNr = date("N", strtotime("{$year}-W{$weeks[$j]}-7"));
                            array_push($weeksDivided, ['weekNumber' => $weeks[$j], 'firstDay' => $from, 'lastDay' => $to, 'firstDayNr' => $firstDayNr, 'lastDayNr' => $lastDayNr]);
                            $flag = 0;
                        }
                    }
                    else {
                        $days = $k - 1;
                        $currentMonthNumber = date('m', strtotime($from . '+ ' . $days . ' days'));
                        if($monthNumber == $currentMonthNumber  && $flag != 0) {
                            $firstDayNr = date("N", strtotime($from . '+ ' . $days . ' days'));
                            $lastDayNr = date("N", strtotime("{$year}-W{$weeks[$j]}-7"));
                            array_push($weeksDivided, ['weekNumber' => $weeks[$j], 'firstDay' => date('Y-m-d', strtotime($from . '+ ' . $days . ' days')), 'lastDay' => $to, 'firstDayNr' => $firstDayNr, 'lastDayNr' => $lastDayNr]);
                            $flag = 0;
                        }
                    }
                }
            }
            else if($j == count($weeks) - 1) {
                $from = date("Y-m-d", strtotime("{$year}-W{$weeks[$j]}-1")); //Returns the date of monday in week
                $to = date("Y-m-d", strtotime("{$year}-W{$weeks[$j]}-7"));   //Returns the date of sunday in week
                if(date('m', strtotime($to)) != $monthNumber) { //last week day is not in given month
                    for($k = 1; $k <= 7; $k++) {
                        $days = $k;
                        $currentMonthNumber = date('m', strtotime($from . '+ ' . $days . ' days'));
                        if($monthNumber != $currentMonthNumber  && $flag != 0) {
                            $corrDays = $days - 1;
                            $firstDayNr = date("N", strtotime("{$year}-W{$weeks[$j]}-1"));
                            $lastDayNr = date("N", strtotime($from . '+ ' . $corrDays . ' days'));
                            array_push($weeksDivided, ['weekNumber' => $weeks[$j], 'firstDay' => $from, 'lastDay' => date('Y-m-d', strtotime($from . '+ ' . $corrDays . ' days')), 'firstDayNr' => $firstDayNr, 'lastDayNr' => $lastDayNr]);
                            $flag = 0;
                        }
                    }
                }
                else { //last week day contain in given month
                    $from = date("Y-m-d", strtotime("{$year}-W{$weeks[$j]}-1")); //Returns the date of monday in week
                    $to = date("Y-m-d", strtotime("{$year}-W{$weeks[$j]}-7"));   //Returns the date of sunday in week
                    $firstDayNr = date("N", strtotime("{$year}-W{$weeks[$j]}-1"));
                    $lastDayNr = date("N", strtotime("{$year}-W{$weeks[$j]}-7"));
                    array_push($weeksDivided, ['weekNumber' => $weeks[$j], 'firstDay' => $from, 'lastDay' => $to, 'firstDayNr' => $firstDayNr, 'lastDayNr' => $lastDayNr]);
                }
            }
            else {
                $from = date("Y-m-d", strtotime("{$year}-W{$weeks[$j]}-1")); //Returns the date of monday in week
                $to = date("Y-m-d", strtotime("{$year}-W{$weeks[$j]}-7"));   //Returns the date of sunday in week
                $firstDayNr = date("N", strtotime("{$year}-W{$weeks[$j]}-1"));
                $lastDayNr = date("N", strtotime("{$year}-W{$weeks[$j]}-7"));
                array_push($weeksDivided, ['weekNumber' => $weeks[$j], 'firstDay' => $from, 'lastDay' => $to, 'firstDayNr' => $firstDayNr, 'lastDayNr' => $lastDayNr]);
            }
        }
//        dd($weeksDivided);
        return $weeksDivided;
    }

    /**
     * @param $sum == 0 indices that we want raw data, $sum == 1 indices that we want agreggate data
     * @return data about camapigns
     */
    public function getCampaignData($date_start, $date_stop, $sum) {
        if($sum == 0) { //raw data
            $campaign_data = DB::table('report_campaign')->select(DB::raw('
            SUM(all_campaigns) as all_campaigns,
            SUM(active_campaigns) as active_campaigns,
            SUBSTRING_INDEX(name,"_",1) as split_name
            '))
                ->whereBetween('date', [$date_start, $date_stop])
                ->where('all_campaigns', '>', 0)
                ->groupBy('split_name')
                ->get();
        }
        else { //agreggate data
            $campaign_data = ReportCampaign::select(DB::raw('
            SUM(all_campaigns) AS sum_campaign,
            SUM(active_campaigns) AS sum_active,
            SUM(received_campaigns) AS sum_received,
            SUM(unreceived_campaigns) AS sum_unreceived
            '))
                ->whereBetween('date', [$date_start, $date_stop])
                ->get();
        }
        return $campaign_data->sortByDesc('all_campaigns');
    }

    /**
     * Pobranie danych dla zbiorczego raportu (sortowanie po dyrektorach)
     */
    private function getMultiDepartmentData($date_start, $date_stop, $month, $year, $deps, $days_in_month) {
        /**
         * Pobranie ostatnich ID z dnia
         */
        $reportIds = DB::table('hour_report')
            ->select(DB::raw('
                MAX(id) as id
            '))
            ->whereBetween('hour_report.report_date', [$date_start, $date_stop])
            ->groupBy('report_date')
            ->groupBy('department_info_id')
            ->whereIn('department_info_id', $deps)
            ->get();
        /**
         * Pobranie danych do raportu
         */
        $hourReports = DB::table('hour_report')
            ->select(DB::raw('
                hour_report.*
            '))
            ->whereBetween('hour_report.report_date', [$date_start, $date_stop])
            ->whereIn('hour_report.id', $reportIds->pluck('id')->toArray())
            ->get();
        /**
         * Pobranie danych z przepracowanych godzin
         */
        $acceptHours = DB::table('work_hours')
            ->select(DB::raw('
                SUM(TIME_TO_SEC(accept_stop) - TIME_TO_SEC(accept_start)) as time_sum,
                date
            '))
            ->join('users', 'users.id', 'work_hours.id_user')
            ->whereBetween('date', [$date_start, $date_stop])
            ->whereIn('users.department_info_id', $deps)
            ->whereIn('users.user_type_id', [1,2])
            ->groupBy('date')
            ->get();
        /**
         * Pobranie danych z przepracowanych godzin
         */
        $acceptHours_2 = DB::table('work_hours')
            ->select(DB::raw('
                SUM(TIME_TO_SEC(accept_stop) - TIME_TO_SEC(accept_start)) as time_sum,
                date,users.department_info_id
                
            '))
            ->join('users', 'users.id', 'work_hours.id_user')
            ->whereBetween('date', [$date_start, $date_stop])
            ->whereIn('users.department_info_id', $deps)
            ->whereIn('users.user_type_id', [1,2])
            ->groupBy('date','users.department_info_id')
            ->get();
        /**
         * Pobranie danych dotyczących janków
         */
        $jankyIds = DB::table('pbx_dkj_team')
            ->select(DB::raw('
                MAX(id) as id
            '))
            ->whereBetween('report_date', [$date_start, $date_stop])
            ->groupBy('report_date')
            ->groupBy('department_info_id')
            ->whereIn('department_info_id', $deps)
            ->get();
        $yanky = DB::table('pbx_dkj_team')
            ->select(DB::raw('
                *
            '))
            ->whereBetween('report_date', [$date_start, $date_stop])
            ->whereIn('id', $jankyIds->pluck('id')->toArray())
            ->get();
        $newYanky = [];
        for ($i = 1; $i <= $days_in_month; $i++) {
            $day = ($i < 10) ? '0' . $i : $i ;
            $loop_date = $year . '-' . $month . '-' . $day;
            if ($yanky->where('report_date', '=', $loop_date)->count() > 0) {
                $tempYanek = new \stdClass();
                $tempYanek->report_date = $loop_date;
                $tempYanek->consultant_without_check = 0;
                $tempYanek->online_consultant = 0;
                $tempYanek->success = 0;
                $tempYanek->count_all_check = 0;
                $tempYanek->count_good_check = 0;
                $tempYanek->count_bad_check = 0;
                $tempYanek->all_jaky_disagreement = 0;
                $tempYanek->good_jaky_disagreement = 0;
                foreach($yanky->where('report_date', '=', $loop_date) as $item) {
                    $tempYanek->consultant_without_check += $item->consultant_without_check;
                    $tempYanek->online_consultant += $item->online_consultant;
                    $tempYanek->success += $item->success;
                    $tempYanek->count_all_check += $item->count_all_check;
                    $tempYanek->count_good_check += $item->count_good_check;
                    $tempYanek->count_bad_check += $item->count_bad_check;
                    $tempYanek->all_jaky_disagreement += $item->all_jaky_disagreement;
                    $tempYanek->good_jaky_disagreement += $item->good_jaky_disagreement;
                }
                $newYanky[] = $tempYanek;
            }
        }
        $newYanky = collect($newYanky);
        /**
         * Pobranie danych z grafiku
         */
        //Pobranie tygodni których dotyczy dany miesiąc
        $schedule_weeks = [];
        for ($i = 1; $i <= intval($days_in_month); $i = $i + 7) {
            $cur_day = ($i < 10) ? '0' . $i : $i;
            $schedule_weeks[] = intval(date('W',strtotime($year . '-'. $month . '-' . $cur_day)));
        }
        $schedule_data_raw = [];
        foreach($schedule_weeks as $week) {
            $schedule_data_raw[] = DB::table('schedule')
                ->select(DB::raw('
                    SUM(TIME_TO_SEC(monday_stop) - TIME_TO_SEC(monday_start)) / 3600 as day1,
                    SUM(TIME_TO_SEC(tuesday_stop) - TIME_TO_SEC(tuesday_start)) / 3600 as day2,
                    SUM(TIME_TO_SEC(wednesday_stop) - TIME_TO_SEC(wednesday_start)) / 3600 as day3,
                    SUM(TIME_TO_SEC(thursday_stop) - TIME_TO_SEC(thursday_start)) / 3600 as day4,
                    SUM(TIME_TO_SEC(friday_stop) - TIME_TO_SEC(friday_start)) / 3600 as day5,
                    SUM(TIME_TO_SEC(saturday_stop) - TIME_TO_SEC(saturday_start)) / 3600 as day6,
                    SUM(TIME_TO_SEC(sunday_stop) - TIME_TO_SEC(sunday_start)) / 3600 as day7,
                    week_num
                '))
                ->join('users', 'users.id', 'schedule.id_user')
                ->whereIn('users.department_info_id', $deps)
                ->whereIn('users.user_type_id', [1,2])
                ->where('week_num', $week)
                ->get();
        }
        $schedule_data_raw = collect($schedule_data_raw);
        $schedule_data = $schedule_data_raw->map(function($item) {
            return $item->first();
        });
        $reps = [];
        for ($i = 1; $i <= $days_in_month; $i++) {
            $day = ($i < 10) ? '0' . $i : $i ;
            $loop_date = $year . '-' . $month . '-' . $day;
            if ($hourReports->where('report_date', '=', $loop_date)->count() > 0) {
                $reports = $hourReports->where('report_date', '=', $loop_date);
                $tempReport = new \stdClass();
                $tempReport->report_date = $loop_date;
                $tempReport->average = 0;
                $tempReport->success = 0;
                $tempReport->janky_count = 0;
                $tempReport->wear_base = 0;
                $tempReport->call_time = 0;
                $tempReport->hour_time_use = 0;
                $tempReport->total_time = 0;
                foreach ($reports as $item) {
                    $tempReport->success += $item->success;
                    $rbh_departments = $acceptHours_2->where('date', '=', $item->report_date);
                    $total_hour_time_use = 0;
                    foreach ($rbh_departments as $rbh_department)
                    {
                        $sigle_hour_report = $reports->where('department_info_id','=',$rbh_department->department_info_id);
                        if(!$sigle_hour_report->isEmpty()){
                            $total_hour_time_use += round(($sigle_hour_report->first()->call_time * ($rbh_department->time_sum/3600)) / 100, 2);
                        }
                    }
                    $tempReport->hour_time_use += $total_hour_time_use;//floatval($item->hour_time_use);
                    $tempReport->total_time += floatval($item->hour_time_use);//($item->call_time > 0) ? ((100 * $item->hour_time_use) / $item->call_time) : 0 ;
                }
                $tempReport->average = ($tempReport->hour_time_use > 0) ? round($tempReport->success / $tempReport->hour_time_use, 2) : 0 ;
                $tempReport->hour_time_use = $total_hour_time_use;
                $reps[] = $tempReport;
            }
        }
        $hourReports = collect($reps);
        /**
         * Przypisanie danych do jednego obiektu
         */
        $newHourReports = $hourReports->map(function($item) use ($newYanky, $acceptHours) {
            //Pobranie danych z jankami
            $toAdd = $newYanky->where('report_date', '=', $item->report_date)->first();
            $item->count_all_check = ($toAdd != null) ? $toAdd->count_all_check : 0;
            $item->count_bad_check = ($toAdd != null) ? $toAdd->count_bad_check : 0;
            //pobranie danych z przepracowanymi godzinami
            $toAddHours = $acceptHours->where('date', '=', $item->report_date)->first();
            $item->time_sum_real_RBH = ($toAddHours != null) ? $toAddHours->time_sum : 0;
            return $item;
        });
        /**
         *Tutaj raport w widoku bierze pierwszy wpis z daną datą, trzeba  posumować dane ze wszystkich oddziałów pogrupowane po datach
         */
        /**
         * Pobranie danych departamentu
         */
        $dep_info = Department_info::whereIn('id', $deps)->get();
        /**
         * Tabela z miesiącami
         */
        $months = [
            '01' => 'Styczeń',
            '02' => 'Luty',
            '03' => 'Marzec',
            '04' => 'Kwiecień',
            '05' => 'Maj',
            '06' => 'Czerwiec',
            '07' => 'Lipiec',
            '08' => 'Sierpień',
            '09' => 'Wrzesień',
            '10' => 'Październik',
            '11' => 'Listopad',
            '12' => 'Grudzień'
        ];
        $data = [
            'date_start' => $date_start,
            'date_stop' => $date_stop,
            'month' => $month,
            'year' => $year,
            'hour_reports' => $newHourReports,
            'dep_info' => $dep_info,
            'schedule_data' => $schedule_data,
            'months' => $months
        ];
        return $data;
    }

    private function getFreeDays($dividedMonth) {

//        dd($dividedMonth);
        $fieldArr = [
            '1' => 'mondayPaid',
            '2' => 'tuesdayPaid',
            '3' => 'wednesdayPaid',
            '4' => 'thursdayPaid',
            '5' => 'fridayPaid',
            '6' => 'saturdayPaid',
            '7' => 'sundayPaid'
                ];
        $safeWeeksArr = []; //safe weeks arr
        $unsafeWeeksArr = [];
        for($i = 0; $i < count($dividedMonth); $i++) {
            if($i != 0 && $i != count($dividedMonth) - 1) {
                array_push($safeWeeksArr, $dividedMonth[$i]['weekNumber']);
            }
            else {
                array_push($unsafeWeeksArr, $dividedMonth[$i]['weekNumber']);
            }
        }

        $freeDaysSafe = Schedule::select(DB::raw('
            id_user,
            SUM(CASE WHEN mondayPaid = 0 THEN 1 ELSE 0 END) + SUM(CASE WHEN tuesdayPaid = 0 THEN 1 ELSE 0 END) + SUM(CASE WHEN wednesdayPaid = 0 THEN 1 ELSE 0 END) + SUM(CASE WHEN thursdayPaid = 0 THEN 1 ELSE 0 END) + SUM(CASE WHEN fridayPaid = 0 THEN 1 ELSE 0 END) + SUM(CASE WHEN saturdayPaid = 0 THEN 1 ELSE 0 END) + SUM(CASE WHEN sundayPaid = 0 THEN 1 ELSE 0 END)
            as notPaidDays
        '))
            ->groupBy('id_user')
            ->whereIn('week_num', $safeWeeksArr)
            ->get();

        $unsafeWeekString1 = '';
        $unsafeWeekString2 = '';
//        dd($unsafeWeeksArr);
        for($i = $dividedMonth[0]['firstDayNr']; $i < 8; $i++) {
            if($i != 7) {
                $unsafeWeekString1 .= 'SUM(CASE WHEN ' . $fieldArr[$i] . ' = 0 THEN 1 ELSE 0 END) + ';
            }
            else {
                $unsafeWeekString1 .= 'SUM(CASE WHEN ' . $fieldArr[$i] . ' = 0 THEN 1 ELSE 0 END)';
            }
        }

        $max = $dividedMonth[count($dividedMonth) - 1]['lastDayNr'];
        for($i = 1; $i <= $max; $i++) {
            if($i != $max) {
                $unsafeWeekString2 .= 'SUM(CASE WHEN ' . $fieldArr[$i] . ' = 0 THEN 1 ELSE 0 END) + ';
            }
            else {
                $unsafeWeekString2 .= 'SUM(CASE WHEN ' . $fieldArr[$i] . ' = 0 THEN 1 ELSE 0 END)';
            }
        }

        $freeDaysUnsafe1 = Schedule::select(DB::raw('
            id_user,
            ' . $unsafeWeekString1 . ' as notPaidDays
        '))
            ->groupBy('id_user')
            ->where('week_num', '=', $unsafeWeeksArr[0])
            ->get();

        $freeDaysUnsafe2 = Schedule::select(DB::raw('
            id_user,
            ' . $unsafeWeekString2 . ' as notPaidDays
        '))
            ->groupBy('id_user')
            ->where('week_num', '=', $unsafeWeeksArr[1])
            ->get();


        $finalArr = [];
        $idArr = array_merge($freeDaysSafe->pluck('id_user')->toArray(), $freeDaysUnsafe1->pluck('id_user')->toArray());
        $fullIdArr = array_merge($idArr, $freeDaysUnsafe2->pluck('id_user')->toArray());
        $fullIdUniqueArray = array_unique($fullIdArr); //array of all user ids

        foreach($fullIdUniqueArray as $userId) {

            $element1 = $freeDaysSafe->where('id_user', '=', $userId)->count() != 0 ? $freeDaysSafe->where('id_user', '=', $userId)->first()->notPaidDays : 0;
            $element2 = $freeDaysUnsafe1->where('id_user', '=', $userId)->count() != 0 ? $freeDaysUnsafe1->where('id_user', '=', $userId)->first()->notPaidDays : 0;
            $element3 = $freeDaysUnsafe2->where('id_user', '=', $userId)->count() != 0 ? $freeDaysUnsafe2->where('id_user', '=', $userId)->first()->notPaidDays : 0;
            array_push($finalArr, ['id_user' => $userId, 'freeDays' => $element1 + $element2 + $element3]);
        }

        return $finalArr;

    }

    private function provisionSystemForTrainers(&$user, $dividedMonth,&$arrayOfDepartmentStatistics = null) {
        $weekNumber = 0;
        if($user->department_type_id == 1){             //trener potwierdzeń
            $clientRouteInfo = ClientRouteInfo::select(
                DB::raw('concat(users.first_name," ",users.last_name) as confirmingUserName'),
                DB::raw('concat(trainer.first_name," ",trainer.last_name) as confirmingUserTrainerName'),
                'confirmingUser',
                'confirmDate',
                'frequency',
                'pairs',
                'actual_success',
                'users.department_info_id',
                'users.coach_id',
                'users.login_phone'
            )
                ->join('users','confirmingUser', '=', 'users.id')
                ->join('department_info as di', 'users.department_info_id','=','di.id')
                ->join('users as trainer','users.coach_id','=','trainer.id')
                ->where('confirmDate', '>=', $dividedMonth[0]->firstDay)
                ->where('confirmDate', '<=', $dividedMonth[count($dividedMonth)-1]->lastDay)
                ->where('users.department_info_id', $user->department_info_id)
                ->where('di.id_dep_type',1)
                ->whereNotNull('confirmingUser')
                ->whereNotNull('users.coach_id')
                ->where('users.coach_id', $user->id)->get(); //client route info poszczególnych konsultantów wybranego trenera w miesiacu
            $confirmationStatistics = ConfirmationStatistics::getConsultantsConfirmationStatisticsForMonth($clientRouteInfo, $dividedMonth, 'coach_id');

            foreach ($confirmationStatistics['sums'] as $confirmationStatisticsWeek){
                $bonusFromSuccessfulPct = ProvisionLevels::get('trainer', $confirmationStatisticsWeek->successfulPct,2);
                $bonusRecordFromSuccessfulPct = null;
                if($bonusFromSuccessfulPct > 0){
                    $bonusRecordFromSuccessfulPct = (object)[
                        'type'      => 2,
                        'id_user'   => $user->id,
                        'amount'   => $bonusFromSuccessfulPct,
                        'comment'   => "Premia tygodniowa (".$dividedMonth[$weekNumber]->firstDay." -- ".$dividedMonth[0]->lastDay.") za osiągnięcie:  ".$confirmationStatisticsWeek->successfulPct."% pokazów zielonych.",
                        'id_manager'=> null,
                        'status'    => 1,
                        'event_date'=> $dividedMonth[$weekNumber]->lastDay,
                        'manager'   => 'System'
                    ];
                    $user->bonuses->push($bonusRecordFromSuccessfulPct);
                }
                $bonusFromUnsuccessfulBadlyPct = ProvisionLevels::get('trainer', $confirmationStatisticsWeek->unsuccessfulBadlyPct,1);
                $bonusRecordFromUnsuccessfulBadlyPct = null;
                if($bonusFromUnsuccessfulBadlyPct > 0){
                    $bonusRecordFromUnsuccessfulBadlyPct = (object)[
                        'type'      => 2,
                        'id_user'   => $user->id,
                        'amount'    => $bonusFromUnsuccessfulBadlyPct,
                        'comment'   => "Premia tygodniowa (".$dividedMonth[$weekNumber]->firstDay." -- ".$dividedMonth[0]->lastDay.") za osiągnięcie:  ".$confirmationStatisticsWeek->unsuccessfulBadlyPct."% czerwonych pokazów.",
                        'id_manager'=> null,
                        'status'    => 1,
                        'event_date'=> $dividedMonth[$weekNumber]->lastDay,
                        'manager'   => 'System'
                    ];
                    $user->bonuses->push($bonusRecordFromUnsuccessfulBadlyPct);
                }
                $user->bonus += $bonusFromSuccessfulPct + $bonusFromUnsuccessfulBadlyPct;

                if($this->getToSave() == 1) {
                    if ($bonusFromSuccessfulPct > 0) {
                        $this->saveBonus($user->id, $bonusRecordFromSuccessfulPct->amount, $bonusRecordFromSuccessfulPct->event_date, $bonusRecordFromSuccessfulPct->comment);
                    }
                    if ($bonusFromUnsuccessfulBadlyPct > 0) {
                        $this->saveBonus($user->id, $bonusRecordFromUnsuccessfulBadlyPct->amount, $bonusRecordFromUnsuccessfulBadlyPct->event_date, $bonusRecordFromUnsuccessfulBadlyPct->comment);
                    }
                }
                $weekNumber++;
            }
        }else if($user->department_type_id == 2){       //trener telemarketing
            foreach ($arrayOfDepartmentStatistics as $item){
                $commissionAvg = Department_info::find($user->department_info_id)->commission_avg;
                $total_week_avg_proc = round((100*$item->total_week_avg)/$commissionAvg,2);
                $bonusFromAvg = ProvisionLevels::get('trainer', $item->janky_proc,3,$total_week_avg_proc, 'avg'); // Średnia
                $bonusRecordFromAvg = null;
                if($bonusFromAvg > 0){
                    $bonusRecordFromAvg = (object)[
                        'type'      => 2,
                        'id_user'   => $user->id,
                        'amount'    => $bonusRecordFromAvg,
                        'comment'   => "Premia tygodniowa (".$dividedMonth[$weekNumber]->firstDay." -- ".$dividedMonth[$weekNumber]->lastDay.") za osiągnięcie:  Średniej na projekcie",
                        'id_manager'=> null,
                        'status'    => 1,
                        'event_date'=> $dividedMonth[$weekNumber]->lastDay,
                        'manager'   => 'System'
                    ];
                    $user->bonuses->push($bonusRecordFromAvg);
                }

                $bonusFromAgreementTarget = ProvisionLevels::get('trainer', $item->janky_proc,3,$item->total_week_goal_proc, 'ammount'); // Cel zgód
                $bonusRecordFromAgreementTarget = null;
                if($bonusFromAgreementTarget > 0){
                    $bonusRecordFromAgreementTarget = (object)[
                        'type'      => 2,
                        'id_user'   => $user->id,
                        'amount'    => $bonusFromAgreementTarget,
                        'comment'   => "Premia tygodniowa (".$dividedMonth[$weekNumber]->firstDay." -- ".$dividedMonth[$weekNumber]->lastDay.") za osiągnięcie: Celu na projekcie",
                        'id_manager'=> null,
                        'status'    => 1,
                        'event_date'=> $dividedMonth[$weekNumber]->lastDay,
                        'manager'   => 'System'
                    ];
                    $user->bonuses->push($bonusRecordFromAgreementTarget);
                }
                $user->bonus += $bonusFromAvg + $bonusFromAgreementTarget;
                if($this->getToSave() == 1){
                    if ($bonusFromAvg > 0) {
                        $this->saveBonus($user->id, $bonusRecordFromAvg->amount, $bonusRecordFromAvg->event_date, $bonusRecordFromAvg->comment);
                    }
                    if ($bonusFromAgreementTarget > 0) {
                        $this->saveBonus($user->id, $bonusRecordFromAgreementTarget->amount, $bonusRecordFromAgreementTarget->event_date, $bonusRecordFromAgreementTarget->comment);
                    }
                }
                $weekNumber++;
            }
        }
    }


    private function provisionSystemForManagers(&$user,$dividedMonth,$arrayOfDepartmentStatistics = null){
        $weekNumber = 0;
        $user->provision = 0;
        if($user->department_type_id == 2){       //trener telemarketing
            $allDepartments = Department_info::where(function ($querry) use ($user){
               $querry->orwhere('menager_id',$user->id)
                   ->orwhere('regionalManager_id',$user->id);
            })->get();
            $commissionAvg = 2.5;
            for($i=0;$i<4;$i++){
                $summaryScore[$i]['totalSuccess']       = 0;
                $summaryScore[$i]['weekGoalSuccess']    = 0;
                $summaryScore[$i]['weekRbh']            = 0;
                $summaryScore[$i]['weekGoalRbh']        = 0;
                $summaryScore[$i]['totalCheck']         = 0;
                $summaryScore[$i]['totalBad']           = 0;
            }
            foreach ($allDepartments as $allDepartment){
                    $commissionAvg = $allDepartment->commission_avg;
                    $departmentStatistics   = $arrayOfDepartmentStatistics[$allDepartment->id];
                    for ($i = 0; $i <count($departmentStatistics);$i++){
                        $summaryScore[$i]['totalSuccess']      += $departmentStatistics[$i]->total_week_success;
                        $summaryScore[$i]['weekGoalSuccess']   += $departmentStatistics[$i]->total_week_goal;
                        $summaryScore[$i]['weekRbh']           += $departmentStatistics[$i]->real_week_rbh;
                        $summaryScore[$i]['weekGoalRbh']       += $departmentStatistics[$i]->week_target_rbh;
                        $summaryScore[$i]['totalCheck']        += $departmentStatistics[$i]->total_week_check;
                        $summaryScore[$i]['totalBad']          += $departmentStatistics[$i]->total_week_bad;
                    }
            }
            foreach ($summaryScore as $item){
                    $total_week_avg_proc    = $item['weekRbh']          != 0 ? round($item['totalSuccess']/$item['weekRbh'],2)                          : 0;
                    $total_week_avg_proc    = $commissionAvg            != 0 ? round((100*$total_week_avg_proc)/$commissionAvg,2)                       : 0;
                    $total_week_goal_proc   = $item['weekGoalSuccess']  != 0 ? round((100*$item['totalSuccess'])/$item['weekGoalSuccess'],2)            : 0;
                    $total_week_rbh_proc    = $item['weekGoalRbh']      != 0 ? round((100*$item['weekRbh'])/$item['weekGoalRbh'],2)                     : 0;
                    $janky_proc             = ($item['totalCheck']      != null && $item['totalCheck'] > 0) ? round(($item['totalBad'] / $item['totalCheck']) * 100, 2)  : 0 ;
                    if($user->user_type_id == 17 ||  $user->user_type_id == 7){
                        $bonusFromAvg = ProvisionLevels::get('manager', $janky_proc,3,$total_week_avg_proc, 'avg',count($allDepartments)); // Średnia
                        $bonusRecordFromAvg = null;
                        if($bonusFromAvg > 0){
                            $bonusRecordFromAvg = (object)[
                                'type'      => 2,
                                'id_user'   => $user->id,
                                'amount'    => $bonusFromAvg,
                                'comment'   => "Premia tygodniowa (".$dividedMonth[$weekNumber]->firstDay." -- ".$dividedMonth[$weekNumber]->lastDay.") za osiągnięcie:  Średniej na projekcie",
                                'id_manager'=> null,
                                'status'    => 1,
                                'event_date'=> $dividedMonth[$weekNumber]->lastDay,
                                'manager'   => 'System'
                            ];
                            $user->bonuses->push($bonusRecordFromAvg);
                        }
                        $bonusFromAgreementTarget = ProvisionLevels::get('manager', $janky_proc,3,$total_week_goal_proc, 'ammount',count($allDepartments)); // Cel zgód
                        $bonusRecordFromAgreementTarget = null;
                        if($bonusFromAgreementTarget > 0){
                            $bonusRecordFromAgreementTarget = (object)[
                                'type'      => 2,
                                'id_user'   => $user->id,
                                'amount'    => $bonusFromAgreementTarget,
                                'comment'   => "Premia tygodniowa (".$dividedMonth[$weekNumber]->firstDay." -- ".$dividedMonth[$weekNumber]->lastDay.") za osiągnięcie: Celu na projekcie",
                                'id_manager'=> null,
                                'status'    => 1,
                                'event_date'=> $dividedMonth[$weekNumber]->lastDay,
                                'manager'   => 'System'
                            ];
                            $user->bonuses->push($bonusRecordFromAgreementTarget);
                        }
                        $user->bonus += $bonusFromAvg + $bonusFromAgreementTarget;
                        if($this->getToSave() == 1){
                            if ($bonusFromAvg > 0) {
                                $this->saveBonus($user->id, $bonusRecordFromAvg->amount, $bonusRecordFromAvg->event_date, $bonusRecordFromAvg->comment);
                            }
                            if ($bonusFromAgreementTarget > 0) {
                                $this->saveBonus($user->id, $bonusRecordFromAgreementTarget->amount, $bonusRecordFromAgreementTarget->event_date, $bonusRecordFromAgreementTarget->comment);
                            }
                        }
                    }else if($user->user_type_id == 14){
                        $bonusFromAvg = ProvisionLevels::get('managerHR', $janky_proc,3,$total_week_rbh_proc, 'rbh'); // Średnia
                        $bonusRecordFromAvg = null;
                        if($bonusFromAvg > 0){
                            $bonusRecordFromAvg = (object)[
                                'type'      => 2,
                                'id_user'   => $user->id,
                                'amount'    => $bonusFromAvg,
                                'comment'   => "Premia tygodniowa (".$dividedMonth[$weekNumber]->firstDay." -- ".$dividedMonth[$weekNumber]->lastDay.") za osiągnięcie:  RBH na projekcie",
                                'id_manager'=> null,
                                'status'    => 1,
                                'event_date'=> $dividedMonth[$weekNumber]->lastDay,
                                'manager'   => 'System'
                            ];
                            $user->bonuses->push($bonusRecordFromAvg);
                        }
                        $bonusFromAgreementTarget = ProvisionLevels::get('managerHR', $janky_proc,3,$total_week_goal_proc, 'ammount'); // Cel zgód
                        $bonusRecordFromAgreementTarget = null;
                        if($bonusFromAgreementTarget > 0){
                            $bonusRecordFromAgreementTarget = (object)[
                                'type'      => 2,
                                'id_user'   => $user->id,
                                'amount'    => $bonusFromAgreementTarget,
                                'comment'   => "Premia tygodniowa (".$dividedMonth[$weekNumber]->firstDay." -- ".$dividedMonth[$weekNumber]->lastDay.") za osiągnięcie: Celu na projekcie",
                                'id_manager'=> null,
                                'status'    => 1,
                                'event_date'=> $dividedMonth[$weekNumber]->lastDay,
                                'manager'   => 'System'
                            ];
                            $user->bonuses->push($bonusRecordFromAgreementTarget);
                        }
                        $user->bonus += $bonusFromAvg + $bonusFromAgreementTarget;
                        if($this->getToSave() == 1){
                            if ($bonusFromAvg > 0) {
                                $this->saveBonus($user->id, $bonusRecordFromAvg->amount, $bonusRecordFromAvg->event_date, $bonusRecordFromAvg->comment);
                            }
                            if ($bonusFromAgreementTarget > 0) {
                                $this->saveBonus($user->id, $bonusRecordFromAgreementTarget->amount, $bonusRecordFromAgreementTarget->event_date, $bonusRecordFromAgreementTarget->comment);
                            }
                        }
                    }
                $weekNumber++;
            }
        }
    }

    private function saveBonus($userID,$amount,$event_date,$comment){
        if($amount > 0 ){
            $penaltyBonusObj                = new PenaltyBonus();
            $penaltyBonusObj->type          = 2;    //premia
            $penaltyBonusObj->id_user       = $userID;
            $penaltyBonusObj->amount        = $amount;
            $penaltyBonusObj->comment       = $comment;
            $penaltyBonusObj->id_manager    = Auth::user()->id;
            $penaltyBonusObj->event_date    = $event_date;
            $penaltyBonusObj->accepted_payment_id    = $this->acceptedPaymentForDepartment->id;
            try{
                $penaltyBonusObj->save();
            }catch (\Exception $exception){
                return 0;
            }
        }
    }

    private function provisionSystemForInstructors(&$user, $dividedMonth, $dep_info,$deps2, $arrayOfDepartmentStatistics = null){
        $weekNumber = 0;
        $user->provision = 0;
        
            if($user->department_type_id == 1){             //szkoleniowiec potwierdzeń

                $clientRouteInfo = ClientRouteInfo::select(
                    DB::raw('concat(users.first_name," ",users.last_name) as confirmingUserName'),
                    DB::raw('concat(trainer.first_name," ",trainer.last_name) as confirmingUserTrainerName'),
                    'confirmingUser',
                    'confirmDate',
                    'frequency',
                    'pairs',
                    'actual_success',
                    'users.department_info_id',
                    'users.coach_id',
                    'users.login_phone'
                )
                    ->join('users','confirmingUser', '=', 'users.id')
                    ->join('department_info as di', 'users.department_info_id','=','di.id')
                    ->join('users as trainer','users.coach_id','=','trainer.id')
                    ->where('confirmDate', '>=', $dividedMonth[0]->firstDay)
                    ->where('confirmDate', '<=', $dividedMonth[count($dividedMonth)-1]->lastDay)
                    ->where('users.department_info_id', $dep_info)
                    ->where('di.id_dep_type',1)
                    ->whereNotNull('confirmingUser')
                    ->whereNotNull('users.coach_id')->get(); //client route info poszczególnych konsultantów w calym oddziale w miesiacu
                $confirmationStatistics = ConfirmationStatistics::getConsultantsConfirmationStatisticsForMonth($clientRouteInfo, $dividedMonth);
                foreach ($confirmationStatistics['sums'] as $confirmationStatisticsWeek){
                    $bonusFromSuccessfulPct = ProvisionLevels::get('instructor', $confirmationStatisticsWeek->successfulPct,2);
                    $bonusRecordFromSuccessfulPct = null;
                    if($bonusFromSuccessfulPct > 0){
                        $bonusRecordFromSuccessfulPct = (object)[
                            'type'      => 2,
                            'id_user'   => $user->id,
                            'amount'    => $bonusFromSuccessfulPct,
                            'comment'   => "Premia tygodniowa (".$dividedMonth[$weekNumber]->firstDay." -- ".$dividedMonth[0]->lastDay.") za osiągnięcie:  ".$confirmationStatisticsWeek->successfulPct."% pokazów zielonych.",
                            'id_manager'=> null,
                            'status'    => 1,
                            'event_date'=> $dividedMonth[$weekNumber]->lastDay,
                            'manager'   => 'System'
                        ];
                        $user->bonuses->push($bonusRecordFromSuccessfulPct);
                    }
                    $bonusFromUnsuccessfulBadlyPct = ProvisionLevels::get('instructor', $confirmationStatisticsWeek->unsuccessfulBadlyPct,1);
                    $bonusRecordFromUnsuccessfulBadlyPct = null;
                    if($bonusFromUnsuccessfulBadlyPct > 0){
                        $bonusRecordFromUnsuccessfulBadlyPct = (object)[
                            'type'      => 2,
                            'id_user'   => $user->id,
                            'amount'    => $bonusFromUnsuccessfulBadlyPct,
                            'comment'   => "Premia tygodniowa (".$dividedMonth[$weekNumber]->firstDay." -- ".$dividedMonth[0]->lastDay.") za osiągnięcie:  ".$confirmationStatisticsWeek->unsuccessfulBadlyPct."% czerwonych pokazów.",
                            'id_manager'=> null,
                            'status'    => 1,
                            'event_date'=> $dividedMonth[$weekNumber]->lastDay,
                            'manager'   => 'System'
                        ];
                        $user->bonuses->push($bonusRecordFromUnsuccessfulBadlyPct);
                    }
                    $user->bonus += $bonusFromSuccessfulPct + $bonusFromUnsuccessfulBadlyPct;
                    if($this->getToSave() == 1){
                        if ($bonusFromSuccessfulPct > 0) {
                            $this->saveBonus($user->id, $bonusRecordFromSuccessfulPct->amount, $bonusRecordFromSuccessfulPct->event_date, $bonusRecordFromSuccessfulPct->comment);
                        }
                        if ($bonusFromUnsuccessfulBadlyPct > 0) {
                            $this->saveBonus($user->id, $bonusRecordFromUnsuccessfulBadlyPct->amount, $bonusRecordFromUnsuccessfulBadlyPct->event_date, $bonusRecordFromUnsuccessfulBadlyPct->comment);
                        }
                    }
                    $weekNumber++;
                }
            }
            else if($user->department_type_id == 2){       //szkoleniowiec telemarketing
                $start = microtime(true);
                $firstStatisticArr = []; //array of recruited to stage 1 statistics
                $secondStatisticsArr = []; //array of averages

                $reportTrainingData = RecruitmentStory::getReportTrainingDataShorter($dividedMonth);
                $candidates = RecruitmentStory::getCandidatesTrainedStageOne($dividedMonth[0]->firstDay, $dividedMonth[count($dividedMonth)-1]->lastDay, $dividedMonth);
                $reportTrainingDataAndHire = RecruitmentStory::getReportTrainingDataAndHireShorter($candidates);

                foreach($dividedMonth as $companyWeek) {
                    $weekNumber++;
                    $weekDataReportTrainingDataAndHire = $reportTrainingDataAndHire->where('week', $weekNumber);
                    $departmentReportTrainingData = $reportTrainingData->where('week', $weekNumber)->where('dep_id', $dep_info)->first();
                    if($departmentReportTrainingData != null){
                        $hiredToCandidatesInStageOne = intval($departmentReportTrainingData->sum_choise) != 0 ? round(100 * $weekDataReportTrainingDataAndHire->where('department_info_id', $dep_info)->count() / intval($departmentReportTrainingData->sum_choise), 2) : 0;
                        array_push($firstStatisticArr, $hiredToCandidatesInStageOne);
                    }else{
                        array_push($firstStatisticArr, 0);
                    }

                    $date_start = $companyWeek->firstDay;
                    $date_stop = $companyWeek->lastDay;
                    $newUsersRbhData = DataNewUsersRbhReport::get($date_start, $date_stop, 1);

                    $sumConsultants = 0; // number of consultants = denumerator for average
                    if(isset($newUsersRbhData[$dep_info])) {
                        $sumConsultants = count($newUsersRbhData[$dep_info]);
                    }

                    $sum_success = 0; // number of successes = numerator for average
                    if(isset($newUsersRbhData[$dep_info])) {
                        foreach($newUsersRbhData[$dep_info] as $rbhInfo) {
                            $sum_success += $rbhInfo->success;
                        }
                    }
                    $avg = $sumConsultants > 0 ? round($sum_success / $sumConsultants, 2) : 0; //new consultants avg

                    array_push($secondStatisticsArr, $avg);
                }

                /** SLOW METHOD BELOW*/
                /*foreach($dividedMonth as $companyWeek) {
                    $date_start = $companyWeek->firstDay;
                    $date_stop = $companyWeek->lastDay;
                    $dataTrainingGroup = RecruitmentStory::getReportTrainingDataShort($date_start,$date_stop, $deps2);
                    $dateHireCandidate = RecruitmentStory::getReportTrainingDataAndHireShort($date_start,$date_stop);
                    $dataTrainingGroup = $this::mapTrainingGroupInfoAndHireCandidate($dataTrainingGroup,$dateHireCandidate);
                    $newUsersData = DataNewUsersRbhReport::get($date_start, $date_stop, 1);

                    foreach($dataTrainingGroup as $recruitmentInfo) { //we are filling firstStatistcArr with parameter: recruited to stage 1.
                        if($recruitmentInfo->dep_id == $dep_info) { //data from user's department
                            $recruitedToStage1 = $recruitmentInfo->sum_choise_stageOne > 0 ? round(100 * $recruitmentInfo->countHireUserFromFirstTrainingGroup / $recruitmentInfo->sum_choise_stageOne, 2) : 0;
                            array_push($firstStatisticArr, $recruitedToStage1);
                        }
                    }

                    $sumConsultants = 0; // number of consultants = denumerator for average

                    if(isset($newUsersData[$dep_info])) {
                        $sumConsultants = count($newUsersData[$dep_info]);
                    }

                    $sum_success = 0; // number of successes = numerator for average
                    if(isset($newUsersData[$dep_info])) {
                        foreach($newUsersData[$dep_info] as $rbhInfo) {
                            $sum_success += $rbhInfo->success;
                        }
                    }
                    $avg = $sumConsultants > 0 ? round($sum_success / $sumConsultants, 2) : 0; //new consultants avg

                    array_push($secondStatisticsArr, $avg);
                }*/

                $time_elapsed_secs = microtime(true) - $start;
                $weekNumber = 0;
                foreach ($arrayOfDepartmentStatistics as $item) {
                    $bonusAvgJankyPct = 0;
                    $bonusRecordAvgJankyPct = null;
                    if(isset($secondStatisticsArr[$weekNumber])) {
                        $bonusAvgJankyPct = ProvisionLevels::get('instructor', $item->janky_proc,3 ,$secondStatisticsArr[$weekNumber], 'avg');
                    }
                    if($bonusAvgJankyPct > 0){
                        $bonusRecordAvgJankyPct = (object)[
                            'type'      => 2,
                            'id_user'   => $user->id,
                            'amount'    => $bonusAvgJankyPct,
                            'comment'   => "Premia tygodniowa (".$dividedMonth[$weekNumber]->firstDay." -- ".$dividedMonth[$weekNumber]->lastDay.") za osiągnięcie:  Średniej na projekcie",
                            'id_manager'=> null,
                            'status'    => 1,
                            'event_date'=> $dividedMonth[$weekNumber]->lastDay,
                            'manager'   => 'System'
                        ];
                        $user->bonuses->push($bonusRecordAvgJankyPct);
                    }

                    $bonusEmploymentJankyPct = 0;
                    $bonusRecordEmploymentJankyPct = null;
                    if(isset($firstStatisticArr[$weekNumber])) {
                        $bonusEmploymentJankyPct = ProvisionLevels::get('instructor', $item->janky_proc,3 ,$firstStatisticArr[$weekNumber], 'employment');
                    }
                    if($bonusEmploymentJankyPct > 0){
                        $bonusRecordEmploymentJankyPct = (object)[
                            'type'      => 2,
                            'id_user'   => $user->id,
                            'amount'    => $bonusEmploymentJankyPct,
                            'comment'   => "Premia tygodniowa (".$dividedMonth[$weekNumber]->firstDay." -- ".$dividedMonth[$weekNumber]->lastDay.") za osiągnięcie: Celu na projekcie",
                            'id_manager'=> null,
                            'status'    => 1,
                            'event_date'=> $dividedMonth[$weekNumber]->lastDay,
                            'manager'   => 'System'
                        ];
                        $user->bonuses->push($bonusRecordEmploymentJankyPct);
                    }

                    $user->bonus += $bonusAvgJankyPct + $bonusEmploymentJankyPct;
                    if($this->getToSave() == 1) {
                        if(isset($secondStatisticsArr[$weekNumber])) {
                            if ($bonusAvgJankyPct > 0) {
                                $this->saveBonus($user->id, $bonusRecordAvgJankyPct->amount, $bonusRecordAvgJankyPct->event_date, $bonusRecordAvgJankyPct->comment);
                            }
                        }
                        if(isset($firstStatisticArr[$weekNumber])) {
                            if ($bonusEmploymentJankyPct > 0) {
                                $this->saveBonus($user->id, $bonusRecordEmploymentJankyPct->amount, $bonusRecordEmploymentJankyPct->event_date, $bonusRecordEmploymentJankyPct->comment);
                            }
                        }
                    }
                    $weekNumber++;
                }
            }

    }

    public function mapTrainingGroupInfoAndHireCandidate($trainingGroupCollect,$dateHireCandidate){
        return $trainingGroupCollect->map(function ($item) use ($dateHireCandidate){
            $dateHireCandidateDepartment = $dateHireCandidate->where('departmentInfoId',$item->dep_id);

            if(!$dateHireCandidateDepartment->isEmpty()){
                $item->countHireUserFromFirstTrainingGroup = $dateHireCandidateDepartment->count();
            }
            $item->procScore = $item->sum_choise_stageOne != 0 ? round(($item->countHireUserFromFirstTrainingGroup/$item->sum_choise_stageOne)*100,2) : 0;
            return $item;
        });
    }

    private function getDepartmentStatistics($weekDateArr, $month, $year, $departments) {
        $firstDayOfMonth = new DateTime(date('Y-m-d', strtotime($year . '-' . $month . '-01')));
        $lastDayOfMonth = new DateTime(date('Y-m-d', strtotime($year .'-'. $month . '-' . date('t', strtotime($year . '-' . $month . '-01')))));

        $days_in_month = date('t', strtotime(date('Y').'-'. $month));

        $today = date('Y-m-d'); //today
        $todayDateTime = new DateTime($today);
        $data = $this->getMultiDepartmentData($firstDayOfMonth->format('Y-m-d'), $lastDayOfMonth->format('Y-m-d'), $month, $year, $departments, $days_in_month);
        $rbhTargetArr = [];
        foreach($weekDateArr as $weekInfo) {
            $total_week_proc_janky = 0;
            $total_week_goal = 0;
            $total_week_success = 0;
            $real_week_RBH = 0;
            $week_target_RBH = 0;
            $total_week_checked = 0;
            $total_week_bad = 0;
            $hour_reports = $data['hour_reports'];
            $dep_info = $data['dep_info'];
            $firstDayOfMonthDateTime = new DateTime($weekInfo->firstDay);
            $lastDayOfWeekDateTime = new DateTime($weekInfo->lastDay);
            $dateDiff = $lastDayOfWeekDateTime->diff($firstDayOfMonthDateTime)->days;
            for($i = 0; $i <= intval($dateDiff); $i++) {
                $depAim = 0;
                $date = date('Y-m-d', strtotime($weekInfo->firstDay . ' + ' . $i .' days'));
                $report = $hour_reports->where('report_date', '=', $date)->where('success', '>', 0)->first();
                $add_default_zero = ($report != null) ? false : true ;
                if ($add_default_zero == false) {
                    $day_number = date('N', strtotime($report->report_date));
                    $real_RBH = round(($report->time_sum_real_RBH / 3600) ,2);
                    if(date('w', strtotime($date)) == 6) { //saturday
                        $depAim = $dep_info[0]['dep_aim_week'];
                    }
                    else { //other than saturday
                        $depAim = $dep_info[0]['dep_aim'];
                    }
                    $goal = ($day_number < 6) ? $dep_info[0]['dep_aim'] : $dep_info[0]['dep_aim_week'];
                    $total_week_goal += $goal;
                    $total_week_success += $report->success;

                    $commisionAvg = $dep_info[0]['commission_avg'];
                    $targetRBH =$commisionAvg != 0 ? round($depAim / $commisionAvg, 2) : 0; //cel rbh
                    $week_target_RBH += $targetRBH;
                    $real_week_RBH += $real_RBH;
                    $total_week_checked += $report->count_all_check;
                    $total_week_bad += $report->count_bad_check;
                }
            }
            $total_week_proc_janky = ($total_week_checked != null && $total_week_checked > 0) ? round(($total_week_bad / $total_week_checked) * 100, 2) : 0 ;
            $total_week_goal_proc = ($total_week_goal != null && $total_week_goal > 0) ? round(($total_week_success / $total_week_goal) * 100, 2) : 0 ;
            $tatal_week_avg = ($total_week_success <=0 || $real_week_RBH <=0)  ? 0: round($total_week_success/$real_week_RBH,2);

            $obj = new \stdClass();
            $obj->total_week_check = $total_week_checked;
            $obj->total_week_bad   = $total_week_bad;
            $obj->total_week_success = $total_week_success;
            $obj->total_week_avg = $tatal_week_avg;
            $obj->week_target_rbh = round($week_target_RBH);
            $obj->real_week_rbh = $real_week_RBH;
            $obj->total_week_goal = $total_week_goal;
            $obj->janky_proc = $total_week_proc_janky;
            $obj->target_rbh_percentage = round($week_target_RBH) != 0 ? 100 * $real_week_RBH / round($week_target_RBH) : 0;
            $obj->total_week_goal_proc = $total_week_goal_proc;
            array_push($rbhTargetArr, $obj);
        }

        return $rbhTargetArr;
    }

    private function provisionSystemForHR(&$user, $month, $year,$arrayOfDepartmentStatistics = null) {
        $weekNumber = 0;
        $weekDateArr = MonthFourWeeksDivision::get($year,$month); // array of objects with week info
        if($user->dep_type_id == 1) { //hr from confirming
            //*****Generating info how much account was added per week
            $infoArr = [];
            $totalProv = 0;
            foreach($weekDateArr as $weekInfo) {
                $data = RecruitmentStory::getReportNewAccountData($weekInfo->firstDay,$weekInfo->lastDay); //info about new accounts in teambox

                foreach($data as $item) {
                    if($item->id == $user->id) {
                        $provision = ProvisionLevels::get('HR',$item->add_user, 1,1);
                        $provisionRecord = null;
                        if($provision > 0){
                            $provisionRecord = (object)[
                                'type'      => 2,
                                'id_user'   => $user->id,
                                'amount'    => $provision,
                                'comment'   => "Premia tygodniowa (".$weekDateArr[$weekNumber]->firstDay." -- ".$weekDateArr[$weekNumber]->lastDay.") za zatrudnienie: ".$item->add_user ." osób",
                                'id_manager'=> null,
                                'status'    => 1,
                                'event_date'=> $weekDateArr[$weekNumber]->lastDay,
                                'manager'   => 'System'
                            ];
                            $user->bonuses->push($provisionRecord);
                        }
                        if($this->getToSave() == 1){
                            if($provision > 0){
                                $this->saveBonus($user->id, $provisionRecord->amount, $provisionRecord->event_date, $provisionRecord->comment);
                            }
                        }
                        $obj = new \stdClass();
                        $obj->provision = $provision;
                        array_push($infoArr, $obj);
                        $totalProv += $provision;
                    }
                }
                $weekNumber++;
            }
            $user->provisions = $infoArr;
            $user->bonus += $totalProv;
            //*****End of generating info how much account was added per week
        }
        else if($user->dep_type_id == 2) { //hr from telemarketing
            $provisions = [];
            $totalProvision = 0;
            foreach ($arrayOfDepartmentStatistics as $target) {
                $provTarget = ProvisionLevels::get('HR', $target->janky_proc, $target->total_week_goal_proc, 2, 'ammount');
                $provTargetRecord = null;
                if($provTarget > 0){
                    $provTargetRecord = (object)[
                        'type'      => 2,
                        'id_user'   => $user->id,
                        'amount'    => $provTarget,
                        'comment'   => "Premia tygodniowa (".$weekDateArr[$weekNumber]->firstDay." -- ".$weekDateArr[$weekNumber]->lastDay.") za osiągnięcie: Celu projektu",
                        'id_manager'=> null,
                        'status'    => 1,
                        'event_date'=> $weekDateArr[$weekNumber]->lastDay,
                        'manager'   => 'System'
                    ];
                    $user->bonuses->push($provTargetRecord);
                }
                $prov = ProvisionLevels::get('HR', $target->janky_proc, $target->target_rbh_percentage,2, 'rbh');
                $provRecord = null;
                if($prov > 0){
                    $provRecord = (object)[
                        'type'      => 2,
                        'id_user'   => $user->id,
                        'amount'    => $prov,
                        'comment'   => "Premia tygodniowa (".$weekDateArr[$weekNumber]->firstDay." -- ".$weekDateArr[$weekNumber]->lastDay.") za zatrudnienie: wymaganego RBH na projekcie",
                        'id_manager'=> null,
                        'status'    => 1,
                        'event_date'=> $weekDateArr[$weekNumber]->lastDay,
                        'manager'   => 'System'
                    ];
                    $user->bonuses->push($provRecord);
                }
                if($this->getToSave() == 1){
                    if($provTarget > 0){
                        $this->saveBonus($user->id, $provTargetRecord->amount, $provTargetRecord->event_date, $provTargetRecord->comment);
                    }
                    if($prov > 0){
                        $this->saveBonus($user->id, $provRecord->amount, $provRecord->event_date, $provRecord->comment);
                    }
                }
                $sumProv = $prov + $provTarget;
                array_push($provisions, $sumProv);
                $totalProvision += $sumProv;
                $weekNumber++;
            }
            $user->bonus += $totalProvision;
        }

        //*****Generating info with audits score
//        $departmentAudits = Audit::where('date_audit', 'like', $year . '-' . $month . '%')
//            ->where('user_type', '=', 3)
//            ->where('department_info_id', '=', $user->department_info_id)
//            ->first();
//
//        if(!is_null($departmentAudits)) {
//            $user->auditScore = $departmentAudits->score;
//        }
//        else {
//            $user->auditScore = -1; // if no data, -1
//        }
//
//        $personalAudits = Audit::where('date_audit', 'like', $year . '-' . $month . '%')
//            ->where('user_type', '=', 2)
//            ->where('trainer_id', '=', $user->id)
//            ->first();
//
//        if(!is_null($personalAudits)) {
//            $user->auditScorePersonal = $personalAudits->score;
//        }
//        else {
//            $user->auditScorePersonal = -1; // if no data, -1
//        }
        //*****End of generating info with audits score
    }


    private function provisionSystemForCoordinators(&$user, $month, $year) {
        $weekDateArr = MonthFourWeeksDivision::get($year, $month); //month divided on 4 weeks
        $firstDayOfMonth = new DateTime(date('Y-m-d', strtotime($year . '-' . $month . '-01')));
        $lastDayOfMonth = new DateTime(date('Y-m-d', strtotime($year .'-'. $month . '-' . date('t', strtotime($year . '-' . $month . '-01')))));

        $days_in_month = date('t', strtotime(date('Y').'-'. $month));

        $departments = Department_info::where('id_dep_type', '=', 2)->get();

        $infoArr = [];
        $today = date('Y-m-d'); //today
        $todayDateTime = new DateTime($today);
        $provisions = [];
        $totalProvision = 0;
        $goals = [];
        $data = $this->getMultiDepartmentData($firstDayOfMonth->format('Y-m-d'), $lastDayOfMonth->format('Y-m-d'), $month, $year, $departments->pluck('id')->toArray(), $days_in_month);
        foreach($weekDateArr as $weekInfo) {
            $firstDayOfWork = null;
            if($user->promotion_date != null) { //user was promoted for coordinator
                $firstDayOfWork = new DateTime($user->promotion_date);
            }
            else { //user started working as coordinator, and wasn't in company priously.
                $firstDayOfWork = new DateTime($user->start_work);
            }
            $daysInPosition = $todayDateTime->diff($firstDayOfWork)->days; //how much days coordinator work on his position
            $campaignData = $this->getCampaignData($weekInfo->firstDay, $weekInfo->lastDay, 1);
            $databasePercentageUsage = $campaignData[0]->sum_campaign != 0 ? 100 * $campaignData[0]->sum_active / $campaignData[0]->sum_campaign : 0; //percent of database usage

            $hour_reports = $data['hour_reports'];
            $dep_info = $data['dep_info'];
            $total_success = 0;
            $total_week_goal = 0;
            $total_week_success = 0;
            $firstDayOfMonthDateTime = new DateTime($weekInfo->firstDay);
            $lastDayOfWeekDateTime = new DateTime($weekInfo->lastDay);
            $dateDiff = $lastDayOfWeekDateTime->diff($firstDayOfMonthDateTime)->days;

            for($i = 0; $i <= intval($dateDiff); $i++) {
                $date = date('Y-m-d', strtotime($weekInfo->firstDay . ' + ' . $i .' days'));
                $report = $hour_reports->where('report_date', '=', $date)->where('success', '>', 0)->first();
                $add_default_zero = ($report != null) ? false : true ;
                if ($add_default_zero == false) {
                    $goal = 0;
                    $total_week_success += $report->success;
                    $day_number = date('N', strtotime($report->report_date));
                    foreach ($dep_info as $dep) {
                        $goal += ($day_number < 6) ? $dep->dep_aim : $dep->dep_aim_week;
                    }
                    $total_week_goal += $goal;
                }
            }
            $total_week_goal_proc = ($total_week_goal != null && $total_week_goal > 0) ? round(($total_week_success / $total_week_goal) * 100, 2) : 0 ;
            $provision = 0;
            if($user->user_type_id == 22) { //coordinators menager
                $provision = ProvisionLevels::get('coordinator leader', $databasePercentageUsage, $total_week_goal_proc);
                $provisionRecord = null;
                if($provision > 0){
                    $provisionRecord = (object)[
                        'type'      => 2,
                        'id_user'   => $user->id,
                        'amount'    => $provision,
                        'comment'   => "Premia tygodniowa(".$weekInfo->firstDay." -- ".$weekInfo->lastDay.") za osiągnięcie celu",
                        'id_manager'=> null,
                        'status'    => 1,
                        'event_date'=> $weekInfo->lastDay,
                        'manager'   => 'System'
                    ];
                    $user->bonuses->push($provisionRecord);
                }
                if($this->getToSave() == 1){
                    if($provision > 0) {
                        $this->saveBonus($user->id, $provisionRecord->amount, $provisionRecord->event_date, $provisionRecord->comment);
                    }
                }
            }
            else if($user->user_type_id == 8) {
                $provision = ProvisionLevels::get('koordynator', $databasePercentageUsage, $total_week_goal_proc, $daysInPosition);
                $provisionRecord = null;
                if($provision > 0){
                    $provisionRecord = (object)[
                        'type'      => 2,
                        'id_user'   => $user->id,
                        'amount'    => $provision,
                        'comment'   => "Premia tygodniowa(".$weekInfo->firstDay." -- ".$weekInfo->lastDay.") za osiągnięcie celu",
                        'id_manager'=> null,
                        'status'    => 1,
                        'event_date'=> $weekInfo->lastDay,
                        'manager'   => 'System'
                    ];
                    $user->bonuses->push($provisionRecord);
                }
                if($this->getToSave() == 1){
                    if($provision > 0) {
                        $this->saveBonus($user->id, $provisionRecord->amount, $provisionRecord->event_date, $provisionRecord->comment);
                    }
                }
            }
            array_push($goals, $total_week_goal_proc);
            array_push($provisions, $provision);
            $totalProvision+= $provision;
        }
        $user->bonus += $totalProvision;
    }

    public function viewPaymentCadrePost(Request $request)
    {
        ini_set('max_execution_time', '1800');
        $this->setToSave($request->toSave);

        $allDepartments = Department_info::all();
        $departmentInfoIds = $allDepartments->pluck('id')->toArray();

        $date_to_post = $request->search_money_month;
        $date = $request->search_money_month . '%';
        $year = substr($date, 0, 4);
        $month = substr($date, 5, 2);

        $dividedMonth = $this->monthPerRealWeekDivision($month, $year);
        $agencies = Agencies::all();
        $salary = DB::table(DB::raw("users"))
            ->whereNotIn('users.user_type_id', [1, 2, 9])
            ->where('users.salary', '>', 0)
            ->selectRaw('
            `users`.`id`,
            `users`.`user_type_id`,
            `users`.`agency_id`,
            `users`.`max_transaction`,
            `users`.`first_name`,
            `users`.`last_name`,
            `users`.`salary_to_account`,
            `users`.`username`,
            `department_type`.`id` as department_type_id,
            `departments`.`name` as dep_name, 
            `department_type`.`name`  as dep_type,
            `department_type`.`id` as dep_type_id,
            `department_info`.`id` as department_info_id,
            `users`.`salary`,
            `users`.`rate`,
            `users`.`additional_salary`,
            `users`.`student`,
            `users`.`documents`,
            `users`.`promotion_date`,
            `users`.`start_work`,
            `users`.`main_department_id`,
             ROUND(salary / DAY(LAST_DAY("' . $date_to_post . '-01' . '")),2) as average_salary'/*,
            //now its counting in method because we need information about every bonus/penalty of user depending on if payments are saved or not
            (SELECT SUM(`penalty_bonus`.`amount`) FROM `penalty_bonus` WHERE `penalty_bonus`.`id_user`=`users`.`id` AND `penalty_bonus`.`event_date` LIKE "' . $date . '" AND `penalty_bonus`.`type`=1 AND `penalty_bonus`.`status`=1) as `penalty`,
            (SELECT SUM(`penalty_bonus`.`amount`) FROM `penalty_bonus` WHERE `penalty_bonus`.`id_user`=`users`.`id` AND `penalty_bonus`.`event_date` LIKE  "' . $date . '" AND `penalty_bonus`.`type`=2 AND `penalty_bonus`.`status`=1) as `bonus`'*/)
            ->where(function ($query) use ($date) {
                $query->orwhere(DB::raw('SUBSTRING(promotion_date,1,7)'), '<', substr($date, 0, strlen($date) - 1))
                    ->orwhere('users.promotion_date', '=', null);
            })
            ->join('department_info', 'department_info.id', 'users.main_department_id')
            ->join('work_hours', 'work_hours.id_user', 'users.id')
            ->join('departments', 'departments.id', 'department_info.id_dep')
            ->join('department_type', 'department_type.id', 'department_info.id_dep_type')
            ->where('work_hours.date', 'like', $date)
            ->groupBy('users.id')
            ->orderBy('users.last_name')->get();

        $idsOfUsers = $salary->pluck('id')->toArray();

        $this->recountSalaryForEmployers($salary, $dividedMonth, $date, $date_to_post);

        $payment_saved = AcceptedPayment::
        whereIn('department_info_id', $departmentInfoIds)
            ->where('cadrePayment', 1)
            ->where('payment_month', 'like', $date)
            ->get();

        $acceptedPaymentUserStories = AcceptedPaymentUserStory::whereIn('accepted_payment_id',$payment_saved->pluck('id')->toArray())->get();

        $bonus_penalty = PenaltyBonus::whereIn('id_user', $idsOfUsers)
        ->select('penalty_bonus.*', DB::raw('CONCAT(users.first_name," ",users.last_name) as manager'))
            ->join('users', 'users.id', 'penalty_bonus.id_manager')
            ->where('status', 1);

        //if payment not saved: whereNull('accepted_payment_id') else if payment_saved: whereIn('accepted_payment_id', ids)
        if(count($payment_saved) == 0){
            $bonus_penalty->whereNull('accepted_payment_id');
        }else{
            $bonus_penalty->whereIn('accepted_payment_id', $payment_saved->pluck('id')->toArray());
        }

        $bonus_penalty = $bonus_penalty->get();

        $this->fillInUserWithBonuses($salary, $bonus_penalty,['bonus','penalty']);

        $arrayOfDepartmentStatistics = [];
        $acceptedPaymentUserStoryInsertQueryArr = [];
        $dividedMonthForDepartmentStatistics = MonthFourWeeksDivision::get($year, $month);
        $deps2 = $allDepartments->where('commission_avg', '!=', 0);

        foreach ($allDepartments as $item) {
            $arrayOfDepartmentStatistics[$item->id] = $this->getDepartmentStatistics($dividedMonthForDepartmentStatistics, substr($dividedMonthForDepartmentStatistics[0]->firstDay, 5, 2), substr($dividedMonthForDepartmentStatistics[0]->firstDay, 0, 4), [$item->id]);
        }
        foreach ($allDepartments as $departmentInfo) {
            $acceptedPayment = $payment_saved->where('department_info_id', $departmentInfo->id)->first();
            $salaryFromDepartment = $salary->where('main_department_id',$departmentInfo->id);
            if (empty($acceptedPayment)) {
                /*
                 * Saving information about accepted payments
                 */
                if ($this->getToSave() == 1) {
                    $acceptedPayment = new AcceptedPayment();
                    $acceptedPayment->cadre_id = Auth::user()->id;
                    $acceptedPayment->payment_month = $request->search_money_month . '-01';
                    $acceptedPayment->department_info_id = $departmentInfo->id;
                    $acceptedPayment->cadrePayment = 1;
                    $acceptedPayment->save();
                    $this->acceptedPaymentForDepartment = $acceptedPayment;
                }
                foreach ($salaryFromDepartment as $user) {
                    if ($user->user_type_id == 4) {
                        $this->provisionSystemForTrainers($user, $dividedMonthForDepartmentStatistics, $arrayOfDepartmentStatistics[$user->department_info_id]);
                    } else if ($user->user_type_id == 5) {
                        $this->provisionSystemForHR($user, $month, $year, $arrayOfDepartmentStatistics[$user->department_info_id]);
                    } else if ($user->user_type_id == 19) {
                        $this->provisionSystemForInstructors($user, $dividedMonthForDepartmentStatistics, $user->department_info_id, $deps2, $arrayOfDepartmentStatistics[$user->department_info_id]);
                    } else if ($user->user_type_id == 21) {
                        $instructorDepartments = Department_info::where('instructor_regional_id', '=', $user->id)->pluck('id')->toArray(); //array of department_info.id of instructor's departments
                        foreach ($instructorDepartments as $singleDepartment) {
                            $this->provisionSystemForInstructors($user, $dividedMonthForDepartmentStatistics, $singleDepartment, $deps2, $arrayOfDepartmentStatistics[$singleDepartment]);
                        }
                    } else if ($user->user_type_id == 8 || $user->user_type_id == 22) { //koordynator + menager of coordinators
                        $this->provisionSystemForCoordinators($user, $month, $year);
                    } else if ($user->user_type_id == 17 || $user->user_type_id == 7 || $user->user_type_id == 14) { // Kierownik + Kierownik Regionaly + kierownik HR + Kierownik Szkoleniowcow
                        $this->provisionSystemForManagers($user, $dividedMonthForDepartmentStatistics, $arrayOfDepartmentStatistics);
                    }


                    if ($this->getToSave() == 1) {
                        /*
                         * Accepting every penalty and bonus that were added after accepting previous month payments for actual user
                         */
                        $bonus_penalty->where('id_user', $user->id)->update(['accepted_payment_id' => $acceptedPayment->id]);

                          /**
                           * Creating table of information about every employer to save - IT HAS TO BE AS LAST INSTRUCTION AFTER COUNTING SALARIES
                           */
                          /*
                           * FIELDS TO INSERT: accepted_payment_id, user_id, salary, rate, additional_salary, student,
                           * salary_to_account, max_transaction, department_info_id, agency_id, user_type_id, created_at, updated_at
                           */
                        array_push($acceptedPaymentUserStoryInsertQueryArr,[
                            'accepted_payment_id'   => $acceptedPayment->id,
                            'user_id'               => $user->id,
                            'salary'                => $user->salary,
                            'rate'                  => $user->rate,
                            'additional_salary'     => $user->additional_salary,
                            'student'               => $user->student,
                            'salary_to_account'     => $user->salary_to_account,
                            'max_transaction'       => $user->max_transaction,
                            'department_info_id'    => $user->department_info_id,
                            'agency_id'             => $user->agency_id,
                            'user_type_id'          => $user->user_type_id,
                            'created_at'            => date('Y-m-d H:i:s'),
                            'updated_at'            => date('Y-m-d H:i:s')]);
                    }
                }
            }else{
                //getting historic information about employers
                foreach ($salaryFromDepartment as $user) {
                    $acceptedPaymentUserStory = $acceptedPaymentUserStories->where('user_id', $user->id)->first();
                    $user->salary               = $acceptedPaymentUserStory->salary;
                    $user->rate                 = $acceptedPaymentUserStory->rate;
                    $user->additional_salary    = $acceptedPaymentUserStory->additional_salary;
                    $user->student              = $acceptedPaymentUserStory->student;
                    $user->salary_to_account    = $acceptedPaymentUserStory->salary_to_account;
                    $user->max_transaction      = $acceptedPaymentUserStory->max_transaction;
                    $user->department_info_id   = $acceptedPaymentUserStory->department_info_id;
                    $user->agency_id            = $acceptedPaymentUserStory->agency_id;
                    $user->user_type_id         = $acceptedPaymentUserStory->user_type_id;
                }
            }
        }

        /**
         * Saving information about every employer
         */
        if ($this->getToSave() == 1) {
            DB::table('accepted_payment_user_story')->insert($acceptedPaymentUserStoryInsertQueryArr);
        }

        /**
         *  Pobranie informacji o departamentach
         */
        $departments = DB::table('department_info')
            ->select(DB::raw('department_info.id as id,departments.name as dep_name,department_type.name as dep_type'))
            ->join('departments','departments.id','department_info.id_dep')
            ->join('department_type','department_type.id','department_info.id_dep_type')
            ->get();

        return view('finances.viewPaymentCadre')
            ->with('month',$date_to_post)
            ->with('salary',$salary->groupby('agency_id'))
            ->with('agencies',$agencies)
            ->with('departments',$departments)
            ->with('payment_saved',$payment_saved);
    }

    private function fillInUserWithBonuses(&$users, $penaltyBonuses, $fieldsName){
        foreach ($users as $user){
            $userBonuses = $penaltyBonuses->where('id_user',$user->id);
            $bonuses = collect([]);
            $user->{$fieldsName[0]} = 0;
            $user->{$fieldsName[1]} = 0;
            foreach($userBonuses as $userBonus){
                $bonuses->push($userBonus);
                if($userBonus->type == 2){
                    $user->{$fieldsName[0]} += $userBonus->amount;
                }else{
                    $user->{$fieldsName[1]} += $userBonus->amount;
                }
            }
            $user->bonuses = collect($bonuses->toArray());
        }
    }

    public function viewPaymentPOST(Request $request)
    {
        $date = $request->search_money_month;
        $salary = $this->getSalary($date.'%', Auth::user()->department_info_id );

        $department_info = Department_info::find(Auth::user()->department_info_id);
        $janky_system = JankyPenatlyProc::where('system_id',$department_info->janky_system_id)->get();
        $agencies = Agencies::all();
        $department_type = Department_types::find($department_info->id_dep_type);
        $count_agreement = $department_type->count_agreement;

        $payment_saved = AcceptedPayment::where('department_info_id','=',Auth::user()->department_info_id)
            ->where('payment_month','like', $date.'%')
            ->whereNull('cadrePayment')
            ->get();

        $idsOfUsers = [];
        foreach ($salary as $agency){
            $idsOfUsers = array_merge($idsOfUsers, $agency->pluck('id')->toArray());
        }

        $bonus_penalty = PenaltyBonus::whereIn('id_user', $idsOfUsers)
            ->select('penalty_bonus.*', DB::raw('CONCAT(users.first_name," ",users.last_name) as manager'))
            ->join('users', 'users.id', 'penalty_bonus.id_manager')
            ->where('status', 1);

        //if payment not saved: whereNull('accepted_payment_id') else if payment_saved: whereIn('accepted_payment_id', ids)
        if(count($payment_saved) == 0){
            $bonus_penalty->whereNull('accepted_payment_id');
        }else{
            $bonus_penalty->whereIn('accepted_payment_id', $payment_saved->pluck('id')->toArray());
        }

        $bonus_penalty = $bonus_penalty->get();
        foreach ($salary as $agency){
            $this->fillInUserWithBonuses($agency,$bonus_penalty,['premia','kara']);
        }

        if($count_agreement == 1)
        {
            return view('finances.viewPayment')
                ->with('month',$date)
                ->with('salary',$salary)
                ->with('department_info',$department_info)
                ->with('janky_system',$janky_system)
                ->with('agencies',$agencies)
                ->with('payment_saved',$payment_saved);
        }
       else
        {
            return view('finances.viewPaymentWithoutAgreement')
                ->with('month',$date)
                ->with('salary',$salary)
                ->with('department_info',$department_info)
                ->with('janky_system',$janky_system)
                ->with('agencies',$agencies)
                ->with('payment_saved',$payment_saved);
        }
    }

    public function viewPenaltyBonusPostEdit(Request $request) {
        $id_manager = Auth::user()->id;

        if(!in_array(Auth::user()->user_type_id, UsersController::getUserTypesPermissionToGivePenaltyBonus())){
            $LogData = array_merge(['T ' => 'Dodanie kary/premii '],['userID:'=>$id_manager]);
            new ActivityRecorder($LogData, 10, 5);
            return view('errors.404');
        }
        $user = User::find($request->user_id);

        if ($user == null || ($request->penalty_type != 1 && $request->penalty_type != 2)) {
            return view('errors.404');
        }

        $object = new PenaltyBonus();
        $object->id_user = $request->user_id;
        $object->type = $request->penalty_type;
        $object->amount = $request->cost;
        $object->event_date = date('Y-m-d');
        $object->id_manager = $id_manager;
        $object->comment = $request->reason;
        $object->event_date = $request->date_penalty;
        $object->save();

        $LogData = array_merge(['T ' => 'Dodanie kary/premii '],$object->toArray());
        new ActivityRecorder($LogData, 10, 1);

        $message_type = ($request->penalty_type == 1) ? 'Kara' : 'Premia' ;
        $message = $message_type . ' została dodana pomyślnie';

        $user = User::find($request->user_id);
        $agencies = Agencies::all();

        Session::flash('message', "Kara/premia dodana pomyślnie!");
        return Redirect::back();

    }

    public function createPenaltyBonusPOST(Request $request) {
        $checkUser = User::find($request->user_id);
        if($checkUser == null){
            return view('errors.404');
        }

        $object = new PenaltyBonus();
        $object->id_user = $request->user_id;
        $object->type = $request->type_penalty;
        $object->amount = $request->cost;
        $object->event_date = $request->date_penalty;
        $object->id_manager = Auth::user()->id;
        $object->comment = $request->reason;
        $object->save();
        $LogData = array_merge(['T ' => 'Dodanie kary/premii '],$object->toArray());
        new ActivityRecorder($LogData, 10, 1);
        Session::flash('message_ok', "Kara/premia dodana pomyślnie!");
        return Redirect::back();
    }

    public function viewPenaltyBonusGet()
    {

        // użytkownicy pracujący
        $users =  User::where('department_info_id', Auth::user()->department_info_id)
            ->whereIn('user_type_id', [1, 2, 9])
            ->where('status_work', '=', 1)
            ->orderBy('last_name')
            ->get();
        $last_month = date("Y-m", strtotime("first day of previous month"));
        $current_month = date("Y-m");
        // zwolnieni miesiąc temu
        $users_fired_last_month =  User::where('department_info_id', Auth::user()->department_info_id)
            ->whereIn('user_type_id', [1, 2, 9])
            ->where('status_work', '=', 0)
            ->where('end_work', 'like', $last_month.'%')
            ->orderBy('last_name')
            ->get();
        // zwolnieni w tym miesiącu
        $users_fired_current_month =  User::where('department_info_id', Auth::user()->department_info_id)
            ->whereIn('user_type_id', [1, 2, 9])
            ->where('status_work', '=', 0)
            ->where('end_work', 'like', $current_month.'%')
            ->orderBy('last_name')
            ->get();
        $merge_array = $users->merge($users_fired_last_month);
        $merge_array = $merge_array->merge($users_fired_current_month);

        return view('finances.viewPenaltyBonus')
            ->with('users',$merge_array->sortBy('last_name'));
    }
    public function viewPenaltyBonusPOST(Request $request)
    {
        // użytkownicy pracujący
        $users =  User::where('department_info_id', Auth::user()->department_info_id)
            ->whereIn('user_type_id', [1, 2, 9])
            ->where('status_work', '=', 1)
            ->orderBy('last_name')
            ->get();

        $last_month = date("Y-m", strtotime("first day of previous month"));
        $current_month = date("Y-m");
        // zwolnieni miesiąc temu
        $users_fired_last_month =  User::where('department_info_id', Auth::user()->department_info_id)
            ->whereIn('user_type_id', [1, 2, 9])
            ->where('status_work', '=', 0)
            ->where('end_work', 'like', $last_month.'%')
            ->orderBy('last_name')
            ->get();
        // zwolnieni w tym miesiącu
        $users_fired_current_month =  User::where('department_info_id', Auth::user()->department_info_id)
            ->whereIn('user_type_id', [1, 2, 9])
            ->where('status_work', '=', 0)
            ->where('end_work', 'like', $current_month.'%')
            ->orderBy('last_name')
            ->get();
        $merge_array = $users->merge($users_fired_last_month);
        $merge_array = $merge_array->merge($users_fired_current_month);

        $view = view('finances.viewPenaltyBonus')->with('users',$merge_array->sortBy('last_name'));

        $date_start = $request->date_penalty_show_start;
        $date_stop = $request->date_penalty_show_stop;
        $query = DB::table('penalty_bonus')
            ->join('users as users', 'penalty_bonus.id_user', '=', 'users.id')
            ->join('users as manager', 'penalty_bonus.id_manager', '=', 'manager.id')
            ->select(DB::raw(
                'users.first_name,
                users.last_name,
                manager.first_name as manager_first_name,
                manager.last_name as manager_last_name,
                penalty_bonus.*
               '))->where('users.department_info_id',Auth::user()->department_info_id)
                ->whereBetween('event_date', [$date_start, $date_stop])
                ->whereIn('type', [1,2])
                ->whereIn('users.user_type_id',[1,2,9])
                ->where('penalty_bonus.status',1);
        if($request->showuser != -1)
        {
            $query
              ->where('users.id' , $request->showuser)
              ->where('status', 1);
        }
        $view->with('users_show',$query->get())
        ->with('date_start',$date_start)
        ->with('date_stop',$date_stop)
        ->with('showuser',$request->showuser);

        return $view;
    }

    public function viewSummaryPaymentGet()
    {
       return view('finances.viewSummaryPayment');
    }
    public function viewSummaryPaymentPOST(Request $request)
    {
        $month = $request->search_summary_money_month;
        $summary_month = SummaryPayment::where('month',$month)->get();
        $departments = Department_info::where('type','!=','')->get();
        return view('finances.viewSummaryPayment')
            ->with('summary_month',$summary_month)
            ->with('month',$month)
            ->with('departments',$departments);
    }


    public function saveSummaryPayment(Request $request)
    {
        if($request->ajax())
        {
            $summary_payment = SummaryPayment::firstOrNew(array('month' =>$request->month,
                'department_info_id' =>Auth::user()->department_info_id));
            $summary_payment->department_info_id = Auth::user()->department_info_id;
            $summary_payment->month = $request->month;
            $summary_payment->payment = $request->payment_total;
            $summary_payment->month = $request->month;
            $summary_payment->hours = $request->rbh_total;
            $summary_payment->documents =$request->documents_total;
            $summary_payment->students = $request->students_total;
            $summary_payment->employee_count = $request->user_total;
            $summary_payment->id_user = Auth::user()->id;
            $summary_payment->save();
            $LogData = array_merge(['T ' => 'Sumowanie wypłat oddziałów '],$summary_payment->toArray());
            new ActivityRecorder($LogData, 24, 1);
            return $summary_payment;
        }
    }
    public function editPenaltyBonus(Request $request)
    {
        if($request->ajax())
        {
            $object = PenaltyBonus::find($request->id);
            if ($object == null) {
                return 0;
            }
            if ($request->type != 1 && $request->type != 2) {
                return 0;
            }
            $object->type = $request->type;
            $object->amount = $request->amount;
            $object->comment = $request->comment;
            $object->id_manager_edit = Auth::user()->id;
            $object->save();
            $LogData = array_merge(['T ' => 'Edycja kary/premii '],$object->toArray());
            new ActivityRecorder($LogData, 10, 2);
            return 1;
        }
    }


    //Custom Function
    private function getSalary($month, $departmentInfoIds)
    {
            $realMonth = substr($month,5,2);
            $realYear = substr($month, 0,4);
            $clientRouteInfoRecords = ClientRouteInfo::where('confirmDate', 'like', $month)->OnlyActive()->get();
            //Czy wypłata jest już zatwierdzona
            $payment_saved = AcceptedPayment::
            whereIn('department_info_id', is_array($departmentInfoIds) ? $departmentInfoIds : [$departmentInfoIds])
            ->where('payment_month','like', $month)->whereNull('cadrePayment')
            ->get();
            $string_to_sql = '';
            if(!$payment_saved->isEmpty()){
                $string_to_sql = "`payment_agency_story`.`agency_id`";
            }else{
                $string_to_sql = "`users`.`agency_id`";
            }
        $query = DB::table(DB::raw("users"))
            ->join('work_hours', 'work_hours.id_user', 'users.id')
            ->join('department_info', 'users.department_info_id', '=', 'department_info.id')
            ->whereIn('users.department_info_id',is_array($departmentInfoIds) ? $departmentInfoIds : [$departmentInfoIds])
            ->where(function ($querry) use ($month){
                $querry->orwhere(DB::raw('SUBSTRING(promotion_date,1,7)'),'>=',substr($month,0,strlen($month)-1))
                    ->orwhere('users.user_type_id','=',1)
                    ->orwhere('users.user_type_id','=',2);
            })
            ->where('work_hours.date','like',$month)
            ->selectRaw('
            `users`.`id`,
            '.$string_to_sql.',              
            `users`.`first_name`,
            `users`.`last_name`,
            `users`.`max_transaction`,
            `users`.`username`,
            `users`.`rate`,
            `users`.`login_phone`,
             SUM( time_to_sec(`work_hours`.`accept_stop`)-time_to_sec(`work_hours`.`accept_start`)) as `sum`,
            `users`.`student`,
            `users`.`documents`,
            `users`.`department_info_id`,
            SUM(`work_hours`.`success`) as `success`,
            `salary_to_account`,
            `users`.`successorUserId`,
            0 as successorSalary,
            id_dep_type
            ');
            /*
            (SELECT SUM(`penalty_bonus`.`amount`) FROM `penalty_bonus` WHERE `penalty_bonus`.`id_user`=`users`.`id` AND `penalty_bonus`.`event_date` LIKE "'.$month.'" AND `penalty_bonus`.`type`=1 AND `penalty_bonus`.`status`=1) as `kara`,
            (SELECT SUM(`penalty_bonus`.`amount`) FROM `penalty_bonus` WHERE `penalty_bonus`.`id_user`=`users`.`id` AND `penalty_bonus`.`event_date` LIKE  "'.$month.'" AND `penalty_bonus`.`type`=2 AND `penalty_bonus`.`status`=1) as `premia`,*/
            if(!$payment_saved->isEmpty()){
                $query = $query
                    ->leftjoin('payment_agency_story',function ($querry) use ($month){
                        $querry->on('payment_agency_story.consultant_id','=','users.id')
                            ->where('payment_agency_story.accept_month','like',$month);
                    });
            }
            $query = $query
            ->groupBy('users.id')
            ->orderBy('users.last_name');

            $r = DB::table(DB::raw('('.$query->toSql().') as r'))
                ->mergeBindings($query)
                    ->leftjoin(
                    DB::raw('(SELECT `dkj`.`id_user`, COUNT(*) as ods FROM `dkj`
                    WHERE `deleted`=0 AND `add_date` LIKE  "'.$month.'"
                    GROUP by `dkj`.`id_user`
                    ) f '),'r.id','f.id_user'
                )
                ->leftjoin(
                    DB::raw('(SELECT `dkj`.`id_user`, COUNT(*) as janki FROM `dkj` where
                   `deleted`=0 AND `dkj_status`=1 AND `add_date` LIKE  "'.$month.'"
                    GROUP by `dkj`.`id_user`) h'),'r.id','h.id_user'
                )
                ->selectRaw('`id`,`agency_id`,`first_name`,`last_name`,`max_transaction`,`username`,`rate`,`login_phone`,`sum`,`student`,`documents`,`success`,
            `f`.`ods`,
            `h`.`janki`,
            `salary_to_account`,
            successorUserId,
            successorSalary,
            id_dep_type,
            department_info_id')->get();
            /*,`kara`,`premia`*/

        $weekDayArr = MonthFourWeeksDivision::get($realYear, $realMonth);

        /**
         * JEZELI BEDA PROBLEMY Z LICZENIEM PO WRPOWADZENIU ZMIAN W 2018-12 ODNOSNIE WYPLAT TO TRZEBA BEDZIE COFNAC ZMIANE - ponizsze zapytanie z
         * UserEmploymentStatus bylo w mapowaniu, odkomentowac i sprawdzic czy to naprawilo problem
         */
        $user_empl_status = UserEmploymentStatus::
        where( function ($querry) use ($r) {
            $querry = $querry->orwhere(function ($query) use($r){
                $query->whereIn('pbx_id', $r->pluck('login_phone')->toArray());
            })
                ->orWhere(function ($query) use($r){
                    $query->whereIn('user_id', $r->pluck('id')->toArray());
                });
            })
            ->where('pbx_id_add_date', 'like', $month)
            ->where('pbx_id', '!=', 0)
            ->where('pbx_id', '!=', null)
            ->get();
            $result = $r->map(function($item) use($month, $clientRouteInfoRecords, $weekDayArr, $user_empl_status) {
                $campaignScoresArr = [];

                /*
                 $user_empl_status = UserEmploymentStatus::
                where( function ($querry) {
                    $querry = $querry->orwhere('pbx_id', $item->login_phone)
                        ->orWhere('user_id', $item->id);
                        });
                    })
                    ->where('pbx_id_add_date', 'like', $month)
                    ->where('pbx_id', '!=', 0)
                    ->where('pbx_id', '!=', null)
                    ->get();
                 */
                //creating provision field for each confirming consultant with data about provisions.
                if($item->id_dep_type == 1) { //konsultant potwierdzeń
                    $globalProvisionSum = 0;

                    foreach($weekDayArr as $week) { //looping after each week
                        $weekScoreArr = [];
                        $showRecordsOfGivenUser = $clientRouteInfoRecords
                            ->where('confirmingUser', '=', $item->id)
                            ->where('confirmDate', '>=', $week->firstDay)
                            ->where('confirmDate', '<', $week->lastDay); //all campaigns that he/she confirm in given week
                        $provisionSum = 0;
                        $badCampaigns = 0;
                        foreach($showRecordsOfGivenUser as $show) {
                            $provision = ProvisionLevels::get('consultant', $show->frequency, '1');
                            array_push($weekScoreArr, $provision);
                            if($provision < 0) {
                                $badCampaigns++;
                            }
                            $provisionSum += $provision;
                            $globalProvisionSum += $provision;
                        }
                        $badCampaignsProvision = ProvisionLevels::get('consultant', $badCampaigns, '2');
                        $obj = new \stdClass();
                        $obj->provisions = $weekScoreArr;
                        $obj->provisionSum =  $provisionSum;
                        $obj->badCampaignsProvision = $badCampaignsProvision; // 50zl for each week without bad campaigns
                        $globalProvisionSum += $badCampaignsProvision;
                        array_push($campaignScoresArr, $obj);
                    }
//                    $item->premia += $globalProvisionSum < 0 ? 0 : $globalProvisionSum;
                }
                $user_empl_status = $user_empl_status->where('user_id','=',$item->id);
                if(count($user_empl_status) == 0 || count($user_empl_status) == 1) {

                    $reports = DB::table('pbx_report_extension')
                        ->select(DB::raw(
                            'SUM(`all_checked_talks`) as sum_all_checked_talks,
                            SUM(`all_bad_talks`) as sum_all_bad_talks,
                            SUM(success) as sum_success
                            '))
                        ->where('pbx_report_extension.pbx_id','=', $item->login_phone)
                        ->whereIn('pbx_report_extension.id', function($query) use($month){
                            $query->select(DB::raw(
                                'MAX(pbx_report_extension.id)'
                            ))
                                ->from('pbx_report_extension')
                                ->where('report_date', 'like', $month)
                                ->groupBy('report_date', 'pbx_id');
                        })
                        ->first();
                    $item->ods = $reports->sum_all_checked_talks;
                    $item->janki = $reports->sum_all_bad_talks;
                    $item->pbx_success = $reports->sum_success;
                }
                else if (count($user_empl_status) > 1) {
                    $sum_janki = $sum_success = $sum_ods = 0;
                    foreach($user_empl_status as $user_status) {

                        $reports = DB::table('pbx_report_extension')
                            ->select(DB::raw(
                                'SUM(`all_checked_talks`) as sum_all_checked_talks,
                            SUM(`all_bad_talks`) as sum_all_bad_talks,
                            SUM(success) as sum_success
                            '))
                            ->where('pbx_report_extension.pbx_id','=', $user_status->pbx_id)
                            ->whereIn('pbx_report_extension.id', function($query) use($user_status,$month){
                                $query->select(DB::raw(
                                    'MAX(pbx_report_extension.id)'
                                ))
                                    ->from('pbx_report_extension');
                                if($user_status->pbx_id_remove_date == null || $user_status->pbx_id_remove_date == '0000-00-00'){
                                    $query->whereBetween('report_date',[$user_status->pbx_id_add_date,substr($month,0,7).'-31',]);
                                }else
                                    $query->whereBetween('report_date',[substr($month,0,7).'-01',$user_status->pbx_id_remove_date]);
                                 $query->groupBy('report_date', 'pbx_id');
                            })
                            ->first();
                        $sum_ods += $reports->sum_all_checked_talks;
                        $sum_janki += $reports->sum_all_bad_talks;
                        $sum_success += $reports->sum_success;
                    }
                    $item->ods = $sum_ods;
                    $item->janki = $sum_janki;
                    $item->pbx_success = $sum_success;
                }
               return $item;
            });

            $this::mapSuccessorSalary($departmentInfoIds,$r,$month);
            $final_salary = $r->groupBy('agency_id');
            return $final_salary;
    }

    public function mapSuccessorSalary($departmentInfoID,&$usersSalary,$month){
        $map = SuccessorHistory::select('successor_history.*','users.rate','users.successorUserId')->join('users','users.id','successor_history.user_id')
            ->whereIn('users.department_info_id',is_array($departmentInfoID) ? $departmentInfoID : [$departmentInfoID])
            ->where(function ($querry) use ($month){
                $querry->orwhere('successor_history.date_stop','<=',substr($month,0,7).'-31')
                    ->orwhere('successor_history.date_stop',null);
            })
            ->get()->groupBy('user_id');

        $map->map(function ($item) use ($month,&$usersSalary){
            $salaryAdd = 0;
            $user_id = $item->first()->successorUserId;
            $rate = $item->first()->rate;
            foreach ($item as $row){
                $actualDate = new DateTime(date('Y-m-d'));
                $successorHistoryDateStart = new DateTime($row->date_start);
                $date_start = null;

                if($actualDate->format('m') == $successorHistoryDateStart->format('m')) {
                    $date_start = $row->date_start;
                }
                else {
                    $date_start = substr($month,0,7).'-01';
                }
                $date_stop = $row->date_stop == null ? date('Y-m-t', strtotime(substr($month,0,7).'-01')) : $row->date_stop;
                $salaryAdd +=  $this::getInfoAboutWorkHour($row->user_id,$date_start,$date_stop)->first()->sumHour;
            }
            $salaryAdd = round(($salaryAdd/3600)*$rate,2);
            $usersSalary->where('id',$user_id)->map(function ($itemSalary) use ($salaryAdd,$user_id){
                if($itemSalary->id == $user_id){
                    $itemSalary->successorSalary = $salaryAdd;
                }
                return $itemSalary;
            });
            return $item;
        });
    }


    public function getInfoAboutWorkHour($user_id,$date_start,$date_stop){
        return $userInfo = Work_Hour::select(DB::raw(
            'SUM( time_to_sec(`work_hours`.`accept_stop`)-time_to_sec(`work_hours`.`accept_start`)) as `sumHour`,
            sum(success) as sumSucces
            '))
            ->where('id_user',$user_id)
            ->where('date','>=',$date_start)
            ->where('date','<=',$date_stop)
            ->get();
    }
    public function deletePenaltyBonus(Request $request) {
        if($request->ajax())
        {
            $object = PenaltyBonus::find($request->id);

            if ($object != null) {
                $object->id_manager_edit = Auth::user()->id;
                $object->status = 0;
                $object->updated_at = date('Y-m-d H:i:s');
                $object->save();
                $LogData = array_merge(['T ' => 'Usunięcie kary/premii'],$object->toArray());
                new ActivityRecorder($LogData, 25, 3);
                return 1;
            } else {
                return 0;
            }
        }
    }

    /**
     * Zapisanie informacji o aktualnym stanie wypłat
     * @param Request $request
     * @return array
     */
    public function paymentStory(Request $request){
        if($request->ajax()){
            //Zapisanie infromacji o zaakceptowaniu wypłat
            $is_exist = AcceptedPayment::where('department_info_id','=',Auth::user()->department_info_id)
                ->where('payment_month','like', $request->accetp_month.'%')
                ->whereNull('cadrePayment')
                ->get();
            if($is_exist->isEmpty()){
                $accept_payment = new AcceptedPayment();
                $accept_payment->cadre_id =  Auth::user()->id;
                $accept_payment->payment_month = $request->accetp_month.'-01';
                $accept_payment->department_info_id = Auth::user()->department_info_id;
                $salary = $this::getSalary($request->accetp_month.'%', Auth::user()->department_info_id );
                $data = array();
                $idsOfConsultants = [];
                foreach ($salary as $agency ){
                    $idsOfConsultants = array_merge($idsOfConsultants, $agency->pluck('id')->toArray());
                    foreach ($agency as $user){
                        array_push($data,array('consultant_id' => $user->id,
                            'agency_id' => $user->agency_id,
                            'cadre_id' => Auth::user()->id,
                            'department_info_id' => Auth::user()->department_info_id,
                            'accept_month' => $request->accetp_month.'-01',
                            'created_at' =>date('Y-m-d H:m:s:i'),
                            'updated_at' => date('Y-m-d H:m:s:i')));
                    }
                }

                if(session()->has('isPaymentAgencyStoryQueryRunning')){
                    if(session('isPaymentAgencyStoryQueryRunning')){
                        $DOUBLING_QUERY_LOG = new DoublingQueryLogs();
                        $DOUBLING_QUERY_LOG->table_name = 'PaymentAgencyStory';
                        $DOUBLING_QUERY_LOG->save();
                    }else{
                        session(['isPaymentAgencyStoryQueryRunning' => true]);
                        PaymentAgencyStory::insert($data);
                        session()->forget('isPaymentAgencyStoryQueryRunning');
                    }
                }else{
                    session(['isPaymentAgencyStoryQueryRunning' => true]);
                    PaymentAgencyStory::insert($data);
                    session()->forget('isPaymentAgencyStoryQueryRunning');
                }

                if(session()->has('isAcceptedPaymentQueryRunning')){
                    if(session('isAcceptedPaymentQueryRunning')){
                        $DOUBLING_QUERY_LOG = new DoublingQueryLogs();
                        $DOUBLING_QUERY_LOG->table_name = 'AcceptedPayment';
                        $DOUBLING_QUERY_LOG->save();
                    }else{
                        session(['isAcceptedPaymentQueryRunning' => true]);
                        $accept_payment->save();
                        session()->forget('isAcceptedPaymentQueryRunning');
                    }
                }else{
                    session(['isAcceptedPaymentQueryRunning' => true]);
                    $accept_payment->save();
                    session()->forget('isAcceptedPaymentQueryRunning');
                }


                $bonus_penalty = PenaltyBonus::whereIn('id_user', $idsOfConsultants)
                    ->select('penalty_bonus.*', DB::raw('CONCAT(users.first_name," ",users.last_name) as manager'))
                    ->join('users', 'users.id', 'penalty_bonus.id_manager')
                    ->where('status', 1);

                //if payment not saved: whereNull('accepted_payment_id') else if payment_saved: whereIn('accepted_payment_id', ids)
                $bonus_penalty->whereNull('accepted_payment_id');

                $bonus_penalty = $bonus_penalty->get();
                foreach ($salary as $agency){
                    $this->fillInUserWithBonuses($agency,$bonus_penalty,['premia','kara']);
                }

                /*
                 * Accepting every penalty and bonus that were added after accepting previous month payments for every consultant
                 */
                $bonus_penalty->update(['accepted_payment_id' => $accept_payment->id]);

                $LogData = array_merge(['T ' => 'Zapisanie wypłat '],$accept_payment->toArray());
                new ActivityRecorder($LogData, 24, 1);
                return $data;
            }
            return null;
        }
    }

    public function viewEmployeeOfTheWeekGet(){
        $userTypes = UserTypes::whereIn('id',[1,2,9,3])->get();
        $departments_info = Department_info::with('departments')->with('department_type');
        $accessToAllDepartments = UserTypes::find(Auth::user()->user_type_id)->all_departments;;
        if($accessToAllDepartments != 1){
            $departments_info->where('id', Auth::user()->department_info_id);
        }else{
            $departments_info->where('id_dep_type',1);
        }

        return view('finances.employeeOfTheWeek.viewEmployeeOfTheWeek')
            ->with('type', 1)
            ->with('accessToAllDepartments',$accessToAllDepartments)
            ->with('userTypes', $userTypes->where('id',1))
            ->with('departments_info', $departments_info->get());
    }

    public function getUserTypesOfDepartmentTypeAjax(Request $request){
        $departmentTypeId = $request->departmentTypeId;

        $userTypes = [];
        if($departmentTypeId == 1){
            $userTypes = [1,4];
        }
        $userTypesGet = UserTypes::whereIn('id',$userTypes)->get();
        return $userTypesGet;
    }

    public function getDepartmentInfoAjax(Request $request){
        $departmentTypeId = $request->departmentTypeId;

        $departmentInfosGet = Department_info::with('departments')
            ->where('id_dep_type',$departmentTypeId)->get();
        return $departmentInfosGet;
    }

    public function getTrainersAjax(Request $request){
        $departmentInfoId = $request->departmentInfoId;
        $month = $request->month;

        $trainersGet = User::select('users.id',
            'users.first_name',
            'users.last_name'
            )->leftJoin('work_hours as wh','wh.id_user','users.id')->distinct()
            ->where('users.user_type_id',4)
            ->where('department_info_id',$departmentInfoId)
            ->where('wh.created_at', 'like', $month.'%')->get();
        return $trainersGet;
    }
/*
    public function viewEmployeeOfTheWeekCadreGet(){
        $userTypes = UserTypes::whereNotIn('id',[1,2,9,3])->get();
        $departments_info = Department_info::with('departments')->with('department_type');
        $accessToAllDepartments = UserTypes::find(Auth::user()->user_type_id)->all_departments;;
        if($accessToAllDepartments != 1){
            $departments_info->where('id', Auth::user()->department_info_id);
        }else{
            $departments_info->where('id_dep_type',1);
        }
        return view('finances.employeeOfTheWeek.viewEmployeeOfTheWeek')
            ->with('type', 2)
            ->with('accessToAllDepartments',$accessToAllDepartments)
            ->with('userTypes', $userTypes->where('id',4))
            ->with('departments_info', $departments_info->get());
    }*/

    public function employeeOfTheWeekSubViewAjax( Request $request){
        if($request->ajax()){
            $selectedMonth = $request->selectedMonth;
            $departmentInfoId = $request->departmentInfoId;
            $departmentTypeId = $request->departmentTypeId;
            $userTypeId = $request->userTypeId;
            $trainerId = $request->trainerId;
            $year = date('Y',strtotime($selectedMonth));
            $month = date('m',strtotime($selectedMonth));
            $dividedMonth = MonthFourWeeksDivision::get($year, $month);
            if($userTypeId == 4 && $departmentTypeId == 1){          //confirmation trainers
                $employeesOfTheWeek = $this->getEmployeesOfTheWeek($userTypeId, $departmentTypeId, $departmentInfoId, $trainerId, $dividedMonth, 1);

                $departmentInfo = Department_info::where('id_dep_type', $departmentTypeId)->get(); //getting every confirmation department

                $clientRouteInfo = $this->getConfirmationConsultantsRoutesInformation($departmentInfo->pluck('id')->toArray(), $dividedMonth);
                $this->updateConfirmationRanking($employeesOfTheWeek->where('accepted',0), $clientRouteInfo, $dividedMonth, $departmentInfoId, [200], 'coach_id', ['successfulPct','provision']);

                $employeesOfTheWeekRankings = EmployeeOfTheWeekRanking::whereIn('employee_of_the_week_id',$employeesOfTheWeek->pluck('id')->toArray())->with('user')->get();
                return view('finances.employeeOfTheWeek.subViewEmployeeOfTheWeekConfirmation')
                    ->with('employeesOfTheWeek',$employeesOfTheWeek->sortBy('first_day_week'))
                    ->with('employeesOfTheWeekRankings',$employeesOfTheWeekRankings)
                    ->with('criterionHeader', '% udanych (prowizja[zł])');
            }else if($userTypeId == 1 && $departmentTypeId == 1){        //confirmation consultants
                $employeesOfTheWeek = $this->getEmployeesOfTheWeek($userTypeId, $departmentTypeId, $departmentInfoId, $trainerId, $dividedMonth, 2);
                $clientRouteInfo = $this->getConfirmationConsultantsRoutesInformation([$departmentInfoId], $dividedMonth, $trainerId);
                $this->updateConfirmationRanking($employeesOfTheWeek->where('accepted',0), $clientRouteInfo, $dividedMonth, $departmentInfoId, [100,50], 'confirmingUser', ['successfulPct','provision']);

                $employeesOfTheWeekRankings = EmployeeOfTheWeekRanking::whereIn('employee_of_the_week_id',$employeesOfTheWeek->pluck('id')->toArray())->with('user')->get();
                return view('finances.employeeOfTheWeek.subViewEmployeeOfTheWeekConfirmation')
                    ->with('employeesOfTheWeek',$employeesOfTheWeek->sortBy('first_day_week'))
                    ->with('employeesOfTheWeekRankings',$employeesOfTheWeekRankings)
                    ->with('criterionHeader', '% udanych (prowizja[zł])');
            }else{
                return 'noView';
            }
        }else{
            return view('errors.404');
        }
    }

    private function getEmployeesOfTheWeek($userTypeId, $departmentTypeId, $departmentInfoId, $coachId, $dividedMonth, $bonusCount){
        $employeesOfTheWeek = EmployeeOfTheWeek::where('user_type_id', $userTypeId)
            ->where('first_day_week','>=',$dividedMonth[0]->firstDay)
            ->where('last_day_week','<=',$dividedMonth[count($dividedMonth) - 1]->lastDay)
            ->where('department_type_id', $departmentTypeId);
        if($departmentInfoId !== null){
            $employeesOfTheWeek->where('department_info_id', $departmentInfoId)
                ->with('department_info');
        }
        if($coachId !== null){
            $employeesOfTheWeek->where('coach_id', $coachId);
        }

        $employeesOfTheWeek = $employeesOfTheWeek->get();
        foreach($dividedMonth as $week){
            $employeeOfTheWeek = $employeesOfTheWeek->where('first_day_week',$week->firstDay)->where('last_day_week',$week->lastDay);
            if(count($employeeOfTheWeek) == 0){
                $employeeOfTheWeek = new EmployeeOfTheWeek();
                $employeeOfTheWeek->user_type_id = $userTypeId;
                $employeeOfTheWeek->department_info_id = $departmentInfoId;
                $employeeOfTheWeek->first_day_week = $week->firstDay;
                $employeeOfTheWeek->last_day_week = $week->lastDay;
                $employeeOfTheWeek->employees_with_bonus = $bonusCount;
                $employeeOfTheWeek->coach_id = $coachId;
                $employeeOfTheWeek->department_type_id = $departmentTypeId;
                $employeeOfTheWeek->save();
                $employeesOfTheWeek->push($employeeOfTheWeek);
            }
        }
        return $employeesOfTheWeek;
    }
    private function updateConfirmationRanking($employeesOfTheWeek, $clientRouteInfo, $dividedMonth, $departmentInfoId, $bonusArr, $secondGroup, $criterion = ['successfulPct','shows']){
        EmployeeOfTheWeekRanking::whereIn('employee_of_the_week_id',$employeesOfTheWeek->pluck('id')->toArray())->delete();

        $confirmationStatistics = ConfirmationStatistics::getConsultantsConfirmationStatisticsForMonth($clientRouteInfo, $dividedMonth, $secondGroup)['sums'];
        foreach ($confirmationStatistics as $confirmationStatisticWeek){
            foreach ($employeesOfTheWeek as $employeeOfTheWeek){
                if($confirmationStatisticWeek->firstDay == $employeeOfTheWeek->first_day_week && $confirmationStatisticWeek->lastDay == $employeeOfTheWeek->last_day_week){
                    $confirmationStatisticWeekArr = [];
                    foreach($confirmationStatisticWeek->secondGrouping->where('shows','>',3)->sortByDesc($criterion[0]) as $secondGroupingSum){
                        array_push($confirmationStatisticWeekArr, $secondGroupingSum);
                    }
                    $groupStartIndex = 0;

                    //BUBBLE sorting after sorting on first criterion
                    for($i = 1; $i < count($confirmationStatisticWeekArr); $i++){
                        if($confirmationStatisticWeekArr[$i]->{$criterion[0]} !== $confirmationStatisticWeekArr[$i-1]->{$criterion[0]}){
                            $endGroupElementIndex = $i-1;
                            while($endGroupElementIndex > $groupStartIndex){
                                for ($j = $groupStartIndex+1; $j <= $endGroupElementIndex; $j++){
                                    try{
                                        if($confirmationStatisticWeekArr[$j-1]->{$criterion[1]} <=
                                            $confirmationStatisticWeekArr[$j]->{$criterion[1]} ) {
                                            $temp = clone $confirmationStatisticWeekArr[$j-1];
                                            $confirmationStatisticWeekArr[$j-1] = $confirmationStatisticWeekArr[$j];
                                            $confirmationStatisticWeekArr[$j] = $temp;
                                        }
                                    }catch(\ErrorException $e){
                                        dd($e, $groupStartIndex, $endGroupElementIndex);
                                    }
                                }
                                $endGroupElementIndex--;
                            }
                            $groupStartIndex = $i;
                        }
                    }

                    $rankingPositionCounter = 0;
                    foreach ($confirmationStatisticWeekArr as $secondGroupingSum){
                        $employeeOfTheWeekRanking = new EmployeeOfTheWeekRanking();
                        $employeeOfTheWeekRanking->employee_of_the_week_id = $employeeOfTheWeek->id;
                        $employeeOfTheWeekRanking->user_id = $secondGroupingSum->secondGroup;
                        $employeeOfTheWeekRanking->bonus = array_key_exists($rankingPositionCounter, $bonusArr) ? $bonusArr[$rankingPositionCounter] : 0;
                        $rankingPositionCounter += 1;
                        $employeeOfTheWeekRanking->ranking_position = $rankingPositionCounter;
                        $employeeOfTheWeekRanking->criterion = $secondGroupingSum->{$criterion[0]}.' ('.$secondGroupingSum->{$criterion[1]}.')';
                        $employeeOfTheWeekRanking->save();
                    }
                }
            }
        }

    }
    private function getConfirmationConsultantsRoutesInformation($departmentInfoIdArray, $dividedMonth, $trainerId = null) {
        $clientRouteInfo = ClientRouteInfo::select(
            DB::raw('concat(users.first_name," ",users.last_name) as confirmingUserName'),
            DB::raw('concat(trainer.first_name," ",trainer.last_name) as confirmingUserTrainerName'),
            'confirmingUser',
            'confirmDate',
            'frequency',
            'pairs',
            'actual_success',
            'users.department_info_id',
            'users.coach_id',
            'users.login_phone'
        )
            ->join('users','confirmingUser', '=', 'users.id')
            ->join('department_info as di', 'users.department_info_id','=','di.id')
            ->join('users as trainer','users.coach_id','=','trainer.id')
            ->where('confirmDate', '>=', $dividedMonth[0]->firstDay)
            ->where('confirmDate', '<=', $dividedMonth[count($dividedMonth)-1]->lastDay)
            ->whereIn('users.department_info_id', $departmentInfoIdArray)
            ->where('di.id_dep_type',1)
            ->whereNotNull('confirmingUser')
            ->whereNotNull('users.coach_id');
        if($trainerId !== null){
            $clientRouteInfo->where('users.coach_id', $trainerId);
        }

        return $clientRouteInfo->get();
    }

    public function acceptBonusEmployeeOfTheWeekAjax(Request $request){
        if($request->ajax()){
            $employeeOfTheWeekId = $request->employeeOfTheWeekId;
            $bonusInfo = $request->bonusInfo;

            EmployeeOfTheWeek::where('id',$employeeOfTheWeekId)->update(['accepted' => 1, 'accepted_by_user_id' => Auth::user()->id]);

            if(is_array($bonusInfo) and count($bonusInfo)>0){
                $employeeOfTheWeek = EmployeeOfTheWeek::where('id',$employeeOfTheWeekId)->first();

                $userType = UserTypes::where('id',$employeeOfTheWeek->user_type_id)->first();
                new ActivityRecorder(array_merge(['T'=>'Akceptacja '.$userType->name.' tygodnia'], ['employeeOfTheWeekId'=>$employeeOfTheWeekId, 'bonusInfo' => $bonusInfo]), 10, 1);

                EmployeeOfTheWeekRanking::where('employee_of_the_week_id',$employeeOfTheWeekId)->update(['bonus' => 0]);
                $employeesOfTheWeekRanking = EmployeeOfTheWeekRanking::where('employee_of_the_week_id',$employeeOfTheWeekId)->get();
                foreach ($bonusInfo as $employeeBonus){
                    $employeeOfTheWeekRankingWithBonus = $employeesOfTheWeekRanking->where('user_id', $employeeBonus['userId'])->first();
                    $employeeOfTheWeekRankingToSwap = $employeesOfTheWeekRanking->where('ranking_position', $employeeBonus['bonusPosition'])->first();

                    $employeeOfTheWeekRankingToSwap->ranking_position = $employeeOfTheWeekRankingWithBonus->ranking_position;
                    $employeeOfTheWeekRankingToSwap->save();

                    $employeeOfTheWeekRankingWithBonus->ranking_position = $employeeBonus['bonusPosition'];
                    $employeeOfTheWeekRankingWithBonus->bonus = abs($employeeBonus['bonus']);
                    $employeeOfTheWeekRankingWithBonus->save();

                    $penaltyBonus = new PenaltyBonus();
                    $penaltyBonus->type = 2;
                    $penaltyBonus->id_user = $employeeBonus['userId'];
                    $penaltyBonus->amount = abs($employeeBonus['bonus']);
                    $penaltyBonus->comment = $userType->name.
                        ' tygodnia: '.
                        date('Y.m.d',strtotime($employeeOfTheWeek->first_day_week)).
                        ' - '.
                        date('Y.m.d',strtotime($employeeOfTheWeek->last_day_week)).
                        ' Miejsce #'.$employeeBonus['bonusPosition'];
                    $penaltyBonus->id_manager = Auth::user()->id;
                    $penaltyBonus->event_date = $employeeOfTheWeek->last_day_week;
                    $penaltyBonus->save();
                }
            }
            return 'success';
        }
        return 'fail';
    }

    private function recountSalaryForEmployers(&$salary, $dividedMonth, $date, $date_to_post)
    {
        $freeDaysData = $this->getFreeDays($dividedMonth); //[id_user, freeDays]
//        dd($freeDaysData);

        /**
         * Pobranie danych osób którzy nie pracowali całego miesiąca
         */
        $days_in_month = date('t', strtotime($date_to_post . "-01"));

        //Zdefiniownie ostatniego dnia miesiąca
        $last_day = $date_to_post. '-' . $days_in_month;
        //zdefiniowanie pierwszego dnia miesiaca
        $first_day = $date_to_post . '-01';

        /**
         * Puste tablice przechowujące dane osob ktorych pensja musi się zmienic
         */
        $working_days = [];
        $work_days_stop = [];
        $work_days_in_between = [];

        /**
         * Pobranie danych osob ktore rozpoczeły prace w tym miesiącu
         */
        $users_by_start = DB::table('users')
            ->select(DB::raw("
                id,
                start_work,
                salary
            "))
            ->where('start_work', 'like', $date)
            ->whereNotIn('user_type_id', [1, 2, 9])
            ->get();

        /**
         * Obliczenie średniej dziennej pensji oraz ilosci przepracowanych dni
         */
        foreach ($users_by_start as $item) {

            $date_diff = strtotime($last_day) - strtotime($item->start_work);

            $user_salary_per_day = $item->salary / $days_in_month;

            $user_salary = $user_salary_per_day * (($date_diff / 3600 / 24) + 1);

            $working_days[$item->id] = round($user_salary, 0);
        }

        foreach ($salary as $user) {
            foreach ($working_days as $key => $item) {
                if ($user->id == $key) {
                    $user->salary = $item;
                }
            }
        }

        /**
         * Pobranie danych osob ktore zakończyły prace w tym miesiącu
         */
        $users_by_stop = DB::table('users')
            ->select(DB::raw("
                id,
                end_work,
                salary
            "))
            ->where('end_work', 'like', $date)
            ->whereNotIn('user_type_id', [1, 2])
            ->get();

        /**
         * Obliczenie średniej dziennej pensji oraz ilosci przepracowanych dni
         */
        foreach ($users_by_stop as $item) {

            $date_diff = strtotime($item->end_work) - strtotime($first_day);

            $user_salary_per_day = $item->salary / $days_in_month;

            $user_salary = $user_salary_per_day * (($date_diff / 3600 / 24) + 1);

            $work_days_stop[$item->id] = round($user_salary, 0);
        }


        foreach ($salary as $value) {
            foreach ($work_days_stop as $key => $item) {
                if ($value->id == $key) {
                    $value->salary = $item;
                }
            }
        }

        /**
         * Pobranie danych osób które rozpoczeły i zakończyły prace w danym miesiącu
         */
        $in_between = DB::table('users')
            ->select(DB::raw("
                id,
                start_work,
                end_work,
                salary
            "))
            ->where('end_work', 'like', $date)
            ->where('start_work', 'like', $date)
            ->whereNotIn('user_type_id', [1, 2, 9])
            ->get();

        /**
         * Obliczenie średniej dziennej pensji oraz ilosci przepracowanych dni
         */
        foreach ($in_between as $item) {

            $date_diff = strtotime($item->end_work) - strtotime($item->start_work);

            $user_salary_per_day = $item->salary / $days_in_month;

            $user_salary = $user_salary_per_day * (($date_diff / 3600 / 24) + 1);

            $work_days_in_between[$item->id] = round($user_salary, 0);
        }

        foreach ($salary as $value) {
            foreach ($work_days_in_between as $key => $item) {
                if ($value->id == $key) {
                    $value->salary = $item;
                }
            }
        }

        foreach ($salary as $onePerson) {
            $flag = false;
            foreach ($freeDaysData as $freeDayData) {
                if ($onePerson->id == $freeDayData['id_user']) {
                    $flag = true;
                    $onePerson->salary -= $onePerson->average_salary * $freeDayData['freeDays'];
                    $onePerson->freeDays = $freeDayData['freeDays'];
                }
            }
            if (!$flag) {
                $onePerson->freeDays = 0;
            }
        }
    }

    public function acceptedPaymentSystemUpdate(){
        if(!in_array(Auth::user()->id,[6964])){
            return Redirect::to('/');
        }
        $monthStart = '2017-06';
        $monthEnd = '2018-11';
        $month = $monthStart;
        $months = [];
        while($month !== $monthEnd){
            $monthToPush = date('Y-m',strtotime($month));
            array_push($months, $monthToPush);
            $month = date('Y-m',strtotime('+1 month', strtotime($month)));

        }
        return view('admin.acceptedPaymentSystem')
            ->with('months', $months);
    }

    public function acceptedPaymentSystemUpdateCadreAjax(Request $request){
        if($request->ajax()){
            //Natalia Skwarek months
            $skwarekMonths = ['manager_id'=> 3898,'startMonth'=>'2017-06','endMonth'=>'2018-05'];
            //MagdalenaCeglarska months
            $ceglarskaMonths = ['manager_id'=> 7011,'startMonth'=>'2018-06','endMonth'=>'2018-07'];
            //Kinga Dygas months
            $dygasMonths = ['manager_id'=> 7551,'startMonth'=>'2017-08','endMonth'=>'2018-08'];
            //Aleksandra Drewin months
            $drewinMonths = ['manager_id'=> 921,'startMonth'=>'2017-09','endMonth'=>'2018-10'];

            $acceptors = [$skwarekMonths, $ceglarskaMonths, $dygasMonths, $drewinMonths];
            $month = $request->month;
            $date = $month.'%';
            $departments = Department_info::with('departments')->with('department_type')->get();
            $users = DB::table(DB::raw("users"))
                ->whereNotIn('users.user_type_id', [1, 2, 9])
                ->where('users.salary', '>', 0)
                ->selectRaw('
            `users`.`id`,
            `users`.`user_type_id`,
            `users`.`agency_id`,
            `users`.`max_transaction`,
            `users`.`first_name`,
            `users`.`last_name`,
            `users`.`salary_to_account`,
            `users`.`username`,
            `department_type`.`id` as department_type_id,
            `departments`.`name` as dep_name, 
            `department_type`.`name`  as dep_type,
            `department_type`.`id` as dep_type_id,
            `department_info`.`id` as department_info_id,
            `users`.`salary`,
            `users`.`rate`,
            `users`.`additional_salary`,
            `users`.`student`,
            `users`.`documents`,
            `users`.`promotion_date`,
            `users`.`start_work`,
            `users`.`main_department_id`')
                ->where(function ($query) use ($date) {
                    $query->orwhere(DB::raw('SUBSTRING(promotion_date,1,7)'), '<', substr($date, 0, strlen($date) - 1))
                        ->orwhere('users.promotion_date', '=', null);
                })
                ->join('department_info', 'department_info.id', 'users.main_department_id')
                ->join('work_hours', 'work_hours.id_user', 'users.id')
                ->join('departments', 'departments.id', 'department_info.id_dep')
                ->join('department_type', 'department_type.id', 'department_info.id_dep_type')
                ->where('work_hours.date', 'like', $date)
                ->groupBy('users.id')
                ->orderBy('users.last_name')->get();
            foreach ($acceptors as $acceptor){
                if(strtotime($acceptor['startMonth']) <= strtotime($month) && strtotime($acceptor['endMonth']) >= strtotime($month) ){
                    if(count($users) == 0){
                        $penaltyBonus = PenaltyBonus::select('penalty_bonus.*','users.main_department_id')->join('users','users.id','id_user')
                            ->whereNotIn('users.user_type_id',[1, 2, 9])
                            ->where('status', 1)
                            ->where('event_date','like',$date)->get();
                        if(count($penaltyBonus)>0){
                            foreach ($departments as $department){
                                $depPenaltyBonus = $penaltyBonus->where('main_department_id', $department->id);
                                if(count($depPenaltyBonus)>0){
                                    $acceptedPayment = AcceptedPayment::where('department_info_id',$department->id)->where('payment_month','like', $date)->where('cadrePayment',1)->first();
                                    if($acceptedPayment == null){
                                        $acceptedPayment = new AcceptedPayment();
                                        $acceptedPayment->cadre_id = $acceptor['manager_id'];
                                        $acceptedPayment->payment_month = $month.'-01';
                                        $acceptedPayment->department_info_id = $department->id;
                                        $acceptedPayment->cadrePayment = 1;
                                        $acceptedPayment->created_at = date('Y-m-d H:i:s');
                                        $acceptedPayment->updated_at = date('Y-m-d H:i:s');
                                        $acceptedPayment->save();
                                    }
                                    foreach ($depPenaltyBonus as $bonus){
                                        $bonus->accepted_payment_id = $acceptedPayment->id;
                                        $bonus->save();
                                    }
                                }
                            }
                        }
                    }else{
                        $acceptedPaymentUserStoryInsertQueryArr = [];
                        $penaltyBonus = PenaltyBonus::select('penalty_bonus.*','users.department_info_id')
                            ->join('users','users.id','id_user')
                            ->whereNotIn('users.user_type_id',[1,2,9])
                            ->where('status', 1)
                            ->where('event_date','like',$date)->get();
                        foreach ($departments as $department) {
                            $depUsers = $users->where('department_info_id',$department->id);
                            if(count($depUsers) > 0){
                                $acceptedPayment = AcceptedPayment::where('department_info_id',$department->id)->where('payment_month','like', $date)->where('cadrePayment',1)->first();
                                if($acceptedPayment == null){
                                    $acceptedPayment = new AcceptedPayment();
                                    $acceptedPayment->cadre_id = $acceptor['manager_id'];
                                    $acceptedPayment->payment_month = $month.'-01';
                                    $acceptedPayment->department_info_id = $department->id;
                                    $acceptedPayment->cadrePayment = 1;
                                    $acceptedPayment->created_at = date('Y-m-d H:i:s');
                                    $acceptedPayment->updated_at = date('Y-m-d H:i:s');
                                    $acceptedPayment->save();
                                }
                                foreach($depUsers as $user){
                                    array_push($acceptedPaymentUserStoryInsertQueryArr,[
                                        'accepted_payment_id'   => $acceptedPayment->id,
                                        'user_id'               => $user->id,
                                        'salary'                => $user->salary,
                                        'rate'                  => $user->rate,
                                        'additional_salary'     => $user->additional_salary,
                                        'student'               => $user->student,
                                        'salary_to_account'     => $user->salary_to_account,
                                        'max_transaction'       => $user->max_transaction,
                                        'department_info_id'    => $user->department_info_id,
                                        'agency_id'             => $user->agency_id,
                                        'user_type_id'          => $user->user_type_id,
                                        'created_at'            => date('Y-m-d H:i:s'),
                                        'updated_at'            => date('Y-m-d H:i:s')]);
                                }
                                $depPenaltyBonus = $penaltyBonus->whereIn('department_info_id', $department->id);
                                if(count($depPenaltyBonus)>0){
                                    foreach ($depPenaltyBonus as $bonus){
                                        $bonus->accepted_payment_id = $acceptedPayment->id;
                                        $bonus->save();
                                    }
                                }
                            }
                        }
                        DB::table('accepted_payment_user_story')->insert($acceptedPaymentUserStoryInsertQueryArr);
                    }
                }
            }
            return $month.' accepted';
        }
        return Redirect::to('/');
    }

    public function acceptedPaymentSystemUpdateAjax(Request $request){
        if($request->ajax()){
            $month = $request->month;
            $date = $month.'%';
            $departments = Department_info::all();
            $salary = $this->getSalary($date, $departments->pluck('id')->toArray());
            if(count($salary) > 0){
                $departmentInfoIds = [];
                $consultantsIds = [];
                $successorsIds = [];
                foreach ($salary as $agency) {
                    $departmentInfoIds = array_merge($departmentInfoIds,$agency->pluck('department_info_id')->unique()->toArray());
                    $consultantsIds = array_merge($consultantsIds,$agency->pluck('id')->unique()->toArray());
                    $successorsIds = array_merge($successorsIds,$agency->where('successorUserId','!=',null)->pluck('successorUserId')->toArray());
                }
                $departmentInfoIds = collect($departmentInfoIds)->unique()->toArray();
                $consultantsIds = collect($consultantsIds)->unique()->toArray();
                $successorsIds = collect($successorsIds)->unique()->toArray();
                if(count($successorsIds)>0){
                    dd($month, $successorsIds);
                }

                $acceptedPayment = AcceptedPayment::whereIn('department_info_id', $departmentInfoIds)
                    ->where('payment_month','like',$date.'-01')
                    ->whereNull('cadrePayment')
                    ->get();
                $bonus_penalty = PenaltyBonus::select('penalty_bonus.*','users.department_info_id')
                    ->join('users','users.id','id_user')
                    ->whereIn('users.user_type_id',[1,2,9])
                    ->where('status', 1)
                    ->where('event_date','like',$date)
                    ->get();
                foreach ($departmentInfoIds as $departmentInfoId){
                    $acceptedPayment = $acceptedPayment->where('department_info_id',$departmentInfoId)->first();
                    /*if($acceptedPayment == null){
                        $acceptedPayment = new AcceptedPayment();
                        $acceptedPayment->cadre_id = Auth::user()->id;
                        $acceptedPayment->payment_month = $date.'-01';
                        $acceptedPayment->department_info_id = $departmentInfoId;
                        $acceptedPayment->cadrePayment = null;
                        $acceptedPayment->created_at = date('Y-m-d H:i:s');
                        $acceptedPayment->updated_at = date('Y-m-d H:i:s');
                        $acceptedPayment->save();
                    }*/
                    //$bonus_penalty->where('department_info_id',$departmentInfoId)->update(['accepted_payment_id'=>$acceptedPayment->id]);
                }
            }
            return $month.' accepted';
        }
        return Redirect::to('/');
    }
}
