<?php
use App\User;
use App\Block;

  if(isset($_GET['type']) && isset($_GET['user']))
  {
    $blockee = preg_replace('#[^a-z0-9]#i', '', $_GET['user']);
    $log_username = Auth::user()->name;

    $exist_count = User::where('name','=', $blockee)->get();

    if($exist_count == "[]")
    {
      echo "$blockee does not exist";
      exit();
    }

    if($_GET['type'] == "block")
    {
      $block_check = Block::where('blocker','=',$log_username) //check if block exists between users
      ->where('blockee','=', $blockee)
      ->orWhere('blocker','=', $blockee)
      ->where('blockee','=',$log_username)->get();

      if($block_check != "[]") //if user is already blocked
      {
        echo "You have already blocked this user.";
	        exit();
      }
      else
      {
        $block = Block::create([
        'blocker' => $log_username,
        'blockee' => $blockee,
        'dateblocked' => Carbon\Carbon::now()
      ]);
        echo "blocked_ok";
	       exit();
      }
    }
    else if ($_GET['type'] == "unblock")
    {
      $block_check = Block::where('blocker','=', $log_username)->where('blockee','=', $blockee)->get();
      if($block_check == "[]")
      {
        echo "User is not blocked, unable to unblock them.";
         exit();
      }
      else
      {
        //same query as block_check2, individual variable
        $block_check3 = Block::where('blocker','=', $log_username)->where('blockee','=', $blockee)->delete();
        echo "unblocked_ok";
         exit();
      }
    }
  }
?>
