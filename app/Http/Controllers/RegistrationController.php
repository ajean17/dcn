<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Profile;
use App\Mail\WelcomeTwo;

class RegistrationController extends Controller
{
     public function __construct()
     {
         $this->middleware('guest');
     }

    public function create()
    {
        return view('registration.create');
    }

    public function activation()
    {
        return view('registration.activate');
    }

    public function store()
    {
      //validates the registration form input
      $this->validate(request(),[
        'name' => 'required',
        'email' => 'required|email',
        'password' => 'required|confirmed'
      ]);
      //creates a user based on the requested information
      $user = User::create([
        'name' => request('name'),
        'email' => request('email'),
        'password' => bcrypt(request('password')),//passwords must be encrypted for the attempt method to work!
        'notescheck' => time()
      ]);

      //log in user upon creation
      auth()->login($user);
      //create a profile and attach it to the user
      $profile = Profile::create(['user_id' => auth()->id()]);
      //send a welcome email to the user
      \Mail::to($user)->send(new WelcomeTwo($user));
      //create a welcome message upon the first LogicException
      session()->flash('message','Welcome to the Dream Catcher Network!');//flashes only exist for a single request, never more
      //send the user to the home page
      return redirect('/');
    }
}
