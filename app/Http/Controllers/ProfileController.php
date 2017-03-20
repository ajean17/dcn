<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Profile;
use App\User;

class ProfileController extends Controller
{
  public function __construct()//no access without being logged in
  {
    $this->middleware('auth');//->except(['index','show']);
  }

  public function index()
  {
    $profiles = Profile::all();
    //$books = Book::latest()->get()
    return view('profile.index',compact('profiles'));
  }

  public function show(User $profileOwner)
  {
   return view('profile.show',compact('profileOwner'));//'friend_check','block_check','block_check2'));
  }

  public function search()
  {
    return view('profile.search');
  }

}
