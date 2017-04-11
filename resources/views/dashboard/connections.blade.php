@extends('layouts.master')

@section('title')
  Dashboard | Connections
@endsection

<?php
  use App\User;
  use App\Friend;

  $logged = Auth::user()->name;

  $friends = Friend::where('user2','=',$logged)->where('accepted','=','1')
  ->orWhere('user1','=',$logged)->where('accepted','=','1')->get();
?>

@section('content')
  <h3>Your Connections</h3>
<hr/>
    <?php
      foreach($friends as $friend)
      {
        $buddy = "";
        if($friend->user1 == $logged)
        {
          $buddy = $friend->user2;
        }
        else if($friend->user2 == $logged)
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
        echo '<div><a href="/profile/'.$guy->name.'">'.$user1pic.'</a><b><p>'.$guy->name.'</p></b></div>';
      }
    ?>

<br/>
<br/>
<hr/>
@endsection
