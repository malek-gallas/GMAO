<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    use AuthenticatesUsers;
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
    /**
     * Login username to be used by the controller.
     *
     * @var string
     */
    protected $username = 'employee_id';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->username = $this->findEmployee();
    }
    /**
     * Get the login employee_id to be used by the controller.
     *
     * @return string
     */
    public function findEmployee()
    {
        $login = request()->input('login');
 
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'employee_id';
 
        request()->merge([$fieldType => $login]);
 
        return $fieldType;
    }
    /**
     * Get employee_id property.
     *
     * @return string
     */
    public function Username()
    {
        return $this->username;
    }
}
