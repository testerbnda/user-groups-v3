<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
     */

    //use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    //protected $redirectTo = RouteServiceProvider::HOME;

    public function getresetpassword($token)
    {
        $tokenData = DB::table('password_resets')->where('token', $token)->first();

        if ($tokenData) {
            return view('auth.passwords.reset')->with(['token' => $token, 'user_email' => $tokenData->email]);
        } else {
            return redirect()->back()->with('error', 'A Network Error occurred. Please try again.');
        }
    }

    public function reset_password_with_token(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:6',
        ]);
        $requested_data = $request->all();

        $setPassword = DB::table('users')->where('email', $requested_data['email'])->update(['password' => Hash::make($requested_data['password']), 'updated_at' => Carbon::now()]);

        if ($setPassword) {
            return redirect('/login')->with('success', 'Password Set Successfully');
        } else {
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }
}
