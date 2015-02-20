<?php namespace App\Http\Controllers;
use Auth;
use Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller {

    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */
   public function index()
    {
        return view('loginPage');
    }

    public function postCheckUser()
    {
        $credentials = array(
            'username' => Request::input('username'),
            'password' => Request::input('password'),
        );
//         Config::set('auth.model', 'CompanyUser');
        if(Auth::attempt( $credentials ))
        {
            if(Auth::user()->user_label == 2)
            return redirect()->intended('user');
            if(Auth::user()->user_label == 1)
                return redirect()->intended('company');
        }
        else
        {
            return redirect('/');
        }
    }
}