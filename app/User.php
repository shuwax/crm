<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name','last_name','username','last_login','password_date','user_type_id','department_info_id',
        'start_work','end_work','status_work','disabled_by_system', 'phone','desc','student','ck','agency_id','guid','login_phone','rate','priv_phone',
        'salary','add_to_salary',
        'email', 'password','id_manager','documents','dating_type','coach_id','recommended_by','promotion_date','degradation_date','successorUserId'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function scopeOnlyConsultant($query){
        return $query->whereIn('user_type_id',[1,2]);
    }
    public function scopeOnlyCadre($query){
        return $query->whereNotIn('user_type_id',[1,2]);
    }
    public function scopeActiveUser($query){
        return $query->where('status_work',1);
    }
    public function scopeAuthDepartment($query){
        return $query->where('department_info_id',Auth::user()->department_info_id);
    }
    public function department_info() {
        return $this->belongsTo('App\Department_info','department_info_id');
    }

    public function user_type() {
        return $this->belongsTo('App\UserTypes', 'user_type_id');
    }

    public function agencies() {
        return $this->belongsTo('App\Agencies', 'agency_id');
    }

    public function employee_of_the_week_ranking(){
        return $this->belongsTo('App\EmployeeOfTheWeekRanking','user_id');
    }

    public function schedule() {
        return $this->hasMany('App\Schedule', 'id_user');
    }

    public function penalty_bonuses() {
        return $this->hasMany('App\PenaltyBonus', 'id_user');
    }

    public function work_hours() {
        return $this->hasMany('App\Work_Hour', 'id_user');
    }

    public function dkj() {
        return $this->hasMany('App\Dkj', 'id_user');
    }

    public function dkj_employe() {
        return $this->hasMany('App\Dkj', 'id_dkj');
    }

    public function privilages() {
        return $this->belongsToMany('App\Links', 'privilage_relation', 'user_type_id', 'link_id');
    }

    public function summaryPaymets() {
        return $this->hasMany('App\SummaryPayment', 'id_user');
    }

    public function equipments() {
        return $this->hasMany('App\Equipments', 'id_user');
    }
    public function accepted_payment_user_story() {
        return $this->hasMany('App\AcceptedPaymentUserStory', 'user_id');
    }

    public function notifications() {
        return $this->hasMany('App\Notifications', 'user_id');
    }

    public function notification_taken() {
        return $this->hasMany('App\Notifications', 'displayed_by');
    }

    public function multiple_departments() {
        return $this->hasMany('App\MultipleDepartments', 'user_id');
    }

    public function userTests() {
        return $this->hasMany('App\UserTest', 'user_id');
    }

    public function userCandidates() {
        return $this->hasMany('App\Candidate', 'cadre_id');
    }

    public function userTrainings() {
        return $this->hasMany('App\GroupTraining', 'leader_id');
    }

    public function userBootstrapNotifications() {
        return $this->hasMany('App\BootstrapNotify', 'user_id');
    }

    public function medicalPackages() {
        return $this->hasMany('App\MedicalPackage', 'user_id');
    }

    public function pbx_report_extension() {
        return $this->hasMany('App\Pbx_report_extension', 'pbx_id', 'login_phone');
    }

    public function trainerConsultants() {
        return $this->hasMany('App\User', 'coach_id');
    }

    public function trainer() {
        return $this->belongsTo('App\User','coach_id');
    }

    /**
     *  Wyłączenie starych kont użytkowników (konta do wyłączenia, konta do wysłania informacji)
     */
    public function DisableUnusedAccount(&$disable_consultant,&$send_alert_consultant){
        $date = date("Y-m-d");
        $date_overdue = strtotime($date."-14 days");
        $date_to_send = strtotime($date."-7 days");
        //Użytkownicy do wyłączenia
        $disable_consultant = User::where('status_work','=',1)
            ->where('last_login',"<",date("Y-m-d",$date_overdue))
            ->whereIn('user_type_id',[1,2])
            ->get();
        //Użytkownicy do przypomnienia
        $send_alert_consultant = User::where('status_work','=',1)
            ->whereBetween('last_login',[date("Y-m-d",$date_overdue),date("Y-m-d",$date_to_send)])
            ->whereIn('user_type_id',[1,2])
            ->get();
    }

    /**
     * @param null $user_type
     * @return null/Collection of users
     */
    public static function getActiveUsers($user_type = null) {
        $all_user_types_arr = UserTypes::select('id')->pluck('id')->toArray(); //List of all present user types
        $users = null;
        if(in_array($user_type, $all_user_types_arr)) { //case when user passed as argument existing user type
            $users = User::select(
                'users.id as id',
                'first_name',
                'last_name',
                'last_login',
                'user_type_id',
                'department_info_id',
                'department_info.id_dep as id_dep',
                'department_info.id_dep_type as id_dep_type',
                'start_work',
                'end_work',
                'phone',
                'login_phone as pbx_id',
                'rate',
                'id_manager',
                'salary',
                'additional_salary',
                'main_department_id',
                'coach_id',
                'promotion_date',
                'degradation_date'
            )
                ->join('department_info', 'department_info.id', '=', 'users.department_info_id')
                ->where('status_work', '=', 1)
                ->where('user_type_id', '=', $user_type)
                ->get();
        }
        else { //case when user didn't passed any argument or it was out of accepted range
            $users = User::select(
                'users.id as id',
                'first_name',
                'last_name',
                'last_login',
                'user_type_id',
                'department_info_id',
                'department_info.id_dep as id_dep',
                'department_info.id_dep_type as id_dep_type',
                'start_work',
                'end_work',
                'phone',
                'login_phone as pbx_id',
                'rate',
                'id_manager',
                'salary',
                'additional_salary',
                'main_department_id',
                'coach_id',
                'promotion_date',
                'degradation_date'
            )
                ->join('department_info', 'department_info.id', '=', 'users.department_info_id')
                ->where('status_work', '=', 1)
                ->get();
        }

        return $users;
    }

}
