<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{

    public function __construct()
    {
      //store() and create() can't be accessed if not a guest
      $this->middleware('guest')->except('destroy');//;
    }

    public function create()
    {
        return view('sessions.login');
    }

    public function reset(Request $request)
    {
      if($request->has('u') && $request->has('e'))
      {
        $u = $request['u'];//preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
      	$e = $request['e'];//$_GET['e'];
      }
      return view('sessions.reset', compact('u','e'));
    }

    public function reclaim()
    {
        return view('sessions.reclaim');
    }

    public function store(Request $request)
    {
      $active = User::where('name','=',request('name'))->where('activated','=','1')->first();

      //Check to see if the attempted user has been activated before allowing them to log in.
      if($active == "")
        return back()->withErrors(['message' => 'Please check your email to activate your account before logging in.']);

      //attempts to authenticate user and auto signs in
      if(!auth()->attempt(request(['name','password'])))
        return back()->withErrors(['message' => 'Wrong username or password, please try again.']);

      if($active->userType == 0)
        return redirect('/gettingStarted');
      else
        return redirect('/profile'.'/'.$request['name']);
    }

    public function destroy()
    {
        auth()->logout();
        return redirect('/');
    }
}
