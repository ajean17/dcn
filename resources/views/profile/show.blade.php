@extends('layouts.master')
<?php
  use App\Friend;
  use App\Block;
  $isFriend = false;
  $friend_button = '<button disabled>Request As Friend</button>';
  $block_button = '<button disabled>Block User</button>';
  $ownerBlockViewer = false;

?>
@section('title')
  {{$profileOwner->name}} | DCN
@endsection

@section('content')
<br/>
  <?php
    $loggedUser = Auth::user()->name;

    if($profileOwner->id == Auth::user()->id)
    {
      $isOwner = true;
      echo "<h1>Your Profile</h1>";
    }
    else
    {
      echo "<h1>$profileOwner->name's Profile</h1>";
      $isOwner = false;
      $friend_check = Friend::where('user1','=',$loggedUser)
      ->where('user2','=',$profileOwner->name)
      ->where('accepted','=','1')
      ->orWhere('user1','=',$profileOwner->name)
      ->where('user2','=',$loggedUser)
      ->where('accepted','=','1')->get();
      //$friend_check = Friend::where('user1','=','Alvin')->where('user2','=','Palmer')->where('accepted','=','1')->orWhere('user1','=','Palmer')->where('user2','=','Alvin')->where('accepted','=','1')->get();

      $block_check = Block::where('blocker','=',$loggedUser)
      ->where('blockee','=',$profileOwner->name)
      ->orWhere('blocker','=',$profileOwner->name)
      ->where('blockee','=',$loggedUser)->get();
      //$block_check = App\Block::where('blocker','=','Alvin')->where('blockee','=','Palmer')->orWhere('blocker','=','Palmer')->where('blockee','=','Alvin')->get();


      /*
        Friend button logic for profile
      */
      if($friend_check!="[]")//If the friend check is not empty
          {
            $isFriend = true;
            $friend_button = '<button onclick="friendToggle(\'unfriend\',\''.$profileOwner->name.'\',\'friendBtn\')">Unfriend</button>';
          }
          else
          {
            $isFriend = false;
            if($ownerBlockViewer == false)
            {
              $friend_button = '<button onclick="friendToggle(\'friend\',\''.$profileOwner->name.'\',\'friendBtn\')">Request As Friend</button>';
              $block_button = '<button onclick="blockToggle(\'block\',\''.$profileOwner->name.'\',\'blockBtn\')">Block User</button>';
            }
          }

        if($block_check != "[]")
        {
          $ownerBlockViewer = true;
          $block_button = '<button onclick="blockToggle(\'unblock\',\''.$profileOwner->name.'\',\'blockBtn\')">Unblock User</button>';
          $friend_button = '<button disabled>Request As Friend</button>';
        }

        }
    /*
    if($isFriend == true)
    {
      $friend_button = '<button onclick="friendToggle(\'unfriend\',\''.$profileOwner.'\',\'friendBtn\')">Unfriend</button>';
      $block_button = '<button onclick="blockToggle(\'block\',\''.$profileOwner.'\',\'blockBtn\')">Block User</button>';
    }
    */
  ?>
  <hr>
  <div class="row profileHead">
    <div class="col-sm-3 profilePic">
      <h1>Profile Picture</h1>
      <img class="featurette-image img-fluid mx-auto" data-src="holder.js/500x500/auto" alt="Generic placeholder image">
      @if($isOwner==false)
        <span id="friendBtn"><?php echo $friend_button; ?></span>
        <span id="friendBtn"><?php echo $block_button; ?></span>
      @endif
    </div>
    <div class="col-sm-9 banner">
      <h1>Banner</h1>
      <div class="bannerTabs">
        <a href="#">Project One</a>
        <a href="#">| Project Two</a>
        <a href="#">| Project Three</a>
      </div>
    </div>
  </div>
  <hr>
  <div class="row profileBody">
    <div class="col-sm-2 quickNav">
      <h1>Quick Nav</h1>
      <ul>
        <li>Element One</li>
        <li>Element Two</li>
        <li>Element Three</li>
        <li>Element Four</li>
      </ul>
    </div>
    <div class="col-sm-10 projectContent">
      <h1>Project Content</h1>
    </div>
  </div>
  <hr>
  <div class="row profileFoot">
    <div class="col-sm-12">
      <h1>Reserved for more</h1>
    </div>
  </div>
@endsection

@section('javascript')
  <script type="text/javascript">
    function friendToggle(type, user, element)
    {
      var conf = confirm("Press OK to confirm the '"+type+"' action for user <?php echo $profileOwner->name; ?>.");
      if(conf != true)
      {
        return false;
      }
      document.getElementById(element).innerHTML = "please wait ...";
      var ajax = ajaxObj("GET", "/friendSystem?type="+type+"&user="+user);
      ajax.onreadystatechange = function()
      {
        if(ajaxReturn(ajax) == true)
        {
          if(ajax.responseText == "friend_request_sent")
          {
            document.getElementById(element).innerHTML = 'OK Friend Request Sent';
          }
          else if(ajax.responseText == "unfriend_ok")
          {
            document.getElementById(element).innerHTML = '<button onclick="friendToggle(\'friend\',\'<?php echo $profileOwner->name; ?>\',\'friendBtn\')">Request As Friend</button>';
          }
          else
          {
            alert(ajax.responseText);
            document.getElementById(element).innerHTML = 'Try again later';
          }
        }
      }
      ajax.send();
    }
    function blockToggle(type, user, elem)
    {
      var conf = confirm("Press OK to confirm the '"+type+"' action on user <?php echo $profileOwner->name; ?>.");
      if(conf != true)
      {
        return false;
      }

      document.getElementById(element).innerHTML = 'please wait ...';
      var ajax = ajaxObj("GET", "/blockSystem?type="+type+"&user="+user);
      ajax.onreadystatechange = function()
      {
        if(ajaxReturn(ajax) == true)
        {
          if(ajax.responseText == "blocked_ok")
          {
            document.getElementById(element).innerHTML = '<button onclick="blockToggle(\'unblock\',\'<?php echo $profileOwner->name; ?>\',\'blockBtn\')">Unblock User</button>';
          }
          else if(ajax.responseText == "unblocked_ok")
          {
            document.getElementById(element).innerHTML = '<button onclick="blockToggle(\'block\',\'<?php echo $profileOwner->name; ?>\',\'blockBtn\')">Block User</button>';
          }
          else
          {
            alert(ajax.responseText);
            document.getElementById(element).innerHTML = 'Try again later';
          }
        }
      }
      ajax.send();
    }
  </script>
@endsection
