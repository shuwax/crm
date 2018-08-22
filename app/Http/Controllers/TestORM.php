<?php

namespace App\Http\Controllers;

use App\CsvReader;
use App\Department_info;
use App\HourRepoerOtherCompany;
use App\HourReport;
use App\PBXDKJTeam;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Request;


class TestORM extends Controller
{
    private $report_type_array = array('TEAM','TERMINAL','PRESENTATION');
    public function test()
    {

        $today = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));

        $hour_stop = $today . ' ' . '23:00:00'; //tutaj zmienic przy wydawaniu na produkcję na  date('H') - 1
        $hour_start = $today . ' ' . '00:00:00';

        $lp = 0;
        $dont_save = false;
        $report_type = 0;
        $department_id = null;
        $url = "https://vc.e-pbx.pl/callcenter/api/statistic-report?statType=23&groupType=" . $this->report_type_array[$report_type];
//        $url ="https://vc.e-pbx.pl/callcenter/api/statistic-report?statType=23&groupType=TEAM&date=2018-04-26";
        $header_array = array('department_info_id', 'average', 'success', '', '', 'wear_base', 'call_time', 'employee_count', 'user_id', 'hour', 'report_date', 'is_send','all_rates', 'all_bad_rates', 'janky_count',  );
        if (!ini_set('default_socket_timeout', 15)) echo "<!-- unable to change socket timeout -->";
        if (($handle = fopen($url, "r")) !== FALSE) {
            while (($data1 = fgetcsv($handle, 1000, ";")) !== FALSE) {
                if ($lp > 2) {
                    $dont_save = false;
                    $save_other_company = false;
                    $i = 0;
                    $typ = 0;
                    $janky = 0;
                    $count_cehck =0;
                    $all_rates = 0;
                    $all_bad_rates = 0;
                    foreach ($data1 as $item) {
                        if ($i == 0) {
                            preg_match_all('!\d+!', $item, $matches);
                            if (!empty($matches[0])) {
                                if ($matches[0][0] != 0)
                                {
                                    // id_department dodać orm
                                    $department_id = Department_info::where('pbx_id',$matches[0][0])->first();
                                    if(isset($department_id->id) && $department_id->id != null){
                                        $spreadsheet_data[$lp][$header_array[$i]] = $department_id->id;
                                    }else if($matches[0][0] == 108){
                                        $spreadsheet_data[$lp][$header_array[$i]] = 108;
                                    }else{
                                        $dont_save = true;
                                    }
                                }
                                else
                                    $dont_save = true;
                            }else
                                $dont_save = true;

                        } else if (!$dont_save) {
                            if ($item == '-' || $item == 'null')
                                $item = 0;
                            if ($i == 1 || $i == 2 || $i == 7) {
                                $spreadsheet_data[$lp][$header_array[$i]] = utf8_encode($item);
                            }
                            if($i == 12 || $i == 13) {
                                $spreadsheet_data[$lp][$header_array[$i]] = utf8_encode($item);
                                if($i == 13) {
                                    $all_bad_rates = utf8_encode($item);
                                }
                                if($i == 12) {
                                    $all_rates = utf8_encode($item);
                                }

                                //janky part
                                if($all_rates == 0) {
                                    $janky_proc_value = 0;
                                }
                                else {
                                    $janky_proc_value = round(100 * $all_bad_rates / $all_rates ,2);
                                }
                                $spreadsheet_data[$lp]['janky_count'] = $janky_proc_value;
                                //end janky part
                            }

                            if ($i == 5 || $i == 6) {
                                $split = explode(" ", $item);
                                $item = $split[0];
                                $spreadsheet_data[$lp][$header_array[$i]] = utf8_encode($item);
                            }
                            $spreadsheet_data[$lp]['user_id'] = 1364;
                            $spreadsheet_data[$lp]['hour'] = date('H:i') . ':00';
                            $spreadsheet_data[$lp]['report_date'] = date('Y-m-d');
                            $spreadsheet_data[$lp]['is_send'] = 0;

                            if($i == 7)
                            {
                                if($spreadsheet_data[$lp]['average'] != 0){
                                    $spreadsheet_data[$lp]['hour_time_use'] = round($spreadsheet_data[$lp]['success']/$spreadsheet_data[$lp]['average'],2);
                                }else{
                                    $spreadsheet_data[$lp]['hour_time_use'] = 0;
                                }
                            }
                        }
                        $i++;
                    }
                }
                $lp++;
            }
            fclose($handle);
        }
        $finall_array = [];
        // usunięcie gniezna z raportu godzinowego
        foreach ($spreadsheet_data as $item){
            if($item['department_info_id'] == 108){
                HourRepoerOtherCompany::insert($item);
            }else
                array_push($finall_array,$item);
        }

        HourReport::insert($finall_array);

    }
}
