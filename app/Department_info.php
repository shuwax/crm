<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Department_info extends Model
{
    protected $table = 'department_info';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'id_dep','id_dep_type','size','commission_avg','commission_hour','commission_start_money','commission_step',
        'dep_aim','dep_aim_week','commission_janky','type','janky_system_id','pbx_id','working_hours_normal','working_hours_week'
    ];

    public function users() {
       return $this->hasMany('App\User');
    }

    public function departments() {
        return $this->belongsTo('App\Departments', 'id_dep');
    }

    public function department_type() {
        return $this->belongsTo('App\Department_types', 'id_dep_type');
    }

    public function summaryPayment() {
        return $this->hasMany('App\SummaryPayment', 'department_info_id');
    }

    public function equipments() {
        return $this->hasMany('App\Equipments', 'department_info_id');
    }

    public function notifications() {
        return $this->hasMany('App\Notifications', 'department_info_id');
    }

    public function employee_of_the_week() {
        return $this->hasMany('App\EmployeeOfTheWeek', 'department_info_id');
    }

    public function multiple_departments(){
        return $this->hasMany('App\MultipleDepartments', 'department_info_id');
    }

    public function menager() {
        return $this->belongsTo('App\User', 'menager_id');
    }
    public function director() {
        return $this->belongsTo('App\User', 'director_id');
    }

    public function accepted_payment_user_story() {
        return $this->hasMany('App\AcceptedPaymentUserStory', 'department_info_id');
    }

    public static function getAllInfoAboutDepartment(){
        $departments = Department_info::all();
        $departments->map(function ($item){
            $item->department_name = $item->departments->name;
            $item->type_name = $item->department_type->name;
            return $item;
        });
        return $departments;
    }

    /**
     * @param User $user
     * @param $type {string} - name of proffesion
     * @return null/int
     * This methdo returns number of departments for given user on his occupation
     */
    public static function numberOfDepartments(User $user, $type) {
        $query = Department_info::select(DB::raw('COUNT(*) as amount'));
        switch($type) {
            case 'menager_id': {
                $query = $query->where('menager_id', '=', $user->id)
                    ->first();
                break;
            }
            case 'regionalManager_id': {
                $query = $query->where('regionalManager_id', '=', $user->id)
                    ->first();
                break;
            }
            case 'director_id': {
                $query = $query->where('director_id', '=', $user->id)
                    ->first();
                break;
            }
            case 'hr_id': {
                $query = $query->where('hr_id', '=', $user->id)
                    ->first();
                break;
            }
            case 'hr_id_second': {
                $query = $query->where('hr_id_second', '=', $user->id)
                    ->first();
                break;
            }
            case 'director_hr_id': {
                $query = $query->where('director_hr_id', '=', $user->id)
                    ->first();
                break;
            }
            case 'instructor_regional_id': {
                $query = $query->where('instructor_regional_id', '=', $user->id)
                    ->first();
                break;
            }
            default: {
                $query = null;
                break;
            }
        }

        return $query->amount ? $query->amount : null;
    }


    /**
     * @param null $deparmentTypesArray
     * @return mixed
     * This method returns list of departments.
     */
    public static function getDepartmentsWithNames($deparmentTypesArray = null) {
        $depTypes = $deparmentTypesArray === null ? Department_types::select('id')->pluck('id')->toArray() : $deparmentTypesArray;

        return Department_info::select('department_info.id as id',
            'departments.name as department_name',
            'department_type.name as department_type',
            'department_info.menager_id',
            'department_info.regionalManager_id',
            'department_info.director_id',
            'department_info.hr_id',
            'department_info.hr_id_second',
            'department_info.director_hr_id',
            'department_info.instructor_regional_id'
        )
            ->join('department_type', 'department_info.id_dep_type', '=', 'department_type.id')
            ->join('departments', 'department_info.id_dep', '=', 'departments.id')
            ->whereIn('department_info.id_dep_type', $depTypes)
            ->get();
    }

    public static function getUserDepartmentType($user_id) {
        $info = Department_info::select(
            'department_info.id as department_info_id',
            'users.id as user_id',
            'id_dep_type'
        )
            ->join('departments', 'department_info.id_dep', '=', 'departments.id')
            ->join('department_type', 'department_info.id_dep_type', '=', 'department_type.id')
            ->join('users', 'department_info.id', '=', 'users.department_info_id')
            ->where('users.id', '=', $user_id)
            ->first();

        return $info;
    }


    /** Edit or modify department
     * @param $request
     */
    public static function addModifyDepartment($request){

        //tutaj sprawdzenie czy oddział jest dodawany do istniejącego miasta czy stworzono  nowy
        if ($request->department != '-1') {
            $id_dep = $request->department;
        } else {
            $department = new Departments();

            $department->name = $request->city;
            $department->desc = $request->desc;
            $department->save();

            $departments = Departments::
            orderBy('id', 'desc')
                ->limit(1)
                ->get();

            $id_dep = $departments[0]->id;
        }
        $department_info_prev = null;
        if(isset($request->selected_department_info_id)) {
            $department_info = Department_info::find($request->selected_department_info_id);
            $department_info_prev = clone $department_info;
        }else{
            $department_info = new Department_info();
            $department_info->id_dep                    = $id_dep;
        }
        $department_info->id_dep_type               = $request->id_dep_type;
        $department_info->size                      = ($request->size != null) ? $request->size : 0 ;
        $department_info->commission_avg            = ($request->commission_avg) ? $request->commission_avg : 0 ;
        $department_info->commission_hour           = ($request->commission_hour) ? $request->commission_hour : 0 ;
        $department_info->commission_step           = ($request->commission_step) ? $request->commission_step : 0 ;
        $department_info->commission_start_money    = ($request->commission_start_money) ? $request->commission_start_money : 0 ;
        $department_info->commission_janky          = ($request->commission_janky) ? $request->commission_janky : 0 ;
        $department_info->dep_aim                   = ($request->dep_aim) ? $request->dep_aim : 0 ;
        $department_info->dep_aim_week              = ($request->dep_aim_week) ? $request->dep_aim_week : 0 ;
        $department_info->type                      = ($request->type != 'Wybierz') ? $request->type : '' ;
        $department_info->janky_system_id           = ($request->janky_system_id) ? $request->janky_system_id : 0 ;
        $department_info->pbx_id                    = ($request->pbx_id) ? $request->pbx_id : 0 ;
        $department_info->working_hours_normal      = ($request->work_hour > 0) ? $request->work_hour : 0 ;
        $department_info->working_hours_week        = ($request->work_hour_weekend > 0) ? $request->work_hour_weekend : 0 ;
        $department_info->blocked                   = 0;
        $department_info->menager_id                = ($request->menager != 0) ? $request->menager : null ;
        $department_info->director_id               = ($request->director != 0) ? $request->director : null ;
        $department_info->director_hr_id            = ($request->director_hr != 0) ? $request->director_hr : null;
        $department_info->hr_id                     = ($request->hrEmployee != 0) ? $request->hrEmployee : null ;
        $department_info->hr_id_second              = ($request->hrEmployee2 != 0) ? $request->hrEmployee2 : null ;
        $department_info->regionalManager_id        = ($request->regionalManager != 0) ? $request->regionalManager : null ;
        $department_info->instructor_regional_id    = ($request->regionalManagersInstructors != 0) ? $request->regionalManagersInstructors : null ;
        try{
            $department_info->save();
        }catch (\Exception $exception){
            return collect();
        }
        return ['department_prev_version' => $department_info_prev,'department_next_version' => $department_info];
    }
}
