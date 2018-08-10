<?php

namespace App\Http\Controllers;

use App\ActivityRecorder;
use App\AuditCriterions;
use App\AuditHeaders;
use App\Cities;
use App\ClientRouteCampaigns;
use App\Clients;
use App\ClientRoute;
use App\ClientRouteInfo;
use App\Department_info;
use App\Hotel;
use App\HotelsClientsExceptions;
use App\HotelsContacts;
use App\Http\StaticMemory;
use App\InvoiceStatus;
use App\PaymentMethod;
use App\PbxCrmInfo;
use App\Route;
use App\RouteInfo;
use App\User;
use App\Voivodes;
use DateTime;
use function foo\func;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use function MongoDB\BSON\toJSON;
use PhpParser\Node\Expr\Array_;
use Session;
use Symfony\Component\HttpKernel\Client;

class CrmRouteController extends Controller
{
    private $validHotelInvoiceTemplatesExtensions = ['pdf'];
    private $validCampaignInvoiceExtensions = ['pdf'];

//prawdopodobnie nieużywane metody
//    public function index()
//    {
//        $departments = Department_info::all();
//        $today = date('Y-m-d');
//        $today .= '';
//        $voivodes = Voivodes::all();
//        $year = date('Y',strtotime("this year"));
//        $numberOfLastYearsWeek = date('W',mktime(0, 0, 0, 12, 27, $year));
//        return view('crmRoute.index')
//            ->with('departments', $departments)
//            ->with('voivodes', $voivodes)
//            ->with('lastWeek', $numberOfLastYearsWeek)
//            ->with('today', $today);
//    }

//    /**
//     * This method saves new routes connected with client
//     */
//    public function indexPost(Request $request) {
//        //Get values from form elements
//        $voivode = $request->voivode;
//        $city = $request->city;
//        $hour = $request->hour;
//        $date = $request->date;
//        $clientType = $request->clientType; // 1 - badania, 2 - wysyłka
//        $clientIdNotTrimmed = $request->clientId;
//
//        //explode values into arrays
//        $voivodeArr = explode(',', $voivode);
//        $cityArr = explode(',', $city);
//        $hourArr = explode(',',$hour);
//        $clientId = explode('_',$clientIdNotTrimmed)[1];
//        $dateArr = explode(',',$date);
//
//        $loggedUser = Auth::user();
//
//        //New insertion into ClientRoute table
//        $clientRoute = new ClientRoute();
//        $clientRoute->client_id = $clientId;
//        $clientRoute->user_id = $loggedUser->id;
//        $clientRoute->status = 0;
//        $clientRoute->type = $clientType; // 1 - badania, 2 - wysyłka
//        $clientRoute->save();
//
//        //New insertions into ClientRouteInfo table
//        for($i = 0; $i < count($voivodeArr); $i++) {
//            for($j = 1; $j <= $hourArr[$i] ; $j++) { // for example if user type 2 hours, method will insert 2 insertions with given row.
//                $clientRouteInfo = new ClientRouteInfo();
//                $clientRouteInfo->client_route_id = $clientRoute->id;
//                $clientRouteInfo->city_id = $cityArr[$i];
//                $clientRouteInfo->voivode_id = $voivodeArr[$i];
//                $clientRouteInfo->date = $dateArr[$i];
//                $clientRouteInfo->verification = 0; // 0 - not set, 1 - set
//                $day = substr($dateArr[$i],8,2);
//
//                $month = substr($dateArr[$i],5,2);
//
//                $year = substr($dateArr[$i], 0,4);
//
//                $date = mktime(0, 0, 0, $month, $day, $year);
//                $weekOfYear = date('W',$date);
//                $clientRouteInfo->weekOfYear = $weekOfYear;
//                $clientRouteInfo->save();
//            }
//        }
//
//        new ActivityRecorder(array_merge(['T'=>'Dodanie trasy dla klienta'],$clientRoute->toArray()),209,1);
//        $request->session()->flash('adnotation', 'Trasa została pomyślnie przypisana dla klienta');
//
//        return Redirect::back();
//
//    }

//    /**
//     * This method saves new routes connected with client
//     */
//    public function indexEditPost(Request $request) {
////        dd($request);
//        //Get values from form elements
//        $voivode = $request->voivode;
//        $city = $request->city;
//        $hour = $request->hour;
//        $date = $request->date;
//        $type = $request->type;
//        $clientIdNotTrimmed = $request->clientId;
//
//        //explode values into arrays
//        $voivodeArr = explode(',', $voivode);
//        $cityArr = explode(',', $city);
//        $hourArr = explode(',',$hour);
//        $clientId = explode('_',$clientIdNotTrimmed)[1];
//        $dateArr = explode(',',$date);
//
//        $loggedUser = Auth::user();
//
////        dd($hourArr);
//        //New insertion into ClientRoute table
//        $clientRoute = ClientRoute::find($request->route_id);
//        $clientRoute->client_id = $clientId;
//        $clientRoute->user_id = $loggedUser->id;
//        $clientRoute->status = 0;
//        $clientRoute->type = $type;
//        $clientRoute->save();
//
//        ClientRouteInfo::where('client_route_id','=',$request->route_id)->delete();
//        //New insertions into ClientRouteInfo table
//        for($i = 0; $i < count($voivodeArr); $i++) {
//            for($j = 1; $j <= $hourArr[$i] ; $j++) { // for example if user type 2 hours, method will insert 2 insertions with given row.
//                $clientRouteInfo = new ClientRouteInfo();
//                $clientRouteInfo->client_route_id = $clientRoute->id;
//                $clientRouteInfo->city_id = $cityArr[$i];
//                $clientRouteInfo->voivode_id = $voivodeArr[$i];
//                $clientRouteInfo->date = $dateArr[$i];
//                $clientRouteInfo->verification = 0; // 0 - not set, 1 - set
//                $day = substr($dateArr[$i],8,2);
//
//                $month = substr($dateArr[$i],5,2);
//
//                $year = substr($dateArr[$i], 0,4);
//
//                $date = mktime(0, 0, 0, $month, $day, $year);
//                $weekOfYear = date('W',$date);
//                $clientRouteInfo->weekOfYear = $weekOfYear;
//                $clientRouteInfo->save();
//            }
//        }
//        $request->session()->flash('adnotation', 'Trasa została pomyślnie przypisana dla klienta');
//
//        new ActivityRecorder(array_merge(['T'=>'Dodanie trasy dla klienta'],$clientRoute->toArray()),212,1);
//
//        return Redirect::back();
//
//    }

//    /**
//     * This method shows specific route
//     */
//    public function specificRouteEditGet($id) {
//        $clients = Clients::all();
//        $cities = Cities::all();
//        $voivodes = Voivodes::all();
//        $departments = Department_info::all(); //niezbędne
//
//        $clientRouteInfo = ClientRouteInfo::select('client_route_info.id', 'voivodeship.name as voivode', 'client_route_info.voivode_id as voivode_id','city.name as city','city.name as cityName', 'client_route_info.city_id as city_id','client_route_info.hotel_price as hotel_price', 'client_route.client_id as client_id', 'client_route_info.client_route_id as client_route_id', 'client_route_info.date as date', 'client_route_info.hotel_id as hotel_id', 'client_route_info.hour as hour', 'client_route.type as type')
//            ->join('city', 'city.id', '=', 'client_route_info.city_id')
//            ->join('voivodeship', 'voivodeship.id', '=', 'client_route_info.voivode_id')
//            ->join('client_route', 'client_route.id', '=', 'client_route_info.client_route_id')
//            ->where('client_route_id', '=', $id)
//            ->where('client_route_info.status', '=', 1)
//            ->get();
//
//        $clientRoute = $this->getClientRouteGroupedByDateSortedByHour($id, $clientRouteInfo);
//        $routeInfo = new \stdClass;
//        $routeInfo->routeName = $this->createRouteName($clientRoute);
//        $routeInfo->firstDate = $clientRoute[0]->date;
//        $routeInfo->week =  $clientRoute[0]->weekOfYear;
//
//        $clientRouteInfoExtended = array();
//        $insideArr = array();
//        $cityId = null;
//        $flag = 0; //indices whether $insideArr push into $clientRouteInfoExtended 1 - push, 0 - don't push
//        $iterator = 0; //It count loops of foreach
//        $iteratorFinish = count($clientRouteInfo); // indices when condition inside foreach should push array into $clientRouteInfoExtended array.
//        $clientName = null;
//        $clientType = null;
//
//        foreach($clientRouteInfo as $info) {
//            if($iterator == 0) {
//                $clientType = $info->type;
//            }
//
//            if($cityId == null) {
//                $flag = 0;
//                $cityId = $info->city_id;
//            }
//            else if($info->city_id == $cityId) {
//                $flag = 0;
//                $cityId = $info->city_id;
//            }
//            else {
//                array_push($clientRouteInfoExtended, $insideArr);
//                $insideArr = [];
//                $flag = 1;
//                $cityId = $info->city_id;
//            }
//
//            $clientRId = $info->client_id;
//
//            $stdClass = new \stdClass();
//
//            foreach($clients as $client) {
//                if($info->client_id == $client->id) {
//                    $stdClass->clientName = $client->name;
//                }
//            }
//
//            $stdClass->client_route_id = $info->client_route_id;
//            $stdClass->city_id = $info->city_id;
//            $stdClass->voivode_id = $info->voivode_id;
//            $stdClass->date = $info->date;
//            $stdClass->hotel_id = $info->hotel_id;
//            $stdClass->hour = $info->hour;
//
//            array_push($insideArr, $stdClass);
//            if($flag == 1) {
//                $flag = 0;
//            }
//            if($iterator == ($iteratorFinish - 1)) {
//                array_push($clientRouteInfoExtended, $insideArr);
//            }
//            $iterator++;
//        }
//
//        $clientRouteInfo = collect($clientRouteInfoExtended);
//
//        $today = date('Y-m-d');
//        $today .= '';
//        $year = date('Y',strtotime("this year"));
//        $numberOfLastYearsWeek = date('W',mktime(0, 0, 0, 12, 27, $year));
//
//        $clientRouteInfo = $clientRouteInfo->sortByDesc('date');
//        $clientRouteInfoAll = ClientRouteInfo::select('client_route_info.date','client_route_info.city_id','city.grace_period')
//            ->join('city','city.id','client_route_info.city_id')
//            ->where('client_route_info.status', '=', 1)
//            ->get();
//        $clientRouteInfo->map(function($item) use($cities,$clientRouteInfoAll) {
//            $cityObject = $cities->where('id','=',$item[0]->city_id)->first();
//            $item[0]->cities = $this::findCityByDistance($cityObject, $item[0]->date,$clientRouteInfoAll,$cities);
//            return $item;
//        });
//        return view('crmRoute.editSpecificRoute')
//            ->with('departments', $departments)
//            ->with('voivodes', $voivodes)
//            ->with('lastWeek', $numberOfLastYearsWeek)
//            ->with('today', $today)
//            ->with('clientRouteInfo',$clientRouteInfo)
//            ->with('clientRId', $clientRId)
//            ->with('routeId',$id)
//            ->with('clientType', $clientType)
//            ->with('routeInfo', $routeInfo);
//    }

    public function addNewRouteTemplateGet() {
        $voivodes = Voivodes::all();

        return view('crmRoute.routeTemplates')->with('voivodes', $voivodes);
    }

    /**
     * This method saves new route template to database
     */
    public function addNewRouteTemplatePost(Request $request) {
        if($request->has('alldata')) {
            $allData = json_decode($request->alldata);
            $allData = array_reverse($allData);
            $routes = new Route();
            $routes->status = 1;
            $routes->save();

            $allCities = Cities::select('id','name')->get();

            $dayFlag = $allData[0]->day;

            $reverseNameArr = [];

            forEach($allData as $record) {
                $name = '';
                $name .=  $allCities->where('id', '=', $record->city)->first()->name . ' + ';
                if($record->day != $dayFlag) {
                    $name = substr($name, 0,strlen($name) - 3) . ' | ';
                }
                array_push($reverseNameArr, $name);
                $dayFlag = $record->day;

                $routes_info = new RouteInfo();
                $routes_info->routes_id = $routes->id;
                $routes_info->voivodeship_id = $record->voivode;
                $routes_info->city_id = $record->city;
                $routes_info->status = 1;
                $routes_info->day = $record->day;
                $routes_info->checkbox = $record->checkbox;
                $routes_info->save();
            }


            $fullName = '';
            $nameArr = array_reverse($reverseNameArr);

            foreach($nameArr as $key => $value) {
                $fullName .= $value;
            }

            $fullName = substr($fullName, 0,strlen($fullName) - 3); // removing last | in name

            $routes->name = $fullName;
            $routes->save();
            $request->session()->flash('adnotation', 'Szablon trasy został dodany pomyślnie!');

            $log = [
                'T' => 'Dodanie szablonu trasy',
                'Nazwa trasy' => $fullName,
                'Id trasy' => $routes->id
            ];

            new ActivityRecorder($log,228, 1);
        }
        else {
            $request->session()->flash('adnotation', 'Błąd nie udało się dodać szablonu trasy, spróbuj ponownie!');
        }
        return Redirect::to('/showRoutes');
    }

    /**
     * This method return view editRouteTemplates
     */
    public function editRouteTemplatesGet($id) {
        $voivodes = Voivodes::all();
        if(isset($id) && $id > 0) {
            $route = $this::prepareRouteTemplate($id);
        }

        return view('crmRoute.editRouteTemplates')
            ->with('voivodes', $voivodes)
            ->with('routeTemplate', $route);
    }

    public function editRouteTemplatesPost($id, Request $request) {
        if($request->has('alldata')) {
            $allData = json_decode($request->alldata);

            $allData = array_reverse($allData);
            $routes = new Route();
            $routes->status = 1;
            $routes->save();

            $allCities = Cities::select('id','name')->get();

            $dayFlag = $allData[0]->day;
            $reverseNameArr = [];


            forEach($allData as $record) {
                $name = '';
                $name .=  $allCities->where('id', '=', $record->city)->first()->name . ' + ';
                if($record->day != $dayFlag) {
                    $name = substr($name, 0,strlen($name) - 3) . ' | ';
                }
                array_push($reverseNameArr, $name);
                $dayFlag = $record->day;

                $routes_info = new RouteInfo();
                $routes_info->routes_id = $routes->id;
                $routes_info->voivodeship_id = $record->voivode;
                $routes_info->city_id = $record->city;
                $routes_info->status = 1;
                $routes_info->day = $record->day;
                $routes_info->checkbox = $record->checkbox;
                $routes_info->save();
            }


            $fullName = '';
            $nameArr = array_reverse($reverseNameArr);

            foreach($nameArr as $key => $value) {
                $fullName .= $value;
            }

            $fullName = substr($fullName, 0,strlen($fullName) - 3); // removing last | in name

            $routes->name = $fullName;
            $routes->save();
            $request->session()->flash('adnotation', 'Szablon trasy został edytowany pomyślnie!');

            $log = [
                'T' => 'Edycja szablonu trasy',
                'Nazwa trasy' => $fullName,
                'Id trasy' => $routes->id
            ];

            new ActivityRecorder($log,231, 1);
        }

        //changing status to inactive for old records
        RouteInfo::where('routes_id', '=', $id)->update(['status' => 0]);
        Route::where('id', '=', $id)->update(['status' => 0]);

        return Redirect::to('/showRoutes');
    }

    /**
     * This method saves new routes connected with client
     */
    public function assigningRoutesToClientsPost(Request $request) {
        if($request->has('alldata') && $request->has('clientInfo')) {
            $allData = json_decode($request->alldata);
            $allData = array_reverse($allData);
            $clientInfo = json_decode($request->clientInfo);

            $clientType = $clientInfo->clientType; // 1 - badania, 2 - wysyłka
            $clientId = $clientInfo->clientId;

            $loggedUser = Auth::user();

            //New insertion into ClientRoute table
            $clientRoute = new ClientRoute();
            $clientRoute->client_id = $clientId;
            $clientRoute->user_id = $loggedUser->id;
            $clientRoute->status = 1;
            $clientRoute->type = $clientType; // 1 - badania, 2 - wysyłka
            $clientRoute->save();

            $dateFlag = $allData[0]->date;
            $name = '';
            $allCities = Cities::select('id','name')->get();
            $reverseNameArr = [];

            foreach($allData as $show) {
                $name = '';
                $name .=  $allCities->where('id', '=', $show->city)->first()->name . ' + ';
                if($show->date != $dateFlag) {
                    $name = substr($name, 0,strlen($name) - 3) . ' | ';
                }
                array_push($reverseNameArr, $name);
                $dateFlag = $show->date;

                $clientRouteCampaigns = new ClientRouteCampaigns();

                for($i = $show->hours - 1; $i >= 0; $i--) { // for example if user type 2 hours, method will insert 2 insertions with given row.
                    $clientRouteInfo = new ClientRouteInfo();
                    $clientRouteInfo->client_route_id = $clientRoute->id;
                    $clientRouteInfo->city_id = $show->city;
                    $clientRouteInfo->voivode_id = $show->voivode;
                    $clientRouteInfo->date = $show->date;
                    $clientRouteInfo->status = 1;
                    $clientRouteInfo->show_order = $show->order;
                    $clientRouteInfo->verification = 0; // 0 - not set, 1 - set

                    $dateArr = explode('-', $show->date);
                    $day = $dateArr[2]; $month = $dateArr[1]; $year = $dateArr[0];
                    $date = mktime(0, 0, 0, $month, $day, $year);
                    $weekOfYear = date('W',$date);

                    $clientRouteInfo->weekOfYear = $weekOfYear;
                    $clientRouteInfo->checkbox = $show->checkbox;
                    $clientRouteInfo->save();

                    if($i == $show->hours - 1) {
                        $clientRouteCampaigns->client_route_info_id = $clientRouteInfo->id;
                        $clientRouteCampaigns->hour_count = $show->hours;
                        $clientRouteCampaigns->save();
                    }
                }
            }

            $fullName = '';
            $nameArr = array_reverse($reverseNameArr);

            foreach($nameArr as $key => $value) {
                $fullName .= $value;
            }

            $fullName = substr($fullName, 0,strlen($fullName) - 3); // removing last | in name
            $clientRoute->route_name = $fullName;
            $clientRoute->save();

            new ActivityRecorder(array_merge(['T'=>'Dodanie trasy dla klienta'],$clientRoute->toArray()),209,1);
            $request->session()->flash('adnotation', 'Trasa została pomyślnie przypisana dla klienta');
        }
        else {
            $request->session()->flash('adnotation', 'Błąd, trasa nie została przypisana do klienta');
        }

        return Redirect::back();
    }

    public function assigningRoutesToClientsGet() {
        $departments = Department_info::all();
        $today = date('Y-m-d');
        $today .= '';
        $voivodes = Voivodes::all();
        $year = date('Y',strtotime("this year"));
        $numberOfLastYearsWeek = date('W',mktime(0, 0, 0, 12, 27, $year));
        return view('crmRoute.assigningRoutesToClients')
            ->with('departments', $departments)
            ->with('voivodes', $voivodes)
            ->with('lastWeek', $numberOfLastYearsWeek)
            ->with('today', $today);
    }

    public function editAssignedRouteGet($id) {
        $voivodes = Voivodes::all();
        $client_route = ClientRoute::select('client.name as name', 'client.id as clientId', 'client_route.type as clientType')
            ->OnlyActiveClientRoutes()
            ->join('client', 'client_route.client_id', '=', 'client.id')
            ->where('client_route.id', '=', $id)
            ->first();

        $client_route_info = ClientRouteInfo::select(DB::raw(
        'client_route_info.city_id as cityId,
         COUNT(*) as hours,
         client_route_info.voivode_id as voivodeId,
         client_route_info.checkbox as checkbox,
         client_route_info.date as date
         '))
            ->where('client_route_id', '=', $id)
            ->where('status', '=', 1)
            ->groupBy('date', 'client_route_info.city_id', 'show_order')
            ->orderBy('date')
            ->orderBy('show_order')
            ->get();

//        dd($client_route_info);

        return view('crmRoute.editAssignedRoute')
            ->with('voivodes', $voivodes)
            ->with('clientRouteInfo', $client_route_info)
            ->with('client_route', $client_route);
    }

    public function editAssignedRoutePost($id, Request $request) {
        if($request->has('alldata') && $request->has('clientInfo')) {
            $allData = json_decode($request->alldata);
            $allData = array_reverse($allData);

            $clientInfo = json_decode($request->clientInfo);

            $client_route_info = ClientRouteInfo::select(DB::raw( //We are grouping records because we want to have similar grouping as in $allData.
                'client_route_info.city_id as cityId,
                 client_route_info.id as id,
                 COUNT(*) as hours,
                 client_route_info.voivode_id as voivodeId,
                 client_route_info.checkbox as checkbox,
                 client_route_info.date as date,
                 client_route_info.show_order as show_order
         '))
                ->where('client_route_id', '=', $id)
                ->where('status', '=', 1)
                ->groupBy('date', 'client_route_info.city_id', 'show_order')
                ->orderBy('date')
                ->get();

            //assigning toAdd as 1, for every record as initial value.
            foreach($allData as $show) {
                $show->toAdd = 1;
            }

            $client_route_info_with_flag = $client_route_info->map(function($item) use($allData) {

                $item->toChange = 1; // indices whether records should be updated
                //if foreach loop finds same object, it change flag "toChange" to 0. it means, it should not be modified
                foreach($allData as $show) {
                    if(($item->cityId == $show->city) && ($item->voivodeId == $show->voivode) && ($item->date == $show->date) && ($item->hours == $show->hours)) {
                        if($item->show_order == $show->order) {
                            $item->toChange = 0; //this group should not be updated about status

                            ClientRouteInfo::where('city_id', '=', $item->cityId) //we are updating static records about order value
                            ->where('voivode_id', '=', $item->voivodeId)
                                ->where('date', '=', $item->date)
                                ->where('status', '=', 1)
                                ->where('show_order', '=', $item->show_order)
                                ->update(['show_order' => $show->order]);

                            $item->show_order = $show->order;
                            $show->toAdd = 0;
                            break;
                        }
                    }

                }

                if($item->toChange == 1) { // we are updating all records from group of records
                    $allRecordsToUpdate = ClientRouteInfo::where('city_id', '=', $item->cityId)
                        ->where('voivode_id', '=', $item->voivodeId)
                        ->where('date', '=', $item->date)
                        ->where('status', '=', 1)
                        ->where('show_order', '=', $item->show_order)
                        ->update(['status' => 0]);
                }
                    return $item;
            });

            $clientRoute = ClientRoute::find($id);
            $clientRoute->update([
                'client_id' => $clientInfo->clientId,
                'type' => $clientInfo->clientType
            ]);
            //This part add modified shows or new shows
            foreach($allData as $show) {
                $clientRouteCampaigns = new ClientRouteCampaigns();

                for($i = $show->hours - 1; $i >= 0; $i--) { // for example if user type 2 hours, method will insert 2 insertions with given row.
                    if(!($show->toAdd == 0)) { // only for those, which should be added(without toAdd == 0)
                        $clientRouteInfo = new ClientRouteInfo();
                        $clientRouteInfo->client_route_id = $clientRoute->id;
                        $clientRouteInfo->city_id = $show->city;
                        $clientRouteInfo->voivode_id = $show->voivode;
                        $clientRouteInfo->date = $show->date;
                        $clientRouteInfo->status = 1;
                        $clientRouteInfo->show_order = $show->order;
                        $clientRouteInfo->verification = 0; // 0 - not set, 1 - set

                        $dateArr = explode('-', $show->date);
                        $day = $dateArr[2]; $month = $dateArr[1]; $year = $dateArr[0];
                        $date = mktime(0, 0, 0, $month, $day, $year);
                        $weekOfYear = date('W',$date);

                        $clientRouteInfo->weekOfYear = $weekOfYear;
                        $clientRouteInfo->checkbox = $show->checkbox;
                        $clientRouteInfo->save();

                        if($i == $show->hours - 1) {
                            $clientRouteCampaigns->client_route_info_id = $clientRouteInfo->id;
                            $clientRouteCampaigns->hour_count = $show->hours;
                            $clientRouteCampaigns->save();
                        }
                    }
                }
            }

            //this part from now is responsible for creating route name
            $client_route_info2 = ClientRouteInfo::select(DB::raw(
                'client_route_info.city_id as cityId,
                 COUNT(*) as hours,
                 client_route_info.voivode_id as voivodeId,
                 client_route_info.checkbox as checkbox,
                 client_route_info.date as date
         '))
                ->where('client_route_id', '=', $id)
                ->where('status', '=', 1)
                ->groupBy('date', 'client_route_info.city_id', 'show_order')
                ->orderBy('date')
                ->orderBy('show_order')
                ->get();


            $dateFlag = $client_route_info2[0]->date;
            $name = '';
            $allCities = Cities::select('id','name')->get();

            foreach($client_route_info2 as $show) {
                if($show->date != $dateFlag) {
                    $name = substr($name, 0,strlen($name) - 3) .  ' | ';
                }

                $name .= $allCities->where('id', '=', $show->cityId)->first()->name . ' + ';
                $dateFlag = $show->date;
            }

            $name = substr($name, 0,strlen($name) - 3); // removing last + in name
            $clientRoute->route_name = $name;
            $clientRoute->save();

            new ActivityRecorder(array_merge(['T' => 'Edycja trasy'], $clientRoute->toArray()), 230, 2);
        }

        return Redirect::back();
    }

    public function getRouteTemplate(Request $request) {
        $idNotTrimmed = $request->route_id;
        $posOfId = strpos($idNotTrimmed,'_');
        $id = substr($idNotTrimmed, $posOfId + 1);

        $route = $this::prepareRouteTemplate($id);
        return $route;
    }

    /**
     * @param {String} templateId
     * @return {String} status
     */
    public function deleteRouteTemplate(Request $request) {
        if($request->has('templateId')) {
            $templateId = $request->templateId;
            $template = Route::where('id', '=', $templateId);
            RouteInfo::where('routes_id', '=', $templateId)->update(['status' => 0]);
            $template->update(['status' => 0]);
            $info = 'Szablon został usunięty pomyślnie';

            new ActivityRecorder(array_merge(['T' => 'Usunięcie szablonu trasy'], $template->first()->toArray()), 195, 3);
        }
        else {
            $info = 'Szablon nie został usunięty';
        }
        return $info;
    }

    /**
     * @param $id
     * @return This method return collection of route_info records ready to send to view.
     */
    private function prepareRouteTemplate($id) {

        $route = RouteInfo::select('voivodeship.id as voivodeId', 'voivodeship.name as voivodeName', 'city.id as cityId', 'city.name as cityName', 'routes_info.day as day', 'routes_info.checkbox')
            ->where([
                ['routes_id', '=', $id],
                ['routes_info.status', '=', 1]
            ])
            ->join('city', 'routes_info.city_id', '=', 'city.id')
            ->join('voivodeship', 'routes_info.voivodeship_id', '=', 'voivodeship.id')
            ->orderBy('routes_info.id','routes_info.day')
            ->get();
        return $route;
    }

    /**
     *  Return Round Voievodeship and city
     */
    public function getVoivodeshipRoundWithoutGracePeriodAjax(Request $request){
        if($request->ajax()) {
            $cityId = $request->cityId;
            $limit = $request->limit;

            $city = Cities::where('id', '=', $cityId)->first();
            $voievodeshipRound = $this::findCityByDistanceWithoutGracePeriod($city, $limit);

            $voievodeshipRound = $voievodeshipRound->groupBy('id');
            $voievodeshipDistinc = array();
            foreach ($voievodeshipRound as $item){
                array_push($voievodeshipDistinc,$item->first());
            }
            $responseArray['voievodeInfo'] = $voievodeshipDistinc;
            $responseArray['cityInfo'] = $voievodeshipRound;
            return $responseArray;
        }
    }

    /**
     *  Return Round Voievodeship and city
     */
    public function getVoivodeshipRoundWithDistanceLimit(Request $request){
        if($request->ajax()) {
            $cityId = $request->cityId;
            if(strlen($request->currentDate) > 10) {
                $currentDate = substr($request->currentDate, 6);
            }
            else {
                $currentDate = $request->currentDate;
            }

            $limit = $request->limit;

            $cities = Cities::all();
            $city = Cities::where('id', '=', $cityId)->first();
            //part responsible for grace period
            $clientRouteInfoAll = ClientRouteInfo::select('client_route_info.date','client_route_info.city_id','city.grace_period', 'city.max_month_show as max_month_show')
                ->join('city','city.id','client_route_info.city_id')
                ->where('client_route_info.status', '=', 1)
                ->orderBy('city.name')
                ->get();
            $voievodeshipRound = $this::findCityByDistanceWithDistanceLimit($city, $currentDate, $clientRouteInfoAll, $cities, $limit);

            $voievodeshipRound = $voievodeshipRound->groupBy('id');
            $voievodeshipDistinc = array();
            foreach ($voievodeshipRound as $item){
                array_push($voievodeshipDistinc,$item->first());
            }
            $responseArray['voievodeInfo'] = $voievodeshipDistinc;
            $responseArray['cityInfo'] = $voievodeshipRound;
            return $responseArray;
        }
    }

    public function findCityByDistanceWithDistanceLimit($city, $currentDate,$clientRoutesInfoWithUsedCities,$cities, $limit){
        if($limit == 'infinity'){
            $voievodeshipRound = Cities::select(DB::raw('voivodeship.id as id,voivodeship.name,city.name as city_name,city.id as city_id, city.max_hour as max_hour', 'city.max_month_show as max_month_show'))
                ->join('voivodeship', 'voivodeship.id', 'city.voivodeship_id')
                ->orderBy('city.name')
                ->get();
        }else {
            $voievodeshipRound = Cities::select(DB::raw('voivodeship.id as id,voivodeship.name,city.name as city_name,city.id as city_id, city.max_hour as max_hour, city.max_month_show as max_month_show,
            ( 3959 * acos ( cos ( radians(' . $city->latitude . ') ) * cos( radians( `latitude` ) )
             * cos( radians( `longitude` ) - radians(' . $city->longitude . ') ) + sin ( radians(' . $city->latitude . ') )
              * sin( radians( `latitude` ) ) ) ) * 1.60 AS distance'))
                ->join('voivodeship', 'voivodeship.id', 'city.voivodeship_id')
                ->having('distance', '<=', $limit)
                ->orderBy('city.name')
                ->get();
        }
        //part responsible for grace period
        if($currentDate != 0) {
            $checkedCities = array(); //In this array we indices cities that should not be in route
            foreach($clientRoutesInfoWithUsedCities as $item) {
                $properDate = date_create($currentDate);
                $properDatePom = date_create($currentDate);
                //wartość karencji dla danego miasta
                $gracePeriod = $item->grace_period;
//                $gracePeriod = null;
//                if($item->city_id == $city->id){
//                    $gracePeriod = $city->grace_period;
//                }else{
//                    $gracePeriod = null;
//                }
                $goodDate = date_create($item->date);
                $dateDifference = date_diff($properDate,$goodDate, true);
                $dateDifference = $dateDifference->format('%a');
                $dateString = $dateDifference . " days";
                $availableAtDate = date_add($properDatePom,date_interval_create_from_date_string($dateString));
                $availableAtDate = date_format($availableAtDate, "Y-m-d");
                if($dateDifference <= $gracePeriod) {
                    $cityInfoObject = new \stdClass();
                    $cityInfoObject->city_id = $item->city_id;
                    $cityInfoObject->available_date =  date_format(date_add($goodDate,date_interval_create_from_date_string(($gracePeriod).' days') ), "Y-m-d");
                    array_push($checkedCities, $cityInfoObject);
                }
            }
            $voievodeshipRound->map(function($item) use($checkedCities, $currentDate){

                $firstDayOfThisMonth = date('Y-m-01', strtotime($currentDate));
                $lastDayOfThisMonth = date('Y-m-t', strtotime($currentDate));
                $allRecordsFromClientRouteInfo = ClientRouteInfo::where('city_id', '=', $item->city_id)
                    ->where('client_route_info.status', '=', 1)
                    ->whereBetween('client_route_info.date', [$firstDayOfThisMonth, $lastDayOfThisMonth])
                    ->get();

                $numberOfRecords = $allRecordsFromClientRouteInfo->count();
                if($numberOfRecords > $item->max_month_show) {
                    $item->max_month_exceeded = 1;
                }
                else {
                    $item->max_month_exceeded = 0;
                }

                $hourNumber = 0; //This variable counts how many times city was used in grace period
                foreach($checkedCities as $cityRecords) {
                    if ($cityRecords->city_id == $item->city_id) {
                        $hourNumber++;
                    }
                }
                $blockFlag = false;
                foreach($checkedCities as $blockedCity) {
                    if($blockedCity->city_id == $item->city_id) {
                        $blockFlag = true;
                        $item->block = 1;
                        $item->available_date = $blockedCity->available_date;
                        if($item->max_hour > $hourNumber) { // limit of hours isn't exceeded
                            $hourDifference = $item->max_hour - $hourNumber;
                            $item->exceeded = 0; // indices that this city is still available for couple of hours
                            $item->used_hours = $hourDifference;
//                            $item->used_hours = $hourNumber;
                        }
                        else {
                            $hourDifference = $hourNumber - $item->max_hour;
                            $item->used_hours = $hourDifference;
                            $item->exceeded = 1; // indices that this city is not available.
                        }
                    }
                }

                if($blockFlag == false) {
                    $item->block = 0;
                    $item->available_date = 0;
                    $item->used_hours = 0;
                    $item->exceeded = 0;
                }

                return $item;
            });

        }

        return $voievodeshipRound;
    }

    public function findCityByDistanceWithoutGracePeriod($city, $limit){
        if($limit == 'infinity'){
            $voievodeshipRound = Cities::select(DB::raw('voivodeship.id as id,voivodeship.name,city.name as city_name,city.id as city_id, city.max_hour as max_hour'))
                ->join('voivodeship', 'voivodeship.id', 'city.voivodeship_id')
                ->orderBy('city.name')
                ->get();
        }else {
            $voievodeshipRound = Cities::select(DB::raw('voivodeship.id as id,voivodeship.name,city.name as city_name,city.id as city_id, city.max_hour as max_hour,
        ( 3959 * acos ( cos ( radians(' . $city->latitude . ') ) * cos( radians( `latitude` ) )
         * cos( radians( `longitude` ) - radians(' . $city->longitude . ') ) + sin ( radians(' . $city->latitude . ') )
          * sin( radians( `latitude` ) ) ) ) * 1.60 AS distance'))
                ->join('voivodeship', 'voivodeship.id', 'city.voivodeship_id')
                ->having('distance', '<=', $limit)
                ->orderBy('city.name')
                ->get();
        }
        return $voievodeshipRound;
    }

    /**
     * This method shows specific route
     */
    public function specificRouteGet($id, $onlyResult = null) {
        if($onlyResult == null) {
            $clientRouteCampaigns = ClientRouteCampaigns::join('client_route_info as cri', 'client_route_info_id', '=', 'cri.id')
                ->select('client_route_info_id', 'hour_count')
                ->where('client_route_id', '=', $id)
                ->where('cri.status', '=', 1)
                ->get();

            $clientRouteInfo = ClientRouteInfo::join('client_route as cr', 'cr.id', '=', 'client_route_id')
                ->join('client as c', 'c.id', '=', 'cr.client_id')
                ->join('city', 'city.id', '=', 'city_id')
                ->join('voivodeship', 'voivodeship.id', '=', 'voivode_id')
                ->select(
                    'client_route_info.id',
                    'cr.route_name',
                    'date',
                    'weekOfYear as week',
                    'c.name as client_name',
                    'user_reservation',
                    'hotel_id',
                    'hour',
                    'city.name as city_name',
                    'voivodeship.name as voivode_name'
                )
                ->where('cr.status', '=', 1)
                ->where('cr.id', '=', $id)
                ->where('client_route_info.status', '=', 1)
                ->orderBy('date')->orderBy('show_order')->orderBy('client_route_info.id')->get();
            $routeInfo = [];
            $pageInfo = [];
            if (!empty($clientRouteInfo)) {
                $pageInfo = (object)[
                    'clientName' => $clientRouteInfo[0]->client_name,
                    'routeName' => $clientRouteInfo[0]->route_name,
                    'week' => $clientRouteInfo[0]->week,
                    'date' => $clientRouteInfo[0]->date,
                    'userReservation' => $clientRouteInfo[0]->user_reservation
                ];
                for ($i = 0; $i < $clientRouteInfo->count(); $i++) {
                    $campaign = [];
                    $campaignHour = $clientRouteCampaigns->where('client_route_info_id', '=', $clientRouteInfo[$i]->id)->first();

                    $campaignHour = empty($campaignHour) ? 0 : $campaignHour->hour_count;
                    if($campaignHour != 0 ){
                        for (; $campaignHour > 0; $campaignHour--) {
                            array_push($campaign, $clientRouteInfo[$i]);
                            $i++;
                        }
                        $i--;
                        array_push($routeInfo, $campaign);
                    }
                }
            }

            $status = [1];
            $hotels = Hotel::whereIn('status', $status)->orderBy('id')->get();
            foreach ($routeInfo as $campaign) {
                $campaign[0]->hotel_page = 0;
                if (!empty($campaign[0]->hotel_id)) {
                    $hotels->each(function ($hotel, $key) use ($campaign) {
                        if ($hotel->id == $campaign[0]->hotel_id) {
                            $campaign[0]->hotel_page = intval(floor($key / 10));
                            return false;
                        }
                    });
                }
            }
            return view('crmRoute.specificRoute')
                ->with('routeInfo', $routeInfo)
                ->with('pageInfo', $pageInfo);
        }else{
            $clientRouteInfo = ClientRouteInfo::select(
                'client_route_info.user_reservation as user_reservation',
                'client_route_info.hotel_price as hotel_price',
                'client_route_info.limits as limits',
                'client_route_info.department_info_id as department_info_id',
                'client_route_info.id as id', 'city.name as cityName',
                'voivodeship.name as voivodeName',
                'client_route.id as client_route_id',
                'city.id as city_id', 'voivodeship.id as voivode_id',
                'client_route_info.date as date', 'client_route_info.hotel_id as hotel_id',
                'client_route_info.hour as hour',
                'client_route.client_id as client_id',
                'client_route_info.weekOfYear as weekOfYear')
                ->join('client_route', 'client_route.id', '=', 'client_route_info.client_route_id')
                ->join('city', 'city.id', '=', 'client_route_info.city_id')
                ->join('voivodeship', 'voivodeship.id', '=', 'client_route_info.voivode_id')
                ->where('client_route.status', '=', 1)
                ->where('client_route_id', '=', $id)
                ->where('client_route_info.status', '=', 1)
                ->get();
            $userReservation = $clientRouteInfo->first()->user_reservation;

            $clientRoute = $this->getClientRouteGroupedByDateSortedByHour($id, $clientRouteInfo);
            $routeInfo = new \stdClass;
            $routeInfo->routeName = $this->createRouteName($clientRoute);
            $routeInfo->firstDate = $clientRoute[0]->date;
            $routeInfo->week =  $clientRoute[0]->weekOfYear;
            $clients = Clients::all();

            $status = [1];

            $hotels = Hotel::whereIn('status', $status)->orderBy('id')->get();

            $clientRouteInfoExtended = array();
            $insideArr = array();
            $cityId = null;
            $flag = 0; //indices whether $insideArr push into $clientRouteInfoExtended 1 - push, 0 - don't push
            $iterator = 0; //It count loops of foreach
            $iteratorFinish = count($clientRouteInfo); // indices when condition inside foreach should push array into $clientRouteInfoExtended array.
            $clientName = null;

            foreach($clientRouteInfo as $info) {
                if($cityId == null) {
                    $flag = 0;
                    $cityId = $info->city_id;
                }
                else if($info->city_id == $cityId) {
                    $flag = 0;
                    $cityId = $info->city_id;
                }
                else {
                    array_push($clientRouteInfoExtended, $insideArr);
                    $insideArr = [];
                    $flag = 1;
                    $cityId = $info->city_id;
                }

                if($clientName == null) {
                    $clientRId = $info->client_id;
                    $clientName = Clients::find($clientRId)->name;
                }

                $stdClass = new \stdClass();

                foreach($clients as $client) {
                    if($info->client_route_id == $client->id) {
                        $stdClass->clientName = $client->name;
                    }
                }

                $stdClass->id = $info->id;
                $stdClass->client_route_id = $info->client_route_id;
                $stdClass->city_id = $info->city_id;
                $stdClass->cityName = $info->cityName;
                $stdClass->voivode_id = $info->voivode_id;
                $stdClass->voivodeName = $info->voivodeName;
                $stdClass->date = $info->date;
                $stdClass->hotel_id = $info->hotel_id;
                $stdClass->hotel_info = Hotel::find($info->hotel_id);
                $stdClass->hour = $info->hour;
                $stdClass->hotel_price = $info->hotel_price;
                $stdClass->limit = $info->limits == null ? 0 : $info->limits;
                $stdClass->department_info_id = $info->department_info_id;
                $stdClass->weekNumber = $info->weekOfYear;
                array_push($insideArr, $stdClass);
                if($flag == 1) {
                    $flag = 0;
                }
                if($iterator == ($iteratorFinish - 1)) {
                    array_push($clientRouteInfoExtended, $insideArr);
                }
                $iterator++;
            }

            $clientRouteInfo = collect($clientRouteInfoExtended);

            $clientRouteInfo->each(function ($city, $key) use ($hotels, &$ddArray) {
                foreach($city as $showHour){
                    $hotels->each(function ($hotel, $key) use ($showHour, &$ddArray) {
                        if($hotel->id == $showHour->hotel_id){
                            $showHour->hotel_page = intval(floor($key/10));
                            return false;
                        }else{
                            $showHour->hotel_page = 0;
                        }
                    });
                }
            });
            return $clientRouteInfo->sortByDesc('date');
        }
    }

    /**
     * This method saves changes about specific route
     */
    public function updateClientRouteInfoHotelsAndHours(Request $request){
        $all_data = json_decode($request->JSONData); //we obtain 3 dimensional array
        //dd($all_data);
        $clientRouteInfoIds = 'clientRouteInfoIds: ';
        foreach($all_data as $campaign) {
            foreach ($campaign->timeHotelArr as $clientRouteInfo){
                try{
                    ClientRouteInfo::where([
                        ['id', '=', $clientRouteInfo->clientRouteInfoId]
                    ])->update([
                        'hotel_id' => $clientRouteInfo->hotelId,
                        'hour' => $clientRouteInfo->time == "" ? null : $clientRouteInfo->time,
                        'user_reservation' => $campaign->userReservation
                    ]);
                    $clientRouteInfoIds .= $clientRouteInfo->clientRouteInfoId . ', ';
                }catch(\Exception $e){
                    return $e;
                }
            }
        }
        new ActivityRecorder(['T'=>'Edycja hoteli i godzin trasy','clientRouteInfoIds:' => $clientRouteInfoIds], 211,2);
        return 'success';
    }

    /**
     * This method saves changes about specific route
     */
   /* public function specificRoutePost(Request $request) {
        $all_data = json_decode($request->JSONData); //we obtain 2 dimensional array
        $clientRouteInfoIds = 'clientRouteInfoIds: ';
        foreach($all_data as $city) {
            $clientRouteInfo = ClientRouteInfo::where([
                ['city_id', '=', $city->cityId],
                ['voivode_id', '=', $city->voivodeId],
                ['client_route_id', '=', $city->clientRouteId]
            ])
                ->get();
            $numberOfRecords = count($clientRouteInfo);
            $iterator = 0;
            foreach($clientRouteInfo as $item) {
                if($city->timeHotelArr[$iterator]->time == '') {
                    $item->hour = null;
                }
                else {
                    $item->hour = $city->timeHotelArr[$iterator]->time;
                }
                if($city->priceHotelArr[$iterator]->price == '') {
                    $item->hotel_price = null;
                }
                else {
                    $item->hotel_price = $city->priceHotelArr[$iterator]->price;
                }

                $item->hotel_id = $city->timeHotelArr[$iterator]->hotelId;
                $item->department_info_id = null; //At this point nobody choose it's value, can't be 0 because
                $item->user_reservation = $city->user_reservation;
                $item->save();
                $iterator++;
                $clientRouteInfoIds .= $item->id . ', ';
            }
        }
        new ActivityRecorder(['T'=>'Edycja hoteli i godzin trasy','clientRouteInfoIds:' => $clientRouteInfoIds], 211,2);

        return $all_data;
    }*/

    /**
     * Return ready route with info
     * @param Request $request
     */
    public function getReadyRoute(Request $request){
        $data = $this::specificRouteGet($request->route_id,true);
        return $data;
    }

    /**
     * Save Campaign Option (department's and limit)
     * @param Request $request
     */
    public function saveCampaignOption(Request $request){
        if($request->ajax()){
            $clientRouteIds = 'ClientRouteInfoIds: ';
            $objectOfChange = $request->objectOfChange;
            foreach ($objectOfChange as $item){
                $clientRoadInfo = ClientRouteInfo::where('status', '=', 1)->where('client_route_info.id', '=', $item['id'])->first();
                $clientRoadInfo->limits = $item['limit'];
                $clientRoadInfo->department_info_id = $item['department_info_id'];
                $clientRoadInfo->save();
                $clientRouteIds .= $item['id'] .', ';
            }
            new ActivityRecorder(array_merge(['T'=>'Edycja parametrów kampanii'],$objectOfChange[0]),213,2);

            return 200;
        }else
            return 500;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    public function getSelectedRoute(Request $request) {
        $idNotTrimmed = $request->route_id;
        $posOfId = strpos($idNotTrimmed,'_');
        $id = substr($idNotTrimmed, $posOfId + 1);
        $voivodes = Voivodes::all();
        $cities = Cities::all();
        $route = RouteInfo::where([
            ['routes_id', '=', $id],
            ['status', '=', 1]
        ])
            ->get();

        $routeExtendedArr = array();
        foreach($route as $item) {
            $routeExtended = new \stdClass();
            $routeExtended->id = $item->id;
            $routeExtended->routes_id = $item->routes_id;
            $routeExtended->voivodeship_id = $item->voivodeship_id;
            $routeExtended->city_id = $item->city_id;
            $routeExtended->status = $item->status;
            foreach($voivodes as $voivode) {
                if($item->voivodeship_id == $voivode->id) {
                    $routeExtended->voivode_name = $voivode->name;
                }
            }
            foreach($cities as $city) {
                if($item->city_id == $city->id) {
                    $routeExtended->city_name = $city->name;
                }
            }
            array_push($routeExtendedArr, $routeExtended);
        }

        $routeExt = collect($routeExtendedArr);
        return $routeExt;
    }

    /**
     * @return $this method sends to server view showClientRoutes with data about weeks in given year.
     */
    public function showClientRoutesGet() {
        $departments = Department_info::getAllInfoAboutDepartment()
        ->whereIn('id_dep_type',[2]);
        $year = date('Y',strtotime("this year"));
        $numberOfThisYearsWeek = date('W',mktime(0, 0, 0, 12, 30, $year));
        return view('crmRoute.showClientRoutes')
            ->with('lastWeek', $numberOfThisYearsWeek)
            ->with('departments', $departments)
            ->with('year', $year);
    }

    /**
     * @param year
     * @return Number of Weeks in given year
     */
    public function getYearWeeksAjax(Request $request) {
        $year = $request->year;
        $numberOfLastYearsWeek = date('W',mktime(0, 0, 0, 12, 29, $year));
        return $numberOfLastYearsWeek;
    }

    public function showClientRoutesAjax(Request $request) {
        $clients = Clients::all();
        $client_route_info = DB::table('client_route')
            ->select(DB::raw('
                client.id as id,
                client.name as name'))
            ->join('client', 'client.id', '=', 'client_route.client_id')
            ->where('client_route.status', '=', 1)
            ->distinct()
            ->get();


//        $newEmptyClient = new Clients();
//        $newEmptyClient->
        return datatables($client_route_info)->make(true);
    }

    /**
     * @param showAllClients(true/false/null), showOnlyAssigned(true/false/null), id(number/null), selectedWeek(number), year(number), typ(number), state(number)
     * This method return data about all client routes to datatable in showClientRoutes
     */
    public function showClientRoutesInfoAjax(Request $request) {
        $clientId = $request->id;
        $clientId = $clientId == '-1' ? '%' : $clientId;

        $showOnlyAssigned = $request->showOnlyAssigned;
        if($request->year > 0) {
            $year = $request->year;
        }
        else {
            $year = date('Y',strtotime("this year"));
        }

        $selectedWeek = $request->selectedWeek;
        $selectedWeek = $selectedWeek == '0' ? '%' : $selectedWeek;

        $typ = $request->typ;
        $typ = $typ == '0' ? '%' : $typ;

        $client_route_info = DB::table('client_route_info')
            ->select('route_name',
                'client_route_info.id',
                'weekOfYear','hour',
                'hotel_id',
                'client.name as clientName',
                'city.name as cityName',
                'date',
                'client_route.type',
                'client_route_id',
                'limits')
            ->join('client_route' ,'client_route.id','=','client_route_id')
            ->join('client' ,'client.id','=','client_route.client_id')
            ->join('city' ,'city.id','=', 'city_id')
            ->where('client_route.status', '=', 1)
            ->where('client_route.client_id','like',$clientId)
            ->where('client_route_info.status', '=', 1)
            ->where('date', 'like', $year . '%')
            ->where('weekOfYear', 'like', $selectedWeek)
            ->where('client_route.type', 'like', $typ);

        $client_route_info =  $client_route_info->get();

        $client_route_ids = $client_route_info->pluck('client_route_id')->unique();

        $fullArray = [];
        foreach($client_route_ids as $client_route_id){

            //this part check whether all limits are assigned
            $infoRecords = $client_route_info->where('client_route_id', '=', $client_route_id);
            $limitFlag = true;
            foreach($infoRecords as $oneRecord) {
                if($oneRecord->limits == null || $oneRecord->limits == 0) {
                    $limitFlag = false;
                    break;
                }
            }

            $client_routes = $this->getClientRouteGroupedByDateSortedByHour($client_route_id, $client_route_info);

            //$route_name = $this->createRouteName($client_routes);
            $hourOrHotelAssigned = $client_routes[0]->hour == null || $client_routes[0]->hotel_id == null ? false : true;
            for($i = 1; $i < count($client_routes);$i++){
                if($hourOrHotelAssigned && ($client_routes[$i]->hotel_id == null || $client_routes[$i]->hour == null) )
                    $hourOrHotelAssigned = false;
            }

            $client_routes[0]->hotelOrHour = $hourOrHotelAssigned;
            $client_routes[0]->hasAllLimits = $limitFlag ? 1 : 0;
            //$client_routes[0]->route_name = $route_name;
            array_push($fullArray, $client_routes[0]);
        }
        $full_clients_routes = collect($fullArray);

        if($showOnlyAssigned == 'true'){
            $full_clients_routes = $full_clients_routes->where('hotelOrHour','=', false);
        }

        return datatables($full_clients_routes)->make(true);
    }

    private function fillClientsRouteNames(){
        $clientRoutes = ClientRoute::all();
        $clientRoutes->each(function ($clientRoute, $key) {
            $clientRouteInfo = $this->getClientRouteGroupedByDateSortedByHour($clientRoute->id);
            $clientRoute->route_name = $this->createRouteName($clientRouteInfo);
            $clientRoute->save();
        });

    }
    private function getClientRouteGroupedByDateSortedByHour($client_route_id, $client_route_info = null){
        $grouped_by_day_client_routes= [];
        if($client_route_info === null){
            $client_route_info = ClientRouteInfo::select( 'client_route_info.id as id', 'city.name as cityName','client_route_info.hour as hour' , 'client_route_info.date as date', 'client_route_info.limits as limits')
                ->join('city', 'city.id', '=', 'client_route_info.city_id')
                ->where('client_route_id', '=', $client_route_id)
                ->where('client_route_info.status', '=', 1)
                ->get();
            foreach ($client_route_info->sortBy('date')->groupBy('date') as $client_route_day) {
                array_push($grouped_by_day_client_routes, $client_route_day);
            }
        }else {
            foreach ($client_route_info->where('client_route_id', '=', $client_route_id)->sortBy('date')->groupBy('date') as $client_route_day) {
                array_push($grouped_by_day_client_routes, $client_route_day);
            }
        }
        $client_routes = [];
        foreach($grouped_by_day_client_routes as $client_route_day){
            foreach ($client_route_day->sortBy('id') as $client_route){
                array_push($client_routes, $client_route);
            }
        }
        return $client_routes;
    }

    private function createRouteName($client_routes){
        $route_name = $client_routes[0]->cityName;

        for($i = 1; $i < count($client_routes);$i++){
            if($client_routes[$i]->cityName !== $client_routes[$i-1]->cityName)
                if($client_routes[$i]->date!=$client_routes[$i-1]->date){
                    $route_name .= ' | '.$client_routes[$i]->cityName;
                }else
                    $route_name .= '+'.$client_routes[$i]->cityName;
        }
        return $route_name;
    }
    /**
     * @param $collection of client_route_info
     * @return array of client_route_id's that has at least one hotel assigned
     */
    private function getExcludedIds($collection) {
        //now we are checking if every client_route group has at least one hotel assigned
        $alreadyCheckedIds = array(); //here we store all already checked client_route_ids.
        $clientRouteIdArr = array(); //here we store all client_route_ids that has at least one hotel

        foreach($collection as $info) {
            $hasHotel = 0;
            $checkedFlag = false;
            foreach($alreadyCheckedIds as $checked) {
                if($info->client_route_id == $checked) {
                    $checkedFlag = true;
                }
            }
            if($checkedFlag == false) {
                $clientRouteId = $info->client_route_id;
                $allClientRouteIdInsertions = ClientRouteInfo::where('client_route_id', '=', $clientRouteId)->where('client_route_info.status', '=', 1)->get();

                if($allClientRouteIdInsertions->where('hotel_id', '!=', null)->count() > 0) {
                    $hasHotel++;
                }
                if($hasHotel > 0) {
                    array_push($clientRouteIdArr, $clientRouteId);
                }
                array_push($alreadyCheckedIds, $clientRouteId);
            }

        }

        return $clientRouteIdArr;
    }

    /**
     * @param $clientRouteId, $toDelete(0,1,2)
     * This method changes status of client_route
     */
    public function showClientRoutesStatus(Request $request) {
        $clientRouteId = $request->clientRouteId;
        $toDelete = $request->delete; // 0,1,2 - actual values 0 - not ready, 1 - started, 2 - finished
        $success = 0;
        if($clientRouteId && $toDelete == '0') {
            $clientRoute = ClientRoute::find($clientRouteId);
            $clientRoute->status = 1;
            $clientRoute->save();
            $success = 1;
            new ActivityRecorder(array_merge(['T'=>'Zmiana statusu trasy','Akcja'=>'Aktywacja'],$clientRoute->toArray()),213,4);
        }
        else if($clientRouteId && $toDelete == '1') {
            $clientRoute = ClientRoute::find($clientRouteId);
            $clientRoute->status = 2;
            $clientRoute->save();
            $success = 1;
            new ActivityRecorder(array_merge(['T'=>'Zmiana statusu trasy','Akcja'=>'Zakończenie'],$clientRoute->toArray()),213,4);
        }
        else if($clientRouteId && $toDelete == '2') {
            $clientRoute = ClientRoute::find($clientRouteId);
            $clientRoute->status = 0;
            $clientRoute->save();
            $success = 1;
            new ActivityRecorder(array_merge(['T'=>'Zmiana statusu trasy','Akcja'=>'Niegotowa'],$clientRoute->toArray()),213,4);
        }

        return $success;
    }

    //Prawdopodobnie nieużywane metody
//    /**
//     * @return $this method returns view addNewRoute with data about all voivodes
//     */
//    public function addNewRouteGet() {
//        $voivodes = Voivodes::all();
//
//        return view('crmRoute.addNewRoute')->with('voivodes', $voivodes);
//    }

//    /**
//     * This method saves new route to database
//     */
//    public function addNewRoutePost(Request $request) {
//        $voivode = $request->voivode;
//        $city = $request->city;
//
//        $voivodeIdArr = explode(',', $voivode);
//        $cityIdArr = explode(',', $city);
//
//        $cityNamesArr = array();
//
//        foreach($cityIdArr as $city) {
//            $givenCity = Cities::where('id', '=', $city)->first();
//            $name = $givenCity->name;
//            array_push($cityNamesArr,$name);
//        }
//
//        $nameOfRoute = '';
//        foreach($cityNamesArr as $name) {
//            $nameOfRoute .= $name . ' | ';
//        }
//        $nameOfRoute = trim($nameOfRoute, ' | ');
//
//        $newRoute = new Route();
//        $newRoute->status = 1; // 1 - aktywne dane, 0 - usunięte dane
//        $newRoute->name = $nameOfRoute;
//        $newRoute->save();
//        foreach($voivodeIdArr as $voivodekey => $voivode) {
//            foreach($cityIdArr as $citykey => $city) {
//                if($voivodekey == $citykey) {
//                    $newRouteInfo = new RouteInfo();
//                    $newRouteInfo->routes_id = $newRoute->id;
//                    $newRouteInfo->voivodeship_id = $voivode;
//                    $newRouteInfo->city_id = $city;
//                    $newRouteInfo->status = 1; // 1 - aktywne dane, 0 - usunięte dane
//                    $newRouteInfo->save();
//                }
//            }
//
//        }
//        new ActivityRecorder(array_merge(['T' => 'Dodanie nowego szablonu trasy'], $newRoute->toArray()),193,1);
//
//        $request->session()->flash('adnotation', 'Trasa została dodana pomyślnie!');
//
//        return Redirect::to('/showRoutes');
//
//    }

    /**
     * This method saves changes related to given route.
     */
    public function editRoute(Request $request)
    {
        if(isset($request->toDelete)) { // usuwamy
            $oldRoute = Route::find($request->route_id);
            $oldRoute->status = 0; // status 0 - usunięty, 1 - aktywny
            $oldRoute->save();

            $oldRoutesInfo = RouteInfo::where('routes_id', '=', $request->route_id)->get();
            foreach($oldRoutesInfo as $oldInfo) {
                $oldInfo->status = 0; // status 0 - usunięty, 1 - aktywny
                $oldInfo->save();
            }

            $request->session()->flash('adnotation', 'Trasa została usunięta pomyślnie!');

            new ActivityRecorder(array_merge(['T'=>'Usunięcie szablonu trasy'],$oldRoute->toArray()),188,3);

            return Redirect::to('/showRoutes');
        }
        else { //edytujemy
            $voivode = $request->voivode;
            $city = $request->city;

            $voivodeIdArr = explode(',', $voivode);
            $cityIdArr = explode(',', $city);

            $cityNamesArr = array();

            foreach ($cityIdArr as $city) {
                $givenCity = Cities::where('id', '=', $city)->first();
                $name = $givenCity->name;
                array_push($cityNamesArr, $name);
            }

            $nameOfRoute = '';
            foreach ($cityNamesArr as $name) {
                $nameOfRoute .= $name . ' | ';
            }
            $nameOfRoute = trim($nameOfRoute, ' | ');

            $thisRoute = Route::find($request->route_id);
            $prevRoute = $thisRoute->toArray();
            $thisRoute->name;
            $thisRoute->name = $nameOfRoute;
            $thisRoute->save();

            $oldRoute = RouteInfo::where([
                ['routes_id', '=', $request->route_id],
                ['status', '=', 1]
            ])
                ->get();

            foreach ($oldRoute as $item) {
                $item->status = '0';
                $item->save();
            }

            foreach ($voivodeIdArr as $voivodekey => $voivode) {
                foreach ($cityIdArr as $citykey => $city) {
                    if ($voivodekey == $citykey) {
                        $newRouteInfo = new RouteInfo();
                        $newRouteInfo->routes_id = $request->route_id;
                        $newRouteInfo->voivodeship_id = $voivode;
                        $newRouteInfo->city_id = $city;
                        $newRouteInfo->status = 1; // 1 - aktywne dane, 0 - usunięte dane
                        $newRouteInfo->save();
                    }
                }
            }
            $request->session()->flash('adnotation', 'Trasa została edytowana pomyślnie!');

            new ActivityRecorder(['T'=>'Edycja szablonu trasy','prev_route'=> $prevRoute, 'new_route'=>$thisRoute->toArray()],188,2);

            return Redirect::to('/showRoutes');
        }

    }

    /**
     * @param id of voivode
     * @return list of cities in each voivode
     */
    public function addNewRouteAjax(Request $request) {
        $voivodeId = $request->id;
        $currentDate = $request->currentDate;
        $cities = Cities::all();
        if($currentDate != 0) {
            $all_cities = Cities::where('voivodeship_id', '=', $voivodeId)->orderBy('name')->get();
            $properDate = date_create($currentDate);
            $properDatePom = date_create($currentDate);

            //lista miast we wszystkich trasach.
//            $citiesAvailable = DB::table('routes_info')->select(DB::raw('
//            city_id as cityId
//            '))
//                ->pluck('cityId')
//                ->toArray();

            //Rekordy clientRoutesInfo w których były użyte miasta
            $clientRoutesInfoWithUsedCities = ClientRouteInfo::select('client_route_info.date','client_route_info.city_id','city.grace_period', 'city.max_month_show as max_month_show')
                ->join('city','city.id','client_route_info.city_id')
                ->where('client_route_info.status', '=', 1)
                ->orderBy('city.name')
                ->get();
            $checkedCities = array(); //In this array we indices cities that should not be in route
            foreach($clientRoutesInfoWithUsedCities as $item) {
                //wartość karencji dla danego miasta
                $properDate = date_create($currentDate);
                $properDatePom = date_create($currentDate);
//
                $gracePeriod = $item->grace_period;
                $goodDate = date_create($item->date);
                $dateDifference = date_diff($properDate,$goodDate, true);
                $dateDifference = $dateDifference->format('%a');
                $dateString = $dateDifference . " days";
                $availableAtDate = date_add($properDatePom,date_interval_create_from_date_string($dateString));
                $availableAtDate = date_format($availableAtDate, "Y-m-d");

                $arrayFlag = false;
                if($dateDifference <= $gracePeriod) {
//                    foreach($checkedCities as $cities) {
//                        if($item->city_id == $cities->city_id) {
//                            $arrayFlag = true;
//                        }
//                    }
                    if($arrayFlag == false) {
                        $cityInfoObject = new \stdClass();
                        $cityInfoObject->city_id = $item->city_id;
                        $cityInfoObject->available_date =  date_format(date_add($goodDate,date_interval_create_from_date_string(($gracePeriod).' days') ), "Y-m-d");
                        array_push($checkedCities, $cityInfoObject);
                    }
                }

            }
//            dd($all_cities->pluck('max_month_show'));
            $all_cities->map(function($item) use($checkedCities, $currentDate){

                $firstDayOfThisMonth = date('Y-m-01', strtotime($currentDate));

                $lastDayOfThisMonth = date('Y-m-t', strtotime($currentDate));

                $allRecordsFromClientRouteInfo = ClientRouteInfo::where('city_id', '=', $item->id)
                    ->where('client_route_info.status', '=', 1)
                    ->whereBetween('client_route_info.date', [$firstDayOfThisMonth, $lastDayOfThisMonth])
                    ->get();

                $numberOfRecords = $allRecordsFromClientRouteInfo->count();
                if($numberOfRecords > $item->max_month_show) {
                    $item->max_month_exceeded = 1;
                }
                else {
                    $item->max_month_exceeded = 0;
                }

                $hourNumber = 0; //This variable counts how many times city was used in grace period
                foreach($checkedCities as $cityRecords) {
                    if ($cityRecords->city_id == $item->id) {
                        $hourNumber++;
                    }
                }

                $blockFlag = false;
                foreach($checkedCities as $blockedCity) {
                    if($blockedCity->city_id == $item->id) {

                        $blockFlag = true;
                        $item->block = 1;
                        $item->available_date = $blockedCity->available_date;
                        if($item->max_hour > $hourNumber) { // limit of hours isn't exceeded
                            $hourDifference = $item->max_hour - $hourNumber;
                            $item->exceeded = 0; // indices that this city is still available for couple of hours
                            $item->used_hours = $hourDifference;
//                            $item->used_hours = $hourNumber;
                        }
                        else {
                            $hourDifference = $hourNumber - $item->max_hour;
                            $item->used_hours = $hourDifference;
                            $item->exceeded = 1; // indices that this city is not available.
                        }

                    }
                }

                if($blockFlag == false) {
                    $item->block = 0;
                    $item->available_date = 0;
                    $item->used_hours = 0;
                    $item->exceeded = 0;
                }

                return $item;
            });
        }
        else {
            $all_cities = Cities::where('voivodeship_id', '=', $voivodeId)->orderBy('name')->get();
        }
//        $all_cities = Cities::where('voivodeship_id', '=', $voivodeId)->get();
        return $all_cities;
    }

    /**
     * @return Return view "show routes"
     */
    public function showRoutesGet() {
        return view('crmRoute.showRoutes');
    }

    /**
     * @return This method sends data about all routes to datatable in showRoutes view
     */
    public function showRoutesAjax(Request $request) {
        $date = $request->date;
        if($date) {
            $properDate = date_create($date);

            //lista miast we wszystkich trasach.
            $citiesAvailable = DB::table('routes_info')->select(DB::raw('
            city_id as cityId
            '))
                ->pluck('cityId')
                ->toArray();

            //Rekordy clientRoutesInfo w których były użyte miasta
            $clientRoutesInfoWithUsedCities = ClientRouteInfo::select('city_id', 'date')
                ->whereIn('city_id', $citiesAvailable)
                ->where('client_route_info.status', '=', 1)
                ->get();
            $checkedCities = array(); //In this array we indices cities that should not be in route
            foreach($clientRoutesInfoWithUsedCities as $item) {
                //wartość karencji dla danego miasta
                $gracePeriod = Cities::find($item->city_id)->grace_period;
                $goodDate = date_create($item->date);
                $dateDifference = date_diff($properDate,$goodDate, true);
                $dateDifference = $dateDifference->format('%a');

                $arrayFlag = false;
                if($dateDifference <= $gracePeriod) {
                    foreach($checkedCities as $cities) {
                        if($item->city_id == $cities) {
                            $arrayFlag = true;
                        }
                    }
                    if($arrayFlag == false) {
                        array_push($checkedCities, $item->city_id);
                    }
                }

            }

            $rout = RouteInfo::select('routes_id')->whereIn('city_id', $checkedCities)->where('status', '=', 1)->groupBy('routes_id')->pluck('routes_id')->toArray();
            $routesFiltered = Route::select('id', 'name')->whereNotIn('id', $rout)->where('status', '=', 1)->get();
            $routes = Route::where('status', '=', 1)->get();
            $routes->map(function($item) use($routesFiltered){
                $item->changeColor = 0;

                if(!$routesFiltered->where('id','=',$item->id)->isEmpty()){
                    $item->changeColor = 1;
                }

                return $item;
            });
        }
        else {
            $routes = Route::where('status', '=', 1)->get();
        }

        return datatables($routes)->make(true);
    }

    /**
     * @param $id of given route
     * @return view with data of given route
     */
    public function routeGet($id) {
        $routeInfo = RouteInfo::where([
            ['routes_id', '=', $id],
            ['status', '=', 1]
        ])->get();
        $cities = Cities::all();
        $clientRouteInfo = ClientRouteInfo::select('client_route_info.date','client_route_info.city_id','city.grace_period', 'city.max_month_show as max_month_show')
            ->join('city','city.id','client_route_info.city_id')
            ->where('client_route_info.status', '=', 1)
            ->get();
        $routeInfo->map(function ($item) use($clientRouteInfo,$cities){
            $city = Cities::find($item->city_id);
            $item->cities = $this::findCityByDistance($city,'2000-01-01',$clientRouteInfo,$cities);
            return $item;
        });
        $voivodes = Voivodes::all();
        $route = Route::where('id', '=', $id)->first();
        $editFlag = true;

        return view('crmRoute.editRoute')
            ->with('routeInfo', $routeInfo)
            ->with('voivodes', $voivodes)
            ->with('editFlag', $editFlag)
            ->with('route', $route);
    }

    /**
     * This method return view addNewHotel with data about voivodes
     */
    public function addNewHotelGet() {
        $voivodes = Voivodes::all();
        return view('crmRoute.addNewHotel')->with('voivodes', $voivodes);
    }

    /**
     * This method saves newly added hotel to database
     */
    public function addNewHotelPost(Request $request) {
        $hotelName = $request->name;
        $voivodeId = $request->voivode;
        $cityId = $request->city;
        $price = $request->price;
        $comment = $request->comment;
        $status = 1; // 1 - aktywne dane, 0 - usunięte dane

        $hotelName = trim($hotelName);

        $newHotel = new Hotel();
        $newHotel->name = $hotelName;
        $newHotel->voivode_id = $voivodeId;
        $newHotel->city_id = $cityId;
        $newHotel->price = $price;
        $newHotel->comment = $comment;
        $newHotel->status = $status;
        $newHotel->save();

        new ActivityRecorder(null,188,1);

        $request->session()->flash('adnotation', 'Hotel został zapisany pomyślnie!');

        return redirect()->route('showHotels');

    }

    /**
     * This method returns view showHotels
     */
    public function showHotelsGet($hotelId = 0) {
        $voivodes = Voivodes::all()->sortByDesc('name');
        $cities = Cities::all()->sortBy('name');
        $paymentMethods = PaymentMethod::all();
        $zipCode = Hotel::select(DB::raw('distinct(zip_code)'))
            ->where('zip_code','!=','null')->get();
        $zipCode->map(function ($item){
            $item->zip_code = $this::zipCodeNumberToString($item->zip_code);
            return $item;
        });
        $clients = Clients::all()->sortByDesc('name');
        return view('crmRoute.showHotels')
            ->with('voivodes', $voivodes)
            ->with('cities', $cities)
            ->with('zipCode',$zipCode)
            ->with('paymentMethods', $paymentMethods)
            ->with('clients', $clients)
            ->with('validHotelInvoiceTemplatesExtensions',json_encode($this->validHotelInvoiceTemplatesExtensions))
            ->with('hotelId', $hotelId);
    }

    /**
     * This method sends data to ajax request about all hotels for view showHotels
     */
    public function showHotelsAjax(Request $request) {
        $voivodeIdArr = $request->voivode;
        $cityIdArr = $request->city;
        $zipCode = $request->zipCode;
        $status = $request->status;

        $hotelId = $request->hotelId;

        if(is_null($voivodeIdArr) && is_null($cityIdArr)) {
            $hotels = Hotel::whereIn('hotels.status', $status);
        }
        else if(!is_null($voivodeIdArr) != 0 && is_null($cityIdArr)) {
            $hotels = Hotel::whereIn('hotels.status', $status)
                ->whereIn('hotels.voivode_id', $voivodeIdArr);
        }
        else if(is_null($voivodeIdArr) && !is_null($cityIdArr) != 0) {
            $hotels = Hotel::whereIn('hotels.status', $status)
                ->whereIn('hotels.city_id', $cityIdArr);
        }
        else {
            $hotels = Hotel::whereIn('status', $status);
        }
        $hotels = $hotels->select(DB::raw(
        '
         hotels.id,
         hotels.name,
         hotels.status,
         hotels.street,
         hotels.voivode_id,
         hotels.city_id,
         hotels.zip_code,
         hotels.comment,
         voivodeship.name as voivodeName,
         city.name as cityName
        '))
            ->join('city','city.id','city_id')
            ->join('voivodeship','voivodeship.id','voivode_id')->orderBy('id');

        if($hotelId != 0){
            $hotels->where('hotels.id','=',$hotelId);
        }

        $hotels = $hotels->get();
        $hotels->map(function ($item){
                $item->zip_code = $this::zipCodeNumberToString($item->zip_code);
            return $item;
        });
        if(!is_null($zipCode) && is_array($zipCode)){
            if(count($zipCode) != 0)
                $hotels = $hotels->whereIn('zip_code',$zipCode);
        }
        return datatables($hotels)->make(true);
    }

    public function zipCodeNumberToString($zipCode){
        if($zipCode == null){
            $zipCode = '';
        }else{
            $length = strlen($zipCode);
            for($i = 0; $i < 5-$length; $i++){
                $zipCode = "0".$zipCode;
            }
            $zipCode = substr($zipCode,0,2).'-'.substr($zipCode,2,5);
        }
        return $zipCode;
    }

    /**
     * This method returns view hotel with data about given hotel
     */
    public function hotelGet($id) {
        $hotel = Hotel::find($id);
        $voivodes = Voivodes::all();
        $cities = Cities::where('voivodeship_id','=',$hotel->voivode_id)->get();
        $idOfHotel = $id;

        return view('crmRoute.hotel')
            ->with('hotel', $hotel)
            ->with('voivodes', $voivodes)
            ->with('cities', $cities)
            ->with('id', $id);
    }

    /**
     * This method saves or deletes given hotel
     */
    public function hotelPost(Request $request, $id) {
        $usun = false;
        if(!is_null($request->usun)) {
            $usun = true;
        }
        if($usun) {
            $hotel = Hotel::find($id);
            $hotel->status = 0; // status 0 - usuniety, status = 1 - aktywny
            $hotel->save();
            new ActivityRecorder($id, 191,3);

            $request->session()->flash('adnotation', 'Hotel został usunięty pomyślnie!');
        }
        else {
            $hotel = Hotel::find($id);
            $hotel->name = $request->name;
            $hotel->voivode_id = $request->voivode;
            $hotel->city_id = $request->city;
            $hotel->price = $request->price;
            $hotel->comment = $request->comment;
            $hotel->save();
            new ActivityRecorder($id, 191,2);

            $request->session()->flash('adnotation', 'Hotel został edytowany pomyślnie!');
        }
        return redirect()->route('showHotels');
    }

    /**
     * Panel to managment all settings about city (VIEW)
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cityPanel(){
        $allVoivodeship = Voivodes::all();
        $allCity = Cities::all();
        return view('crmRoute.cityPanel')
            ->with('allVoivodeship',$allVoivodeship);
    }

    /**
     *  Return all city with info
     */
    public function getCity(Request $request){
        if($request->ajax()){
            $cities = Cities::select(['city.id','city.name','city.max_hour'
                ,'city.grace_period','city.status','voivodeship.name as vojName',
                'voivodeship.id as vojId',
                'city.zip_code','city.latitude','city.longitude'])
                ->join('voivodeship','voivodeship.id','city.voivodeship_id')
                ->get();
            return datatables($cities)->make(true);
        }
    }

    public function findCityByDistance($city, $currentDate,$clientRoutesInfoWithUsedCities,$cities, $removeLimit = false){
        $distance = 100;
        if($removeLimit){
            $voievodeshipRound = Cities::select(DB::raw('voivodeship.id as id,voivodeship.name,city.name as city_name,city.id as city_id, city.max_hour as max_hour', 'city.max_month_show as max_month_show'))
                ->join('voivodeship', 'voivodeship.id', 'city.voivodeship_id')
                ->get();
        }else {
            $voievodeshipRound = Cities::select(DB::raw('voivodeship.id as id,voivodeship.name,city.name as city_name,city.id as city_id, city.max_hour as max_hour, city.max_month_show as max_month_show,
            ( 3959 * acos ( cos ( radians(' . $city->latitude . ') ) * cos( radians( `latitude` ) )
             * cos( radians( `longitude` ) - radians(' . $city->longitude . ') ) + sin ( radians(' . $city->latitude . ') )
              * sin( radians( `latitude` ) ) ) ) * 1.60 AS distance'))
                ->join('voivodeship', 'voivodeship.id', 'city.voivodeship_id')
                ->having('distance', '<=', $distance)
                ->get();
        }
        //part responsible for grace period
        if($currentDate != 0) {
            $checkedCities = array(); //In this array we indices cities that should not be in route
            foreach($clientRoutesInfoWithUsedCities as $item) {
                $properDate = date_create($currentDate);
                $properDatePom = date_create($currentDate);
                //wartość karencji dla danego miasta
                $gracePeriod = $item->grace_period;
//                $gracePeriod = null;
//                if($item->city_id == $city->id){
//                    $gracePeriod = $city->grace_period;
//                }else{
//                    $gracePeriod = null;
//                }
                $goodDate = date_create($item->date);
                $dateDifference = date_diff($properDate,$goodDate, true);
                $dateDifference = $dateDifference->format('%a');
                $dateString = $dateDifference . " days";
                $availableAtDate = date_add($properDatePom,date_interval_create_from_date_string($dateString));
                $availableAtDate = date_format($availableAtDate, "Y-m-d");
                if($dateDifference <= $gracePeriod) {
                        $cityInfoObject = new \stdClass();
                        $cityInfoObject->city_id = $item->city_id;
                        $cityInfoObject->available_date =  date_format(date_add($goodDate,date_interval_create_from_date_string(($gracePeriod).' days') ), "Y-m-d");
                        array_push($checkedCities, $cityInfoObject);
                }
            }
            $voievodeshipRound->map(function($item) use($checkedCities, $currentDate){
                $firstDayOfThisMonth = date('Y-m-01', strtotime($currentDate));
                $lastDayOfThisMonth = date('Y-m-t', strtotime($currentDate));
                $allRecordsFromClientRouteInfo = ClientRouteInfo::where('city_id', '=', $item->city_id)
                    ->where('client_route_info.status', '=', 1)
                    ->whereBetween('client_route_info.date', [$firstDayOfThisMonth, $lastDayOfThisMonth])
                    ->get();

                $numberOfRecords = $allRecordsFromClientRouteInfo->count();
                if($numberOfRecords > $item->max_month_show) {
                    $item->max_month_exceeded = 1;
                }
                else {
                    $item->max_month_exceeded = 0;
                }

                $hourNumber = 0; //This variable counts how many times city was used in grace period
                foreach($checkedCities as $cityRecords) {
                    if ($cityRecords->city_id == $item->city_id) {
                        $hourNumber++;
                    }
                }
                $blockFlag = false;
                foreach($checkedCities as $blockedCity) {
                    if($blockedCity->city_id == $item->city_id) {
                        $blockFlag = true;
                        $item->block = 1;
                        $item->available_date = $blockedCity->available_date;
                        if($item->max_hour > $hourNumber) { // limit of hours isn't exceeded
                            $hourDifference = $item->max_hour - $hourNumber;
                            $item->exceeded = 0; // indices that this city is still available for couple of hours
                            $item->used_hours = $hourDifference;
//                            $item->used_hours = $hourNumber;
                        }
                        else {
                            $hourDifference = $hourNumber - $item->max_hour;
                            $item->used_hours = $hourDifference;
                            $item->exceeded = 1; // indices that this city is not available.
                        }
                    }
                }

                if($blockFlag == false) {
                    $item->block = 0;
                    $item->available_date = 0;
                    $item->used_hours = 0;
                    $item->exceeded = 0;
                }

                return $item;
            });

        }

        return $voievodeshipRound;
    }

    /**
     *  Return Round Voievodeship and city
     */
    public function getVoivodeshipRound(Request $request){
        if($request->ajax()) {
            $cityId = $request->cityId;
            $currentDate = $request->currentDate;
            if($request->has('removeLimit'))
                $removeLimit = filter_var($request->removeLimit, FILTER_VALIDATE_BOOLEAN);
            else
                $removeLimit = false;

            $cities = Cities::all();
            $city = Cities::where('id', '=', $cityId)->first();
            //part responsible for grace period
            $clientRouteInfoAll = ClientRouteInfo::select('client_route_info.date','client_route_info.city_id','city.grace_period', 'city.max_month_show as max_month_show')
                ->join('city','city.id','client_route_info.city_id')
                ->where('client_route_info.status', '=', 1)
                ->get();
            $voievodeshipRound = $this::    findCityByDistance($city, $currentDate, $clientRouteInfoAll, $cities, $removeLimit);

            $voievodeshipRound = $voievodeshipRound->groupBy('id');
            $voievodeshipDistinc = array();
            foreach ($voievodeshipRound as $item){
                array_push($voievodeshipDistinc,$item->first());
            }
            $responseArray['voievodeInfo'] = $voievodeshipDistinc;
            $responseArray['cityInfo'] = $voievodeshipRound;
            return $responseArray;
        }
    }


    /**
     * Save new/edit hotel
     * @param Request $request
     */
    public function saveNewHotel(Request $request)
    {
        if ($request->ajax()) {
            $data = [];
            $action = 0;
            if ($request->hotelId == 0) { // new Hotel
                $newHotel = new Hotel();
                $data = ['T' => 'Dodanie nowego hotelu'];
                $action = 1;
            } else {    // Edit Hotel
                $newHotel = Hotel::find($request->hotelId);
                $data = ['T' => 'Edycja hotelu'];
                $action = 2;
            }
            $newHotel->bidType = $request->bidType;
            $newHotel->city_id = $request->city;
            $newHotel->street = $request->street;
            //$newHotel->price    = $request->price;
            $newHotel->name = $request->name;
            $newHotel->voivode_id = $request->voivode;
            $newHotel->comment = $request->comment;
            $newHotel->status = $request->hotelStatus;
            $newHotel->zip_code = $request->zipCode;
            $newHotel->payment_method_id = $request->paymentMethodId;
            $newHotel->parking = $request->parking;
            $newHotel->hour_bid = $request->hourBid;
            $newHotel->daily_bid = $request->dailyBid;
            $newHotel->save();

            $emails = $request->emails;
            $phones = $request->phones;

            $hotelContactsIds =  HotelsContacts::where('hotel_id','=', $newHotel->id)->get()->pluck('id')->toArray();
            if ((is_array($emails) || is_object($emails)) && !is_null($emails)) {
                foreach ($emails as $email) {
                    $contact = null;
                    if ($email['new'] === 'false') {
                        $contact = HotelsContacts::find($email['id']);
                        if(array_search($email['id'],$hotelContactsIds) !== false) {
                            unset($hotelContactsIds[array_search($email['id'], $hotelContactsIds)]);
                        }
                    } else {
                        $contact = new HotelsContacts;
                    }
                    $contact->hotel_id = $newHotel->id;
                    $contact->contact = $email['value'];
                    $contact->type = 'mail';
                    $contact->suggested = $email['suggested'] == 'true' ? 1 : 0;
                    $contact->save();
                }
            }
            if ((is_array($phones) || is_object($phones)) && !is_null($phones)) {
                foreach ($phones as $phone) {
                    $contact = null;
                    if ($phone['new'] === 'false') {
                        $contact = HotelsContacts::find($phone['id']);
                        if(array_search($phone['id'],$hotelContactsIds) !== false) {
                            unset($hotelContactsIds[array_search($phone['id'], $hotelContactsIds)]);
                        }
                    } else {
                        $contact = new HotelsContacts;
                    }
                    $contact->hotel_id = $newHotel->id;
                    $contact->contact = $phone['value'];
                    $contact->type = 'phone';
                    $contact->suggested = $phone['suggested'] == 'true' ? 1 : 0;
                    $contact->save();
                }
            }

            if(!is_null($hotelContactsIds)){
                HotelsContacts::whereIn('id',$hotelContactsIds)->delete();
            }

            HotelsClientsExceptions::where('hotel_id', '=', $newHotel->id)->delete();
            $clientsExceptions = $request->clientsExceptions;
            if ((is_array($clientsExceptions) || is_object($clientsExceptions)) && !is_null($clientsExceptions)) {
                foreach ($clientsExceptions as $clientExceptionId) {
                    $hotelClientException = new HotelsClientsExceptions;
                    $hotelClientException->hotel_id = $newHotel->id;
                    $hotelClientException->client_id = $clientExceptionId;
                    $hotelClientException->save();
                }

            }

            //saving information of new hotel for uploading files in uploadHotelFilesAjax method
            session()->put('savedHotelId', $newHotel->id);
            new ActivityRecorder(array_merge($data, $newHotel->toArray()), 198, $action);
            return 200;
        }
    }

    /**
     * Upload hotel invoice templates
     */
    public function uploadHotelFilesAjax(Request $request){
        if(session()->has('savedHotelId')){
            $fileNames = json_decode($request->fileNames);
            foreach ($fileNames as $fileName) {
                $hotelInvoiceTemplatesPath =  $fileName.'_files';

                $file = $request->file($fileName);
                if ($file !== null) {
                    $img = $file->getClientOriginalName();

                    // get uploaded file's extension
                    $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));

                    // check's valid format
                    if (in_array($ext, $this->validHotelInvoiceTemplatesExtensions)) {
                        if (!in_array($hotelInvoiceTemplatesPath, Storage::allDirectories())) {
                            Storage::makeDirectory($hotelInvoiceTemplatesPath);
                        }
                        $hotelId = session()->get('savedHotelId');
                        //insert $path in the database
                        $hotel = Hotel::find($hotelId);
                        $hotel->invoice_template_path = $file->storeAs($hotelInvoiceTemplatesPath, rand(1000,100000).'_'.$fileName.'_'.$hotelId.'.'. $ext);
                        $hotel->save();
                        new ActivityRecorder(array_merge(['T'=>'Dodanie szablonu faktury do hotelu'],$hotel->toArray()),198, 1);
                        session()->remove('savedHotelId');
                        return 'success';
                    } else {
                        session()->remove('savedHotelId');
                        return '';
                    }
                }
                session()->remove('savedHotelId');
            }
        }
        return 'fail';
    }

    public static function downloadHotelFiles($id){
        $hotel = Hotel::find($id);
        $url = $hotel->invoice_template_path;
        return Storage::download($url);
    }

    /**
     * Save new/edit city
     * @param Request $request
     */
    public function saveNewCity(Request $request){
        if($request->ajax()){
            $data = [];
            $action = 0;
            if($request->cityID == 0) { // new city
                $newCity = new Cities();
                $data = ['T' => 'Dodanie nowego miasta'];
                $action = 1;
            }
            else{    // Edit city
                $newCity = Cities::find($request->cityID);
                $data = ['T'=>'Edycja miasta'];
                $action = 2;
            }
            $newCity->voivodeship_id = $request->voiovedshipID;
            $newCity->name = $request->cityName;
            $newCity->max_hour = $request->eventCount;
            $newCity->grace_period = $request->gracePeriod;
            $newCity->latitude = $request->latitude;
            $newCity->longitude = $request->longitude;
            $newCity->zip_code = $request->zipCode;
            $newCity->max_month_show = $request->maxShows;
            $newCity->median = $request->median;

            if(is_null($request->status)) {
                $newCity->status = 0;
            }
            else {
                $newCity->status = $request->status;
            }


//            if($request->weekGrace != '') {
//                $newCity->grace_week = $request->weekGrace;
//            }
//            else {
//                $newCity->grace_week = 0;
//            }

            $newCity->save();
            new ActivityRecorder(array_merge(['T'=>'Dodanie nowego miasta'],$newCity->toArray()), 207, 1);

            return 200;
        }
    }

    /**
     * turn off hotel change status to 0 disable or 1 avaible
     * @param Request $request
     */
    public function changeStatusHotel(Request $request){
        if($request->ajax()){
            $newHotel = Hotel::find($request->hotelId);
            if($newHotel->status == 0) {
                $newHotel->status = 1;
//                new ActivityRecorder(null, 193, 3);
            }

            else {
                $newHotel->status = 0;
//                new ActivityRecorder(null, 193, 4);
            }
            new ActivityRecorder(array_merge(['T'=>'Zmiana statusu hotelu'], $newHotel->toArray()),198,4);
            $newHotel->save();
        }
    }

    /**
     * turn off city change status to 1 disable or 0 avaible
     * @param Request $request
     */
    public function changeStatusCity(Request $request){
        if($request->ajax()){
            $newCity = Cities::find($request->cityId);
            if($newCity->status == 0) {
                $newCity->status = 1;
                //new ActivityRecorder(null, 193, 3);
            }

            else {
                $newCity->status = 0;
                //new ActivityRecorder(null, 193, 4);
            }

            new ActivityRecorder(array_merge(['T'=>'Zmiana statusu miasta'], $newCity->toArray()),207,4);
            $newCity->save();
        }
    }
    /**
     * find city by id
     */
    public function findCity(Request $request){
        if($request->ajax()){
            $city = Cities::find($request->cityId);
            return $city;
        }
    }
    /**
     * find Hotel by id
     */
    public function findHotel(Request $request){
        if($request->ajax()){
            $hotel = Hotel::find($request->hotelId);
            $files = Storage::files(dirname($hotel->invoice_template_path));
            if(in_array($hotel->invoice_template_path,$files))
                $hotel->invoice_template_path = basename($hotel->invoice_template_path);
            else
                $hotel->invoice_template_path = '';
            $contacts = HotelsContacts::where('hotel_id','=',$hotel->id)->get();
            $clientsExceptions = HotelsClientsExceptions::where('hotel_id','=',$hotel->id)->get();
            return ['hotel'=>$hotel,'contacts'=>$contacts, 'clientsExceptions'=>$clientsExceptions->pluck('client_id')->toArray()];
        }
    }


    /**
     * This method returns view showRoutesDetailed
     */
    public function showRoutesDetailedGet() {
        $year = date('Y',strtotime("this year"));

        $weeksString = date('W', strtotime("this week"));
        $numberOfLastYearsWeek = date('W',mktime(0, 0, 0, 12, 30, $year));

        $departmentInfo = DB::table('department_info')->select(DB::raw('
        department_info.id as id, 
        department_type.name as name, 
        departments.name as name2
        '))
            ->join('department_type', 'department_info.id_dep_type', '=', 'department_type.id')
            ->join('departments', 'department_info.id_dep', '=', 'departments.id')
            ->get();

        return view('crmRoute.showRoutesDetailed')
            ->with('lastWeek', $numberOfLastYearsWeek)
            ->with('currentWeek', $weeksString)
            ->with('currentYear', $year)
            ->with('departmentInfo', $departmentInfo);
    }

    /**
     * @param
     * @return This method send to datatable info about client_route_info records.
     */
    public function showRoutesDetailedAjax(Request $request) {
        $detailedInfo = ClientRouteInfo::where('status', '=', 1)->get();
        $cities = Cities::all();
//        $detailedInfo->map(function($item) {
//
//            return $item;
//        });

        return datatables($detailedInfo)->make(true);
    }


    /**
     * CampaignsInfo
     * @params: weeks - array, years - array, departments - array, typ - array
     * This method returns to server data for datatable about all records from ClientRouteInfo table.
     */

    public function campaignsInfo(Request $request){
        $years = $request->years;
        $weeks = $request->weeks;
        $departments = $request->departments;
        $typ = $request->typ;

        $campaignsInfo = ClientRouteInfo::select(DB::raw('
        client_route_info.id as id,
        client_route_info.date as date,
        client_route_info.hour as hour,
        client_route_info.pbx_campaign_id as nrPBX,
        client_route_info.baseDivision as baseDivision,
        client_route_info.verification as verification,
        client_route_info.weekOfYear as weekOfYear,
        client_route_info.limits as limits,
        client_route_info.actual_success as actual_success,
        YEAR(client_route_info.date) as year,       
        ( case when client_route_info.actual_success is null then 0 - client_route_info.limits
         else
          client_route_info.actual_success - client_route_info.limits end) as loseSuccess,       
        client.name as clientName,
        departments.name as departmentName,
        department_type.name as departmentName2,
        client_route_info.comment as comment,
        city.name as cityName,
        0 as totalScore,
        client_route.type as typ
        '))
        ->join('client_route','client_route.id','client_route_info.client_route_id')
        ->leftjoin('client','client.id','client_route.client_id')
        ->leftjoin('city','city.id','client_route_info.city_id')
        ->leftjoin('department_info','department_info.id','client_route_info.department_info_id')
        ->leftjoin('departments','departments.id','department_info.id_dep')
        ->leftjoin('department_type', 'department_type.id', '=', 'department_info.id_dep_type')
        ->where('client_route_info.status', '=', 1) //now it's important
        ->whereIn('client_route.status',[1,2]);

        if($years[0] != '0') {
            $campaignsInfo = $campaignsInfo->whereIn(DB::raw('YEAR(client_route_info.date)'), $years);
        }

        if($weeks[0] != '0') {
            $campaignsInfo = $campaignsInfo->whereIn('weekOfYear', $weeks);
        }

        if($departments[0] != '0') {
            $campaignsInfo = $campaignsInfo->whereIn('client_route_info.department_info_id', $departments);
        }

        if($typ[0] != '0') {
            $campaignsInfo = $campaignsInfo->whereIn('client_route.type', $typ);
        }
        return datatables($campaignsInfo->get())->make(true);
    }

    /**
     * @param ids - array, limit - number, comment - text, verification - (0,1), invitation - number, department - number
     * @return adnotation for user
     * This method changes limits for selected by user records.
     */
    public function showRoutesDetailedUpdateAjax(Request $request) {
        $ids = json_decode($request->ids);
        $nrPBX = $request->nrPBX;
        if(!is_numeric($nrPBX)){
            $nrPBX = null;
        }

        $baseDivision = $request->baseDivision;
        $limit = $request->limit;
        $comment = $request->comment;
        $invitation = $request->invitation;
        $department = $request->department;
        $verification = $request->verification;
        $liveInvitations = $request->liveInvitation;

        $clientRouteInfoRecords = ClientRouteInfo::where('status', '=', 1)->whereIn('id', $ids)->get();

        if($nrPBX !=''){
            foreach($clientRouteInfoRecords as $record) {
                $record->pbx_campaign_id = $nrPBX;
                $record->save();
            }
        }
        if($baseDivision !=''){
            foreach($clientRouteInfoRecords as $record) {
                $record->baseDivision = $baseDivision;
                $record->save();
            }
        }
        if($limit != '') {
            foreach($clientRouteInfoRecords as $record) {
                $record->limits = $limit;
                $record->save();
            }
        }

        if($comment != '') {
            foreach($clientRouteInfoRecords as $record) {
                $record->comment = $comment;
                $record->save();
            }
        }

        if($invitation != '') {
            foreach($clientRouteInfoRecords as $record) {

            }
        }

        if($department != '') {
            foreach($clientRouteInfoRecords as $record) {
                $record->department_info_id = $department;
                $record->save();
            }
        }

        if($verification != '') {
            foreach($clientRouteInfoRecords as $record) {
                $record->verification = $verification;
                $record->save();
            }
        }

        if($liveInvitations != '') {
            foreach($clientRouteInfoRecords as $record) {
                $record->actual_success = $liveInvitations;
                $record->save();
            }
        }


        if(count($clientRouteInfoRecords) > 1) {
            $adnotation = "Rekordy zostały zmienione";
        }
        else {
            $adnotation = "Rekord został zmieniony";
        }

        $log = "ClientRouteInfoIds: ";
        foreach($clientRouteInfoRecords as $record) {
            $log .= $record->id . ', ';
        }

        new ActivityRecorder(['T'=>'Edycja informacji o kampaniach','campaign_ids' => '['.implode(",",$clientRouteInfoRecords->pluck('id')->toArray()).']'],215,2);

        return $adnotation;
    }

    /**
     * This method returns view showCitiesStatistics
     */
    public function showCitiesStatisticsGet() {

        return view('crmRoute.showCitiesStatistics');
    }

    /**
     * @param dateStart, dateStop
     * This method returns data about cities used in given range of dates
     */
    public function showCitiesStatisticsAjax(Request $request) {
        $dateStart = $request->startDate;
        $dateStop = $request->stopDate;

        $clientRouteInfo = DB::table('client_route_info')->select(DB::raw('
            city.name as cityName,
            city.id as cityId,
            voivodeship.name as voivodeName,
            COUNT(client_route_info.city_id) as ilosc
        '))
            ->join('city', 'city.id', '=', 'client_route_info.city_id')
            ->join('voivodeship', 'voivodeship.id', '=', 'city.voivodeship_id')
            ->where('client_route_info.status', '=', 1)
            ->whereBetween('client_route_info.date', [$dateStart, $dateStop])
            ->groupBy('voivodeship.name', 'city.name')
            ->get();
        return datatables($clientRouteInfo)->make(true);
    }

    /**
     * @param City_id, dateStart, dateStop
     * This method returns data about clientROuteInfo records from given range of dates.
     */
    public function getClientRouteInfoRecords(Request $request) {
        $cityId = $request->cityId;
        $dateStart = $request->dateStart;
        $dateStop = $request->dateStop;

        $clientRouteInfoRecords = ClientRouteInfo::select('city.name as cityName', 'client_route_info.date as date')
            ->join('city', 'city.id', '=', 'client_route_info.city_id')
            ->where('city_id', '=', $cityId)
            ->where('client_route_info.status', '=', 1)
            ->whereBetween('date', [$dateStart, $dateStop])
            ->orderBy('date')
            ->get();

        return $clientRouteInfoRecords;
    }

    /**
     * Show aheadPlanning view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function aheadPlanningGet(){
        $year = date('Y',strtotime("this year"));

        $weeksString = date('W', strtotime("this week"));
        $numberOfLastYearsWeek = date('W',mktime(0, 0, 0, 12, 30, $year));

        $departmentInfo = DB::table('department_info')->select(DB::raw('
        department_info.id as id, 
        department_type.name as name, 
        departments.name as name2
        '))
        ->join('department_type', 'department_info.id_dep_type', '=', 'department_type.id')
        ->join('departments', 'department_info.id_dep', '=', 'departments.id')
        ->where('id_dep_type','=',2)
        ->get();

        return view('crmRoute.aheadPlanning')
            ->with('lastWeek', $numberOfLastYearsWeek)
            ->with('currentWeek', $weeksString)
            ->with('currentYear', $year)
            ->with('departmentInfo', $departmentInfo);
    }

    public function getaHeadPlanningInfo(Request $request){
        $departmentInfo = DB::table('department_info')->select(DB::raw('
        department_info.id as id, 
        department_type.name as name, 
        departments.name as name2
        '))
            ->join('department_type', 'department_info.id_dep_type', '=', 'department_type.id')
            ->join('departments', 'department_info.id_dep', '=', 'departments.id')
            ->where('id_dep_type','=',2)
            ->get();

        $startDate  = $request->startDate;
        $stopDate   = $request->stopDate;
        $actualDate = $startDate;
        $aheadPlanningData = collect();

        $routeInfoOverall = ClientRouteInfo::select(DB::raw('
            date,
            department_info_id,
            SUM(limits) as sumOfLimits,
            SUM(actual_success) as sumOfActualSuccess
        '))
            ->where('client_route_info.status', '=', 1)
            ->groupBy('date', 'department_info_id')
            ->get();

        while($actualDate <= $stopDate){
            $dayCollect = collect();
            $dayCollect->offsetSet('numberOfWeek',date('W',strtotime($actualDate)));
            $dayCollect->offsetSet('dayName',$this::getNameOfWeek($actualDate));
            $dayCollect->offsetSet('day',$actualDate);
            $totalScore = 0;
            $allSet = true;


            $unallocatedLimits = $routeInfoOverall
                ->where('department_info_id','=',null)
                ->where('date', '=', $actualDate)
                ->first()['sumOfLimits'];

            foreach ($departmentInfo as $item){
                $routeInfo = $routeInfoOverall
                    ->where('department_info_id' ,'=', $item->id)
                    ->where('date', '=', $actualDate)
                    ->first();

                $dayLimit = $routeInfo['sumOfLimits'];
                $daySuccess = $routeInfo['sumOfActualSuccess'];

                $wynik = (is_null($daySuccess) ? 0 : $daySuccess) - (is_null($dayLimit) ? 0 : $dayLimit) - (is_null($unallocatedLimits) ? 0 : $unallocatedLimits);
                $dayCollect->offsetSet($item->name2, $wynik);

                $totalScore += $wynik;
            }
            $isSet = ClientRouteInfo::where('date','=',$actualDate)
                ->where('department_info_id','=',null)
                ->where('status', '=', 1)
                ->get()
                ->count();
            if($isSet != 0)
                $allSet = "Nie";
            else
                $allSet = "Tak";
            $dayCollect->offsetSet('allSet',$allSet);
            $dayCollect->offsetSet('totalScore',$totalScore);
            $aheadPlanningData->push($dayCollect);
            $actualDate = date('Y-m-d', strtotime($actualDate. ' + 1 days'));
        }

        $departmentsInvitationsAverages = $request->departmentsInvitationsAverages;
        if($departmentsInvitationsAverages == null){
            $stopDate = date('Y-m-d');
            $startDate = date('Y-m-d', strtotime($stopDate. ' - 14 days'));
            $departmentsInvitationsAverages = $this->getDepartmentsInvitationsAverages($startDate,$stopDate,$routeInfoOverall,$departmentInfo);
        }
        $allInfoCollect = collect();
        $allInfoCollect->offsetSet('aheadPlanningData', $aheadPlanningData);
        $allInfoCollect->offsetSet('departmentsInvitationsAveragesData',$departmentsInvitationsAverages);
        return $allInfoCollect;
    }

    public function getDepartmentsInvitationsAverages($startDate, $stopDate, $routeInfoOverall, $departmentInfo){
        $departmentsInvitationsAveragesInfo = collect();
        foreach ($departmentInfo as $item) {
            $actualDate = $startDate;
            $weekSum = 0;
            $weekDivider = 0;

            $saturdaySum = 0;
            $saturdayDivider = 0;

            $sundaySum = 0;
            $sundayDivider = 0;
            while ($actualDate < $stopDate) {
                $routeInfo = $routeInfoOverall->where('department_info_id' ,'=', $item->id)
                    ->where('date', '=', $actualDate)
                    ->first();
                if($routeInfo['sumOfActualSuccess'] != 0){
                    if(date('N',strtotime($actualDate)) <6){
                        $weekSum += $routeInfo['sumOfActualSuccess'];
                        $weekDivider++;
                    } else if (date('N', strtotime($actualDate)) == 6) {
                        $saturdaySum += $routeInfo['sumOfActualSuccess'];
                        $saturdayDivider++;
                    } else if (date('N', strtotime($actualDate)) == 7) {
                        $sundaySum += $routeInfo['sumOfActualSuccess'];
                        $sundayDivider++;
                    }
                }

                $actualDate = date('Y-m-d', strtotime($actualDate. ' + 1 days'));
            }

            $departmentAverages = collect();
            $average = 0;
            if($weekDivider != 0){
                $average = $weekSum / $weekDivider;
            }
            $departmentAverages->offsetSet('week',floor($average));

            if($saturdayDivider != 0){
                $average = $saturdaySum / $saturdayDivider;
            }else{
                $average = $average*95/100;
            }
            $departmentAverages->offsetSet('saturday',floor($average));

            if($sundayDivider != 0){
                $average = $sundaySum / $sundayDivider;
            }else{
                $average = $average*80/100;
            }
            $departmentAverages->offsetSet('sunday',floor($average));

            $departmentsInvitationsAveragesInfo->offsetSet($item->name2, $departmentAverages);
        }
        return $departmentsInvitationsAveragesInfo;
    }

    private function getNameOfWeek($date){
        $arrayOfWeekName = [
            '1' => 'Poniedziałek',
            '2' => 'Wtorek',
            '3' => 'Środa',
            '4' => 'Czwartek',
            '5' => 'Piątek',
            '6' => 'Sobota',
            '7' => 'Niedziela'];
        return $arrayOfWeekName[date('N',strtotime($date))+0];
    }

    /**
     * This method returns view presentationStatistics with a data necessary for table
     */
    public function presentationStatisticsGet()
    {

        $year = date('Y',strtotime("this year"));
        $currentMonth = date('m', strtotime("now"));

        $actualMonth = date('Y-m');
        $actualClientsId = ClientRouteInfo::
            join('client_route','client_route.id','client_route_info.client_route_id')
            ->where('date','like',$actualMonth.'%')
            ->where('client_route_info.status', '=', 1)
            ->where('client_route.status', '=', 1)
            ->groupBy('client_route.client_id')
            ->get()
            ->pluck('client_id')->toArray();
        $date = new DateTime(date('Y-m').'-01');
        $week = $date->format("W");
        //Pobranie równych czterech tygodni
        $split_month = $this->monthPerWeekDivision(date('m'),date('Y'));
        $allInfo = Clients::select(DB::raw(
                'client.id,
                client.name,
                client.type,
                count(client_route_info.client_route_id) as amount,
                client_route_info.date
                '))
            ->join('client_route','client_route.client_id','client.id')
            ->join('client_route_info','client_route_info.client_route_id','client_route.id')
            ->where('client_route.status', '=', 1)
            ->where('client_route_info.status', '=', 1)
            ->whereIn('client.id',$actualClientsId)
            ->whereBetween('client_route_info.date',[$split_month[0]->date,$split_month[count($split_month)-1]->date])
            ->groupBy('id','date')
            ->get();


        foreach($split_month as $singleDay) {
            $shortDate = new \stdClass();
            $singleDay->shortDate = substr($singleDay->date, 5);
            $singleDay->hidden = 0;
        }

        $groupAllInfo = $allInfo->groupBy('type');
        $uniqueClients = $allInfo->unique('name')->groupBy('type');

        //add last sum item to split_month
        $sumObj = new \stdClass();
        $sumObj->date = 'Suma';
        $sumObj->name = 'Suma';
        $sumObj->hidden = 0;
        array_push($split_month, $sumObj);

        $clientArr = array();
        $objectsArr = array();

        /**
         * This part is responsible for creating additional arrays of objects to $groupAllInfo. Each array represents all records for given client with amount, date, name.
         */
        foreach($groupAllInfo as $item) {
            $clientCollect = $uniqueClients[$item->first()->type]->pluck('name');
            $typeArray = array();
            foreach($clientCollect as $clientList) {
                $clientArray = array();
                $weekSum = 0;
                $weekIterator = 1;
                $foreachIterator = 0;
                foreach($split_month as $day) {
                    if($day->name != 'Suma') {
                        $insertionsWithClientAndDate = $item->where('date', '=', $day->date)->where('name', '=', $clientList); //wszystkie wpisy z danego dnia dla danego klienta
                        $dataObject = new \stdClass();
                        $dataObject->name = $clientList;
                        $dataObject->date = $day->date;
                        $dataObject->dayNumber = $day->dayNumber;
                        $dataObject->week = $weekIterator;
                        $dataObject->type = 0; // 0 - day data

                        if($insertionsWithClientAndDate->count() == 0) { //jesli nie ma takiego wpisu
                            $dataObject->amount = 0;
                        }
                        else {
                            $insertionsWithClientAndDate = $insertionsWithClientAndDate->first();
                            $dataObject->amount = $insertionsWithClientAndDate->amount;
                        }
                        $weekSum += $dataObject->amount;
                        array_push($clientArray, $dataObject);
                    }
                    else {
                        $dataObject = new \stdClass();
                        $dataObject->name = $clientList;
                        $dataObject->amount = $weekSum;
                        $dataObject->type = 1; // 1 - sum
                        $dataObject->week = $weekIterator;

                        array_push($clientArray, $dataObject);
                        $weekSum = 0;
                        $weekIterator++;
                    }

                    if($foreachIterator == count($split_month)) {
                        $dataObject = new \stdClass();
                        $dataObject->name = $clientList;
                        $dataObject->amount = $weekSum;
                        $dataObject->type = 1; // 1 - sum
                        $dataObject->week = $weekIterator;

                        array_push($clientArray, $dataObject);
                    }
                    $foreachIterator++;
                }
            $item->offsetSet($clientList,$clientArray);
            }
        }

        //This part is responsible for generating sum row.
        foreach($groupAllInfo as $group) {
            $sumArray = array();
            $clientCollect = $uniqueClients[$group->first()->type]->pluck('name');
            $sumAmount = 0;
            foreach($split_month as $day) {
                $daySum = 0;
                if($day->name == "Suma") {
                    $sumObject = new \stdClass();
                    $sumObject->date = 'Suma';
                    $sumObject->daySum = 0;
                    array_push($sumArray, $sumObject);
                }
                else {
                    foreach ($clientCollect as $oneClient) {
                        foreach ($group[$oneClient] as $key => $value) {
                            if($value->type == 0) { //day data
                                if($day->date == $value->date) {
                                        $daySum += $value->amount;
                                    }
                                }
                        }

                    }
                    $sumObject = new \stdClass();
                    $sumObject->date = $day->date;
                    $sumObject->daySum = $daySum;
                    array_push($sumArray, $sumObject);
                }
            }
            $daySumObject = new \stdClass();
            $group->offsetSet("daySum", $sumArray);
        }

        return view('crmRoute.presentationStatistics')
            ->with('clients',$uniqueClients)
            ->with('days',$split_month)
            ->with('allInfo',$groupAllInfo)
            ->with('months',$this->monthArray())
            ->with('month',date('m'))
            ->with('currentYear', $year)
            ->with('currentMonth', $currentMonth);
    }

    public function presentationStatisticsAjax(Request $request) {

            $month = $request->month;
            $year = $request->year;
            $days = $this->monthPerWeekDivision($month,$year);
            return $days;
    }

    /**
     * @params: year - "2017", month - '4'
     * This function returns data to datatable about statistics from given year and month
     */
    public function presentationStatisticsPost(Request $request) {
        $year = $request->year;
        $month = $request->month;
        if($month < 10) {
            $month = '0' . $month;
        }
        $weekNumber = $request->week;
//        dd($weekNumber);

        $actualMonth = date($year . '-' . $month);
        $currentMonth = date($month);
        $actualClientsId = ClientRouteInfo::
        join('client_route','client_route.id','client_route_info.client_route_id')
            ->where('date','like',$actualMonth.'%')
            ->where('client_route_info.status', '=', 1)
            ->where('client_route.status', '=', 1)
            ->groupBy('client_route.client_id')
            ->get()
            ->pluck('client_id')->toArray();
        $date = new DateTime($year. '-' . $month . '-01');
        $week = $date->format("W");
        //Pobranie równych czterech tygodni
        $split_month = $this->monthPerWeekDivision($month,$year);
        $allInfo = Clients::select(DB::raw(
            'client.id,
                client.name,
                client.type,
                count(client_route_info.client_route_id) as amount,
                client_route_info.date
                '))
            ->join('client_route','client_route.client_id','client.id')
            ->join('client_route_info','client_route_info.client_route_id','client_route.id')
            ->where('client_route.status', '=', 1)
            ->where('client_route_info.status', '=', 1)
            ->whereIn('client.id',$actualClientsId)
            ->whereBetween('client_route_info.date',[$split_month[0]->date,$split_month[count($split_month)-1]->date])
            ->groupBy('id','date')
            ->get();
        $groupAllInfo = $allInfo->groupBy('type');
        $uniqueClients = $allInfo->unique('name')->groupBy('type');

        $lastMonthVariable = null;
        foreach($split_month as $key => $value) {
            if($weekNumber != '%') {
                if($value->weekNumber == $weekNumber) {
                    $shortDate = new \stdClass();
                    $value->shortDate = substr($value->date, 5);
                    $value->hidden = 0; //0 - show, 1 - hide
                }
                else {
                    $value->hidden = 1; //0 - show, 1 - hide
                }
            }
            else {
                $shortDate = new \stdClass();
                $value->shortDate = substr($value->date, 5);
                $value->hidden = 0; //0 - show, 1 - hide
            }
            $lastMonthVariable = $value->weekNumber;
        }


        //add last sum item to split_month if user selected last month's week or all weeks
        if($lastMonthVariable == $weekNumber || $weekNumber == '%') {
            $sumObj = new \stdClass();
            $sumObj->date = 'Suma';
            $sumObj->name = 'Suma';
            $sumObj->hidden = 0; //0 - show, 1 - hide
            array_push($split_month, $sumObj);
        }

        $clientArr = array();
        $objectsArr = array();


        /**
         * This part is responsible for creating additional arrays of objects to $groupAllInfo. Each array represents all records for given client with amount, date, name.
         */
        foreach($groupAllInfo as $item) {
            $clientCollect = $uniqueClients[$item->first()->type]->pluck('name');
            $typeArray = array();
            foreach($clientCollect as $clientList) {
                $clientArray = array();
                $weekSum = 0;
                $weekIterator = 1;
                $foreachIterator = 0;
                foreach($split_month as $day) {
//                    dd($split_month);
                    if($day->hidden == 0) {
                        if($day->name != 'Suma') {
                            $insertionsWithClientAndDate = $item->where('date', '=', $day->date)->where('name', '=', $clientList); //wszystkie wpisy z danego dnia dla danego klienta
                            $dataObject = new \stdClass();
                            $dataObject->name = $clientList;
                            $dataObject->date = $day->date;
                            $dataObject->dayNumber = $day->dayNumber;
                            $dataObject->week = $weekIterator;
                            $dataObject->type = 0; // 0 - day data

                            if($insertionsWithClientAndDate->count() == 0) { //jesli nie ma takiego wpisu
                                $dataObject->amount = 0;
                            }
                            else {
                                $insertionsWithClientAndDate = $insertionsWithClientAndDate->first();
                                $dataObject->amount = $insertionsWithClientAndDate->amount;
                            }
                            $weekSum += $dataObject->amount;
                            array_push($clientArray, $dataObject);
                            $lastMonthVariable == $day->weekNumber;
                        }
                        else {
                            $dataObject = new \stdClass();
                            $dataObject->name = $clientList;
                            $dataObject->amount = $weekSum;
                            $dataObject->type = 1; // 1 - sum
                            $dataObject->week = $weekIterator;

                            array_push($clientArray, $dataObject);
                            $weekSum = 0;
                            $weekIterator++;
                        }
                    }

                    if($foreachIterator == count($split_month)) {
                        $dataObject = new \stdClass();
                        $dataObject->name = $clientList;
                        $dataObject->amount = $weekSum;
                        $dataObject->type = 1; // 1 - sum
                        $dataObject->week = $weekIterator;

                        array_push($clientArray, $dataObject);
                    }
                    $foreachIterator++;
                }
                $item->offsetSet($clientList,$clientArray);
            }
        }

        //This part is responsible for generating sum row.
        foreach($groupAllInfo as $group) {
            $sumArray = array();
            $clientCollect = $uniqueClients[$group->first()->type]->pluck('name');
            $sumAmount = 0;
            foreach($split_month as $day) {
                if($day->hidden == 0) {
                    $daySum = 0;
                    if($day->name == "Suma") {
                        $sumObject = new \stdClass();
                        $sumObject->date = 'Suma';
                        $sumObject->daySum = 0;
                        $sumObject->hidden = 0; //0 - show, 1 - hide
                        array_push($sumArray, $sumObject);
                    }
                    else {
                        foreach ($clientCollect as $oneClient) {
                            foreach ($group[$oneClient] as $key => $value) {
                                if($value->type == 0) { //day data
                                    if($day->date == $value->date) {
                                        $daySum += $value->amount;
                                    }
                                }
                            }

                        }
                        $sumObject = new \stdClass();
                        $sumObject->date = $day->date;
                        $sumObject->daySum = $daySum;
                        $sumObject->hidden = 0; //0 - show, 1 - hide
                        array_push($sumArray, $sumObject);
                    }
                }
            }
            $daySumObject = new \stdClass();
            $group->offsetSet("daySum", $sumArray);
        }

        return view('crmRoute.presentationStatistics')
            ->with('clients',$uniqueClients)
            ->with('days',$split_month)
            ->with('allInfo',$groupAllInfo)
            ->with('months',$this->monthArray())
            ->with('month',date('m'))
            ->with('currentYear', $year)
            ->with('currentMonth', $currentMonth);
    }

    public function monthPerWeekDivision($month,$year){
        $arrayOfWeekName = [
            '1' => 'Poniedziałek',
            '2' => 'Wtorek',
            '3' => 'Środa',
            '4' => 'Czwartek',
            '5' => 'Piątek',
            '6' => 'Sobota',
            '7' => 'Niedziela'];

        $days_in_month = date('t', strtotime($year . '-' . $month));
        $numberOfWeekPreviusMonth = $this::getWeekNumber(date('Y-m-d', strtotime($year.'-'.$month.'-01'. ' - 1 days')));
        $weeks = [];
        for ($i = 1; $i <= $days_in_month; $i++) {
            $loop_day = ($i < 10) ? '0' . $i : $i ;
            $date = $year.'-'.$month.'-'.$loop_day;
            $actualWeek = $this::getWeekNumber($date);
            if($actualWeek != $numberOfWeekPreviusMonth){
                foreach($arrayOfWeekName as $key => $value) {
                    if($value == $this::getNameOfWeek($date)) {
                        $weeksObj = new \stdClass();
                        $weeksObj->date = $date;
                        $weeksObj->weekNumber = date('W', strtotime($date));
                        $weeksObj->name = $this::getNameOfWeek($date);
                        $weeksObj->dayNumber = $key;
                        array_push($weeks,$weeksObj);

                        // czy niedziela
                        if($weeksObj->name == $arrayOfWeekName[7]) {
                            $sumObj = new \stdClass();
                            $sumObj->date = 'Suma';
                            $sumObj->weekNumber = date('W', strtotime($date));
                            $sumObj->name = 'Suma';
                            array_push($weeks, $sumObj);
                        }
                    }
                }
            }
        }
        $lastNumberOfWeek = $actualWeek;
        $dateNextMonth = date('Y-m-d', strtotime($date . ' + 1 days'));
        $daysInNextMonth = date('t', strtotime($dateNextMonth));
        for ($i = 1; $i <= $daysInNextMonth; $i++) {
            $loop_day = ($i < 10) ? '0' . $i : $i ;
            $date = date('Y-m',strtotime($dateNextMonth)).'-'.$loop_day;
            $actualWeek = $this::getWeekNumber($date);
            if($actualWeek == $lastNumberOfWeek){
                foreach($arrayOfWeekName as $key => $value) {
                    if($value == $this::getNameOfWeek($date)) {
                        $weeksObj = new \stdClass();
                        $weeksObj->date = $date;
                        $weeksObj->weekNumber = date('W', strtotime($date));
                        $weeksObj->name = $this::getNameOfWeek($date);
                        $weeksObj->dayNumber = $key;
                        array_push($weeks,$weeksObj);
                    }
                }
            }else{
                break;
            }
        }
        return $weeks;
    }

    public function getWeekNumber($date){
        $actualWeek = new DateTime($date);
        $actualWeek = $actualWeek->format("W");
        return $actualWeek;
    }

    public function monthArray(){
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
        return $months;
    }
    public function getPresentationInfo(){
        $client = Clients::select('name','type')->get();
        return datatables($client)->make(true);
    }

    public function getClientRouteInfo(){

        $weeksString = date('W', strtotime("this week"));
        $numberOfLastYearsWeek = date('W',mktime(0, 0, 0, 12, 30, date('Y')));
        $clients = Clients::select('id', 'name')->get();
        return view('crmRoute.showClientRouteInfo')
            ->with('currentWeek',$weeksString)
            ->with('currentYear',date('Y'))
            ->with('lastWeek',$numberOfLastYearsWeek)
            ->with('clients', $clients);
    }

    public function datatableClientRouteInfoAjax(Request $request){
        $clientRouteInfo = ClientRouteInfo::select(
            'client.name as clientName',
            'weekOfYear',
            'client_route_info.date',
            'city.name as cityName',
            'hotels.name as hotelName',
            'hotel_price as hotelPrice',
            'user_reservation as userReservation')
            ->join('client_route', 'client_route.id', '=', 'client_route_info.client_route_id')
            ->join('client','client.id','=','client_route.client_id')
            ->join('city', 'city.id', '=', 'client_route_info.city_id')
            ->leftjoin('hotels', 'hotels.id','=','hotel_id')
            ->where('client_route_info.status', '=', 1)
            ->where('client_route.status', '=', 1)
            ->whereBetween('client_route_info.date', [$request->dateStart, $request->dateStop]);

        if($request->clients[0] != 0) {
            $clientRouteInfo = $clientRouteInfo->whereIn('client.id', $request->clients);
        }
        if($request->showWithoutHotelInput == 'true')
            $clientRouteInfo = $clientRouteInfo->where('hotels.name',null);
        return datatables($clientRouteInfo->get())->make(true);
    }

    /**
     * @param City_id, date
     * This method returns data about clientROuteInfo records from given range of dates.
     */
    public function getClientRouteInfoRecord(Request $request) {
        $cityId = $request->city_id;
        $date = $request->date;
        $dateStart = date('Y-m-d', strtotime("+1 month", strtotime($date)));
        $dateStop = date('Y-m-d', strtotime("-1 month", strtotime($date)));

        $clientRouteInfoRecords = ClientRouteInfo::select('city.name as cityName', 'client_route_info.date as date')
            ->join('city', 'city.id', '=', 'client_route_info.city_id')
            ->where('city_id', '=', $cityId)
            ->where('client_route_info.status', '=', 1)
            ->whereBetween('date', [$dateStop, $dateStart])
            ->orderBy('date')
            ->get();

        return $clientRouteInfoRecords;
    }

    public function allCitiesInGivenVoivodeAjax(Request $request) {
        if($request->ajax()) {
            $voivodeId = $request->id;
            $allCitiesFromGivenVoivode = Cities::select('id', 'name')
                ->where('voivodeship_id', '=', $voivodeId)
                ->get();

            return $allCitiesFromGivenVoivode;
        }
    }

    public function getCampaignsInvoices($id = 0){
        if($id <= 0) {
            $clients = Clients::all();
            $invoiceStatuses = InvoiceStatus::all();
            return view('crmRoute.campaignsInvoices')
                ->with('routeId', $id)
                ->with('clients', $clients)
                ->with('invoiceStatuses', $invoiceStatuses)
                ->with('firstDate', date('Y-m-d',strtotime('-7 Days')))
                ->with('lastDate', ClientRouteInfo::select('date')->where('client_route_info.status', '=', 1)->orderBy('date','desc')->limit(1)->get()[0]->date)
                ->with('validCampaignInvoiceExtensions',json_encode($this->validCampaignInvoiceExtensions));
        }else{
            $client = ClientRoute::find($id);
            return view('crmRoute.campaignsInvoices')
                ->with('routeId', $id)
                ->with('client', $client)
                ->with('validCampaignInvoiceExtensions',json_encode($this->validCampaignInvoiceExtensions));
        }
    }

    public function invoicesMail(){
        return view('mail.invoices');
    }
    public function sendMailWithInvoice(Request $request){
        if($request->ajax()){
            $campaignID =  $request->actualCampaignID;
            $campaing = ClientRouteCampaigns::find($campaignID);
            $fileName = explode("/",$campaing->invoice_path);
            $fileName = $fileName[1];
            $storageURL = $request->root().'/api/getInvoice/'.$fileName;
            $selectedMail = $request->selectedEmails;
            $messageTitle = $request->messageTitle;
            $messageBody  = $request->messageBody;
            $mail_type = 'invoices';
            $data = [
                'messageBody' => $messageBody
                ];
            $accepted_users = collect();
            foreach($selectedMail as $item){
                $users = new User();
                $users->username = $item;
                $users->first_name = '';
                $users->last_name = '';
                $accepted_users->push($users);
            }
            $this::sendMail($mail_type,$data,$accepted_users,$messageTitle,$storageURL);
            $campaing->invoice_status_id = 3;
            $campaing->invoice_send_date = date('Y-m-d G:i');
            $campaing->save();
            new ActivityRecorder(array_merge(['T'=>'Wysłanie faktury mailem'],$campaing->toArray()),225, 2);
            return 200;
        }
        return 500;
    }
    public function sendMail($mail_type,$data,$accepted_users,$mail_title,$storageURL){
        /* UWAGA !!! ODKOMENTOWANIE TEGO POWINNO ZACZĄC WYSYŁAĆ MAILE*/
        Mail::send('mail.' . $mail_type, $data, function($message) use ($accepted_users, $mail_title,$storageURL)
        {
            $message->from('noreply.verona@gmail.com', 'Verona Consulting');
            foreach($accepted_users as $user) {
                if (filter_var($user->username, FILTER_VALIDATE_EMAIL)) {
                    $message->to($user->username, $user->first_name . ' ' . $user->last_name)->subject($mail_title);
                 }
            }
            $message->attach($storageURL, array(
                    'as' => 'faktura.pdf',
                    'mime' => 'application/pdf')
            );

        });
    }
    public function downloadCampaignInvoicePDF($id){
        $clientRouteCampaign = ClientRouteCampaigns::find($id);
        $url = $clientRouteCampaign->invoice_path;
        return Storage::download($url);
    }

    public function getCampaignsInvoicesDatatableAjax(Request $request){
        $routeId = $request->routeId;
        $clientId = $request->clientId;
        $invoiceStatusId = $request->invoiceStatusId;
        $firstDate = $request->firstDate;
        $lastDate = $request->lastDate;
        $clientRouteCampaigns = ClientRouteCampaigns::select('client_route_campaigns.id',
            'invoice_path',
            'is.id as invoice_status_id',
            'is.name_pl',
            'penalty',
            'route_name',
            'invoice_send_date',
            'invoice_payment_date',
            'h.name as hotel_name',
            'c.id as client_id',
            'cri.date',
            'c.name as client_name')
            ->join('client_route_info as cri','cri.id','=','client_route_campaigns.client_route_info_id')
            ->join('invoice_status as is','client_route_campaigns.invoice_status_id','=','is.id')
            ->join('hotels as h','cri.hotel_id','=','h.id')
            ->join('client_route as cr','cri.client_route_id','=','cr.id')
            ->join('client as c','cr.client_id','=','c.id')
            ->where('cr.status', '=', 1)
            ->where('cri.status', '=', 1);
        if ($routeId > 0) {
            $clientRouteCampaigns->where('cri.client_route_id', '=', $routeId);
        } else if (!is_null($firstDate) || !is_null($lastDate)) {
            if (!is_null($firstDate) && !is_null($lastDate)) {
                $clientRouteCampaigns->whereBetween('cri.date', [$firstDate, $lastDate]);
            } else {
                if (!is_null($firstDate))
                    $clientRouteCampaigns->where('cri.date', '>=', $firstDate);
                if (!is_null($lastDate))
                    $clientRouteCampaigns->where('cri.date', '<=', $lastDate);
            }
        }
        if($clientId>0){
            $clientRouteCampaigns->where('cr.client_id','=',$clientId);
        }
        if($invoiceStatusId>0){
            $clientRouteCampaigns->where('invoice_status_id','=',$invoiceStatusId);
        }
        //dd($clientRouteCampaigns->get(),$firstDate,$lastDate);

        return datatables($clientRouteCampaigns->get())->make(true);
    }

    /**
     * Upload campaign invoice
     */
    public function uploadCampaignInvoiceAjax(Request $request){
        $fileNames = json_decode($request->fileNames);

        $success = true;
        foreach ($fileNames as $fileName) {
            $campaignInvoicePath =  $fileName.'_files';

            $file = $request->file($fileName);
            if ($file !== null) {
                $img = $file->getClientOriginalName();

                // get uploaded file's extension
                $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));

                // check's valid format
                if (in_array($ext, $this->validCampaignInvoiceExtensions)) {
                    if (!in_array($campaignInvoicePath, Storage::allDirectories())) {
                        Storage::makeDirectory($campaignInvoicePath);
                    }
                    $campaignId = json_decode($request->campaignId);
                    //insert $path in the database
                    $campaign = ClientRouteCampaigns::find($campaignId);
                    if($campaign !== null) {
                        $campaign->invoice_path = $file->storeAs($campaignInvoicePath, rand(1000, 100000) . '_' . $fileName . '_' . $campaignId . '.' . $ext);
                        $campaign->invoice_status_id = 2;
                        $campaign->save();
                        new ActivityRecorder(array_merge(['T'=>'Dodanie szablonu faktury do hotelu'],$campaign->toArray()),198, 1);
                    }
                } else {
                    $success = false;
                }
            } else{
                $success = false;
            }
        }
        if($success)
            return 'success';
        else
            return 'error';
    }

    public function getClientInfoAjax(Request $request){
        return Clients::find($request->clientId);
    }

    public function confirmPaymentAjax(Request $request)
    {
        $campaignId = $request->campaignId;
        $dateTime = $request->dateTime;
        $penalty = $request->penalty;
        $date = date_create_from_format('Y-m-d G:i', $dateTime);
        $date = $date->format('Y-m-d G:i');
        if ($date !== false) {
            $clientRouteCampaign = ClientRouteCampaigns::find($campaignId);
            if ($clientRouteCampaign !== null) {
                $clientRouteCampaign->invoice_payment_date = $date;
                $clientRouteCampaign->invoice_status_id = 4;
                $clientRouteCampaign->penalty = $penalty;
                $clientRouteCampaign->save();
                new ActivityRecorder(array_merge(['T'=>'Akceptacja zapłacenia fakury'],$clientRouteCampaign->toArray()),225, 2);
                return 'success';
            }
            return 'error';
        }else{
            return 'error';
        }
    }

    public function clientReport(Request $request){
            $data['infoClient'] = $this::getDataToCSV($request->clientID,$request->year
                ,$request->selectedWeek);
            $data['distincRouteID'] = $data['infoClient']->groupby('clientRouteID');
            return $data;
    }

    public function getDataToCSV($clientID,$year,$selectedWeek){
        if($year == 0)
            $year = '%';
        if($selectedWeek == 0)
            $selectedWeek = '%';
        $data = ClientRouteInfo::
        select(DB::raw('
            client_route_info.client_route_id as clientRouteID,
            client_route_info.date,
            client_route_info.hour,
            city.name as cityName,
            hotels.name as hotelName,
            hotels.street,
            hotels.zip_code,
            payment_methods.name as paymentMethod,
            0 as hotelContact,
            0 as toPay,
            hotels.bidType,
            case 
                when bidType = 1 then hotels.hour_bid 
                when bidType = 2 then hotels.daily_bid
                else 0
            end as bid,
            client.name as clientName,
            client_gift_type.name as clientGiftName,
            client_meeting_type.name clientMeetingName,
            hotels.id as hotelID       
        '))
            ->join('client_route','client_route.id','client_route_info.client_route_id')
            ->join('client','client.id','client_route.client_id')
            ->leftjoin('client_gift_type','client_gift_type.id','client.gift_type_id')
            ->leftjoin('client_meeting_type','client_meeting_type.id','client.meeting_type_id')
            ->leftjoin('hotels','hotels.id','client_route_info.hotel_id')
            ->leftjoin('payment_methods','payment_methods.id','hotels.payment_method_id')
            ->leftjoin('city','city.id','hotels.city_id')
            ->where('client_route.status', '=', 1)
            ->where('client_route.client_id','=',$clientID)
            ->where('client_route_info.weekOfYear','like',$selectedWeek)
            ->where('client_route_info.status', '=', 1)
            ->where(DB::raw('YEAR(client_route_info.date)'),'like',$year)
            ->orderBy('date')
            ->orderBy('cityName')
            ->orderBy('hour')
            ->get();
        $onlyHotel = Hotel::select(DB::raw('
            hotels.id as hotelID,
            hotels_contacts.*
            '))
            ->leftjoin('hotels_contacts','hotels_contacts.hotel_id','hotels.id')
            ->whereIn('hotels.id',$data->pluck('hotelID')->toArray())
            ->get();
        $routeAllreadySetBil = array();
        $data->map(function ($item) use ($onlyHotel,&$routeAllreadySetBil,$data){
            $item->hour = substr($item->hour,0,5);
            $item->zip_code = $this::zipCodeNumberToString($item->zip_code);
            //Sprawczenie czy hotel jest wpisany do trasy
            if($item->hotelID != null){
                if(!in_array([$item->clientRouteID => $item->hotelID],$routeAllreadySetBil)){
                    array_push($routeAllreadySetBil,[$item->clientRouteID => $item->hotelID]);
                    if($item->bidType == 2){
                        $item->toPay = $item->bid;
                    }else  if($item->bidType == 1){
                        $eventCount = $data->where('clientRouteID','=',$item->clientRouteID)
                            ->where('hotelID','=',$item->hotelID)
                            ->count();
                        $item->toPay = $eventCount*$item->bid;
                    }else{
                        $item->paymentMethod = 'Brak danych';
                        $item->toPay = 'Brak danych';
                    }
                }else{
                    $item->paymentMethod = '';
                    $item->toPay = '';
                }
            }
            $item->hotelContact = $this::getHotelContact($onlyHotel,$item->hotelID);
        });
        return $data;
    }
    public function hotelConfirmationGet(){
        $allClients = ClientRouteCampaigns::select(DB::raw('distinct(client.id),client.name'))
            ->join('client_route_info','client_route_info.id','client_route_campaigns.client_route_info_id')
            ->join('client_route','client_route.id','client_route_info.client_route_id')
            ->join('client','client.id','client_route.client_id')
            ->where('client_route.status', '=', 1)
            ->where('client_route_info.status', '=', 1)
            ->get();
        return view('crmRoute.hotelConfirmation')
            ->with('allClients',$allClients);
    }

    public function getConfirmHotelInfo(Request $request){

        $dayPlus = date("Y-m-d",strtotime($request->dataStart.' + 1 days'));
        $clientID = $request->clientInfo;
        $confirmStatus = $request->confirmStatus;

        if($clientID == 0)
            $clientID = '%';
        if($confirmStatus == -1)
            $confirmStatus = '%';
        $hotelToConfirm = ClientRouteCampaigns::
           select(DB::raw('
            client_route_campaigns.id as campainID,
            client_route_info.hotel_id as hotelID,
            hotels.name as hotelName,
            client_route.route_name as route_name,
            city.name as cityName,
            0 as contact,
            client.name as clientName,
            client_route_campaigns.hotel_confirm_status as confirmStatus,
            client_route_info.date as eventDate
           '))
            ->join('client_route_info','client_route_info.id','client_route_campaigns.client_route_info_id')
            ->join('client_route','client_route.id','client_route_info.client_route_id')
            ->join('client','client.id','client_route.client_id')
            ->leftjoin('hotels','hotels.id','client_route_info.hotel_id')
            ->leftjoin('city','city.id','hotels.city_id')
            ->where('client_route.status', '=', 1)
            ->where('client_route_info.date','like',$dayPlus)
            ->where('client_route_info.status', '=', 1)
            ->where('client.id','like',$clientID)
            ->where('client_route_campaigns.hotel_confirm_status','like',$confirmStatus)
            ->groupBy('client_route_info.client_route_id')
            ->groupBy('client_route_info.show_order')
            ->get();
        $onlyHotel = Hotel::select(DB::raw('
            hotels.id as hotelID,
            hotels_contacts.*
            '))
            ->leftjoin('hotels_contacts','hotels_contacts.hotel_id','hotels.id')
            ->whereIn('hotels.id',$hotelToConfirm->pluck('hotelID')->toArray())
            ->get();
        $hotelToConfirm->map(function ($item) use ($onlyHotel){
            if($item->hotelID == null){
                $item->hotelName = 'Hotel nie został przypisany !';
            }
            $item->contact =$this::getHotelContact($onlyHotel,$item->hotelID);
           return $item;
        });
        return datatables($hotelToConfirm)->make(true);
    }

    public function changeConfirmStatus(Request $request){
        if($request->ajax()){
            $campaign = ClientRouteCampaigns::find($request->campaignID);
            $campaign->hotel_confirm_status = $request->confirmStatus;
            $campaign->save();
            return 200;
        }else return 500;

    }

    public function getHotelContact($hotelGroup,$hotelID){
        $contact = $hotelGroup->where('hotelID','=',$hotelID)->where('type','like','phone');
        if(!$contact->isEmpty()){
            $concat_phone = $contact->pluck('contact')->toarray();
            if(count($concat_phone) != 0){
                $concatStr = '';
                foreach($concat_phone as $phone)
                    $concatStr .= ' '.$phone;
                return $concatStr;
            }else{
                return 'Brak Danych';
            }
        }else{
            return 'Brak Danych';
        }
    }

    /*
     * This method changes limits for given set of clientRoute ids
     */
    public function changeLimitsAjax(Request $request) {
        $limit1 = $request->limit1;
        $limit2 = $request->limit2;
        $limit3 = $request->limit3;

        $singleLimit = $request->singleLimit;

        $ids = json_decode($request->ids);
        $onlyIds = []; // here we have only client_route id's [120, 130, 132]
        foreach($ids as $id) {
            $tempArr = explode('_', $id);
            array_push($onlyIds, $tempArr[1]);
        }

        //required clientRouteCampaigns records
        $campaignRecords = ClientRouteCampaigns::select('client_route_campaigns.hour_count', 'client_route_info.date', 'client_route_campaigns.id', 'client_route_info.client_route_id', 'client_route_info.id as client_route_info_id')
            ->ActiveCampaigns($onlyIds)
            ->get();

        //create new collection and setting properites by client_route_id
        $ClientRouteCampaignsGroupedByClientRoutes = collect();
        foreach($onlyIds as $ids) {
            $ClientRouteCampaignsGroupedByClientRoutes[$ids] = null;
        }

        foreach($ClientRouteCampaignsGroupedByClientRoutes as $key => $value) { //we create collection with clientRoute keys and inside clientRouteInfo records
            $arrayOfInfo = [];
            $givenClientRouteCampaigns = $campaignRecords->where('client_route_id', '=', $key); // campaigns only from one route

            foreach($givenClientRouteCampaigns as $oneCampaign) { //working on single campaign
                $basicId = $oneCampaign->client_route_info_id; //clientRouteInfo id of first campaign show
                $numberOfHours = $oneCampaign->hour_count; // number of hours in campaign

                //this procedure assign property "onlyOne" which indices whether campaign is single show
                for($hourCount = 0; $hourCount < $numberOfHours; $hourCount++) {
                    $rec = ClientRouteInfo::where('id', '=', $basicId + $hourCount)->first();
                    if($numberOfHours == 1) {
                        $rec->onlyOne = 1;
                    }
                    else {
                        $rec->onlyOne = 0;
                    }
                    array_push($arrayOfInfo, $rec);
                }
            }
            $ClientRouteCampaignsGroupedByClientRoutes[$key] = collect($arrayOfInfo);
        }

        foreach($ClientRouteCampaignsGroupedByClientRoutes as $key => $value) { //we create collection with clientRoute keys and inside clientRouteInfo records
            $recGroupedByDate = $ClientRouteCampaignsGroupedByClientRoutes[$key]->groupBy('date'); //we are grouping records by date

            //this procedure checks whether in single day campaign is only 2 hour
            foreach($recGroupedByDate as $singleDateGroup) {
                $onlyTwoHourCampaign = null;
                $i = 0;
                if($singleDateGroup->count() == 2) {
                    $onlyTwoHourCampaign = true;
                    foreach($singleDateGroup as $singleDateItem) { //order items in date.
                        if($singleDateItem->onlyOne == 1) {
                            $onlyTwoHourCampaign = false;
                        }
                    }

                }

                //if inside single day there is only single 2 hour campaign we assign every clientRouteInfo record property onlyTwoHour = 1; Also we numerate hours inside day container
                foreach($singleDateGroup as $singleDateItem) { //order items in date.
                    if($onlyTwoHourCampaign == true) {
                        $singleDateItem->onlyTwoHour = 1;
                    }
                    else {
                        $singleDateItem->onlyTwoHour = 0;
                    }
                    $singleDateItem->nr = $i;
                    $i++;
                }

                $recGroupedByShowOrder = $singleDateGroup->groupBy('show_order');

                foreach($recGroupedByShowOrder as $orderedShow) {

                    //here we assign limits according to different scenario
                    foreach($orderedShow as $singleItem) {

                        for($show_order = 0; $show_order < 3; $show_order++) {
                            if($singleItem->show_order == $show_order && $singleItem->nr == 0) {
                                ClientRouteInfo::where('id', '=', $singleItem->id)->update(['limits' => $limit1]);
                            }
                            else if($singleItem->show_order == $show_order && $singleItem->nr == 1) {
                                ClientRouteInfo::where('id', '=', $singleItem->id)->update(['limits' => $limit2]);
                            }
                            else if($singleItem->show_order == $show_order && $singleItem->nr == 2) {
                                ClientRouteInfo::where('id', '=', $singleItem->id)->update(['limits' => $limit3]);
                            }
                        }

                        //case if there is single hour campaign
                        if($singleItem->onlyOne == 1) {
                            ClientRouteInfo::where('id', '=', $singleItem->id)->update(['limits' => $singleLimit]);
                        }

                        //case if inside one day there is only 2 hour campaign
                        if($singleItem->onlyTwoHour == 1 && $singleItem->nr == 0) {
                            ClientRouteInfo::where('id', '=', $singleItem->id)->update(['limits' => $limit2]);
                        }
                        else if($singleItem->onlyTwoHour == 1 && $singleItem->nr == 1) {
                            ClientRouteInfo::where('id', '=', $singleItem->id)->update(['limits' => $limit3]);
                        }
                    }
                }
            }
        }
        return '1';
    }

    //This method changes clientRoute status to 0 (inactive);
    public function deleteGivenRouteAjax($id, Request $request) {
        return ClientRoute::where('id', '=', $id)->update(['status' => 0]);
    }

}
