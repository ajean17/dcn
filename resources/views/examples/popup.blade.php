@extends('layouts.master')

@section('title')
  Pop up example
@endsection

@section('content')
    <div id="mainform">
      <h2>jQuery Popup Form Example</h2>
      <!-- Required div starts here -->
      <div class="form" id="popup">
        <b>1.Onload Popup Login Form</b><br/><hr/>
        <span>Wait for 3 second.Login Popup form Will appears.</span><br/><br/><br/>

        <b>2.Onclick Popup Contact Form</b><hr/>
        <p id="onclick">Popup</p>
        <br/>
      </div>
    </div>

    <!--Contact Form -->
    <div id="contactdiv">
      <form class="form" action="#" id="contact">
        <img src="images/button_cancel.png" class="img" id="cancel"/>
        <h3>Contact Form</h3>
        <hr/><br/>
        <label>Name: <span>*</span></label>
        <br/>
        <input type="text" id="name" placeholder="Name"/><br/>
        <br/>
        <label>Email: <span>*</span></label>
        <br/>
        <input type="text" id="email" placeholder="Email"/><br/>
        <br/>
        <label>Contact No: <span>*</span></label>
        <br/>
        <input type="text" id="contactno" placeholder="10 digit Mobile no."/><br/>
        <br/>
        <label>Message:</label>
        <br/>
        <textarea id="message" placeholder="Message......."></textarea><br/>
        <br/>
        <input type="button" id="send" value="Send"/>
        <input type="button" id="cancel" value="Cancel"/>
        <br/>
      </form>
    </div>

@endsection

@section('javascript')
  <script>
    $(document).ready(function()
    {
      //setTimeout(popup, 3000);
      function popup()
      {
        $("#logindiv").css("display", "block");
      }
      $("#login #cancel").click(function()
      {
        $(this).parent().parent().hide();
      });
      $("#onclick").click(function()
      {
        $("#contactdiv").css("display", "block");
      });
      $("#contact #cancel").click(function()
      {
        $(this).parent().parent().hide();
      });
      // Contact form popup send-button click event.
      $("#send").click(function()
      {
        var name = $("#name").val();
        var email = $("#email").val();
        var contact = $("#contactno").val();
        var message = $("#message").val();
        if (name == "" || email == "" || contactno == "" || message == "")
        {
          alert("Please Fill All Fields");
        }
        else
        {
          if (validateEmail(email))
          {
            $("#contactdiv").css("display", "none");
          }
          else
          {
            alert('Invalid Email Address');
          }
          function validateEmail(email)
          {
            var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
            if (filter.test(email))
            {
              return true;
            }
            else
            {
              return false;
            }
          }
        }
      });
      // Login form popup login-button click event.
      $("#loginbtn").click(function()
      {
        var name = $("#username").val();
        var password = $("#password").val();
        if (username == "" || password == "")
        {
          alert("Username or Password was Wrong");
        }
        else
        {
          $("#logindiv").css("display", "none");
        }
      });
    });
  </script>

  <style>
    @import url(http://fonts.googleapis.com/css?family=Fauna+One|Muli);
    #mainform
    {
      width:960px;
      margin:20px auto;
      padding-top:20px;
      font-family: 'Fauna One', serif;
      display:block;
    }
    h2
    {
      margin-left: 65px;
      text-shadow:1px 0px 3px gray;
    }
    h3
    {
      font-size:18px;
      text-align:center;
      text-shadow:1px 0px 3px gray;
    }
    #onclick
    {
      padding:3px;
      color:green;
      cursor:pointer;
      padding:5px 5px 5px 15px;
      width:70px;
      color:white;
      background-color:#123456;
      box-shadow:1px 1px 5px grey;
      border-radius:3px;
    }
    b
    {
      font-size:18px;
      text-shadow:1px 0px 3px gray;
    }
    #popup
    {
      padding-top:80px;
    }
    .form
    {
      border-radius:2px;
      padding:20px 30px;
      box-shadow:0 0 15px;
      font-size:14px;
      font-weight:bold;
      width:350px;
      margin:20px 250px 0 35px;
      float:left;
    }
    input
    {
      width:100%;
      height:35px;
      margin-top:5px;
      border:1px solid #999;
      border-radius:3px;
      padding:5px;
    }
    input[type=button]
    {
      background-color:#123456;
      border:1px solid white;
      font-family: 'Fauna One', serif;
      font-Weight:bold;
      font-size:18px;
      color:white;
      width:49%;
    }
    textarea
    {
      width:100%;
      height:80px;
      margin-top:5px;
      border-radius:3px;
      padding:5px;
      resize:none;
    }
    #contactdiv
    {
      opacity:0.92;
      position: absolute;
      top: 200px;
      left: auto;
      height: 100%;
      width: 100%;
      background-color: rgba(0,0,0,.8);
      display: none;
    }
    #login,#contact
    {
      width:350px;
      margin:0px;
      background-color:white;
      font-family: 'Fauna One', serif;
      position: relative;
      border: 5px solid rgb(90, 158, 181);
    }
    .img
    {
      float: right;
      margin-top: -35px;
      margin-right: -37px;
    }
    #contact
    {
      left: 50%;
      top: 50%;
      margin-left: -210px;
      margin-top: -255px;
    }
  </style>
@endsection
