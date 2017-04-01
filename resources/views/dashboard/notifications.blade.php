@extends('layouts.master')

@section('title')
  Notifications | DCN
@endsection

<?php
  use App\User;
  use App\Friend;
  use App\Notification;

  $notification_list = "";
  $log_username = Auth::user()->name;
  $notes = Notification::where('username','=',$User->name)->orderBy('created_at','desc')->get();
  $picURL = "";
  //echo $notes;
  if($notes == "[]")
  {
  	$notification_list = "You do not have any notifications";
  }
  else
  {
  	/*while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC))
    {
  		$noteid = $row["id"];
  		$initiator = $row["initiator"];
  		$app = $row["app"];
  		$note = $row["note"];
  		$date_time = $row["date_time"];
  		$date_time = strftime("%b %d, %Y", strtotime($date_time));
  		$notification_list .= "<p><a href='user.php?u=$initiator'>$initiator</a> | $app<br />$note</p>";
  	}*/
    foreach ($notes as $note)
    {
      $noteid = $note->id;
  		$initiator = $note->initiator;
  		$app = $note->app;
  		$note = $note->note;
  		$date_time = $note->created_at;
  		$date_time = strftime("%b %d, %Y", strtotime($date_time));
  		$notification_list .= "<p><a href='user.php?u=$initiator'>$initiator</a> | $app<br />$note</p>";
    }
  }
  //Update the user's notescheck property, to signal that the notifications have been checked
  User::where('name','=',$log_username)->update(array('notescheck'=> Carbon\Carbon::now()));
  ?>
  <?php
  /*FRIEND REQUESTS**/
  $friend_requests = "";
  $requests = Friend::where('user2','=',$User->name)->where('accepted','=','0')->orderBy('created_at','asc')->get();
  if($requests == "[]")
  {
  	$friend_requests = 'No friend requests';
  }
  else
  {
    foreach ($requests as $request)
    {
      $reqID = $request->id;
  		$user1 = $request->user1;
  		$datemade = $request->created_at;
  		$datemade = strftime("%B %d", strtotime($datemade));
  		$user1avatar = User::where('name','=',$user1)->first();//First gives you an object instead of an array
      $user1avatar = $user1avatar ->avatar;
      $user1pic = '<img src="/uploads/user/'.$user1.'/images'.'/'.$user1avatar.'" alt="'.$user1.'" class="user_pic">';
  		if($user1avatar == NULL)
      {
        $picURL = "/images/Default.jpg";
  			$user1pic = '<img src="'.$picURL.'" alt="'.$user1.'" class="user_pic">';
  		}
  		$friend_requests .= '<div id="friendreq_'.$reqID.'" class="friendrequests">';
  		$friend_requests .= '<a href="/profile/'.$user1.'">'.$user1pic.'</a>';
  		$friend_requests .= '<div class="user_info" id="user_info_'.$reqID.'">'.$datemade.' <a href="/profile/'.$user1.'">'.$user1.'</a> requests friendship<br /><br />';
  		$friend_requests .= '<button onclick="friendReqHandler(\'accept\',\''.$reqID.'\',\''.$user1.'\',\'user_info_'.$reqID.'\')">accept</button> or ';
  		$friend_requests .= '<button onclick="friendReqHandler(\'reject\',\''.$reqID.'\',\''.$user1.'\',\'user_info_'.$reqID.'\')">reject</button>';
  		$friend_requests .= '</div>';
  		$friend_requests .= '</div>';
    }
  }
?>

@section('content')
  <div id="notesBox"><h2>Notifications</h2><?php echo $notification_list; ?></div>
  <div id="friendReqBox"><h2>Friend Requests</h2><?php echo $friend_requests; ?></div>
  <div style="clear:left;"></div>
@endsection

@section('javascript')
<script type="text/javascript">
  function friendReqHandler(action,reqid,user1,elem)
  {
    /*var conf = confirm("Press OK to '"+action+"' this friend request.");
    if(conf != true)
    {
      return false;
    }*/
    document.getElementById(elem).innerHTML = "processing ...";
    var ajax = ajaxObj("GET", "/friendSystem?action="+action+"&reqid="+reqid+"&user1="+user1);//+"&logged="+loggedin);
    ajax.onreadystatechange = function()
    {
      if(ajaxReturn(ajax) == true)
      {
        if(ajax.responseText == "accepted")
        {
          document.getElementById(elem).innerHTML = "<b>Request Accepted!</b><br />Your are now friends";
        }
        else if(ajax.responseText == "rejected")
        {
          document.getElementById(elem).innerHTML = "<b>Request Rejected</b><br />You chose to reject friendship with this user";
        }
        else
        {
          document.getElementById(elem).innerHTML = ajax.responseText;
        }
      }
    }
    ajax.send();
  }
</script>
@endsection