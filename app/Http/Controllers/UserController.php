<?php namespace App\Http\Controllers;
use App\Leave;
use App\LeaveCategories;
use App\UserDetails;
use Auth;
use Request;
use Session;
use DB;
use App\HolidayInfo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Symfony\Component\Security\Core\User\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller {

    public function __construct()
    {
        date_default_timezone_set(Auth::user()->CompanyUser->time_zone);
    }
    public function getIndex()
    {
        $data = array();
        $data['leaveUpdate'] = Leave::where('user_id', Auth::user()->id)
                                    ->where('user_noti_status', 1)
                                    ->count();
        $data['max_info'] = UserDetails::maxRow();
        if (!empty($data['max_info']) && $data['max_info']->logout_date == '0000-00-00') {
            $data['status'] = 'Punch Out';
        } else {
            $data['status'] = "Punch In";
        }
         $maxToday= UserDetails::maxRowToday();
        if($maxToday) {
            $timeDiff = strtotime(date('Y-m-d H:i:s')) - strtotime($maxToday->login_time);
            if($timeDiff == 0)
                $timeDiff = 1;
            Session::put('timeTrack', $timeDiff);
        }
            return view('Users.dashBoard', $data);
    }

    public function getLogout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function getPunchOut()
    {
        $last_id = UserDetails::maxRow();
        $last_id = $last_id->id;
        $punchOut = UserDetails::find($last_id);
        $punchOut->logout_date = date('Y-m-d', time());
        $punchOut->logout_time = date('Y-m-d H:i:s', time());
        if($punchOut->save()){
            Session::forget('timeTrack');
            Session::flash('punchMessageSuccess', 'You Are Punch Out');
        }
        else{
            Session::flash('punchMessageError', 'There is Error When You Are Punch Out');
        }
        return redirect('user');
    }
    public function getPunchIn()
    {
        $last_id = UserDetails::maxRow();
        if($last_id) {
            if (Auth::user()->time) {
                if (date('Y-m-d', time()) == $last_id->login_date) {
                    $status = 'Present';
                } else {
                    if (date('H:i:s', time()) > date(Auth::user()->time, time())) {
                        $status = 'Late';
                        Session::flash('welcome_message', 'You Are Late Today!');
                    } else {
                        Session::flash('welcome_message', 'Thanks for come in time');
                        $status = 'Present';
                    }
                }
            } else {
                $status = 'Present';
            }
        }
        else {
            $status = 'Present';
        }
        $punchIn = new UserDetails();
        $punchIn->status = $status;
        $punchIn->user_id = Auth::user()->id;
        $punchIn->user_name = Auth::user()->username;
        $punchIn->login_time = date('Y-m-d H:i:s', time());
        $punchIn->login_date = date('Y-m-d', time());
        $punchIn->save();
        Session::flash('punchMessageSuccess', 'You Are Punch In');
        return redirect('user');
    }

    public function anyUpdateProfile()
    {
        if(Request::all()){
            $ignoreID = Auth::user()->id;
            $rules = array(
                'user_first_name'  => 'required|alpha_dash',
                'user_last_name'  => 'required|alpha_dash',
                'username'=> "unique:users,username,$ignoreID",
                'user_email' => 'email'
            );
            /* Laravel Validator Rules Apply */
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()):
                return $validator->messages()->first();
            else:
                $userUpdate = \App\User::find($ignoreID);
                $userUpdate->user_first_name = trim(Request::input('user_first_name'));
                $userUpdate->user_last_name = trim(Request::input('user_last_name'));
                $userUpdate->username = trim(Request::input('username'));
                $userUpdate->user_email = trim(Request::input('user_email'));
                $userUpdate->save();
            endif;
            return 'true';
        }
        return view('Users.updateProfile');
    }

    public function anyChangePassword()
    {
        if(Request::all()){
            $rules = array(
                'new_password'  => 'sometimes|required|same:confirm_new_password|min:6',
                'password'  => 'sometimes|required|password_check',
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
        }
        return view('Users.changePassword');
    }

    public function anyApplyLeave()
    {
        $first_day_this_year = date('Y-01-01');
        $last_day_this_year  =date('Y-12-t');
        $leaveByCategory = DB::table('leaves')
            ->join('leave_categories', 'leaves.leave_category_id', '=', 'leave_categories.id')
            ->select(DB::raw('count(leaves.leave_category_id) as category_used'),
            'leave_categories.id','leave_categories.category','leave_categories.category_num')
            ->where('leaves.leave_date','>=', $first_day_this_year)
            ->where('leaves.leave_date','<=', $last_day_this_year)
            ->where('leaves.leave_status', 1)
            ->where('leaves.user_id', Auth::user()->id)
            ->where('leave_categories.company_id', Auth::user()->company_id)
            ->groupBy('leaves.leave_category_id','leave_categories.category','leave_categories.category_num','leave_categories.id')
            ->get();
        $allCategory = LeaveCategories::all();
        $leaveBudget = array();
        foreach($allCategory as $key=>$category):
            $searchId = $category->id;
            $expectedArray = array_filter($leaveByCategory, function($searchArray) use ($searchId) {
                return ($searchArray->id == $searchId);
            });
            $expectedArray = array_merge_recursive($expectedArray);
            $leaveBudget[$category->id]["id"]=$category->id;
            $leaveBudget[$category->id]["category"]=$category->category;
            if(empty($expectedArray)) {
                $leaveBudget[$category->id]["categoryUsed"] = 0;
                $leaveBudget[$category->id]["categoryBudget"] = $category->category_num;
            }
            else {
                $leaveBudget[$category->id]["categoryUsed"] = $expectedArray[0]->category_used;
                $leaveBudget[$category->id]["categoryBudget"] = $category->category_num - $expectedArray[0]->category_used;
            }
            $leaveBudget[$category->id]["categoryTotal"] = $category->category_num;
        endforeach;
            Session::put('checkBudget', $leaveBudget);
        $data['leaveBudget'] = $leaveBudget;
        return view('Users.applyLeave',$data);
    }

    public function postLeaveApply()
    {
        $checkBudget= Session::get('checkBudget');
        $leaveCause = Request::input('leave_cause');
        $leaveCategoryId = Request::input('leave_category_id');
        $leaveDate = Request::input('leave_date');
        $firstDayThisYear = date('Y-01-01');
        $lastDayThisYear  = date('Y-12-t');

        foreach ($leaveDate as $key=>$singleDate):
            if($singleDate == '') {
                return 'Please fill all Leave date field';
            }
            elseif($singleDate > $lastDayThisYear) {
                return 'You Can Apply leave only for this Year';
            }
            elseif($singleDate < $firstDayThisYear) {
                return 'You Can Apply leave only for this Year';
            }
        endforeach;

        foreach($checkBudget as $budget):
            if($budget['id'] == $leaveCategoryId) {
                $key=$key+1;
                if($budget['categoryBudget']<$key) {
                    return 'You Cross Your Leave Budget.PLease Check Again.';
                }
            }
        endforeach;

        foreach ($leaveDate as $singleDate):
             $checkExisting  = Leave::where('leave_date' , $singleDate)
                ->where('user_id', Auth::user()->id)
                ->first();
            if($checkExisting) {
                return 'You are Already Apply '.$singleDate;
            }
        endforeach;

        foreach ($leaveDate as $singleDate){
            $leaveSave = new Leave();
            $leaveSave->leave_date = $singleDate;
            $leaveSave->user_id = Auth::user()->id;
            $leaveSave->leave_category_id = $leaveCategoryId;
            $leaveSave->leave_cause = $leaveCause;
            $leaveSave->save();
        }
        return 'true';
    }

    public function getMyLeave()
    {
        Leave::where('user_id', Auth::user()->id)->update(array('user_noti_status' => 0));
        $data['myLeave'] = Leave::where('user_id', Auth::user()->id)->get();
        return view('Users.myLeave', $data);
    }
    public function getReport()
    {
        $data['startDate'] = Request::input('s_date');
        $data['endDate'] = Request::input('e_date');
        $data['id'] = Auth::user()->id;
        $data['userInfo'] = \App\User::find($data['id']);
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
        return view('Users.report',$data);

    }

    public function anyFullCalender()
    {
        if(Request::all()) {
            $data['startDate'] = Request::input('from');
            $data['endDate'] = Request::input('to');
            $data['id'] = Auth::user()->id;
            $data['userInfo'] = \App\User::find($data['id']);
            if (!$data['userInfo']) {
                Session::flash('flashError', 'This User Is Not Found');
                return redirect('user/full-calender');
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
                Session::flash('flashError', 'You are not Any Work From '.$data['startDate'].' to '. $data['endDate']);
                return redirect('user/full-calender');
            }
            return view('Users.fullCalender', $data);
        }else{
            $user = new \App\User();
            $data['allUser'] = $user->allUser();
            return view('Users.fullCalenderRequest',$data);
        }
    }

    public function anyTableReport()
    {
        if(Request::all()) {
            $data['startDate'] = Request::input('from');
            $data['endDate'] = Request::input('to');
            $data['id'] = Auth::user()->id;
            $data['userInfo'] = \App\User::find($data['id']);
            if (!$data['userInfo']) {
                Session::flash('flashError', 'This User Is Not Found');
                return redirect('user/table-report');
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
                Session::flash('flashError', 'You are not Any Work From '.$data['startDate'].' to '. $data['endDate']);
                return redirect('user/table-report');
            }

            return view('Users.report', $data);
        }else {
            $user = new \App\User();
            $data['allUser'] = $user->allUser();
            return view('Users.tableReportRequest', $data);
        }
    }

    public function getDatesFromRange($start, $end) {
        $dates = array($start);
        while (end($dates) < $end) {
            $dates[] = date('Y-m-d', strtotime(end($dates) . ' +1 day'));
        }
        return $dates;
    }
}
