<?php

namespace App\Http\Controllers;

use App\Agencies;
use App\Candidate;
use App\CoachChange;
use App\CoachHistory;
use App\Department_info;
use App\Pbx_report_extension;
use App\SuccessorHistory;
use Exception;
use App\PrivilageRelation;
use App\User;
use App\UserEmploymentStatus;
use App\UserTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Validator;
use Session;
use App\ActivityRecorder;
use Illuminate\Support\Facades\URL;
use App\MedicalPackage;

class UsersController extends Controller
{
    public static function getUserTypesPermissionToGivePenaltyBonus(){
        $userTypesPermissionToGivePenaltyBonus = PrivilageRelation::join('links','links.id','link_id')->where('link', 'view_penalty_bonus')->get()->pluck('user_type_id')->toArray();
        return $userTypesPermissionToGivePenaltyBonus;
    }
    /**
     * Wyświetlanie widou dla telemarketera
     */
    public function add_consultantGet()
    {
        $agencies = Agencies::all();
        $user = User::find(Auth::user()->id);
        $workingUsers = User::where('status_work', '=', 1)//są zatrudnieni
        ->whereIn('user_type_id', [1, 2])
            ->where('department_info_id', '=', Auth::user()->department_info_id)// wybiera dział aktualnie zalogowanego użytkownika
            ->get();

        $workingTreners = User::whereIn('user_type_id', [4, 12, 20])
            ->where('status_work', '=', 1)
            ->where('department_info_id', '=', Auth::user()->department_info_id)
            ->get();

        return view('hr.addNewUser')
            ->with('agencies', $agencies)
            ->with('send_type', $user->department_info->type)
            ->with('type', 1)
            ->with('allActiveUser', [])
            ->with('recomendingPeople', $workingUsers)
            ->with('workingTreners', $workingTreners);

    }

    public function add_CadreGet()
    {
        $agencies = Agencies::all();
        $user_types = UserTypes::all();
        $department_info = Department_info::all();
        $allActiveUser = User::activeUser()->onlyConsultant()->get();
        return view('hr.addNewUser')
            ->with('agencies', $agencies)
            ->with('department_info', $department_info)
            ->with('user_types', $user_types)
            ->with('allActiveUser', $allActiveUser)
            ->with('type', 2);

    }

    public function uniqueUsername(Request $request)
    {
        if ($request->ajax()) {
            $user = User::where('username', '=', $request->username)->count();

            return ($user > 0) ? 1 : 0;
        }
    }

    public function uniqueEmail(Request $request)
    {
        if ($request->ajax()) {
            $user = User::where('email_off', $request->email)->get();
        }
        if ($user->isEmpty())
            echo 0;
        else
            echo 1;
    }

    public function add_userPOST(Request $request)
    {
        $redirect = 0;
        $send_type = Department_info::find(Auth::user()->department_info_id);
        $send_type = $send_type->type;
        $agencies = Agencies::all();
        $user = new User;
        $userEmployment = new UserEmploymentStatus;


        $user->max_transaction = $request->maxTransaction;
        $user->username = $request->username;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        if ($request->email == null) {
            $user->email_off = null;
        } else {
            $user->email_off = $request->email;
        }
        $user->password = bcrypt($request->password);
        if ($request->coach_id != 0) {
            $user->coach_id = $request->coach_id;
        } else {
            $user->coach_id = null;
        }
        if ($request->recommended_by != 0) {
            $user->recommended_by = $request->recommended_by;
        } else {
            $user->recommended_by = null;
        }
        $user->salary = $request->salary;
        $user->additional_salary = $request->additional_salary;
        $user->created_at = date("Y-m-d H:i:s");
        $user->updated_at = date("Y-m-d H:i:s");
        $user->password_date = date("Y-m-d");
        if ($request->phone == null) {
            $request->phone = 0;
        }

        $user->guid = base64_encode($request->password);

        if (isset($request->department_info) && isset($request->user_type)) {
            $redirect = 1;
            $user->user_type_id = $request->user_type;
            $user->department_info_id = $request->department_info;
            $user->main_department_id = $request->department_info;
        } else {
            if (Auth::user()->user_type_id == 13) {
                $user->user_type_id = 2;
            } else {
                $user->user_type_id = 1;
            }
            $user->department_info_id = Auth::user()->department_info_id;
            $user->main_department_id = Auth::user()->department_info_id;
        }
        $user->dating_type = ($request->dating_type != null) ? $request->dating_type : 0;
        $user->candidate_id = ($request->candidate_id != null) ? $request->candidate_id : null;

        if($request->candidate_id) { //candidate_id is known
            $candidate = Candidate::select('cadre_id')->where('id', '=', $request->candidate_id)->first();
            if($candidate) { //candidate has been found. => assigning created_by field to HR employee who started recruitment process.
                $user->created_by = $candidate->cadre_id;
            }
            else { //candidate hasn't been found
                $user->created_by = Auth::user()->id;
            }
        }
        else { //candidate_id is unknown
            $user->created_by = Auth::user()->id;
        }

        $user->start_work = $request->start_date;
        $user->status_work = 1;
        $user->phone = $request->phone;
        $user->private_phone = $request->private_phone;
        $user->description = $request->description;
        $user->student = $request->student;
        $user->salary_to_account = $request->salary_to_account;
        $user->agency_id = $request->agency_id;
        $user->login_phone = ($request->login_phone != null) ? $request->login_phone : 0;
        if ($request->rate == 'Nie dotyczy')
            $request->rate = 0;
        $user->rate = $request->rate;
        $user->id_manager = Auth::id();
        $user->documents = $request->documents;
        try{
            $user->save();
        }catch (Exception $exception){
            Session::flash('message_error', "Problem podczas zapisywania użytkownika");
            return Redirect::back();
        }
        if($user->user_type_id == 9){
            $successorHistory = new SuccessorHistory();
            $successorHistory->user_id = $user->id;
            $successorHistory->status = 1;
            $successorHistory->date_start = date('Y-m-d');
            try{
                $successorHistory->save();
            }catch (Exception $exception){
                dd($exception);
                Session::flash('message_error', "Problem podczas zapisywania informacji o sukcesorze");
                return Redirect::back();
            }
            $user->successorUserId = $request->successorUserId;
            $user->save();
        }
        $userEmployment->pbx_id = $request->login_phone;
        $userEmployment->pbx_id_add_date = $request->start_date;
        $userEmployment->pbx_id_remove_date = 0;
        $userEmployment->user_id = $user->id;
        try{
            $userEmployment->save();
        }catch (Exception $exception){
            Session::flash('message_error', "Problem z dodaniem użytkownika");
            return Redirect::back();
        }
        $data = [
            'T' => 'Dodanie użytkownika',
            'id' =>  $user->id,
            'max_transaction' =>  $user->max_transaction,
            'created_at' => $user->created_at,
            'dating_type' => $user->dating_type,
            'password_date' => $user->password_date,
            'username' =>  $user->username,
            'email_off' =>  $user->email_off,
            'first_name' =>  $user->first_name,
            'last_name' =>  $user->last_name,
            'updated_at' =>  $user->updated_at,
            'phone' =>  $user->phone,
            'coach_id' =>  $user->coach_id,
            'recommended_by' =>  $user->recommended_by,
            'promotion_date' => $user->promotion_date,
            'degradation_date' => $user->degradation_date,
            'email_off' =>  $user->email_off,
            'private_phone' =>  $user->private_phone,
            'description' =>  $user->description,
            'student' =>  $user->student,
            'salary_to_account' =>  $user->salary_to_account,
            'agency_id' =>  $user->agency_id,
            'rate' =>  $user->rate,
            'salary' =>  $user->salary,
            'documents' =>  $user->documents,
            'id_manager' =>  $user->id_manager,
            'additional_salary' =>  $user->additional_salary,
            'status_work' =>  $user->status_work,
            'dating_type' =>  $user->dating_type,
            'start_work' =>  $user->start_work,
            'candidate_id' =>  $user->candidate_id,
            'department_info_id' =>  $user->department_info_id,
            'main_department_id' =>  $user->main_department_id,
            'password' =>  $user->password,
            'guid' =>  $user->guid,
            'end_work' =>  $user->end_work,
            'user_type_id' =>  $user->user_type_id,
            'login_phone' =>  $user->login_phone,
        ];

        new ActivityRecorder($data, 8,1);

        /**
         * Dodanie pakietu medycznego
         */
        if ($request->medical_package_active > 0) {
            $this->addMedicalPackage($request, $user->id);
        }

        Session::flash('message_ok', "Użytkownik dodany pomyślnie!");
        return Redirect::back();

    }

    public function cadre_management_fireGet()
    {
        return view('hr.cadreManagementFire');
    }

    public function employee_managementGet()
    {
        return view('hr.employeeManagement');
    }

    public function cadre_managementGet()
    {
        return view('hr.cadreManagement');
    }

    public function edit_consultantGet($id)
    {
        $agencies = Agencies::all();
        $user = User::find($id);
        $department_info = Department_info::all();
        $userTypes = UserTypes::all();

        if ($user == null || ($user->user_type_id != 1 && $user->user_type_id != 2)) {
            return view('errors.404');
        }

        if ($user->department_info_id != Auth::user()->department_info_id) {
            return view('404');
        }

        /*
        *Wyszukuję użytkowników którzy pracują
        */
        //pobranie wszystkich użytkowników z danego działu
        $workingUsers = User::where('status_work', '=', 1)//są zatrudnieni
        ->whereIn('user_type_id', [1, 2])
            ->where('department_info_id', '=', Auth::user()->department_info_id)// wybiera dział aktualnie zalogowanego użytkownika
            ->get();

        $workingTreners = User::whereIn('user_type_id', [4, 12, 20])
            ->where('status_work', '=', 1)
            ->where('department_info_id', '=', Auth::user()->department_info_id)
            ->get();

        return view('hr.editUser')->with('agencies', $agencies)
            ->with('user', $user)
            ->with('department_info', $department_info)
            ->with('userTypes', $userTypes)
            ->with('type', 1)
            ->with('allActiveUser', [])
            ->with('userTypesPermissionToGivePenaltyBonus', UsersController::getUserTypesPermissionToGivePenaltyBonus())
            ->with('userIdsPermisionToGivePenaltyBonus', [])
            ->with('succesorVisableStatus', "hidden")
            ->with('recomendingPeople', $workingUsers)
            ->with('workingTreners', $workingTreners);

    }

    public function edit_cadreGet($id, Request $request)
    {
        $flag = $request->session()->get('flag');
        $request->session()->forget('flag');

        if (Auth::user()->user_type->all_departments == 1) {

            $user = User::find($id);
            $promotionDateFloatYearhMonth = (integer) date('Ym', strtotime($user->promotion_date));
            $nowDataeFloatYearhMonth = (integer) date('Ym');
            if($user->promotion_date == null OR $promotionDateFloatYearhMonth < $nowDataeFloatYearhMonth){
                $user->payment_type = 1;
            }else{
                $user->payment_type = 0;
            }
            $agencies = Agencies::all();
            $department_info = Department_info::all();
            $month = date('m');
            $months_names = ['Styczeń', 'Luty', 'Marzec', 'Kwiecien', 'Maj', 'Czerwiec', 'Lipiec', 'Sierpień', 'Wrzesień', 'Padziernik', 'Listopad', 'Grudzień'];

            function month_reverse($mnt)
            {
                if ($mnt < 0) {
                    $mnt += 12;
                }
                return $mnt;
            }

            $months = [$months_names[month_reverse($month - 1)], $months_names[month_reverse($month - 2)]];
            $actual_month = date('Y-m') . '%';
            $previous_month = date('Y-m', strtotime(date('Y-m') . " -1 month")) . "%";

            $penalty_bonuses[0] = DB::table('penalty_bonus')
                ->select(DB::raw('
                SUM(CASE WHEN `type` = 2 AND `status` = 1 THEN `amount` ELSE 0 END) as premia ,
                SUM(CASE WHEN `type` = 1 AND `status` = 1 THEN `amount` ELSE 0 END) as kara
            '))
                ->where('id_user', '=', $id)
                ->where('status', '=', 1)
                ->where('event_date', 'like', $actual_month)
                ->get();

            $penalty_bonuses[1] = DB::table('penalty_bonus')
                ->select(DB::raw('
                   SUM(CASE WHEN `type` = 2 AND `status` = 1 THEN `amount` ELSE 0 END) as premia ,
                   SUM(CASE WHEN `type` = 1 AND `status` = 1 THEN `amount` ELSE 0 END) as kara
               '))
                ->where('id_user', '=', $id)
                ->where('status', '=', 1)
                ->where('event_date', 'like', $previous_month)
                ->get();

            $userTypes = UserTypes::all();
            $allActiveUser = User::activeUser()->onlyConsultant()->get();
            if($user->successorUserId != null || $user->user_type_id == 9)
                $succesorVisableStatus = "";
            else
                $succesorVisableStatus = "hidden";
            return view('hr.editUser')
                ->with('agencies', $agencies)
                ->with('userTypes', $userTypes)
                ->with('user', $user)
                ->with('penalty_bonuses', $penalty_bonuses)
                ->with('month', $months)
                ->with('department_info', $department_info)
                ->with('allActiveUser', $allActiveUser)
                ->with('userTypesPermissionToGivePenaltyBonus', UsersController::getUserTypesPermissionToGivePenaltyBonus())
                ->with('userIdsPermisionToGivePenaltyBonus', [])
                ->with('succesorVisableStatus', $succesorVisableStatus)
                ->with('type', 2);
        } else if ($flag == "true") {
            return Redirect::to('edit_cadre/' . $user->id);
        } else {
            return Redirect::back();
        }
    }

    public function edit_cadrePOST($id, Request $request)
    {

        $manager_id = Auth::user()->id;
        $url_array = explode('/', URL::previous());
        $urlValidation = end($url_array);
        if ($urlValidation != $id) {
            return view('errors.404');
        }

        $user = User::find($id);
        $oldUserType = $user->user_type_id;
        $loggedUser = Auth::user();
        $userEmployment = UserEmploymentStatus::
        where('pbx_id', '=', $user->login_phone)
            ->where('user_id', '=', $user->id)
            ->orderBy('id', 'desc')
            ->first();
        $date = date("Y-m-d"); // actual date

        if ($user->user_type_id == 1 || $user->user_type_id == 2) { //users are only consultants
            if ($request->status_work == "1") { //editing working employee
                if ($user->status_work == "1") {
                    if ($user->login_phone != $request->login_phone) { //user changes pbx_id
                        if ($userEmployment) { //user has history in user_employment_status
                            $userEmployment->pbx_id_remove_date = $date;
                            $userEmployment->save();
                            $user->login_phone = $request->login_phone;
                            $userEmployment1 = new UserEmploymentStatus();
                            if ($request->login_phone == 0) {
                                $userEmployment1->pbx_id = null;
                            } else {
                                $userEmployment1->pbx_id = $request->login_phone;
                            }
                            $userEmployment1->pbx_id_add_date = $date;
                            $userEmployment1->user_id = $user->id;
                            $userEmployment1->save();

                        } else { //user has no history in user_employment_status and we add new insertion
                            $userEmployment4 = new UserEmploymentStatus(); //insertion with old pbx_id
                            if ($user->login_phone == 0) {
                                $userEmployment4->pbx_id = 0;
                            } else {
                                $userEmployment4->pbx_id = $user->login_phone;
                            }

                            $userEmployment4->user_id = $user->id;
                            $userEmployment4->pbx_id_add_date = $date;
                            $userEmployment4->pbx_id_remove_date = $date;
                            $userEmployment4->save();
                            $userEmployment1 = new UserEmploymentStatus(); //insertion with new pbx_id
                            if ($request->login_phone == 0) {
                                $userEmployment1->pbx_id = null;
                            } else {
                                $userEmployment1->pbx_id = $request->login_phone;
                            }

                            $userEmployment1->user_id = $user->id;
                            $userEmployment1->pbx_id_add_date = $date;
                            $userEmployment1->pbx_id_remove_date = 0;
                            $userEmployment1->save();
                        }
                    }
                    $user->end_work = null;
                }
            }
        }


        if ($user == null) {
            return view('errors.404');
        }

        /**
         * Opcja dodania pakietu medycznego (Jezeli zmienna $request->medical_package_is_new == 1 && $request->medical_package_active == 1)
         * Lub zmiany gdy zmienna $request->medical_package_is_edited == 1
         */
        if ($request->medical_package_is_new == 1 && $request->medical_package_active == 1) {
            $this->addMedicalPackage($request, $user->id);
        } else if ($request->medical_package_is_edited == 1 && $request->medical_package_active == 1) {
            $this->changeMedicalPackage($request, $user);
        }

        $user->max_transaction = $request->maxTransaction;
        $user->username = $request->username;
        $user->email_off = $request->username;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->updated_at = date("Y-m-d H:i:s");
        $user->phone = $request->phone;
        if ($request->coach_id != 0) {
            $user->coach_id = $request->coach_id;
        } else {
            $user->coach_id = null;
        }

        if ($request->recommended_by != 0) {
            $user->recommended_by = $request->recommended_by;
        } else {
            $user->recommended_by = null;
        }
        $user->email_off = $request->email;
        $user->private_phone = $request->private_phone;
        $user->description = $request->description;
        $user->student = $request->student;
        $user->salary_to_account = $request->salary_to_account;
        $user->agency_id = $request->agency_id;

        // if the edited user is a consultant
        if ($user->user_type_id == 1 || $user->user_type_id == 2) {
            if ($user->status_work != "0") {
                $user->login_phone = ($request->login_phone != null) ? $request->login_phone : 0;
            }
            if ($request->status_work == 0) { //firing employee
                if ($user->status_work == 1) {
                    if ($userEmployment) { //user has history in user_employment_status
                        $userEmployment->pbx_id_remove_date = $request->stop_date;
                        $user->login_phone = null;
                        $userEmployment->save();
                    } else { // user has no history in user_employment_status
                        $userEmployment2 = new UserEmploymentStatus();
                        if ($request->login_phone == 0) {
                            $userEmployment2->pbx_id = $user->login_phone;
                        } else {
                            $userEmployment2->pbx_id = $request->login_phone;
                        }
                        if($user->login_phone !== null) {
                            $userEmploymentStatusOfAnotherUserPbxId = UserEmploymentStatus::where('pbx_id', $user->login_phone)->where('user_id','<>', $user->id)->orderBy('id','desc')->first();
                            if(empty($userEmploymentStatusOfAnotherUserPbxId)){
                                $userEmployment2->pbx_id_add_date = $user->start_work;
                            }else{
                                $userWithSamePbxIdStillWorking = User::where('id',$userEmploymentStatusOfAnotherUserPbxId->user_id)->where('status_work',1)->first();
                                if(empty($userWithSamePbxIdStillWorking)){
                                    if($userEmploymentStatusOfAnotherUserPbxId->pbx_id_remove_date == null || $userEmploymentStatusOfAnotherUserPbxId->pbx_id_remove_date == '0000-00-00'){
                                        $userEmploymentStatusOfAnotherUserPbxId->pbx_id_remove_date = $user->start_work;
                                        $userEmploymentStatusOfAnotherUserPbxId->save();
                                        $userEmployment2->pbx_id_add_date = $user->start_work;
                                    }else{
                                        $userEmployment2->pbx_id_add_date = $userEmploymentStatusOfAnotherUserPbxId->pbx_id_remove_date;
                                    }
                                }else{
                                    $userEmployment2->pbx_id_add_date = $user->stop_date;
                                }
                            }
                        }else{
                            $userEmployment2->pbx_id_add_date = $user->stop_date;
                        }
                        $user->login_phone = null;
                        $userEmployment2->pbx_id_remove_date = $request->stop_date;
                        $userEmployment2->user_id = $user->id;
                        $userEmployment2->save();
                    }
                }
            }else { //re-hiring employee
                if ($user->status_work == 0) {
                    $userEmployment3 = new UserEmploymentStatus(); //adding new insertion
                    if ($request->login_phone == 0) {
                        $userEmployment3->pbx_id = null;
                    } else {
                        $userEmployment3->pbx_id = $request->login_phone;
                        $user->login_phone = $request->login_phone;
                        $user->save();
                    }

                    $userEmployment3->user_id = $user->id;
                    if(\DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->getTimestamp() <
                        \DateTime::createFromFormat('Y-m-d', $request->start_date)->getTimestamp()){
                        $userEmployment3->pbx_id_add_date = $request->start_date;
                    }else{
                        $userEmployment3->pbx_id_add_date = date('Y-m-d');
                    }
                    $userEmployment3->pbx_id_remove_date = null;
                    $userEmployment3->save();
                }
            }
        }

        $user->rate = $request->rate;
        $user->salary = $request->salary;
        $user->documents = $request->documents;
        $user->id_manager = $manager_id;
        $user->additional_salary = $request->additional_salary;
        $user->status_work = $request->status_work;
        $user->dating_type = $request->dating_type;
        $user->start_work = $request->start_date;
        if ($request->candidate_id != null)
            $user->candidate_id = $request->candidate_id;
        if ($request->department_info_id != null) {
            $check_department = Department_info::find($request->department_info_id);
            if ($check_department == null) {
                return view('errors.404');
            }
            $user->department_info_id = $request->department_info_id;
            $user->main_department_id = $request->department_info_id;
        }

        $type_redirect = 0; // 0 - brak zamiany typu konta, 1 - przekierowanie do konsultanta, 2 - do kadry
        if ($request->user_type != null && $request->user_type != 0) {
            if($request->user_type != 9) { // uprawnienia nie mogą być zmieniane na sukcesora -- tylko podczas tworzenia konta można nadać takie uprawnienia
                if($user->user_type_id == 9 ){ // uprawnienia nie mogą być zmieniane z sukcesora na inne
                    new ActivityRecorder(array_merge(['T'=>'Próba zmiany uprawnień z sukcesora'], ['user' =>$user->toArray()]),32,5);
                    return view('errors.404');
                }
                if ($user->user_type_id == 1 || $user->user_type_id == 2) {
                    if ($request->user_type > 2) {// AWANS
                        if (in_array(Auth::user()->user_type_id, [4, 9, 12])) { //jeżeli trenerzy i sukcesorzy próbują kogoś awansować to zwróć 404
                            new ActivityRecorder(array_merge(['T'=>'Próba zmiany uprawnień, do których użytkownik nie miał dostępu'], ['user' =>$user->toArray()]),10,5);
                            return view('errors.404');
                        }

                        $successorAccount = User::where('successorUserId',$user->id)->first();

                        if(!empty($successorAccount)){
                            //jeżeli konsultant ma konto sukcesora to podczas awansu konsultanta,
                            // konto sukcesora zmienia status na niepracujace
                            $successorAccount->status_work = 0;
                            $successorAccount->save();
                            $successorHistory = SuccessorHistory::where('user_id',$successorAccount->id)->first();
                            $successorHistory->date_stop = date('Y-m-d');
                            $successorHistory->save();
                        }
                        if ($userEmployment) { //gdy mamy historie w bazie danych
                            $userEmployment->pbx_id_remove_date = $date;
                            $userEmployment->save();
                        } else { //gdy nie mamy historii w bazie danych
                            $userEmployment5 = new UserEmploymentStatus();
                            $userEmployment5->user_id = $user->id;
                            if ($user->login_phone == 0) {
                                $userEmployment5->pbx_id = null;
                            } else {
                                $userEmployment5->pbx_id = $user->login_phone;
                            }
                            $userEmployment5->pbx_id_add_date = $date;
                            $userEmployment5->pbx_id_remove_date = $date;
                            $userEmployment5->save();
                        }
                        $user->login_phone = null;
                        $user->promotion_date = date('Y-m-d');
                        $type_redirect = 2;
                    }
                    $user->user_type_id = $request->user_type;
                } else if ($user->user_type_id > 2) {
                        if ($request->user_type < 3) {
                            //Degradacja
                            $user->login_phone = null;
                            $user->degradation_date = date('Y-m-d');
                            $type_redirect = 1;
                        }
                        $user->user_type_id = $request->user_type;
                }
            } else {
                if($user->user_type_id != 9 ){
                    new ActivityRecorder(array_merge(['T'=>'Próba zmiany uprawnień na sukcesora'], ['user' =>$user->toArray()]),10,5);
                    return view('errors.404');
                }
            }
        }
        if ($request->status_work == "1") {
            $user->end_work = null;
            $user->disabled_by_system = 0;
        } else {
            $user->end_work = $request->stop_date;
            if($user->user_type_id == 7){
                Department_info::where('menager_id', $user->id)->update(['menager_id' => null]);
            }
            if($user->user_type_id == 17){
                Department_info::where('menager_id', $user->id)->update(['menager_id' => null]);
                Department_info::where('regionalManager_id', $user->id)->update(['regionalManager_id' => null]);
            }
            if($user->user_type_id == 15){
                Department_info::where('director_id', $user->id)->update(['director_id' => null]);
            }
            if($user->user_type_id == 14){
                Department_info::where('director_hr_id', $user->id)->update(['director_hr_id' => null]);
            }
            if($user->user_type_id == 21){
                Department_info::where('instructor_regional_id', $user->id)->update(['instructor_regional_id' => null]);
            }
            if($user->user_type_id == 5){
                Department_info::where('hr_id', $user->id)->update(['hr_id' => null]);
                Department_info::where('hr_id_second', $user->id)->update(['hr_id_second' => null]);
            }
        }
        if ($request->password != '') {
            $user->password = bcrypt($request->password);
            $user->guid = base64_encode($request->password);
        };
        /**
         * automatyczne rozwiązanie pakietu medycznego w przypadku zakończenia pracy
         */
        if ($request->status_work == 0) {
            $month_to_end = date('Y-m-t', strtotime($request->stop_date));
            MedicalPackage::where('user_id', '=', $user->id)
                ->where('deleted', '=', 0)
                ->where('month_stop', '=', null)
                ->update(['deleted' => 1, 'month_stop' => $month_to_end]);
        }

        $promotionDateFloatYearhMonth = (integer) date('Ym', strtotime($user->promotion_date));
        $nowDataeFloatYearhMonth = (integer) date('Ym');
        if($request->payment_type == 0){
            if($promotionDateFloatYearhMonth < $nowDataeFloatYearhMonth)
                $user->promotion_date = date('Y-m-d');
        }else if($request->payment_type == 1){
            if($promotionDateFloatYearhMonth >= $nowDataeFloatYearhMonth)
                $user->promotion_date = date('Y-m-d',strtotime('last day of previous month'));
        }
        if($user->user_type_id == 9){
            $user->successorUserId = $request->successorUserId;
        }else{
            $user->successorUserId = null;
        }

        //Zmiana statusu z sukcesora na inny typ lub zwolnienie sukcesora zmiana statusu
        if($oldUserType == 9 || $user->user_type_id == 9){
            $save = false;
            if(($oldUserType == 9 && $user->user_type_id != 9) || ($oldUserType == 9 && $user->status_work == 0)){
                $successorHistory = SuccessorHistory::where('user_id',$user->id)
                    ->where('status',1)->get()->first();
                if(empty($successorHistory)){
                    $successorHistory = new SuccessorHistory();
                    $successorHistory->user_id = $user->id;
                    $successorHistory->status  = 0;
                    $successorHistory->date_start = date('Y-m-d');
                    $successorHistory->date_stop = date('Y-m-d');
                }else{
                    $successorHistory->date_stop = date('Y-m-d');
                    $successorHistory->status  = 0;
                }
                $save = true;
            }else{
                $successorHistory = SuccessorHistory::where('user_id',$user->id)
                    ->where('status',1)->get();
                if($successorHistory->isEmpty()){
                    $successorHistory = new SuccessorHistory();
                    $successorHistory->user_id = $user->id;
                    $successorHistory->status  = 1;
                    $successorHistory->date_start = date('Y-m-d');
                    $save = true;
                }
            }
            try{
                if($save)
                    $successorHistory->save();
            }catch (Exception $exception){
                dd($exception);
                Session::flash('message_error', "Problem podczas zapisywania informacji o sukcesorze");
                return Redirect::back();
            }
        }



        try{
            $user->save();
        }catch (Exception $exception){
            Session::flash('message_error', "Problem podczas zapisywania użytkownika");
            return Redirect::back();
        }
//        dd($user->toArray());
        $data = [
            'T' => 'Edycja użytkownika',
            'id' =>  $user->id,
            'max_transaction' =>  $user->max_transaction,
            'created_at' => $user->created_at,
            'dating_type' => $user->dating_type,
            'password_date' => $user->password_date,
            'username' =>  $user->username,
            'email_off' =>  $user->email_off,
            'first_name' =>  $user->first_name,
            'last_name' =>  $user->last_name,
            'updated_at' =>  $user->updated_at,
            'phone' =>  $user->phone,
            'coach_id' =>  $user->coach_id,
            'recommended_by' =>  $user->recommended_by,
            'promotion_date' => $user->promotion_date,
            'degradation_date' => $user->degradation_date,
            'email_off' =>  $user->email_off,
            'private_phone' =>  $user->private_phone,
            'description' =>  $user->description,
            'student' =>  $user->student,
            'salary_to_account' =>  $user->salary_to_account,
            'agency_id' =>  $user->agency_id,
            'rate' =>  $user->rate,
            'salary' =>  $user->salary,
            'documents' =>  $user->documents,
            'id_manager' =>  $user->id_manager,
            'additional_salary' =>  $user->additional_salary,
            'status_work' =>  $user->status_work,
            'dating_type' =>  $user->dating_type,
            'start_work' =>  $user->start_work,
            'candidate_id' =>  $user->candidate_id,
            'department_info_id' =>  $user->department_info_id,
            'main_department_id' =>  $user->main_department_id,
            'password' =>  $user->password,
            'guid' =>  $user->guid,
            'end_work' =>  $user->end_work,
            'user_type_id' =>  $user->user_type_id,
            'login_phone' =>  $user->login_phone,
            ];

        new ActivityRecorder($data,10,2);

        /**
         * Ewentualna zmiana pakietów medycznych
         */

        $redirectFlag = false; //false = brak uprawnien, true = posiada uprawnienia
        $userPrivilage = Auth::user()->user_type->all_departments;
        if ($userPrivilage == 1) {
            $redirectFlag = true;
        }
        Session::flash('message_edit', "Dane zostały zaktualizowane!");

        if ($redirectFlag) {
            $request->session()->put('flag', 'true');
        }
        Session::flash('adnotation', "Pracownik został edytowany! Brak możliwości dalszej edycji pracownika (brak uprawnień)."); //sesja wykorzystywana w /employee_management

        if ($type_redirect == 0) {
            return Redirect::back();
        } else if ($type_redirect == 1) {
            return Redirect::to('edit_consultant/' . $user->id);
        } else if ($redirectFlag == false) {
            return Redirect::to('/employee_management');
        } else {
            return Redirect::to('edit_cadre/' . $user->id);
        }
    }

    public function datatableEmployeeManagement(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('users')
                ->select(DB::raw('
                users.*,
                (CASE WHEN users.coach_id is null THEN null else coach.first_name end) as coach_first_name,
                (CASE WHEN users.coach_id is null THEN null else coach.last_name end) as coach_last_name'))
                ->leftjoin('users as coach', 'coach.id', 'users.coach_id')
                ->whereIn('users.user_type_id', [1, 2])
                ->where('users.department_info_id', Auth::user()->department_info_id);
        }
        return datatables($query)
            ->filterColumn('student', function ($query, $keyword) {
                $sql = "users.student = ?";
                if (strtolower($keyword) == 'tak')
                    $query->whereRaw($sql, ["1"]);
                else if (strtolower($keyword) == 'nie')
                    $query->whereRaw($sql, ["0"]);
            })->filterColumn('status_work', function ($query, $keyword) {
                $sql = "users.status_work = ?";
                if (mb_strtolower($keyword) == 'pracujący')
                    $query->whereRaw($sql, ["1"]);
                else if (mb_strtolower($keyword) == "niepracujący")
                    $query->whereRaw($sql, ["0"]);
            })->filterColumn('documents', function ($query, $keyword) {
                $sql = "users.documents = ?";
                if (mb_strtolower($keyword) == 'posiada')
                    $query->whereRaw($sql, ["1"]);
                else if (mb_strtolower($keyword) == "brak")
                    $query->whereRaw($sql, ["0"]);
            })->
            make(true);
    }

    public function datatableCadreManagement(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('users')
                ->join('department_info', 'department_info.id', '=', 'users.main_department_id')
                ->join('department_type', 'department_info.id_dep_type', '=', 'department_type.id')
                ->join('departments', 'department_info.id_dep', '=', 'departments.id')
                ->join('user_types', 'users.user_type_id', '=', 'user_types.id')
                ->select(DB::raw('
                users.*,
                department_type.name as department_type_name,
                departments.name as department_name,
                user_types.name as user_type_name
                '))
                ->where('users.user_type_id', '!=', 1)
                ->where('users.user_type_id', '!=', 2)
                ->where('users.status_work', '=', 1);
            return datatables($query)->make(true);
        }
    }

    public function datatableCadreManagementFire(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('users')
                ->join('department_info', 'department_info.id', '=', 'users.department_info_id')
                ->join('department_type', 'department_info.id_dep_type', '=', 'department_type.id')
                ->join('departments', 'department_info.id_dep', '=', 'departments.id')
                ->join('user_types', 'users.user_type_id', '=', 'user_types.id')
                ->select(DB::raw('
                users.*,
                department_type.name as department_type_name,
                departments.name as department_name,
                user_types.name as user_type_name
                '))
                ->where('users.user_type_id', '!=', 1)
                ->where('users.user_type_id', '!=', 2)
                ->where('users.status_work', '=', 0)
                ->orderBY('end_work', 'desc');
            return datatables($query)->make(true);
        }
    }

    public function passwordChangeGet()
    {
        return view('home.passChange');
    }

    public function passwordChangePost(Request $request)
    {
        $old_password = base64_decode(Auth::user()->guid);
        if ($request->old_pass == $old_password) {
            $user = User::find(Auth::user()->id);
            $user->password_date = date('Y-m-d');
            $user->password = bcrypt($request->new_pass);
            $user->guid = base64_encode($request->new_pass);
            $user->save();

            new ActivityRecorder('Zmiana hasła przez użytkownika, nowe hasło: ' . base64_encode($request->new_pass),39,4);
            Session::flash('message_ok', "Hasło zmienione pomyślnie!");
            return Redirect::to('/');
        } else {
            Session::flash('message_nok', "Podałeś nieprawidłowe stare hasło!");
            return Redirect::back();
        }
    }

    public function cadreHRGet()
    {
        return view('hr.cadreHR');
    }

    public function datatableCadreHR(Request $request)
    {
        $data = DB::table('users')
            ->select(DB::raw('
                users.*,
                departments.name as dep_name,
                department_type.name as dep_name_type
            '))
            ->join('department_info', 'department_info.id', '=', 'users.department_info_id')
            ->join('departments', 'departments.id', '=', 'department_info.id_dep')
            ->join('department_type', 'department_type.id', '=', 'department_info.id_dep_type')
            ->where('users.user_type_id', '=', 5)
            ->get();

        return datatables($data)->make(true);
    }

    /**
     * Sprawdzenie czy numer kolejki pbx jest unikalny
     */
    public function uniquePBX(Request $request)
    {
        if ($request->ajax()) {
            $user = User::where('login_phone', '=', $request->login_phone)
                ->where('status_work', '=', '1')->count();

            return ($user > 0) ? 1 : 0;
        }
    }

    /**
     * Sprawdzenie czy email jest unikalny edycja
     */
    public function uniquerEmailEdit(Request $request)
    {
        if ($request->ajax()) {
            $email = $request->email;
            $id = $request->user_id;

            $check = User::where('email_off', '=', $email)->get();

            if ($check->count() == 0) {
                return 0;
            } else if ($check->count() > 0 && $check[0]->id != $id) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    /**
     * Funkcja dodająca pakiety medyczne dla nowych pracowników
     */
    public function addMedicalPackage(Request $request, $id)
    {

        /**
         * Dodanie pliku
         */
        $file_extension = $request->file('user_scan')->getClientOriginalExtension();
        $file_name = $request->first_name . '_' . $request->last_name;
        $store_name = $file_name . '_' . time() . '.' . $file_extension;
        $request->file('user_scan')->storeAs('medicalscan', $store_name);
        $request->store_name = $store_name;

        /**
         * Pobranie ilości osób w pakiecie medycznym
         */
        $sum = $request->totalMemberSum;

        for ($i = 0; $i < $sum; $i++) {
            $medicalPackage = new MedicalPackage();

            $medicalPackage->user_id = $id;
            $medicalPackage->pesel = $request->pesel[$i];
            $medicalPackage->user_first_name = $request->user_first_name[$i];
            $medicalPackage->user_last_name = $request->user_last_name[$i];
            $medicalPackage->birth_date = $request->birth_date[$i];
            $medicalPackage->postal_code = $request->postal_code[$i];
            $medicalPackage->city = $request->city[$i];
            $medicalPackage->street = $request->street[$i];
            $medicalPackage->house_number = $request->house_number[$i];
            $medicalPackage->flat_number = $request->flat_number[$i];
            $medicalPackage->phone_number = $request->phone_number[$i];
            $medicalPackage->family_member = ($i > 0) ? 1 : null;
            $medicalPackage->deleted = 0;
            $medicalPackage->package_name = $request->package_name;
            $medicalPackage->package_variable = $request->package_variable;
            $medicalPackage->cadre_id = Auth::user()->id;
            $medicalPackage->package_scope = ($i > 0) ? 'R-OM' : 'P-OM';
            $medicalPackage->scan_path = $store_name;
            $medicalPackage->month_start = $request->medical_start;
            $medicalPackage->created_at = date('Y-m-d H:i:s');
            $medicalPackage->updated_at = null;

            $medicalPackage->save();
            $log = array('T' => 'dodanie pakietu medycznego');
            $log = array_merge($log, $medicalPackage->toArray());
            new ActivityRecorder($log, 10, 1);
        }
    }

    /**
     * Edycja pakietu medycznego
     */
    private function changeMedicalPackage(Request $request, User $user)
    { //dd($request);
        $old_medical_ids = $user->medicalPackages->where('deleted', '=', 0);
        $old_medical_ids = $old_medical_ids->pluck('id')->toArray();

        $medical_ids_from_form = $request->medical_id;
        /**
         *  $to_delete - tablica z ID osób którym usuwamy opiekę medyczną
         */
        if (is_array($medical_ids_from_form)) {
            $to_delete = array_diff($old_medical_ids, $medical_ids_from_form);

            foreach ($to_delete as $key => $value) {
                $medicalPackage = MedicalPackage::find($value);

                $medicalPackage->deleted = 1;
                $medicalPackage->updated_by = Auth::user()->id;
                $medicalPackage->updated_at = date('Y-m-d H:i:s');
                $medicalPackage->month_stop = date('Y-m-d H:i:s');

                $medicalPackage->save();
            }
        }

        /**
         * Sprawdzenie czy podmieniony został plik z umową dla użytkownika
         */
        if ($request->file('user_scan') != null) {
            // Pobranie rozszerzenia pliku
            $file_extension = $request->file('user_scan')->getClientOriginalExtension();
            // Konkatenacja imienia i nazwiska pracownika
            $file_name = $request->first_name . '_' . $request->last_name;
            // Utworzenie nazwy pliku - imie, nazwisko, aktualny czas + rozszerzenie
            $store_name = $file_name . '_' . time() . '.' . $file_extension;
            // Zapis pliku do lokalizacji /storage/app/medicalscan
            $request->file('user_scan')->storeAs('medicalscan', $store_name);
            $scan_path = $store_name;
        } else {
            $scan_path = $user->medicalPackages->where('deleted', '=', 0)->first()->scan_path;
        }

        /**
         * Trzeba przekręcić wszystkie wpisy z formularza, jezeli medical_id == 0 dodajemy nowy, jezeli jest już id to podmieniamy
         */
        $count_form = count($request->medical_id);

        for ($i = 0; $i < $count_form; $i++) {

            if ($request->medical_id[$i] == 0) {
                /**
                 * Jezeli dodajemy nowego posiadacza opieki
                 */
                $medicalPackage = new MedicalPackage();

                $medicalPackage->user_id = $user->id;
                $medicalPackage->pesel = $request->pesel[$i];
                $medicalPackage->user_first_name = $request->user_first_name[$i];
                $medicalPackage->user_last_name = $request->user_last_name[$i];
                $medicalPackage->birth_date = $request->birth_date[$i];
                $medicalPackage->postal_code = $request->postal_code[$i];
                $medicalPackage->city = $request->city[$i];
                $medicalPackage->street = $request->street[$i];
                $medicalPackage->house_number = $request->house_number[$i];
                $medicalPackage->flat_number = $request->flat_number[$i];
                $medicalPackage->phone_number = $request->phone_number[$i];
                $medicalPackage->family_member = ($i > 0) ? 1 : null;
                $medicalPackage->deleted = 0;
                $medicalPackage->package_name = $request->package_name;
                $medicalPackage->package_variable = $request->package_variable;
                $medicalPackage->cadre_id = Auth::user()->id;
                $medicalPackage->package_scope = ($i > 0) ? 'R-OM' : 'P-OM';
                $medicalPackage->scan_path = $scan_path;
                $medicalPackage->month_start = $request->medical_start;

                $medicalPackage->save();
            } else {
                /**
                 * Edycja istniejacych danych
                 */
                $medicalPackage = MedicalPackage::find(intval($request->medical_id[$i]));

                $medicalPackage->pesel = $request->pesel[$i];
                $medicalPackage->user_first_name = $request->user_first_name[$i];
                $medicalPackage->user_last_name = $request->user_last_name[$i];
                $medicalPackage->birth_date = $request->birth_date[$i];
                $medicalPackage->postal_code = $request->postal_code[$i];
                $medicalPackage->city = $request->city[$i];
                $medicalPackage->street = $request->street[$i];
                $medicalPackage->house_number = $request->house_number[$i];
                $medicalPackage->flat_number = $request->flat_number[$i];
                $medicalPackage->phone_number = $request->phone_number[$i];
                $medicalPackage->family_member = ($i > 0) ? 1 : null;
                $medicalPackage->deleted = 0;
                $medicalPackage->package_name = $request->package_name;
                $medicalPackage->package_variable = $request->package_variable;
                $medicalPackage->scan_path = $scan_path;
                $medicalPackage->month_start = $request->medical_start;
                $medicalPackage->updated_by = Auth::user()->id;
                $medicalPackage->updated_at = date('Y-m-d H:i:s');

                $medicalPackage->save();
            }
//            dd($medicalPackage);
            $data = array('T' => 'edycja pakietu medycznego');
            $log = array_merge($data, $medicalPackage->toArray());
                new ActivityRecorder($log, 10, 2);
        }
    }

    /**
     * Funkcja usuwająca całkowicie pakiet medyczny
     */
    public function deleteMedicalPackage(Request $request)
    {
        if ($request->ajax()) {

            /**
             * Pobranie wszystkich aktywnych wpisów dla danego użytkownika
             */
            $packages = MedicalPackage::where('user_id', '=', $request->user_id)
                ->where('deleted', '=', 0)
                ->get();

            foreach ($packages as $package) {

                $package->deleted = 1;
                $package->updated_by = Auth::user()->id;
                $package->month_stop = $request->medical_stop;
                $package->updated_at = date('Y-m-d H:i:s');

                $package->save();

                $log = array('T' => 'Usunięcie pakietu medycznego');
                $log = array_merge($log, $package->toArray());
                new ActivityRecorder($log, 10, 3);
            }
            return 1;
        }
    }

    /**
     * Dane na temat pakietów medycznych (domyślnie)
     */
    public function medicalPackagesAllGet()
    {
        return $this->getMedicalPackagesData(date('Y'), date('m'));
    }

    /**
     * Dane na temat pakietów medycznych (wybór)
     */
    public function medicalPackagesAllPost(Request $request)
    {
        return $this->getMedicalPackagesData($request->medical_year, $request->medical_month);
    }

    /**
     * Metoda pobierająca dane na temat
     */
    private function getMedicalPackagesData($year, $selectedMonth)
    {
        $month = $year . '-' . $selectedMonth . '%';
        $monthLimit = $year . '-' . $selectedMonth . '-01';
        $prevMonth = $this->getPreviousMonth($year, $selectedMonth);
        /**
         * Pobranie pakietow ktore sa nie edytowane i starsze niz miesiac
         */
        $packagesOldNotEdited = MedicalPackage::where('deleted', '=', 0)
            ->where(function ($query) use ($month) {
                $query->where('updated_at', 'not like', $month)
                    ->orWhere('updated_at', '=', null);
            })
            ->where('month_start', 'not like', $month)
            ->where('month_start', '<=', $monthLimit)
            ->where('hard_deleted', '=', null)
            ->get();

        /**
         * Edytowane w tym miesiącu (Edycja dotyczy zmian danych pakietów które nie są nowe)
         */
        $packagesOldEdited = MedicalPackage::where('deleted', '=', 0)
            ->where('month_start', 'not like', $month)
            ->where('updated_at', 'like', $month)
            ->where('hard_deleted', '=', null)
            ->get();

        /**
         * Nowe w tym miesiącu OK
         */
        $packagesNewMonth = MedicalPackage::where('month_start', 'like', $month)
            ->where('hard_deleted', '=', null)
            ->get();

        /**
         * Usunięte w tym miesiącu OK
         */
        $packagedDeletedThisMonth = MedicalPackage::where('deleted', '=', 1)
            ->where('month_stop', 'like', $prevMonth)
            ->where('hard_deleted', '=', null)
            ->get();

        $packagesOldNotEdited = $packagesOldNotEdited->map(function ($item) {
            $item['flag'] = 0;
            return $item;
        });

        $packagesOldEdited = $packagesOldEdited->map(function ($item) {
            $item['flag'] = 1;
            return $item;
        });

        $packagesNewMonth = $packagesNewMonth->map(function ($item) {
            $item['flag'] = 2;
            return $item;
        });

        $packagedDeletedThisMonth = $packagedDeletedThisMonth->map(function ($item) {
            $item['flag'] = 3;
            return $item;
        });

        $merged = $packagesOldNotEdited;
        $merged = $merged->merge($packagesOldEdited);
        $merged = $merged->merge($packagesNewMonth);
        $merged = $merged->merge($packagedDeletedThisMonth);

        /**
         * Tablica z miesiącami
         */
        $months = collect([
            ['id' => '01', 'name' => 'Styczeń'],
            ['id' => '02', 'name' => 'Luty'],
            ['id' => '03', 'name' => 'Marzec'],
            ['id' => '04', 'name' => 'Kwiecień'],
            ['id' => '05', 'name' => 'Maj'],
            ['id' => '06', 'name' => 'Czerwiec'],
            ['id' => '07', 'name' => 'Lipiec'],
            ['id' => '08', 'name' => 'Sierpień'],
            ['id' => '09', 'name' => 'Wrzesień'],
            ['id' => '10', 'name' => 'Październik'],
            ['id' => '11', 'name' => 'Listopad'],
            ['id' => '12', 'name' => 'Grudzień']
        ]);

        return view('hr.allMedicalPackages')
            ->with('months', $months)
            ->with('selected_month', $selectedMonth)
            ->with('packages', $merged);
    }

    /**
     * Pobranie danych poprzedniego miesiąca
     */
    private function getPreviousMonth($year, $month)
    {
        $month = intval($month);
        $month -= 1;
        if ($month <= 0) {
            $month += 12;
            $year -= 1;
        }

        $month = ($month < 10) ? '0' . $month : $month;

        return $year . '-' . $month . '%';
    }

    /**
     * Rozszerzony raport pakietów medycznych
     */
    public function medicalPackagesRaportExtendedGet()
    {
        $data = $this->getMedicalPackagesExtendedData(date('Y-m'));

        return view('admin.medicalReportExtended')
            ->with('packages', $data['packages'])
            ->with('selected_year', date('Y'))
            ->with('selected_month', date('m'))
            ->with('months', $data['months']);
    }

    /**
     * Rozszerzony raport pakeitów medycznych (wybór)
     */
    public function medicalPackagesRaportExtendedPost(Request $request)
    {
        $data = $this->getMedicalPackagesExtendedData($request->year . '-' . $request->month);

        return view('admin.medicalReportExtended')
            ->with('packages', $data['packages'])
            ->with('selected_year', $request->year)
            ->with('selected_month', $request->month)
            ->with('months', $data['months']);
    }

    /**
     * Pobranie danych na temat pakietów medycznych
     */
    private function getMedicalPackagesExtendedData($month)
    {
        /**
         * Tablica z miesiącami
         */
        $months = collect([
            ['id' => '01', 'name' => 'Styczeń'],
            ['id' => '02', 'name' => 'Luty'],
            ['id' => '03', 'name' => 'Marzec'],
            ['id' => '04', 'name' => 'Kwiecień'],
            ['id' => '05', 'name' => 'Maj'],
            ['id' => '06', 'name' => 'Czerwiec'],
            ['id' => '07', 'name' => 'Lipiec'],
            ['id' => '08', 'name' => 'Sierpień'],
            ['id' => '09', 'name' => 'Wrzesień'],
            ['id' => '10', 'name' => 'Październik'],
            ['id' => '11', 'name' => 'Listopad'],
            ['id' => '12', 'name' => 'Grudzień']
        ]);

        $first_day_of_month = $month . '-01';

        $last_day = date('t', strtotime($month));

        $last_day_of_month = $month . '-' . $last_day;

        /**
         * Dane pakietów z danego miesiąca
         */
        $medicalPackages = DB::table('medical_packages')
            ->select(DB::raw('
                SUM(CASE WHEN users.user_type_id IN (1,2) THEN 1 ELSE 0 END) as consultant_sum,
                SUM(CASE WHEN users.user_type_id NOT IN (1,2) THEN 1 ELSE 0 END) as cadre_sum,
                COUNT(medical_packages.id) as total_sum,
                departments.name AS dep_name,
                department_type.name AS dep_name_type
            '))
            ->join('users', 'users.id', 'medical_packages.user_id')
            ->join('department_info', 'department_info.id', 'users.department_info_id')
            ->join('departments', 'department_info.id_dep', 'departments.id')
            ->join('department_type', 'department_info.id_dep_type', 'department_type.id')
            ->where('medical_packages.month_start', '<=', $last_day_of_month)
            ->where(function ($query) use ($last_day_of_month) {
                $query->where('medical_packages.month_stop', '<=', $last_day_of_month)
                    ->orWhere('medical_packages.month_stop', '=', null);
            })
            ->where('medical_packages.family_member', '=', null)
            ->groupBy('department_info.id')
            ->get();

        $data = [
            'months' => $months,
            'packages' => $medicalPackages
        ];

        return $data;
    }

    public function coachChangeGet()
    {
        $coaches = User::where('department_info_id', '=', Auth::user()->department_info_id)
            ->whereIn('id', User::where('coach_id', '<>', null)
                ->pluck('coach_id')
                ->unique())
            ->get();
        //where('department_info_id','=',Auth::user()->department_info_id)
        $newCoaches = User::where([
            ['status_work', '=', 1],
            ['department_info_id', '=', Auth::user()->department_info_id]
        ])
            ->whereIn('user_type_id', [4,20])
            ->get();

        return view('hr.coachChange')
            ->with('coaches', $coaches)
            ->with('newCoaches', $newCoaches);
    }

    public function coachChangePost(Request $request)
    {
        $error = false;
        $newCoachId = $request->newCoach_id;
        $coachId = $request->coach_id;
        if ($coachId && $newCoachId) {
            try {
                $consultantsWithPrevCoach = User::where([
                    ['coach_id', '=', $coachId],
                    ['status_work', '=', 1]
                ])->get();

            } catch (Exception $e) {
                report($e);
                return ['type' => 'warning', 'msg' => 'Coś poszło nie tak, spróbuj później', 'title' => "Nie udało się!"];
            }

        } else {
            Session::flash('message_warning', 'Wybierz obu trenerów');
            return Redirect::back();
        }

        $error = !$this->coachChange($coachId, $newCoachId, $consultantsWithPrevCoach);

        if ($request->has('action')) {
            if ($error)
                return ['type' => 'warning', 'msg' => 'Coś poszło nie tak, spróbuj później', 'title' => "Nie udało się!"];
            //ponizszy if nie ma znaczenia, to jest tylko flaga oznaczenia wykonania ajax
            if ($request->action == "coachChange") {
                $log = array(
                    'T' => 'Zmiana trenera grupy',
                    'newCoach_id' => $request->newCoach_id,
                    'coachId' => $request->coach_id
                );
                new ActivityRecorder($log,206,4);
                return ['type' => 'success', 'msg' => 'Pomyślna zmiana trenera grupy', 'title' => "Udało się!"];
                }


            return ['type' => 'success', 'msg' => 'Pomyślna zmiana trenera grupy', 'title' => "Udało się!"];
        } else {
            if ($error)
                Session::flash('message_warning', 'Coś poszło nie tak, spróbuj później');
            else
                Session::flash('message_ok', 'Pomyślna zmiana trenera grupy');
            return Redirect::back();
        }
    }

    public function coachChange($coachId, $newCoachId, $consultantsWithPrevCoach)
    {
        //dd($consultantsWithPrevCoach->pluck('id')->toArray());
        try {
            CoachChange::create([
                'coach_id' => $newCoachId,
                'prev_coach_id' => $coachId,
                'editor_id' => Auth::user()->id
            ]);

            $coachChangeId = CoachChange::max('id');
            foreach ($consultantsWithPrevCoach as $consultant) {
                CoachHistory::create([
                    'user_id' => $consultant->id,
                    'coach_change_id' => $coachChangeId
                ]);
                $consultant->coach_id = $newCoachId;
                $consultant->save();
            }
            Pbx_report_extension::where([
                ['report_date','=',date('Y-m-d')],
                ['actual_coach_id','=', $coachId]
            ])->whereIn('user_id',$consultantsWithPrevCoach->pluck('id')->toArray())
                ->update(['actual_coach_id' => $newCoachId]);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    public function coachChangeRevertPost(Request $request)
    {
        $error = false;
        $coachChangeId = $request->coach_change_id;

        try {
            $coachChange = CoachChange::where('id', $coachChangeId)->get()->first();
            $allConsultantsIdsWithSelectedCoachingChange = CoachHistory::where('coach_change_id', $coachChangeId)
                ->pluck('user_id')->toArray();
            $allConsultantsBeforeChange = User::whereIn('id', $allConsultantsIdsWithSelectedCoachingChange)->get();
        } catch (Exception $e) {
            report($e);
            return ['type' => 'warning', 'msg' => 'Coś poszło nie tak, spróbuj później', 'title' => "Nie udało się!"];
        }

        $error = !$this->coachChange($coachChange->coach_id,
            $coachChange->prev_coach_id,
            $allConsultantsBeforeChange);
        if (!$error) {
            $coachChange->status = 1;
            $coachChange->save();
        }
        if ($request->has('action')) {
            if ($error)
                return ['type' => 'warning', 'msg' => 'Coś poszło nie tak, spróbuj później', 'title' => "Nie udało się!"];
            //ponizszy if nie ma znaczenia, to jest tylko flaga oznaczenia wykonania ajax
            if ($request->action == "coachChangeRevert") {
                $log = array(
                    'T' => 'Cofnięcie zmiany',
                    'coachChangeId' => $request->coach_change_id,
                    'previousCoachId' => $coachChange->prev_coach_id
                );
                new ActivityRecorder($log,206,4);
                return ['type' => 'success', 'msg' => 'Pomyślne cofnięcie zmiany', 'title' => "Udało się!"];
            }
            return ['type' => 'success', 'msg' => 'Pomyślne cofnięcie zmiany', 'title' => "Udało się!"];
        } else {
            return Redirect::back();
        }
    }

    public function datatableCoachChange(Request $request)
    {
        $coachChanges = null;
        if ($request->ajax())
            if (Auth::user()->user_type_id == 3)
                $coachChanges = DB::table('coach_change as c')
                    ->select('c.id',
                        'u1.first_name as c_first_name', 'u1.last_name as c_last_name',
                        'u2.first_name as pc_first_name', 'u2.last_name as pc_last_name',
                        'c.created_at')
                    ->leftJoin('users as u1', 'c.coach_id', '=', 'u1.id')
                    ->leftJoin('users as u2', 'c.prev_coach_id', '=', 'u2.id')
                    ->where('c.status', '=', 0)
                    ->orderBy('c.id', 'desc')
                    ->get();

        return datatables($coachChanges)->make(true);
    }

    public function employeeSearchPost(Request $request)
    {
        Validator::make($request->all(), [
            'login_phone' => 'required|numeric'
        ], [
            'required' => 'Pole wyszukiwania jest wymagane',
            'numeric' => 'W polu muszą znajdować się tylko cyfry'
        ])->validate();

        $users = User::where('login_phone', '=', $request->login_phone)->get();
        if ($users->count() == 1) {
            $user = $users->first();
            /*$department_info_id = $user->department_info_id;
            $department_info = Department_info::where('id','=',$department_info_id)->first();
            $department_type_name = Department_types::where('id','=',$department_info->id_dep_type)->get();
            $department_name = Departments::where('id','=',$department_info->id_dep)->get();*/
            $department_type_name = $user->department_info->department_type->name;
            $department_name = $user->department_info->departments->name;

            $user_info = ['first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'department_type_name' => $department_type_name,
                'department_name' => $department_name];

            //dd($user_info);

            Session::flash('found', true);
            Session::flash('user_info', $user_info);
        } else {
            Session::flash('found', false);
        }
        return Redirect::back();
    }
}
