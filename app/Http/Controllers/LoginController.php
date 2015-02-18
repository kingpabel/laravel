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
            return redirect()->intended('user');
        }
        else
        {
            return redirect('/');
        }
    }


    public function postCheckCompany()
    {
        $credentials = array(
            'company_user_name' => Request::input('company_user_name'),
            'password' => Request::input('password'),
        );
        return Request::input('company_user_name');
        if(Auth::attempt( $credentials ))
        {
            return redirect()->intended('user');
        }
        else
        {
            return redirect('/');
        }
    }



}