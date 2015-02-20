<?php namespace App\Http\Controllers;
use App\Leave;
use App\LeaveCategories;
use App\UserDetails;
use Auth;
use Request;
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
        /*
        $query = $this->UserDetails->find();
        $late_user = $query->contain(['Users'])
            ->where(array('login_date' => date('Y-m-d', time())))
            ->where(array('Users.company_id' => $this->Auth->user('id')))
            ->where(array('UserDetails.status' => 'Late'))
            ->group(array('UserDetails.user_id'))
            ->order(array('UserDetails.id' => 'ASC'))
            ->all();*/
    }

    public function getLogout()
    {
        Auth::logout();
        return redirect('/');
    }
}