<?php
use App\User;
use App\Block;
  if(isset($_GET['type']) && isset($_GET['blockee']))
  {
    $blockee = preg_replace('#[^a-z0-9]#i', '', $_GET['blockee']);

    $block_check = Block::where('blocker','=',Auth::user()->name) //check if block exists between users
    ->where('blockee','=',$profileOwner->name)
    ->orWhere('blocker','=',$profileOwner->name)
    ->where('blockee','=',Auth::user()->name)->get();

    if($block_check == "[]") //if: block does exist [blockcheck not empty]
    {
      echo "$blockee does not exist";
      exit();
    }
    //check if user1 already has user2 blocked
    $block_check2 = Block::where('blocker','=',Auth::user()->name)->where('blockee','=',$profileOwner->name)>get();
    if($_GET['type'] == "block")
    {
      if($block_check2 != "[]") //if user is already blocked
      {
        echo "You have already blocked this user.";
	        exit();
      }
      else
      {
        Block::insert(['blocker' => Auth::user()->name, 'blockee' => $profileOwner->name]);
        echo "blocked_ok";
	       exit();
      }
      else if ($_GET['type'] == "unblock")
      {
        if($block_check2 == "[]")
        {
          echo "User is not blocked, unable to unblock them.";
  	       exit();
        }
        else
        {
          //same query as block_check2, individual variable
          $block_check3 = Block::where('blocker','=',Auth::user()->name)->where('blockee','=',$profileOwner->name)>get();
          $block_check3->delete();
          echo "unblocked_ok";
  	       exit();
        }
      }
    }
  }
?>
