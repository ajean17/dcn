@extends('layouts.master')

@section('title')
  Registration | DCN
@endsection

@section('content')
  <!--div class="col-sm-8"-->
  <legend class="m-b-1 text-xs-center">Registration</legend>

  <form method="POST" id="signupform" action="/register"><!--onsubmit="return false;"-->
    {{csrf_field()}}

    <div class="form-group">
    <label for="name">User Name:</label>
    <input type="text" class="form-control" id="username" name="name" maxlength="16">
    <span id="unamestatus"></span>
    </div>

    <div class="form-group">
    <label for="email">Email:</label>
    <input type="email" class="form-control" id="email" name="email" maxlength="88">
    </div>

    <div class="form-group">
    <label for="first">First Name:</label>
    <input type="text" class="form-control" id="first" name="first" maxlength="16">
    </div>

    <div class="form-group">
    <label for="last">Last Name:</label>
    <input type="text" class="form-control" id="last" name="last" maxlength="16">
    </div>

    <div class="form-group">
    <label for="password">Password:</label>
    <input type="password" class="form-control" id="password" name="password" maxlength="100">
    </div>

    <div class="form-group">
    <label for="password_confirmation">Confirm Password:</label>
    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" maxlength="100">
    </div>

    <div class="form-group">
      <label for="location">Your Location:</label>
      <select name="location" id="location">
      	<option value="AL">Alabama</option>
      	<option value="AK">Alaska</option>
      	<option value="AZ">Arizona</option>
      	<option value="AR">Arkansas</option>
      	<option value="CA">California</option>
      	<option value="CO">Colorado</option>
      	<option value="CT">Connecticut</option>
      	<option value="DE">Delaware</option>
      	<option value="DC">District Of Columbia</option>
      	<option value="FL">Florida</option>
      	<option value="GA">Georgia</option>
      	<option value="HI">Hawaii</option>
      	<option value="ID">Idaho</option>
      	<option value="IL">Illinois</option>
      	<option value="IN">Indiana</option>
      	<option value="IA">Iowa</option>
      	<option value="KS">Kansas</option>
      	<option value="KY">Kentucky</option>
      	<option value="LA">Louisiana</option>
      	<option value="ME">Maine</option>
      	<option value="MD">Maryland</option>
      	<option value="MA">Massachusetts</option>
      	<option value="MI">Michigan</option>
      	<option value="MN">Minnesota</option>
      	<option value="MS">Mississippi</option>
      	<option value="MO">Missouri</option>
      	<option value="MT">Montana</option>
      	<option value="NE">Nebraska</option>
      	<option value="NV">Nevada</option>
      	<option value="NH">New Hampshire</option>
      	<option value="NJ">New Jersey</option>
      	<option value="NM">New Mexico</option>
      	<option value="NY">New York</option>
      	<option value="NC">North Carolina</option>
      	<option value="ND">North Dakota</option>
      	<option value="OH">Ohio</option>
      	<option value="OK">Oklahoma</option>
      	<option value="OR">Oregon</option>
      	<option value="PA">Pennsylvania</option>
      	<option value="RI">Rhode Island</option>
      	<option value="SC">South Carolina</option>
      	<option value="SD">South Dakota</option>
      	<option value="TN">Tennessee</option>
      	<option value="TX">Texas</option>
      	<option value="UT">Utah</option>
      	<option value="VT">Vermont</option>
      	<option value="VA">Virginia</option>
      	<option value="WA">Washington</option>
      	<option value="WV">West Virginia</option>
      	<option value="WI">Wisconsin</option>
      	<option value="WY">Wyoming</option>
      </select>
    </div>

    <div class="form-group">
      <button type="submit" id="signupbtn" class="btn btn-default">
        Create Account
      </button>
      <span id="status"></span>
    </div>
  </form>
  <hr/>
  <div>
    <a href="#" id= "termsLink" onclick="return false">
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

@section('javascript')
  <script>
    var token = '{{Session::token()}}';
    var url = '{{route('check')}}';
    $(document).ready(function()
    {
      var $user = $('#username');
      var $email = $('#email');
      var $pass = $('#password');
      var $passConf = $('#password_confirmation');
      var $status = $('#status');
      var $first = $('#first');
      var $last = $('#last');
      var $location = $('#location');

      $('#username, #email, #password, #password_confirmation, #first, #last, #location').on('click', function(){$status.html("");});
      $('#termsLink').on('click', function()
      {
        $('#terms').css('display','block');
        $status.html("");
      })
      //checks user name
      $user.on('blur', function()
      {
        if($user.val() != "")
        {
          $('#unamestatus').html('checking...');
          $.ajax(
          {
            method: 'POST',
            url: url,
            data: {username: $user.val(), _token: token}
          }).done(function (msg)
          {
            $('#unamestatus').html(msg['message']);
          });
        }
      });

      /*$('#signupbtn').on('click', function()
      {
        var u = $user.val();
        var e = $email.val();
        var p1 = $pass.val();
        var p2 = $passConf.val();
        var f = $first.val();
        var l = $last.val();
        var loc = $location.val();
        //if any of the above elements are empty
        if(u == "" || e == "" || p1 == "" || p2 == "" || f == "" || l == "" || loc == "")
          $status.html('Fill out all of the form data');
        else if(p1 != p2)//if the passwords do not match
          $status.html('Your password fields do not match');
        else if( $('#terms').css('display') == "none")//if the terms have not been viewed yet, display is still none
          $status.html('Please view the terms of use');
        else
        {
          $('#signupbtn').css('display','none');
          $status.html("please wait...");

          $.ajax(
          {
            method: 'POST',
            url: url,
            data: {user: u, email: e, password: p1, first: f, last: l, location: loc, _token: token}
          }).done(function (msg)
          {
            //console.log(msg['message']);
            if(msg['message'] != "signup_success")
            {
              $('#signupbtn').css('display','block');
              $status.html(msg['message']);
            }
            else if(msg['message'] == "signup_success")
            {
              $('#signupform').html("OK "+u+", check your email inbox and junk mail box at <u>"+e+"</u> in a moment to complete the sign up process by activating your account. You will not be able to do anything on the site until you successfully activate your account.")
              $('#terms').css('display','none');
              $('#termsLink').css('display','none');
            }
          });
        }
      });*/
    });
  </script>
@endsection
