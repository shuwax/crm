<?php

namespace App\Http\Controllers;

use App\AuditCriterions;
use App\AuditHeaders;
use App\Cities;
use App\Clients;
use App\ClientRoute;
use App\ClientRouteInfo;
use App\Department_info;
use App\Hotel;
use App\Route;
use App\RouteInfo;
use App\Voivodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use function MongoDB\BSON\toJSON;
use Session;
use Symfony\Component\HttpKernel\Client;

class CrmRouteController extends Controller
{
    public function index()
    {
        $departments = Department_info::all();
        $voivodes = Voivodes::all();
        return view('crmRoute.index')->with('departments', $departments)->with('voivodes', $voivodes);
    }

    /**
     * This method saves new routes connected with client
     */
    public function indexPost(Request $request) {
        //Get values from form elements
        $voivode = $request->voivode;
        $city = $request->city;
        $hour = $request->hour;
        $date = $request->date;
        $clientIdNotTrimmed = $request->clientId;

        //explode values into arrays
        $voivodeArr = explode(',', $voivode);
        $cityArr = explode(',', $city);
        $hourArr = explode(',',$hour);
        $clientId = explode('_',$clientIdNotTrimmed)[1];
        $dateArr = explode(',',$date);

        $loggedUser = Auth::user();

        //New insertion into ClientRoute table
        $clientRoute = new ClientRoute();
        $clientRoute->client_id = $clientId;
        $clientRoute->user_id = $loggedUser->id;
        $clientRoute->save();

        //New insertions into ClientRouteInfo table
        for($i = 0; $i < count($voivodeArr); $i++) {
            for($j = 1; $j <= $hourArr[$i] ; $j++) { // for example if user type 2 hours, method will insert 2 insertions with given row.
                $clientRouteInfo = new ClientRouteInfo();
                $clientRouteInfo->client_route_id = $clientRoute->id;
                $clientRouteInfo->city_id = $cityArr[$i];
                $clientRouteInfo->voivode_id = $voivodeArr[$i];
                $clientRouteInfo->date = $dateArr[$i];
                $clientRouteInfo->save();
            }
        }
        $request->session()->flash('adnotation', 'Trasa została pomyślnie przypisana dla klienta');

        return Redirect::back();

    }

    public function specificRouteGet($id) {
        $clientRouteInfo = ClientRouteInfo::where('client_route_id', '=', $id)->get();
        $clients = Clients::all();
        $cities = Cities::all();
        $voivodes = Voivodes::all();
        $hotels = Hotel::all();

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
                $clientRId = ClientRoute::find($info->client_route_id)->client_id;
                $clientName = Clients::find($clientRId)->name;
            }

            $stdClass = new \stdClass();

            foreach($cities as $city) {
                if($info->city_id == $city->id) {
                    $stdClass->cityName = $city->name;
                }
            }

            foreach($voivodes as $voivode) {
                if($info->voivode_id == $voivode->id) {
                    $stdClass->voivodeName = $voivode->name;
                }
            }

            foreach($clients as $client) {
                if($info->client_route_id == $client->id) {
                    $stdClass->clientName = $client->name;
                }
            }

            $stdClass->client_route_id = $info->client_route_id;
            $stdClass->city_id = $info->city_id;
            $stdClass->voivode_id = $info->voivode_id;
            $stdClass->date = $info->date;
            $stdClass->hotel_id = $info->hotel_id;
            $stdClass->hour = $info->hour;

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

        return view('crmRoute.specificInfo')
            ->with('clientRouteInfo', $clientRouteInfoExtended)
            ->with('hotels', $hotels)
            ->with('clientName', $clientName);
    }

    public function specificRoutePost(Request $request) {
        $all_data = json_decode($request->JSONData); //we obtain 2 dimensional array
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
                $item->hour = $city->timeArr[$iterator] . ':00';
                $item->hotel_id = $city->hotelId;
                $item->save();
                $iterator++;
            }
        }

        return $all_data;
    }

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

    public function showClientRoutesGet() {

        return view('crmRoute.showClientRoutes');
    }

    public function showClientRoutesAjax(Request $request) {
        $clients = Clients::all();
        $client_route_info = DB::table('client_route_info')
            ->select(DB::raw('
                client.id as id,
                client.name as name
            '))
            ->join('client_route', 'client_route.id', '=', 'client_route_info.client_route_id')
            ->join('client', 'client.id', '=', 'client_route.client_id')
            ->groupBy('client.name')
            ->get();

        return datatables($client_route_info)->make(true);
    }

    public function showClientRoutesInfoAjax(Request $request) {
        $allDataArr = array();
        $cities = Cities::all();
        $hotels = Hotel::all();
        $client_route = ClientRoute::where('client_id', '=', $request->id)->pluck('id')->toArray();
        $client_route_info = ClientRouteInfo::whereIn('client_route_info', $client_route)->get();

        $data2 = $client_route_info->map(function($item) use($hotels, $cities) {
            foreach($cities as $city) {
                if($city->id == $item->city_id) {
                    $item->cityName = $city->name;
                }
            }
            foreach($hotels as $hotel) {
                if(isset($item->hotel_id)) {
                    if($hotel->id == $item->hotel_id) {
                        $item->hotelName = $hotel->name;
                    }
                }
                else {
                    $item->hotelName = "Brak przypisanego hotelu";
                }
            }
            return $item;
        });

        return datatables($data2)->make(true);
    }


    /**
     * @return $this method returns view addNewRoute with data about all voivodes
     */
    public function addNewRouteGet() {
        $voivodes = Voivodes::all();

        return view('crmRoute.addNewRoute')->with('voivodes', $voivodes);
    }

    /**
     * This method saves new route to database
     */
    public function addNewRoutePost(Request $request) {
        $voivode = $request->voivode;
        $city = $request->city;

        $voivodeIdArr = explode(',', $voivode);
        $cityIdArr = explode(',', $city);

        $cityNamesArr = array();

        foreach($cityIdArr as $city) {
            $givenCity = Cities::where('id', '=', $city)->first();
            $name = $givenCity->name;
            array_push($cityNamesArr,$name);
        }

        $nameOfRoute = '';
        foreach($cityNamesArr as $name) {
            $nameOfRoute .= $name . ' | ';
        }
        $nameOfRoute = trim($nameOfRoute, ' | ');

        $newRoute = new Route();
        $newRoute->status = 1; // 1 - aktywne dane, 0 - usunięte dane
        $newRoute->name = $nameOfRoute;
        $newRoute->save();

        foreach($voivodeIdArr as $voivodekey => $voivode) {
            foreach($cityIdArr as $citykey => $city) {
                if($voivodekey == $citykey) {
                    $newRouteInfo = new RouteInfo();
                    $newRouteInfo->routes_id = $newRoute->id;
                    $newRouteInfo->voivodeship_id = $voivode;
                    $newRouteInfo->city_id = $city;
                    $newRouteInfo->status = 1; // 1 - aktywne dane, 0 - usunięte dane
                    $newRouteInfo->save();
                }
            }

        }

        $request->session()->flash('adnotation', 'Trasa została dodana pomyślnie!');

        return Redirect::to('/showRoutes');

    }

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
            return Redirect::to('/showRoutes');
        }

    }

    /**
     * @param id of voivode
     * @return list of cities in each voivode
     */
    public function addNewRouteAjax(Request $request) {
        $cityId = $request->id;
        $all_cities = Cities::where('voivodeship_id', '=', $cityId)->get();
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
        $routes = Route::where('status', '=', 1)->get();

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

        $request->session()->flash('adnotation', 'Hotel został dodany pomyślnie!');

        return Redirect::back();

    }

    /**
     * This method returns view showHotels
     */
    public function showHotelsGet() {
        $voivodes = Voivodes::all()->sortByDesc('name');
        $cities = Cities::all()->sortBy('name');
        return view('crmRoute.showHotels')
            ->with('voivodes', $voivodes)
            ->with('cities', $cities);
    }

    /**
     * This method sends data to ajax request about all hotels for view showHotels
     */
    public function showHotelsAjax(Request $request) {
        $voivodeIdArr = $request->voivode;
        $cityIdArr = $request->city;

        if(is_null($voivodeIdArr) && is_null($cityIdArr)) {
            $hotels = Hotel::where('status', '=', 1)->get();
        }
        else if(!is_null($voivodeIdArr) != 0 && is_null($cityIdArr)) {
            $hotels = Hotel::where('status', '=', 1)
                ->whereIn('voivode_id', $voivodeIdArr)
                ->get();
        }
        else if(is_null($voivodeIdArr) && !is_null($cityIdArr) != 0) {
            $hotels = Hotel::where('status', '=', 1)
                ->whereIn('city_id', $cityIdArr)
                ->get();
        }
        else {
            $hotels = Hotel::where('status', '=', 1)->get();
        }

        $voivodes = Voivodes::all();
        $cities = Cities::all();
        $hotelArr = array();
        foreach($hotels as $hotel) {
            $hotelsExtended = new \stdClass();
            $hotelsExtended->id = $hotel->id;
            $hotelsExtended->name = $hotel->name;
            $hotelsExtended->voivode_id = $hotel->voivode_id;
            $hotelsExtended->city_id = $hotel->city_id;
            foreach($voivodes as $voivode) {
                if($hotel->voivode_id == $voivode->id) {
                    $hotelsExtended->voivodeName = $voivode->name;
                }
            }
            foreach($cities as $city) {
                if($hotel->city_id == $city->id) {
                    $hotelsExtended->cityName = $city->name;
                }
            }
            array_push($hotelArr,$hotelsExtended);
        }
        $colection = collect($hotelArr);


        return datatables($colection)->make(true);
    }

    /**
     * This method returns view hotel with data about given hotel
     */
    public function hotelGet($id) {
        $hotel = Hotel::find($id);
        $voivodes = Voivodes::all();
        $cities = Cities::all();
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
            $request->session()->flash('adnotation', 'Hotel został edytowany pomyślnie!');
        }
        return Redirect::to('/hotel/'. $id);
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

    /**
     * Save new/edit city
     * @param Request $request
     */
    public function saveNewCity(Request $request){
        if($request->ajax()){
            if($request->cityID == 0) // new city
                $newCity = new Cities();
            else    // Edit city
                $newCity = Cities::find($request->cityID);
            $newCity->voivodeship_id = $request->voiovedshipID;
            $newCity->name = $request->cityName;
            $newCity->max_hour = $request->eventCount;
            $newCity->grace_period = $request->gracePeriod;
            $newCity->status = 0;
            $newCity->save();
            return 200;
        }
    }

    /**
     * turn off city change status to 1 disable or 0 avaible
     * @param Request $request
     */
    public function changeStatusCity(Request $request){
        if($request->ajax()){
            $newCity = Cities::find($request->cityId);
            if($newCity->status == 0)
                $newCity->status = 1;
            else
                $newCity->status = 0;
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


}
