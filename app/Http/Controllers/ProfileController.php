<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Profile;

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

  public function show(Profile $profile)
  {
    return view('profile.show',compact('profile'));
    //return view('profile.settings');
  }

  public function settings()
  {
    return view('profile.settings');
  }
}
