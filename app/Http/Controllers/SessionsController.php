<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class SessionsController extends Controller
{
  public function create()
  {
    return view('sessions.login');
  }

  public function store()
  {

  //Attempt to auth the user, and if not return them back
  if(! auth()->attempt(request(['email', 'password']))) //ISN"T MATCHING IDK WHY!
  {
    return back()->withErrors([
      'message' => 'Email/Password do not match our records. Try Again.'
    ]);
  }

  //Redirect to the home page
  return redirect()->home();
  }

  public function destroy()
  {
    auth()->logout();

    return redirect()->home();
  }

}
