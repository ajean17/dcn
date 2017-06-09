<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Dialogue;
use App\Friend;

class DashboardController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }
  public function account(User $User)
  {
    return view('dashboard.settings',compact('User'));
  }
  public function notifications(User $User)
  {
    //$notes = Notification::where('username','=',$User)->orderBy('created_at','desc')->get();
    return view('dashboard.notifications',compact('User'));
  }
  public function inbox(User $inboxOwner)
  {
    $mostRecent = "";
    $talkTo = "";
    $mostRecent1 = Dialogue::where('user1','=',$inboxOwner->name)->max('lastMessage');
    $mostRecent2 = Dialogue::where('user2','=',$inboxOwner->name)->max('lastMessage');

    if($mostRecent1 != "" || $mostRecent2 != "")
    {
      if($mostRecent1 >= $mostRecent2)
      {
        $mostRecent = $mostRecent1;
      }
      else if ($mostRecent1 <= $mostRecent2)
      {
        $mostRecent = $mostRecent2;
      }
      //echo $mostRecent;
      $currentDialogue = Dialogue::where('lastMessage','=',$mostRecent)->first();

      if($currentDialogue->user1 == $inboxOwner->name)
      {
        $talkTo = $currentDialogue->user2;
      }
      else if($currentDialogue->user2 == $inboxOwner->name)
      {
        $talkTo = $currentDialogue->user1;
      }
    }

    $conversations = Dialogue::where('user1','=',$inboxOwner->name)
    ->where('user2','!=',$inboxOwner->name)
    ->orWhere('user2','=',$inboxOwner->name)
    ->where('user1','!=',$inboxOwner->name)->get();

    $friends = Friend::where('user2','=',$inboxOwner->name)->where('accepted','=','1')
    ->orWhere('user1','=',$inboxOwner->name)->where('accepted','=','1')->get();

    return view('dashboard.inbox',compact('inboxOwner','conversations','friends','talkTo'));
  }

  public function search()
  {
    return view('dashboard.search');
  }
}
