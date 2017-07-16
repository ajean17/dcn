@extends('layouts.master')

@section('title')
  Notifications | DCN
@endsection

<?php
  use App\User;
  use App\Friend;
  use App\Notification;

  $notification_list = "";
  $log_userid = Auth::user()->id;
  $notes = Notification::where('user_id','=',$User->id)->orderBy('created_at','desc')->get();
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
  User::where('name','=',$log_userid)->update(array('notescheck'=> Carbon\Carbon::now()));
  ?>
  <?php
  /*FRIEND REQUESTS**/
  $friend_requests = "";
  $requests = Friend::where('user2','=',$User->id)->where('accepted','=','0')->orderBy('created_at','asc')->get();
  if($requests == "[]")
  {
  	$friend_requests = 'No friend requests';
  }
  else
  {
    foreach ($requests as $request)
    {
      $reqID = $request->id;
  		$user_1 = $request->user1;
  		$datemade = $request->created_at;
  		$datemade = strftime("%B %d", strtotime($datemade));
  		$user1 = User::where('id','=',$user_1)->first();//First gives you an object instead of an array
      $user1avatar = $user1->avatar;
      $user1pic = '<img src="/uploads/user/'.$user1->id.'/images'.'/'.$user1avatar.'" alt="'.$user1->name.'" class="user_pic">';
  		if($user1avatar == NULL)
      {
        $picURL = "/images/Default.jpg";
  			$user1pic = '<img src="'.$picURL.'" alt="'.$user1->name.'" class="user_pic">';
  		}
  		$friend_requests .= '<div id="friendreq_'.$reqID.'" class="friendrequests">';
  		$friend_requests .= '<a href="/profile/'.$user1->id.'">'.$user1pic.'</a>';
  		$friend_requests .= '<div class="user_info" id="user_info_'.$reqID.'">'.$datemade.' <a href="/profile/'.$user1->id.'">'.$user1->id.'</a> requests friendship<br /><br />';
  		$friend_requests .= '<button onclick="friendReqHandler(\'accept\',\''.$reqID.'\',\''.$user1->id.'\',\'user_info_'.$reqID.'\')">accept</button> or ';
  		$friend_requests .= '<button onclick="friendReqHandler(\'reject\',\''.$reqID.'\',\''.$user1->id.'\',\'user_info_'.$reqID.'\')">reject</button>';
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
  var token = '{{Session::token()}}';
  var url= '{{route('friend')}}';

  function friendReqHandler(action,reqid,user1,elem)
  {
    //document.getElementById(elem).innerHTML = "processing ...";
    var $elem = $('#'+elem);
    var log = "<?php echo Auth::user()->id;?>";

    $elem.html("processing...");
    //console.log("Action "+action+" ReqID "+reqid+" User1 "+user1+" elem "+elem);
    $.ajax(
    {
      method: 'POST',
      url: url,
      data: {action: action, reqid: reqid, user1: user1, log: log, _token: token}
    }).done(function (msg)
    {
      //console.log(msg['message']);
      if(msg['message'] == "accepted")
        $elem.html("<b>Request Accepted!</b><br />Your are now friends");//$tog.html('<button id="unblock">Unblock User</button>');

      else if(msg['message'] == "rejected")
        $elem.html("<b>Request Rejected</b><br />You chose to reject friendship with this user");//$tog.html('<button id="block">Block User</button>');

      else
        $elem.html(msg['message'])

    });

    /*var ajax = ajaxObj("GET", "/friendSystem?action="+action+"&reqid="+reqid+"&user1="+user1);//+"&logged="+loggedin);
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
    ajax.send();*/
  }
</script>
@endsection
