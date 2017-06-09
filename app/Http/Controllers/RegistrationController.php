<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Profile;
use App\Mail\Welcome;
use \Storage;

class RegistrationController extends Controller
{
     public function __construct()
     {
         $this->middleware('guest');
     }

    public function create()
    {
        return view('registration.register');
    }

    public function activation(Request $request)
    {
      $message = "";
      if($request->has('u') && $request->has('e') && $request->has('p'))
      {
        //Connect to database and sanitize incoming $_GET variables
        $u = $request['u'];//preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
        $e = $request['e'];
        $p = $request['p'];
        //Evaluate the lengths of the incoming $_GET variables
        if(strlen($u) < 3 || strlen($e) < 5 || strlen($p) == "")
          //Log this issue into a text file and email details to yourself
          $message = "activation_string_length_issues";

        //Check their credentials against the database
        $numrows = User::where('name','=',$u)->where('email','=',$e)->where('password','=',$p)->where('activated','=','0')->first();

        // Evaluate for a match in the system (0 = no match, 1 = match)
        if($numrows == "")
          //Log this potential hack attempt to text file and email details to yourself
          $message = "Either your credentials are not matching anything in our system, or you have already been activated.";

        else // Match was found, you can activate them
          User::where('name','=',$u)->update(Array('activated'=> '1'));
      }
      else
        // Log this issue of missing initial $_GET variables
        $message = "missing_GET_variables";

      return view('message', compact('message'));
    }

    public function register(Request $request)
    {
      if($request->has('username'))
      {
        $username = $request['username'];
        $uname_check = User::where('name','=',$username)->first();
        $message = "Testing";

        if (strlen($username) < 3 || strlen($username) > 16)
        //if the username is less than 3 character or more than 16 characters
        {
          $message = '<strong style="color:#F00;">3 - 16 characters please</strong>';
        }
        if (is_numeric($username[0]))
        //if the first character of the username is not a letter
        {
          $message = '<strong style="color:#F00;">Usernames must begin with a letter</strong>';
        }
        if ($uname_check == "")
        //if the username has not been taken
        {
          $message = '<strong style="color:#009900;">' . $username . ' is OK</strong>';
        }
        else if ($uname_check != "")
        {
          $message = '<strong style="color:#F00;">' . $username . ' is taken</strong>';
        }
        return response()->json(['message' => $message]);
      }

      if($request->has('user') && $request->has('email') && $request->has('password'))
      {
        // GATHER THE POSTED DATA INTO LOCAL VARIABLES
        $message = "On a roll";//preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
        $u = $request['user'];
        $e = $request['email'];
        $p = $request['password'];

        // DUPLICATE DATA CHECKS FOR USERNAME AND EMAIL
        $u_check = User::where('name','=',$u)->first();
        $e_check = User::where('email','=',$e)->first();

        // FORM DATA ERROR HANDLING
        if($u == "" || $e == "" || $p == "")
          $message = "The form submission is missing values.";
        else if ($u_check != "")
          $message = "The username you entered is already taken";
        else if ($e_check != "")
          $message = "That email address is already in use in the system";
        else if (strlen($u) < 3 || strlen($u) > 16)
          $message = "Username must be between 3 and 16 characters";
        else if (is_numeric($u[0]))
          $message = 'Username cannot begin with a number';
        else
        {
          //Add user info into the database table for the main site table
          $user = User::create([
            'name' => $u,
            'email' => $e,
            'password' => bcrypt($p),
            'activated' => '1'
          ]);

          $profile = Profile::create([
            'username' => $u
          ]);

          // Create directory(folder) to hold each user's files(pics, MP3s, etc.)
          /*if (!file_exists("user/$u"))
          {
            Storage::disk('local')->makeDirectory('uploads/user/'.$u.'/images');
            Storage::disk('local')->makeDirectory('uploads/user/'.$u.'/videos');
            Storage::disk('local')->makeDirectory('uploads/user/'.$u.'/sounds');
          }
          /* Establish their row in the useroptions table
          $sql = "INSERT INTO useroptions (id, username, background) VALUES ('$uid','$u','original')";
          $query = mysqli_query($db_conx, $sql);*/

          // Email the user their activation link || YOU NEED TO ESTABLISH WHO IT IS FROM HELLO@EXAMPLE IS UNACCEPTABLE
          //\Mail::to($user)->send(new Welcome($user));
          //Be sure to set the env mail driver appropriately and restart the serve for it to take effect
          $message = "signup_success";
        }
        return response()->json(['message' => $message]);
      }
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
