<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class DashboardController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }

  public function index()
  {
      return view('dashboard.home');
  }

  public function account(User $User)
  {
    return view('dashboard.settings',compact('User'));
  }

  public function notifications(User $User)
  {
    //$notes = Notification::where('username','=',$User)->orderBy('created_at','desc')->get();
    return view('dashboard.notifications',compact('User'));
  }

  public function inbox(User $inboxOwner)
  {
    return view('dashboard.inbox',compact('inboxOwner'));
  }
}
