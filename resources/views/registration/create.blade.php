@extends('layouts.master')

@section('title')
  Registration | DCN
@endsection

<?php
  use App\User;
  use App\Mail\Welcome;

  if(isset($_GET["usernamecheck"]))
  /*FORM VALIDATION CODE*/
  {
  	$username = preg_replace('#[^a-z0-9]#i', '', $_GET['usernamecheck']);
    $uname_check = User::where('name','=',$username)->get();
    //echo $uname_check;
    if (strlen($username) < 3 || strlen($username) > 16)
    //if the username is less than 3 character or more than 16 characters
    {
	    echo '<strong style="color:#F00;">3 - 16 characters please</strong>';
	    exit();
    }
    if (is_numeric($username[0]))
    //if the first character of the username is not a letter
    {
      echo '<strong style="color:#F00;">Usernames must begin with a letter</strong>';
      exit();
    }
    if ($uname_check == "[]")
    //if the username has not been taken
    {
	    echo '<strong style="color:#009900;">' . $username . ' is OK</strong>';
	    exit();
    }
    else
    {
	    echo '<strong style="color:#F00;">' . $username . ' is taken</strong>';
	    exit();
    }
  }

  // Ajax calls this REGISTRATION code to execute
  if(isset($_GET["u"]))
  {
  	// GATHER THE POSTED DATA INTO LOCAL VARIABLES
  	$u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
  	$e = $_GET['e'];//$e = mysqli_real_escape_string($db_conx, $_GET['e']);
  	$p = $_GET['p'];

  	// DUPLICATE DATA CHECKS FOR USERNAME AND EMAIL
    $u_check = User::where('name','=',$u)->get();
  	$e_check = User::where('email','=',$e)->get();
  	// FORM DATA ERROR HANDLING
  	if($u == "" || $e == "" || $p == "")
    {
  		echo "The form submission is missing values.";
      exit();
  	}
    else if ($u_check != "[]")
    {
      echo "The username you entered is already taken";
      exit();
  	}
    else if ($e_check != "[]")
    {
      echo "That email address is already in use in the system";
      exit();
  	}
    else if (strlen($u) < 3 || strlen($u) > 16)
    {
      echo "Username must be between 3 and 16 characters";
      exit();
    }
     else if (is_numeric($u[0]))
    {
      echo 'Username cannot begin with a number';
      exit();
    }
    else
    {
      // Add user info into the database table for the main site table
      $user = User::create([
        'name' => $u,
        'email' => $e,
        'password' => bcrypt($p)
      ]);

      // Create directory(folder) to hold each user's files(pics, MP3s, etc.)
  		if (!file_exists("user/$u"))
      {
        Storage::disk('local')->makeDirectory('uploads/user/'.$u.'/images');
        Storage::disk('local')->makeDirectory('uploads/user/'.$u.'/videos');
        Storage::disk('local')->makeDirectory('uploads/user/'.$u.'/sounds');
      }
  		/* Establish their row in the useroptions table
  		$sql = "INSERT INTO useroptions (id, username, background) VALUES ('$uid','$u','original')";
  		$query = mysqli_query($db_conx, $sql);*/

  		// Email the user their activation link || YOU NEED TO ESTABLISH WHO IT IS FROM HELLO@EXAMPLE IS UNACCEPTABLE
      \Mail::to($user)->send(new Welcome($user));
      //Be sure to set the env mail driver appropriately and restart the serve for it to take effect
      echo "signup_success";
      exit();
  	}
  	exit();
  }
?>

@section('content')

  <h1>Sign Up</h1>

  <form method="GET" id="signupform" onsubmit="return false;"><!--action="/register"-->
    {{csrf_field()}}

    <div class="form-group">
      <label for="name">User Name:</label>
      <input type="text" class="form-control" id="username" name="name"
      onblur="checkusername()" onkeyup="restrict('username')" maxlength="16">
      <span id="unamestatus"></span>
    </div>

    <div class="form-group">
      <label for="email">Email:</label>
      <input type="email" class="form-control" id="email" name="email"
      onfocus="emptyElement('status')" onkeyup="restrict('email')" maxlength="88">
    </div>

    <div class="form-group">
      <label for="password">Password:</label>
      <input type="password" class="form-control" id="password" name="password"
      onfocus="emptyElement('status')" maxlength="100">
    </div>

    <div class="form-group">
      <label for="password_confirmation">Confirm Password:</label>
      <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
      onfocus="emptyElement('status')" maxlength="100">
    </div>

    <div class="form-group">
      <button type="submit" id="signupbtn" onclick="signup()" class="btn btn-default">
        Create Account
      </button>
      <span id="status"></span>
    </div>
  </form>
  <hr/>
  <div>
    <a href="#" id= "termsLink" onclick="return false" onmousedown="openTerms()">
      View the Terms Of Use
    </a>
  </div>
  <div id="terms" style="display:none;">
      <h3>DCN Terms Of Use</h3>
      <p>1. Activate your account.</p>
      <p>2. Don't be a troll.</p>
      <p>3. Get money.</p>
  </div>
  @include('layouts.errors')
@endsection
