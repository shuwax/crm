<?php
/**
 * Created by PhpStorm.
 * User: veronaprogramista
 * Date: 17.10.18
 * Time: 13:52
 */

namespace App\Http\Controllers\Statistics;


use App\Notifications;
use App\User;
use App\Utilities\Dates\MonthFourWeeksDivision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ITNotificationStatisticsController
{
    public function iTNotificationStatisticsGet(){
        return view('statistics.iTNotificationStatistics');
    }

    public function iTNotificationStatisticsData($monthIntoCompanyWeeksDivision){
        $iTRealizedNotificationStatistics = Notifications::select('displayed_by',
            DB::raw('cast(data_stop as date) as date_stop'),                //from datetime to date
            DB::raw('count(notifications.id) as notificationsCount'),       //notifications count realized
            DB::raw('case when count(nr.id) = 0 then 0 else round(sum(case when nr.id is not null then average_rating else 0 end)/count(nr.id),4) end as average_rating'),
            DB::raw('count(nr.id) as notificationsRatedCount')
        )
            ->leftJoin('notification_rating as nr','nr.notification_id','notifications.id')
            ->whereNotNull('displayed_by')
            ->groupBy('displayed_by')
            ->groupBy(DB::raw('cast(data_stop as date)'))
            ->where('status','>',0)
            ->whereNotNull('data_stop')
            ->whereBetween('data_stop',[$monthIntoCompanyWeeksDivision[0]->firstDay, $monthIntoCompanyWeeksDivision[count($monthIntoCompanyWeeksDivision)-1]->lastDay])
            ->get();
        $iTUnrealizedNotificationStatistics = Notifications::select('displayed_by',
            DB::raw('cast(data_start as date) as date_start'),
            DB::raw('cast(data_stop as date) as date_stop'))
            ->whereNotNull('displayed_by')
            ->where(function ($query) use ($monthIntoCompanyWeeksDivision){
                $query->where(DB::raw('cast(data_start as date)'),'<>',DB::raw('cast(data_stop as date)'))
                    ->where(function ($query) use($monthIntoCompanyWeeksDivision) {
                        $query->whereBetween('data_stop',[$monthIntoCompanyWeeksDivision[0]->firstDay, $monthIntoCompanyWeeksDivision[count($monthIntoCompanyWeeksDivision)-1]->lastDay])
                            ->orWhere(function ($query) use ($monthIntoCompanyWeeksDivision){
                                $query->whereBetween('data_start',[$monthIntoCompanyWeeksDivision[0]->firstDay, $monthIntoCompanyWeeksDivision[count($monthIntoCompanyWeeksDivision)-1]->lastDay]);
                            });
                    })
                    ->orWhere(function ($query){
                        $query->whereNull('data_stop')
                        ->where('status','<>',0);
                    });
            })
            ->get();

        //dd($iTRealizedNotificationStatistics->sortBy('date_stop')->groupBy('displayed_by')->toArray());
        //dd($iTUnrealizedNotificationStatistics->groupBy('displayed_by')->toArray());
        return ['iTRealizedNotificationStatistics' => $iTRealizedNotificationStatistics ,'iTUnrealizedNotificationStatistics' => $iTUnrealizedNotificationStatistics];
    }

    public function iTNotificationsStatisticsDataToView($view, $month){
        $monthIntoCompanyWeeksDivision = MonthFourWeeksDivision::get(date('Y',strtotime($month)), date('m',strtotime($month)));
        $iTNotificationStatisticsData = $this->iTNotificationStatisticsData($monthIntoCompanyWeeksDivision);
        return $view->with('iTRealizedNotificationStatistics',$iTNotificationStatisticsData['iTRealizedNotificationStatistics'])
            ->with('iTUnrealizedNotificationStatistics', $iTNotificationStatisticsData['iTUnrealizedNotificationStatistics']);
    }


    public function iTNotificationsStatisticsAjax(Request $request){
        $month = $request->selectedMonth;
        $monthIntoCompanyWeeksDivision = MonthFourWeeksDivision::get(date('Y',strtotime($month)), date('m',strtotime($month)));
        $monthIntoCompanyWeeksDivision[0]->firstDay = $month.'-01';
        $iTNotificationStatisticsData = $this->iTNotificationStatisticsData($monthIntoCompanyWeeksDivision);
        $ITids = collect(array_merge($iTNotificationStatisticsData['iTRealizedNotificationStatistics']->pluck('displayed_by')->unique()->toArray(),
            $iTNotificationStatisticsData['iTUnrealizedNotificationStatistics']->pluck('displayed_by')->unique()->toArray()))->unique();
        $programmers = User::select('id','first_name','last_name')->whereIn('id', $ITids)->get();
        return ['programmers'=> $programmers,
            'iTRealizedNotificationStatistics' => $iTNotificationStatisticsData['iTRealizedNotificationStatistics']->groupBy('displayed_by'),
            'iTUnrealizedNotificationStatistics' => $iTNotificationStatisticsData['iTUnrealizedNotificationStatistics']->groupBy('displayed_by'),
            'monthIntoCompanyWeeksDivision' => $monthIntoCompanyWeeksDivision];
    }
}