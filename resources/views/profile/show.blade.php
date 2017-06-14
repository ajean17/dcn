@extends('layouts.master')
<?php
  use App\Friend;
  use App\Block;
  use App\Profile;
  use App\Project;
  use App\User;
  use App\Category;

  $loggedUser = Auth::user()->name;
  $isOwner = false;
  $isFriend = false;
  $ownerBlockViewer = false;
  $hasContent = false;
  $who = "";
  $what = "";
  $friend_button = '<button disabled class="navButton">Request As Friend</button>';
  $block_button = '<button disabled class="navButton">Block User</button>';
  if($profileOwner->userType == 1)
    $what = "Inventor";
  else if($profileOwner->userType == 2)
    $what = "Investor";
  else
    return redirect('/gettingStarted');
  //Make sure the profile owner has a profile created
  $profile = Profile::where('username','=',$profileOwner->name)->first();
  if($profile == "")
    $profile = Profile::create(['username' => $profileOwner->name]);
  //Retrieve the projects of this profile owner
  $projectOne = Project::where('id','=',$profile->projectOneID)->first();
    //$projectTwo = Project::where('id','=',$profile->projectTwoID)->first();
  if($projectOne != "")// && $projectTwo == "")
    $hasContent = true;
  //Pull up the list of the profile owner's friends
  $friends = Friend::where('user2','=',$profileOwner->name)->where('accepted','=','1')
  ->orWhere('user1','=',$profileOwner->name)->where('accepted','=','1')->get();
  //Verify if this profile belongs to the one visiting the page
  if($profileOwner->id == Auth::user()->id)
  {
    $isOwner = true;
    $who = "Your";
  }
  else
  {
    $who = $profileOwner->name."'s";
    //Check to see if the profile owner and logged in user are friends
    $friend_check = Friend::where('user1','=',$loggedUser)->where('user2','=',$profileOwner->name)->where('accepted','=','1')
    ->orWhere('user1','=',$profileOwner->name)->where('user2','=',$loggedUser)->where('accepted','=','1')->get();
    //Check to see if the profile owner and logged in user are blocked
    $block_check = Block::where('blocker','=',$loggedUser)->where('blockee','=',$profileOwner->name)
    ->orWhere('blocker','=',$profileOwner->name)->where('blockee','=',$loggedUser)->get();
    //Friend  and Block button logic for profile
    if($friend_check != "[]")//If the friend check is not empty
    {
      $isFriend = true;
      $friend_button = '<button class="navButton" id="unfriend">Unfriend</button>';
    }
    else
    {
      $isFriend = false;
      if($ownerBlockViewer == false)
      {
        $friend_button = '<button class="navButton" id="friend">Request As Friend</button>';
        $block_button = '<button class="navButton" id="block">Block User</button>';
      }
    }
    if($block_check != "[]")
    {
      $ownerBlockViewer = true;
      $block_button = '<button class="navButton" id="unblock">Unblock User</button>';
      $friend_button = '<button class="navButton" disabled>Request As Friend</button>';
    }
  }
  $parent = "WHERE parent IS NULL";
  $categories = DB::select(DB::raw('SELECT * FROM categories '.$parent.' ORDER BY name ASC'));
  function elementUpload($number)
  {
    ?>
    <div class="form-group">
      <label for="element{{$number}}"><b>Element {{$number}}:</b></label>
      &nbsp;<button type='button' class="btn btn-outline-primary btn-sm" id="{{$number}}NameTog">Rename Element</button><br/>
      <input type="text" id="{{$number}}Name" class="form-control element" name="{{$number}}Name" placeholder="Provide a new name for this element." size="29" maxlength="28">
      Select the type of content you wish to place for this element.<br/>
      <input type="radio" name="{{$number}}Type" value="text" onclick="showType('{{$number}}')">Text &nbsp;
      <input type="radio" name="{{$number}}Type" value="embed" onclick="showType('{{$number}}')">Embedding &nbsp;
      <input type="radio" name="{{$number}}Type" value="upload" onclick="showType('{{$number}}')">Upload &nbsp;<br/>
      <input type="text" class="form-control element" id="el-{{$number}}-text" name="{{$number}}T" placeholder="Enter Description">
      <input type="text" class="form-control element" id="el-{{$number}}-embed" name="{{$number}}E" placeholder="Enter Embed Link">
      <input type="file" class="form-control element" id="el-{{$number}}-upload" name="{{$number}}U" placeholder="Upload File">
    </div>
    <?php
  }
?>
@section('title')
  {{$profileOwner->name}} | DCN
@endsection

@section('content')
  @include ('layouts.errors')
  <br/>
  <!--THE PROFILE HEADER-->
  <div class="row">
    <!--THE PROFILE PICTURE/AVATAR-->
    <div class=" col-sm-2 profileLeft">
      <div id="profile_pic_box">
        @if($isOwner==true)
          <!--FORM TO CHANGE AVATAR-->
          <a id="editAvatar" href="#" onclick="return false;" onmousedown="toggleElement('avatar_form')">Edit Avatar</a>
          <form id="avatar_form" enctype="multipart/form-data" method="post" action="/photoSystem/<?php echo Auth::user()->name?>">
            {{csrf_field()}}
            <h4>Change your avatar</h4>
            <input type="file" name="avatar" required>
            <p><input type="submit" value="Upload"></p>
          </form>
        @endif
        <?php
          if($profileOwner->avatar == NULL)
            echo '<img src="/images/Default.jpg" width="245px" height="245px" alt="Profile Picture"><br/>';
          else
            echo '<img src="/uploads/user/'.$profileOwner->name.'/images'.'/'.$profileOwner->avatar.'" width="250px" height="250px" alt="Profile Picture"><br/>';
        ?>
      </div>
      <hr/>
      <div id="titles">
        <center><strong><p>{{$profileOwner->name}}</p></strong></center>
        <p><strong>Role:</strong> {{$what}}</p>
        @if($hasContent == true)
        <p><strong>Company:</strong><br/>{{$projectOne->name}}</p>
        <p><strong>Industry:</strong><br/> {{$projectOne->category}} {{$projectOne->subCategory}}</p>
        @endif
      </div>
      <hr/>
      <div id="quickNav">
        <button type="button" class="navButton" data-toggle="modal" data-target="#connectionsModal">View Connections</button>
      @if($isOwner == true)
        <button type="button" class="navButton" data-toggle="modal" data-target="#profileModal">Edit Profile</button>
        <button type="button" class="navButton" data-toggle="modal" data-target="#settingsModal">Account Settings</button>
      @else
        <?php
          echo $friend_button;
          echo $block_button;
          //Mentor and mentee buttons
        ?>
      @endif
      </div>
    </div>
    <!--THE PROJECT CONTENT AREA-->
    <div class="col-sm-10 profileRight" data-spy="scroll" data-target="#qNav" data-offset="20">
      <div id="projectContent">
        @if($hasContent == true)
        <br/>
        <div id="tabs">
          @if($projectOne->elementOne != NULL)<button id ="tab1" class="tablinks">{{$projectOne->oneName}}</button>@endif
          @if($projectOne->elementTwo != NULL)<button id ="tab2" class="tablinks">{{$projectOne->twoName}}</button>@endif
          @if($projectOne->elementThree != NULL)<button id ="tab3" class="tablinks">{{$projectOne->threeName}}</button>@endif
          @if($projectOne->elementFour != NULL)<button id ="tab4" class="tablinks">{{$projectOne->fourName}}</button>@endif
          @if($projectOne->elementFive != NULL)<button id ="tab5" class="tablinks">{{$projectOne->fiveName}}</button>@endif
        </div>
        <hr/>
        @endif
        <center>
          <?php
            if($hasContent == true)
            {
              if($projectOne->oneType != "upload")
                echo "<div id='elementOne'>
                        <h3>".$projectOne->oneName."</h3>
                        <br/>
                        ".$projectOne->elementOne."
                      </div>";
              else
                echo "<div id='elementOne'>
                        <h3>".$projectOne->oneName."</h3>
                        <br/>
                        <img  class='modalPic' src='/uploads/user/".$profileOwner->name."/images"."/".$projectOne->elementOne."' width='600px' height='600px' alt='Profile Picture' data-toggle='modal' data-target='#elemOneModal'>
                      </div>
                      <div id='elemOneModal' class='modal fade' role='dialog'>
                        <div class='modal-dialog modal-lg bigModal'>
                          <div class='modal-content'>
                            <div class='modal-header'>
                              <button type='button' class='close' data-dismiss='modal'>&times;</button>
                            </div>
                            <div class='modal-body'>
                              <img class='fullPic' src='/uploads/user/".$profileOwner->name."/images"."/".$projectOne->elementOne."' width='600px' height='600px' alt='Profile Picture'>
                            </div>
                            <div class='modal-footer'>
                              <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                            </div>
                          </div>
                        </div>
                      </div>";
                    //reformat the modal size to provide a full screen effect  
              if($projectOne->twoType != "upload")
                echo "<div id='elementTwo'><h3>".$projectOne->twoName."</h3><br/>".$projectOne->elementTwo."</div><br/><br/>";
              else
                echo "<div id='elementTwo'><h3>".$projectOne->twoName."</h3><br/><img src='/uploads/user/".$profileOwner->name."/images"."/".$projectOne->elementTwo."' width='600px' height='600px' alt='Profile Picture'></div>";

              if($projectOne->threeType != "upload")
                echo "<div id='elementThree'><h3>".$projectOne->threeName."</h3><br/>".$projectOne->elementThree."</div>";
              else
                echo "<div id='elementThree'><h3>".$projectOne->threeName."</h3><br/><img src='/uploads/user/".$profileOwner->name."/images"."/".$projectOne->elementThree."' width='600px' height='600px' alt='Profile Picture'></div>";

              if($projectOne->fourType != "upload")
                echo "<div id='elementFour'><h3>".$projectOne->fourName."</h3><br/>".$projectOne->elementFour."</div>";
              else
                echo "<div id='elementFour'><h3>".$projectOne->fourName."</h3><br/><img src='/uploads/user/".$profileOwner->name."/images"."/".$projectOne->elementFour."' width='600px' height='600px' alt='Profile Picture'></div>";

              if($projectOne->fiveType != "upload")
                echo "<div id='elementFive'><h3>".$projectOne->fiveName."</h3><br/>".$projectOne->elementFive."</div>";
              else
                echo "<div id='elementFive'><h3>".$projectOne->fiveName."</h3><br/><img src='/uploads/user/".$profileOwner->name."/images"."/".$projectOne->elementFive."' width='600px' height='600px' alt='Profile Picture'></div>";
            }
            else if($hasContent == false)
            {
              echo "<h1>No content to display yet.....</h1>";
            }
          ?>
        </center>
      </div>
    </div>
  </div>
  <br/>
  <!--LIST OF CONNECTIONS MODAL-->
  <div id="connectionsModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">{{$who}} Connections</h4>
        </div>
        <div class="modal-body">
          <?php
            foreach($friends as $friend)
            {
              $buddy = "";
              if($friend->user1 == $profileOwner->name)
              {
                $buddy = $friend->user2;
              }
              else if($friend->user2 == $profileOwner->name)
              {
                $buddy = $friend->user1;
              }
              $guy =  User::where('name','=',$buddy)->first();
              $user1avatar = $guy ->avatar;
              $user1pic = '<img src="/uploads/user/'.$guy->name.'/images'.'/'.$user1avatar.'" alt="'.$guy->name.'" class="user_pic">';
          		if($user1avatar == NULL)
              {
                $picURL = "/images/Default.jpg";
          			$user1pic = '<img src="'.$picURL.'" alt="'.$guy->name.'" class="user_pic">';
          		}
              //echo '<div><a href="/profile/'.$guy->name.'">'.$user1pic.'</a><b><p>'.$guy->name.'</p></b></div>';
              echo '<div class="friendrequests">
                      <a href="/profile/'.$guy->name.'">'.$user1pic.'</a>
                      <div class="user_info"><b><p>'.$guy->name.'</p></b>
                      </div>
                    </div><hr/>';
            }
          ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!--PROFILE MANAGEMENT MODAL-->
  <div id="profileModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Edit Mode</h4>
        </div>
        <div class="modal-body">
          <form id="profileContentForm" enctype="multipart/form-data" method="post" action="/projectSystem"><!--onsubmit="return false;"-->
            {{csrf_field()}}
            <input type="hidden" name="userName" value="{{Auth::user()->name}}">
            <center>
              <h5>You may add up to five elements to your project's profile</h5>
              @include ('layouts.errors')
            </center>
            <div class="form-group">
              <label for="category"><b>Select a Category that Describes your Project</b></label><br/>
                <?php
                  echo "<select id='categories' name=\"category\" onmouseup='showSub()'>";
                  echo "<option value='dummy' disabled selected>Select your option</option>";
                  foreach($categories as $category)
                  {
                    echo "<option value='".$category->name."'>".$category->name."</option>";
                  }
                  echo "</select><br/>";
                  foreach($categories as $category)
                  {
                    echo "<div style='display:none;' id='".$category->name."'><select name='subCategory'>";
                    echo "<option value='dummy2' disabled selected>Select an advanced category (Optional)</option>";
                    $subCategory = Category::where('parent','=',$category->name)->orderBy('name','asc')->get();
                    foreach($subCategory as $sub)
                    {
                      echo "<option value='".$sub->name."'>".$sub->name."</option>";
                    }
                    echo "</select></div>";
                  }
                ?>
              </select>
            </div>
            <div class="form-group">
              <label for="title"><b>Please provide a title for your project.</b></label><br/>
              <input type="text" id="projectTitle" name="title">
            </div>
            <?php
              elementUpload("One");
              elementUpload("Two");
              elementUpload("Three");
              elementUpload("Four");
              elementUpload("Five");
            ?>
            <div class="form-group">
              <button type="submit" id="contentButton" onclick="addContent()" class="btn btn-default">
                Update Profile
              </button>
              <span id="status"></span>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!--ACCOUNT SETTINGS MODAL-->
  <div id="settingsModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Account Settings</h4>
        </div>
        <div class="modal-body">
          <!--SETTINGS GO HERE-->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <br/>
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
      //console.log(type + " " + user + " " + log);
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
          //console.log(msg['message']);
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
          //console.log(msg['message']);
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
      var $edit = $('#editAvatar');

      $edit.hide();
      $('#elementOne, #elementTwo, #elementThree, #elementFour, #elementFive').hide();
      $('#elementOne').show();
      $friend.on('click', function(){toggle('friend');});
      $unfriend.on('click',function(){toggle('unfriend');});
      $block.on('click',function(){toggle('block');});
      $unblock.on('click',function(){toggle('unblock');});

      $('#profile_pic_box').mouseover(function(){$edit.show();});
      $('#profile_pic_box').mouseout(function(){$edit.hide();});

      $('#OneNameTog').on('click', function()
      {
        if($('#OneName').css('display')=="none")
          $('#OneName').show();
        else
          $('#OneName').hide();
      });
      $('#TwoNameTog').on('click', function()
      {
        if($('#TwoName').css('display')=="none")
          $('#TwoName').show();
        else
          $('#TwoName').hide();
      });
      $('#ThreeNameTog').on('click', function()
      {
        if($('#ThreeName').css('display')=="none")
          $('#ThreeName').show();
        else
          $('#ThreeName').hide();
      });
      $('#FourNameTog').on('click', function()
      {
        if($('#FourName').css('display')=="none")
          $('#FourName').show();
        else
          $('#FourName').hide();
      });
      $('#FiveNameTog').on('click', function()
      {
        if($('#FiveName').css('display')=="none")
          $('#FiveName').show();
        else
          $('#FiveName').hide();
      });
      $('#tab1, #tab2, #tab3, #tab4, #tab5').on('click', function()
      {
        $('#elementOne, #elementTwo, #elementThree, #elementFour, #elementFive').hide();
        switch (this.id)
        {
          case 'tab1':
            $('#elementOne').show();
          break;
          case 'tab2':
            $('#elementTwo').show();
          break;
          case 'tab3':
            $('#elementThree').show();
          break;
          case 'tab4':
            $('#elementFour').show();
          break;
          case 'tab5':
            $('#elementFive').show();
          break;
        }
      });
    });
    //THE FOLLOWING FUNCTIONS ARE FOR THE PROJECT MANAGEMENT FORM
    function showSub()
    {
      var categories = document.getElementById('categories');
      var list = categories.value;
      if(list != "dummy")
      {
        var sub = document.getElementById(list);
        //console.log("List is: "+list);

        if(sub.style.display == "none")
          sub.style.display = "block";
        else if(sub.style.display == "block")
          sub.style.display = "none";
      }
    }
    function showType(select)
    {
      //console.log('Select = ' + select);
      var radios = document.getElementsByName(select + "Type");
      var textBox = document.getElementById("el-"+select+"-text");
      var embedBox = document.getElementById("el-"+select+"-embed");
      var uploadBox = document.getElementById("el-"+select+"-upload");
      var radioValue = "ok";

      for(var x = 0; x < radios.length; x ++)
      {
        if (radios[x].checked)
        {
         radioValue = radios[x].value;
        }
      }
      //console.log('Value=' + radioValue);
      switch(radioValue)
      {
        case "text":
          textBox.style.display = "block";
          embedBox.style.display = "none";
          uploadBox.style.display = "none";
        break;

        case "embed":
          textBox.style.display = "none";
          embedBox.style.display = "block";
          uploadBox.style.display = "none";
        break;
        case "upload":
          textBox.style.display = "none";
          embedBox.style.display = "none";
          uploadBox.style.display = "block";
        break;
      }
    }
  </script>
@endsection
