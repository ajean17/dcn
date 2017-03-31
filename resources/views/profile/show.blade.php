@extends('layouts.master')
<?php
  use App\Friend;
  use App\Block;
  use App\Profile;
  use App\Project;

  $loggedUser = Auth::user()->name;
  $isOwner = false;
  $isFriend = false;
  $ownerBlockViewer = false;
  $hasContent = false;

  $profile = Profile::where('username','=',$loggedUser)->first();
  if($profile == "")
  {
    $profile = Profile::create([
      'username' => $loggedUser
    ]);
  }

  $projectOne = Project::where('id','=',$profile->projectOneID)->first();
  //$projectTwo = Project::where('id','=',$profile->projectTwoID)->first();
  if($projectOne == "")// && $projectTwo == "")
  {
    $hasContent = false;
  }
  else
  {
    $hasContent = true;
  }

  if($profileOwner->id == Auth::user()->id)
  {
    $isOwner = true;
  }
?>
@section('title')
  {{$profileOwner->name}} | DCN
@endsection

@section('content')
  <?php

    $friend_button = '<button disabled>Request As Friend</button>';
    $block_button = '<button disabled>Block User</button>';
    if($isOwner == "true")
    {
      echo "<h1>Your Profile</h1>";
    }
    else
    {
      echo "<h1>$profileOwner->name's Profile</h1>";
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

      //Friend button logic for profile
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
  ?>
  <hr>
  <div class="row profileHead">
    <div class=" profilePic" id="profile_pic_box">
      @if($isOwner==true)
        <a href="#" onclick="return false;" onmousedown="toggleElement('avatar_form')">Edit Avatar</a>
        <form id="avatar_form" enctype="multipart/form-data" method="post" action="/photoSystem/<?php echo Auth::user()->name?>">
          {{csrf_field()}}
          <h4>Change your avatar</h4>
          <input type="file" name="avatar" required>
          <p><input type="submit" value="Upload"></p>
        </form>
      @endif
      <?php
        if($profileOwner->avatar == NULL)
        {
          echo '<img src="/images/Default.jpg" width="245px" height="245px" alt="Profile Picture"><br/>';
        }
        else
        {
          echo '<img src="/uploads/user/'.$profileOwner->name.'/images'.'/'.$profileOwner->avatar.'" width="250px" height="250px" alt="Profile Picture"><br/>';
        }
      ?>
    </div>
    @if($isOwner==false)
      <span id="friendBtn"><?php echo $friend_button; ?></span>
      <span id="blockBtn"><?php echo $block_button; ?></span>
    @endif
    @include ('layouts.errors')
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
    <div onclick="hideNav()" class="col-sm-2 quickNav">
      <h1>Quick Nav</h1>
      <ul>
        <li>Element One</li>
        <li>Element Two</li>
        <li>Element Three</li>
        <li>Element Four</li>
        <li>Element Five</li>
      </ul>
    </div>
    <div class="col-sm-10 projectContent">
      <h1>Project Content</h1>
        <center>
          <?php
            if($hasContent == true)
            {
              echo "<h3>Element One</h3>".$projectOne->elementOne."<hr/>";
              echo "<h3>Element Two</h3>".$projectOne->elementTwo."<hr/>";
              echo "<h3>Element Three</h3>".$projectOne->elementThree."<hr/>";
              echo "<h3>Element Four</h3>".$projectOne->elementFour."<hr/>";
              echo "<h3>Element Five</h3>".$projectOne->elementFive;
            }
            else if($hasContent == false)
            {
              echo "<h3>No content to display yet...</h3>";
            }
          ?>
        </center>
    </div>
  </div>
  <hr>
@endsection

@section('javascript')
  <script type="text/javascript">

    $(document).ready(mainJQuery);
    function friendToggle(type, user, element)
    {
      /*var conf = confirm("Press OK to confirm the '"+type+"' action for user <?php echo $profileOwner->name; ?>.");
      if(conf != true)
      {
        return false;
      }*/
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

    function blockToggle(type, user, element)
    {
      /*var conf = confirm("Press OK to confirm the '"+type+"' action on user <?php echo $profileOwner->name; ?>.");
      if(conf != true)
      {
        return false;
      }*/

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
