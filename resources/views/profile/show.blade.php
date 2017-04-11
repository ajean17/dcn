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

  $profile = Profile::where('username','=',$profileOwner->name)->first();
  if($profile == "")
  {
    $profile = Profile::create([
      'username' => $profileOwner->name
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
        $friend_button = '<button id="unfriend">Unfriend</button>';
      }
      else
      {
        $isFriend = false;
        if($ownerBlockViewer == false)
        {
          $friend_button = '<button id="friend">Request As Friend</button>';
          $block_button = '<button id="block">Block User</button>';
        }
      }
      if($block_check != "[]")
      {
        $ownerBlockViewer = true;
        $block_button = '<button id="unblock">Unblock User</button>';
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
      <!--h1>Project Content</h1-->
        <center>
          <?php
            if($hasContent == true)
            {
              echo "<h1>".$projectOne->name."</h1>";
              echo "<h5>".$projectOne->category."</h5>";
              echo "<h6>".$projectOne->subCategory."</h6>";
              if($projectOne->oneType != "upload")
                echo "<h3>Element One</h3>".$projectOne->elementOne."<hr/>";
              else
                echo '<h3>Element One</h3><img src="/uploads/user/'.$profileOwner->name.'/images'.'/'.$projectOne->elementOne.'" width="250px" height="250px" alt="Profile Picture"><hr/>';

              if($projectOne->twoType != "upload")
                echo "<h3>Element Two</h3>".$projectOne->elementTwo."<hr/>";
              else
                echo '<h3>Element Two</h3><img src="/uploads/user/'.$profileOwner->name.'/images'.'/'.$projectOne->elementTwo.'" width="250px" height="250px" alt="Profile Picture"><hr/>';

              if($projectOne->threeType != "upload")
                echo "<h3>Element Three</h3>".$projectOne->elementThree."<hr/>";
              else
                echo '<h3>Element Three</h3><img src="/uploads/user/'.$profileOwner->name.'/images'.'/'.$projectOne->elementThree.'" width="250px" height="250px" alt="Profile Picture"><hr/>';

              if($projectOne->fourType != "upload")
                echo "<h3>Element Four</h3>".$projectOne->elementFour."<hr/>";
              else
                echo '<h3>Element Four</h3><img src="/uploads/user/'.$profileOwner->name.'/images'.'/'.$projectOne->elementFour.'" width="250px" height="250px" alt="Profile Picture"><hr/>';

              if($projectOne->fiveType != "upload")
                echo "<h3>Element Five</h3>".$projectOne->elementFive;
              else
                echo '<h3>Element Five</h3><img src="/uploads/user/'.$profileOwner->name.'/images'.'/'.$projectOne->elementFive.'" width="250px" height="250px" alt="Profile Picture"><hr/>';
            }
            else if($hasContent == false)
            {
              echo "<h1>No content to display yet.....</h1>";
            }
          ?>
        </center>
    </div>
  </div>
  <hr>
@endsection

@section('javascript')
  <script type="text/javascript">
    var token = '{{Session::token()}}';
    var urlf= '{{route('friend')}}';
    var urlb= '{{route('block')}}';

    function toggle(type)
    {
      var $tog = $('#'+type);
      var user = "<?php echo $profileOwner->name?>";
      var log = "<?php echo Auth::user()->name?>";
      console.log(type + " " + user + " " + log);
      $tog.html("please wait...");

      if(type == "friend" || type == "unfriend")
      {
        $.ajax(
        {
          method: 'POST',
          url: urlf,
          data: {type: type, user: user, log: log, _token: token}
        }).done(function (msg)
        {
          console.log(msg['message']);
          if(msg['message'] == "friend_request_sent")
          {
            $tog.html('OK Friend Request Sent');
          }
          else if(msg['message'] == "unfriend_ok")
          {
            $tog.html('Unfriended');//$tog.html('<button id="friend">Request As Friend</button>');
          }
          else
          {
            alert(msg['message']);
            $tog.html('Try again later.')
          }
        });
      }

      if(type == "block" || type == "unblock")
      {
        $.ajax(
        {
          method: 'POST',
          url: urlb,
          data: {type: type, user: user, log: log, _token: token}
        }).done(function (msg)
        {
          console.log(msg['message']);
          if(msg['message'] == "blocked_ok")
          {
            $tog.html('Blocked');//$tog.html('<button id="unblock">Unblock User</button>');
          }
          else if(msg['message'] == "unblocked_ok")
          {
            $tog.html('Unblocked');//$tog.html('<button id="block">Block User</button>');
          }
          else
          {
            alert(msg['message']);
            $tog.html('Try again later.')
          }
        });
      }
    }

    $(document).ready(function()
    {
      var $friend = $('#friend');
      var $block = $('#block');
      var $unfriend = $('#unfriend');
      var $unblock = $('#unblock');

      $friend.on('click', function(){toggle('friend');});
      $unfriend.on('click',function(){toggle('unfriend');});
      $block.on('click',function(){toggle('block');});
      $unblock.on('click',function(){toggle('unblock');});
    });

  </script>
@endsection
