<?php namespace App\Http\Controllers;
    //use App\Holiday;
    use App\Leave;
    use App\LeaveCategories;
    use App\UserDetails;
    use App\HolidayInfo;
    use Auth;
    use Request;
    use Response;
    use Session;
    use DB;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Support\Facades\Input;
    use Illuminate\Support\Facades\Redirect;
    use App\Http\Controllers\Controller;
    use Symfony\Component\Security\Core\User\User;
    use Illuminate\Support\Facades\Hash;

    class CompanyController extends Controller
    {

        public function __construct()
        {
            date_default_timezone_set(Auth::user()->Company->time_zone);
        }

        public function  getIndex()
        {
            LoginController::autoPunchOutCheck(\App\User::UserIdList());
            $data['startDate'] = date('Y-m-d');
            $data['endDate'] = date('Y-m-d');
            $data['attendanceReport'] = UserDetails::
            select(DB::raw('timediff(logout_time,login_time) as timediff'),
                'login_date','logout_date','id','login_time','logout_time','user_id')
                ->whereHas('User', function($q) {
                    $q->where('company_id', Auth::user()->company_id);
                })
                ->where('login_date', '>=',  $data['startDate'])
                ->where('logout_date', '<=', $data['endDate'])
                ->where('logout_time', '!=', '0000-00-00 00:00:00')
                ->orderBy('id', 'ASC')
                ->get();
             $data['activeUser'] = UserDetails::where('login_date', date('Y-m-d', time()))
                                                ->whereHas('User', function($q) {
                                                    $q->where('company_id', Auth::user()->company_id);
                                                })
                                                ->groupBy('user_id')
                                                ->orderBy('id')
                                                ->get();
            $data['lateUser'] = UserDetails::where('login_date', date('Y-m-d', time()))
                ->whereHas('User', function($q) {
                    $q->where('company_id', Auth::user()->company_id);
                })
                ->where('status', 'Late')
                ->groupBy('user_id')
                ->orderBy('id')
                ->get();

             $data['totalUser'] = \App\User::where('company_id', Auth::user()->company_id)
                                                    ->where('user_label', '>', 1)->count();
             $data['withLeaveNotification'] = Leave::whereHas('User', function($q) {
                                                            $q->where('company_id', Auth::user()->company_id);
                                                            })->where('admin_noti_status', 1)->count();
            return view('Company.home',$data);
        }

        public function anyCreateUser()
        {
            if (Request::all()) {
    //            return 'fsd';
                $ignoreID = Auth::user()->id;
                $rules = array(
                    'username'=> "unique:users,username|alpha_dash",
                    'password'  => 'required|min:6|max:10',
                    'ip_address' => 'sometimes|ip'
                );
                /* Laravel Validator Rules Apply */
                $validator = Validator::make(Input::all(), $rules);
                if ($validator->fails()):
                    $validationError =  $validator->messages()->first();
                    Session::flash('flashError', $validationError);
                    return redirect('company/create-user');
                else:
                    $userCreate = new \App\User();
                    $userCreate->username = trim(Request::input('username'));
                    $userCreate->password = Hash::make(trim(Request::input('password')));
                    $userCreate->ip_address = trim(Request::input('ip_address'));
                    $userCreate->company_id = Auth::user()->company_id;
                    $userCreate->save();
                endif;
                Session::flash('flashSuccess', 'User Created Successfully');
                return redirect('company/all-user');
            }
            return view('Company.createUser');
        }

        public  function getAllUser()
        {
            $user = new \App\User();
            $data['allUser'] = \App\User::where('company_id', Auth::user()->company_id)
                ->where('user_label', '>', 1)->paginate(10);
            $data['userTable'] = view('Company.userTable', $data);
            return view('Company.allUser',$data);
        }

        public function getSearchUser(){
            $search = Input::get('search');
            $user = new \App\User();
            $data['allUser'] = $user->whereRaw("username regexp '[[:<:]]$search'")
                ->where('company_id', Auth::user()->company_id)
                    ->where('user_label', '>', 1)->paginate(10);
            return view('Company.userTable', $data);
        }

        public function anyStatusChange($id = null)
        {
            $status = Request::input('status');
            $user = \App\User::find($id);
            if($status == 'active')
            $user->status = 1;
            if($status == 'inactive')
                $user->status = 0;
            $user->save();
            Session::flash('flashSuccess', 'Status Changed');
            return 'true';
            $user = new \App\User();
            $data['allUser'] = $user->allUser();
            return view('Company.allUserAjax',$data);
        }

        public  function anyAddIp()
        {
            $response = array();
            $rules = array(
                    'ip_address' => 'required|ip'
                );
                /* Laravel Validator Rules Apply */
                $validator = Validator::make(Input::all(), $rules);
                if ($validator->fails()):
                    $errorMessage = $validator->messages()->first();
                    $response['type'] = 'error';
                    $response['info'] = $errorMessage;
                    return Response::json($response);
                else:
                    $userCreate = \App\User::find(Request::input('id'));
                    $userCreate->ip_address = trim(Request::input('ip_address'));
                    $userCreate->save();
                endif;
                    Session::flash('flashSuccess', 'IP Added Successfully');
                    $user = new \App\User();
                    $data['allUser'] = $user->allUser();
                    $response['type'] = 'success';
                    $response['info'] = (String) view('Company.allUserAjax',$data);
                    return Response::json($response);
        }

        public function postRemoveIp($id)
        {
            $user = \App\User::find($id);
            $user->ip_address = '';
            $user->save();
            Session::flash('flashSuccess', 'IP Removed');
            return 'true';
            $user = new \App\User();
            $data['allUser'] = $user->allUser();
            return view('Company.allUserAjax',$data);
        }

        public function anyUserUpdate($id)
        {
            $data['user'] = \App\User::find($id);
            return view('Company.userUpdate',$data);
        }

        public function postUpdateUserUsername($id){
                    $rules = array(
                        'username' => "required|alpha_dash|unique:users,username,$id",
                    );
                /* Laravel Validator Rules Apply */
                $validator = Validator::make(Input::all(), $rules);
                if ($validator->fails()):
                    return $validator->messages()->first();
                else:
                    $userUpdate = \App\User::find($id);
                    $userUpdate->username = trim(Request::input('username'));
                    $userUpdate->save();
                endif;
                return 'true';
        }

        public function  postUpdateUserPassword($id){
                    $rules = array(
                        'password' => "required|min:6|max:10",
                    );
                /* Laravel Validator Rules Apply */
                $validator = Validator::make(Input::all(), $rules);
                if ($validator->fails()):
                    return $validator->messages()->first();
                else:
                    $userUpdate = \App\User::find($id);
                    $userUpdate->password = Hash::make(Request::input('password'));
                    $userUpdate->save();
                endif;
                return 'true';
        }
        public function  postUpdateUserTime($id){
                    $rules = array(
                        'time' => "required",
                    );
                /* Laravel Validator Rules Apply */
                $validator = Validator::make(Input::all(), $rules);
                if ($validator->fails()):
                    return $validator->messages()->first();
                else:
                    $userUpdate = \App\User::find($id);
                    $userUpdate->time = Request::input('time');
                    $userUpdate->save();
                endif;
                return 'true';
        }

        public function  postUpdateAutoPunchOutTime($id){
            $rules = array(
                'time' => "required|date_format:H:i:s",
            );
            /* Laravel Validator Rules Apply */
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()):
                return $validator->messages()->first();
            else:
                $userUpdate = \App\User::find($id);
                $userUpdate->auto_punch_out_time = Request::input('time');
                $userUpdate->save();
            endif;
            return 'true';
        }

        public function anyUpdateMe()
        {
            if (Request::all()) {
                $companyID = Request::input('companyID');
                $id = Auth::user()->id;
                $rules = array(
                    'company_name' => "required|unique:company_info,company_name,$companyID",
                    'company_email' => "required|email|unique:company_info,company_email,$companyID",
                    'phone' => "required",
                    'username' => "required|alpha_dash|unique:users,username,$id",
                    'user_first_name' => "required",
                    'user_last_name' => 'required'
                );
                /* Laravel Validator Rules Apply */
                $validator = Validator::make(Input::all(), $rules);
                if ($validator->fails()):
                    return $validator->messages()->first();
                else:
                    $userUpdate = \App\User::find($id);
                    $userUpdate->username = trim(Request::input('username'));
                    $userUpdate->user_first_name = trim(Request::input('user_first_name'));
                    $userUpdate->user_last_name = trim(Request::input('user_last_name'));
                    $userUpdate->Company->company_name = trim(Request::input('company_name'));
                    $userUpdate->Company->company_email = trim(Request::input('company_email'));
                    $userUpdate->Company->phone = trim(Request::input('phone'));
                    $userUpdate->push();
                endif;
                return 'true';
            }
            else{
            $data['myInfo'] = \App\User::find(Auth::user()->id);
            return view('Company.companyUpdate', $data);
            }
        }

        public function anyChangePassword()
        {
            if (Request::all()) {
                $rules = array(
                    'new_password'  => 'required|same:confirm_new_password|min:6',
                    'current_pass'  => 'required|password_check',
                );
                $messages = array(
                    'new_password.same' => 'New Password and Confirm password are not Matched',
                );
                /* Laravel Validator Rules Apply */
                $validator = Validator::make(Input::all(), $rules, $messages);
                if ($validator->fails()):
                    return $validator->messages()->first();
                else:
                    $userUpdate = \App\User::find(Auth::user()->id);
                    $userUpdate->password = Hash::make(Request::input('new_password'));
                    $userUpdate->save();
                endif;
                return 'true';
            } else{
                return view('Company.passwordChange');
            }
        }

        public function anyCreateHoliday()
        {
            if(Request::all()){
               $holidayList = Request::input('holiday');

                foreach($holidayList as $holiday){
                    if($holiday == '')
                        return 'Please Fill All the Field';
                    $checkExisting = HolidayInfo::where('holiday', $holiday)
                        ->first();
                    if($checkExisting)
                        return "$holiday has Already Added as a Holiday";
                }

                foreach($holidayList as $holiday){
                   $saveHoliday = new HolidayInfo();
                    $saveHoliday->holiday = $holiday;
                    $saveHoliday->save();
                }
                Session::flash('flashSuccess', 'Holiday Created Successfully');
                return 'true';

            }else {
                return view('Company.createHoliday');
            }
        }

        public function anyAllHoliday()
        {
            $data['allHoliday'] = HolidayInfo::all();
            return view('Company.allHoliday', $data);
        }

        public function anyDeleteHoliday($id)
        {
            $holidayDelete = HolidayInfo::find($id);
            $holidayDelete->delete();
            return 'true';
        }

        public function getAllLeave()
        {
            Leave::whereHas('User', function($q) {
                $q->where('company_id', Auth::user()->company_id);
            })
                ->update(array('admin_noti_status' => 0));
            $data['allLeave'] = Leave::whereHas('User', function($q) {
                $q->where('company_id', Auth::user()->company_id);
            })
                ->whereHas('LeaveCategories', function($q) {
                    $q->where('deleted_at');
                })
                ->orderBy('id', 'desc')
                ->paginate(15);
            $data['leaveTable'] = view('Company.leaveTable', $data);
            return view('Company.allLeave',$data);
        }

        public function getSearchLeave(){
            $data['allLeave'] = Leave::whereHas('User', function($q) {
                $search = Input::get('search');
                $q->where('company_id', Auth::user()->company_id);
                $q->whereRaw("username regexp '[[:<:]]$search'");
            })
                ->whereHas('LeaveCategories', function($q) {
                    $q->where('deleted_at');
                })
                ->orderBy('id', 'desc')
                ->paginate(15);
            return view('Company.leaveTable', $data);
        }

        public function getChangeLeaveStatus($id)
        {
            if(Request::input('status') == 'grant'){
                $categoryID = Request::input('categoryID');
                $first_day_this_year = date('Y-01-01');
                $last_day_this_year  =date('Y-12-t');
                $leaveNumber = Leave::where('leave_category_id', $categoryID)
                    ->where('leave_date','>=', $first_day_this_year)
                    ->where('leave_date','<=', $last_day_this_year)
                    ->where('user_id', Request::input('userID'))
                    ->where('leave_status', 1)
                    ->count();
                if($leaveNumber == Request::input('categoryBudget') || $leaveNumber > Request::input('categoryBudget'))
                return 'false';
                $statusChange = Leave::find($id);
                $statusChange->leave_status = 1;
                $statusChange->user_noti_status = 1;
                $statusChange->save();
                Session::flash('success', 'Leave Status Changed');
                return 'true';
            }elseif(Request::input('status') == 'reject'){
                $statusChange = Leave::find($id);
                $statusChange->leave_status = 2;
                $statusChange->user_noti_status = 1;
                $statusChange->save();
                Session::flash('success', 'Leave Status Changed');
                return 'true';
            }elseif(Request::input('status') == 'delete'){
                $leaveDelete = Leave::find($id);
                $leaveDelete->delete();
                return 'true';
            }
            $data['allLeave'] = Leave::whereHas('User', function($q) {
                $q->where('company_id', Auth::user()->company_id);
            })->get();
            return (String) view('Company.allLeaveAjax',$data);
        }

        public function anyLeaveCategory()
        {
            if (Request::all()) {
                $checkExist = LeaveCategories::where('category', Request::input('category'))
                    ->where('company_id', Auth::user()->company_id)
                    ->first();
                if($checkExist){
                    $response['type'] = 'error';
                    $response['info'] = 'This Category Already Taken';
                    return Response::json($response);
                }

                $rules = array(
                    'category'=> "required|alpha_dash",
                    'category_num'  => 'required|max:2',
                );
                /* Laravel Validator Rules Apply */
                $validator = Validator::make(Input::all(), $rules);
                if ($validator->fails()):
                    $errorMessage =  $validator->messages()->first();
                    $response['type'] = 'error';
                    $response['info'] = $errorMessage;
                    return Response::json($response);
                else:
                    $categoryCreate = new LeaveCategories();
                    $categoryCreate->category = trim(Request::input('category'));
                    $categoryCreate->category_num = Request::input('category_num');
                    $categoryCreate->save();
                    $response['type'] = 'success';
                    $response['id'] = $categoryCreate->id;
                    $data['allCategory'] = LeaveCategories::all();
//                    $response['info'] = (String) view('Company.leaveCategoryAjax',$data);
                    $response['info'] = 'Leave Category Created Successfully';
                    return Response::json($response);
                endif;

            } else{
            $data['allCategory'] = LeaveCategories::all();
            return view('Company.leaveCategory', $data);
        }
        }

        public function getDeleteLeaveCategory($id)
        {
            $leaveCategoriesDelete = LeaveCategories::find($id);
            Leave::where('leave_category_id', $id)->delete();
            $leaveCategoriesDelete->delete();
            return 'true';
        }

        public function getReport()
        {
            $data['startDate'] = Request::input('s_date');
            $data['endDate'] = Request::input('e_date');
            $data['id'] = Request::input('id');
            $data['userInfo'] = \App\User::where('company_id', Auth::user()->company_id)
                                            ->where('id', $data['id'])->first();
            if(!$data['userInfo'])
                return 'There User is Not Your Company';
            $data['attendanceReport'] = UserDetails::
            select(DB::raw('timediff(logout_time,login_time) as timediff'),
                'login_date','logout_date','id','login_time','logout_time','user_id','status')
                ->where('user_id', $data['id'])
                ->where('login_date', '>=',  $data['startDate'])
                ->where('logout_date', '<=', $data['endDate'])
                ->orderBy('id', 'ASC')
                ->get()
                ->toArray();
            $data['allDate']= $this->getDatesFromRange( $data['startDate'], $data['endDate']);
            $data['allHoliday'] = HolidayInfo::where('holiday', '>=',  $data['startDate'])
                ->where('holiday', '<=', $data['endDate'])
                ->get()
                ->toArray();
            $data['allLeave'] = Leave::where('leave_date', '>=',  $data['startDate'])
                ->where('leave_date', '<=', $data['endDate'])
                ->where('user_id', $data['id'])
                ->where('leave_status', 1)
                ->get()
                ->toArray();
            return view('Company.report',$data);

        }

        public function getSummeryReport()
        {
            $data['startDate'] = Request::input('s_date');
            $data['endDate'] = Request::input('e_date');
            $data['attendanceReport'] = UserDetails::
                select(DB::raw('timediff(logout_time,login_time) as timediff'),
                    'login_date','logout_date','id','login_time','logout_time','user_id')
                ->whereHas('User', function($q) {
                    $q->where('company_id', Auth::user()->company_id);
                })
                ->where('login_date', '>=',  $data['startDate'])
                ->where('logout_date', '<=', $data['endDate'])
                ->where('logout_time', '!=', '0000-00-00 00:00:00')
                ->orderBy('id', 'ASC')
                ->get();
            return view('Company.summeryReport',$data);
        }

        public function anyReportSummery()
        {
            if(Request::all()){
                $data['startDate'] = Request::input('from');
                $data['endDate'] = Request::input('to');
                $data['attendanceReport'] = UserDetails::
                select(DB::raw('timediff(logout_time,login_time) as timediff'),
                    'login_date','logout_date','id','login_time','logout_time','user_id')
                    ->whereHas('User', function($q) {
                        $q->where('company_id', Auth::user()->company_id);
                    })
                    ->where('login_date', '>=',  $data['startDate'])
                    ->where('logout_date', '<=', $data['endDate'])
                    ->where('logout_time', '!=', '0000-00-00 00:00:00')
                    ->orderBy('id', 'ASC')
                    ->get();
                if ($data['attendanceReport']->isEmpty()) {
                    Session::flash('flashError', 'There is no report.Because None of Employee Has Not Work From '. $data['startDate'].' to '. $data['endDate']);
                    return redirect('company/report-summery');
                }

                return view('Company.summeryReport',$data);
            }
            else{
                return view('Company.summeryReportRequest');
            }
        }

        public function getDatesFromRange($start, $end) {
            $dates = array($start);
            while (end($dates) < $end) {
                $dates[] = date('Y-m-d', strtotime(end($dates) . ' +1 day'));
            }
            return $dates;
        }

        public function anyFullCalender()
        {
            if(Request::all()) {
                $data['startDate'] = Request::input('from');
                $data['endDate'] = Request::input('to');
                $data['id'] = Request::input('id');
                $data['userInfo'] = \App\User::where('company_id', Auth::user()->company_id)
                    ->where('id', $data['id'])->first();
                if (!$data['userInfo']) {
                    Session::flash('flashError', 'This User Is Not Your Company');
                    return redirect('company/full-calender');
                }

                $data['attendanceReport'] = UserDetails::
                select(DB::raw('timediff(logout_time,login_time) as timediff'),
                    'login_date', 'logout_date', 'id', 'login_time', 'logout_time', 'user_id', 'status')
                    ->where('user_id', $data['id'])
                    ->where('login_date', '>=', $data['startDate'])
                    ->where('logout_date', '<=', $data['endDate'])
                    ->orderBy('id', 'ASC')
                    ->get()
                    ->toArray();

                if (!$data['attendanceReport']) {
                    Session::flash('flashError', $data['userInfo']->username.' Has not Any Work From '.$data['startDate'].' to '. $data['endDate']);
                    return redirect('company/full-calender');
                }


                return view('Company.fullCalender', $data);
            }else{
                $user = new \App\User();
                $data['allUser'] = $user->allUser();
                return view('Company.fullCalenderRequest',$data);
            }
        }

        public function anyTableReport()
        {
            if(Request::all()) {
                $data['startDate'] = Request::input('from');
                $data['endDate'] = Request::input('to');
                $data['id'] = Request::input('id');
                $data['userInfo'] = \App\User::where('company_id', Auth::user()->company_id)
                    ->where('id', $data['id'])->first();
                if (!$data['userInfo']) {
                    Session::flash('flashError', 'This User Is Not Your Company');
                    return redirect('company/table-report');
                }
                $data['allDate']= $this->getDatesFromRange( $data['startDate'], $data['endDate']);
                $data['allHoliday'] = HolidayInfo::where('holiday', '>=',  $data['startDate'])
                    ->where('holiday', '<=', $data['endDate'])
                    ->get()
                    ->toArray();
                $data['allLeave'] = Leave::where('leave_date', '>=',  $data['startDate'])
                    ->where('leave_date', '<=', $data['endDate'])
                    ->where('user_id', $data['id'])
                    ->where('leave_status', 1)
                    ->get()
                    ->toArray();
                $data['attendanceReport'] = UserDetails::
                select(DB::raw('timediff(logout_time,login_time) as timediff'),
                    'login_date', 'logout_date', 'id', 'login_time', 'logout_time', 'user_id', 'status')
                    ->where('user_id', $data['id'])
                    ->where('login_date', '>=', $data['startDate'])
                    ->where('logout_date', '<=', $data['endDate'])
                    ->orderBy('id', 'ASC')
                    ->get()
                    ->toArray();

                if (!$data['attendanceReport']) {
                    Session::flash('flashError', $data['userInfo']->username.' Has not Any Work From '.$data['startDate'].' to '. $data['endDate']);
                    return redirect('company/table-report');
                }

                return view('Company.report', $data);
            }else {
                $user = new \App\User();
                $data['allUser'] = $user->allUser();
                return view('Company.tableReportRequest', $data);
            }
        }

        public function getLogout()
        {
            Auth::logout();
            return redirect('/');
        }

        public function getDeleteUser($id)
        {
            $user = \App\User::find($id);
            UserDetails::where('user_id', $id)->delete();
            Leave::where('user_id', $id)->delete();
            $user->delete();
        }
    }