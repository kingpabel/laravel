<?php namespace App\Http\Controllers;
use App\Leave;
use App\LeaveCategories;
use App\UserDetails;
use Auth;
use Request;
use Response;
use Session;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Symfony\Component\Security\Core\User\User;
use Illuminate\Support\Facades\Hash;

class CompanyController extends Controller
{

    public function __construct()
    {
        date_default_timezone_set('Asia/Dhaka');
    }

    public function  getIndex()
    {
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
            return redirect('company/create-user');
        }
        return view('Company.createUser');
    }

    public  function getAllUser()
    {
        $user = new \App\User();
        $data['allUser'] = $user->allUser();
        return view('Company.allUser',$data);
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
    public function postUpdateUserTime($id){
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
    public function getLogout()
    {
        Auth::logout();
        return redirect('/');
    }
}