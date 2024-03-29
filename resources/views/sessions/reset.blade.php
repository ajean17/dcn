@extends('layouts.master')

@section('title')
  Change Password | OneTribe
@endsection

@section('content')
  <form method="GET" onsubmit="return false;" id="newPasswordForm">
    {{ csrf_field() }}
    <h4>Enter your new password</h4>

    <!--div class="form-group">
      <label for="password"> Temporary Password:</label>
      <input type="password" class="form-control" id="temppassword" name="temppassword"
      onfocus="emptyElement('status')" maxlength="100">
    </div-->

    <div class="form-group">
      <label for="password"> New Password:</label>
      <input type="password" class="form-control" id="newPassword" name="newPassword"
      onfocus="emptyElement('status')" maxlength="100">
    </div>

    <div class="form-group">
      <label for="password_confirmation">Confirm Password:</label>
      <input type="password" class="form-control" id="newpassword_confirmation" name="newpassword_confirmation"
      onfocus="emptyElement('status')" maxlength="100">
    </div>

    <div class="form-group">
      <button type="submit" id="updatePasswordButton" onclick="updatePassword()" class="btn btn-default">Send</button>
    </div>
    <span id="status"></span>
  </form>
@endsection

@section('javascript')
  <script>
    var token = '{{Session::token()}}';
    var url= '{{route('password')}}';

    $(document).ready(function()
    {
      var password = $('#newPassword').val;
      var confirm = $('#newpassword_confirmation').val;
      var status = $('#status');
      var u = "<?php echo $u?>";
      var e = "<?php echo $e?>";

      $('#updatePasswordButton').on('click', function()
      {
        if(password != "" && confirm != "")
        {
          if(password != confirm)
            status.html('Passwords do not match.');
          else
          {
            $('#updatePasswordButton').hide();
            status.html('please wait...');

            $.ajax(
            {
              method: 'POST',
              url: url,
              data: {u:u, e:e, p:password, _token:token}
            }).done(function (msg)
            {
              //console.log(msg['message']);
              if(msg['message'] != "reset_success")
              {
                status.html(msg['message']);
                $('#updatePasswordButton').show();
              }
              else if(msg['message'] == "reset_success")
                $('#newPasswordForm').html('Your password has successfully been reset!');
            });
          }
        }
      });
    });
    /*function updatePassword()
    {
      var password = document.getElementById('newPassword').value;
      var confirm = document.getElementById('newpassword_confirmation').value;
      var status = document.getElementById('status');
      if(password != "" && confirm != "")
      {
        if(password != confirm)
        {
          status.innerHTML = 'Passwords do not match.';
        }
        else
        {
          document.getElementById("updatePasswordButton").style.display = "none";
          status.innerHTML = 'please wait ...';
      		var ajax = ajaxObj("GET", "passwordSystem?u="+u+"&e="+e+"&p="+password);
      		ajax.onreadystatechange = function()
      		{
            if(ajaxReturn(ajax) == true)
      			{
              if(ajax.responseText != "reset_success")
      				{
      					status.innerHTML = ajax.responseText;
      					document.getElementById("updatePasswordButton").style.display = "block";
      				}
      				else if(ajax.responseText == "reset_success")
      				{
      					window.scrollTo(0,0);
      					document.getElementById("newPasswordForm").innerHTML = "Your password has successfully been reset!";
      				}
            }
          }
          ajax.send();
        }
      }
    }*/
  </script>
@endsection
