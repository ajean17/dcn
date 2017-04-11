<?php
use App\User;
use App\Friend;

if (isset($_GET['type']) && isset($_GET['user']))
	{
		$user = preg_replace('#[^a-z0-9]#i', '', $_GET['user']);//The profile owner being added or blocked
		$log_username = Auth::user()->name;//The one logged in
		//Check to see if the user to befriend or block exists
		$exist_count = User::where('name','=',$user)->get();/*->where('activated','=','1')*->get();*/
		if($exist_count == "[]")
		//If nothing matches in the DB stop everything and tell the user
		{
			echo "$user does not exist.";
			exit();
		}
		if($_GET['type'] == "friend")
		//If friend request
		{
			//Check to see if the logged in user sent a request to the profile owner already that has been accepted
			$row_count1 = Friend::where('user1','=',$log_username)
			->where('user2','=',$user)
			->where('accepted','=','1')->get();

			//Check to see if the profile owner has sent a request to the logged in user already that has been accepted
			$row_count2 = Friend::where('user1','=',$user)
			->where('user2','=',$log_username)
			->where('accepted','=','1')->get();

			//Check to see if the logged in user sent a request to the profile owner already that has not been accepted
			$row_count3 = Friend::where('user1','=',$log_username)
			->where('user2','=',$user)
			->where('accepted','=','0')->get();

			//Check to see if the profile owner has sent a request to the logged in user already that has not been accepted
			$row_count4 = Friend::where('user1','=',$user)
			->where('user2','=',$log_username)
			->where('accepted','=','0')->get();

			if ($row_count1 != "[]" || $row_count2 != "[]")
			//If the profile owner and logged in user are already friends
			{
        echo "You are already friends with $user.";
        exit();
    	}
			else if ($row_count3 != "[]")
			//If the logged in user has already sent request to the profile owner
			{
	      echo "You have a pending friend request already sent to $user.";
	      exit();
	    }
			else if ($row_count4 != "[]")
			//If the profile owner has already sent a request to the logged in user
			{
	      echo "$user has requested to friend with you first. Check your friend requests.";
	      exit();
	    }
			else
			//Create a new friendship request between the logged in user and the profile owner
			{
				$newFriendship = Friend::create([
					'user1' => $log_username,
					'user2' => $user
				]);
	      echo "friend_request_sent";
	      exit();
			}
		}
		else if($_GET['type'] == "unfriend")
		{
			//Check to see if the logged in user and profile owner are currently friends
			$row_count = Friend::where('user1','=',$user)
			->where('user2','=',$log_username)
			->where('accepted','=','1')
			->orWhere('user1','=',$log_username)
			->where('user2','=',$user)
			->where('accepted','=','1')->get();

			if ($row_count != '[]')
			//If the two are friends, delete their friendship record
			{
				//DB::table('friends')
				Friend::where('user1','=',$user)
				->where('user2','=',$log_username)
				->where('accepted','=','1')
				->orWhere('user1','=',$log_username)
				->where('user2','=',$user)
				->where('accepted','=','1')->delete();

		    echo "unfriend_ok";
		    exit();
		  }
			else
			//Otherwide notify the user that they are not even friends
			{
		    echo "No friendship could be found between your account and $user, therefore we cannot unfriend you.";
		    exit();
			}
		}
	}
?>

<?php
	/*PARSING FOR ACCEPTING OR REJECTING FRIENDSHIPS*/
	if(isset($_GET['action']) && isset($_GET['reqid']) && isset($_GET['user1']))
	{
		$reqid = preg_replace('#[^0-9]#', '', $_GET['reqid']);
		$user = preg_replace('#[^a-z0-9]#i', '', $_GET['user1']);
		$log_username = Auth::user()->name;//The one logged in
		$exist_count = User::where('name','=',$user)->get();/*->where('activated','=','1')*->get();*/

		if($exist_count == "[]")
		//If nothing matches in the DB stop everything and tell the user
		{
			echo "$user does not exist.";
			exit();
		}
		if($_GET['action'] == "accept")
		{
			$row_count = Friend::where('user1','=',$user)
			->where('user2','=',$log_username)
			->where('accepted','=','1')
			->orWhere('user1','=',$log_username)
			->where('user2','=',$user)
			->where('accepted','=','1')->get();

	    if ($row_count != "[]")
			{
        echo "You are already friends with $user.";
        exit();
	    }
			else
			{
				Friend::where('id','=',$reqid)
				->where('user1','=',$user)
				->where('user2','=',$log_username)
				->update(array('accepted' => '1'));
        echo "<b>Request Accepted!</b><br />Your are now friends...";
        exit();
			}
		}
		else if($_GET['action'] == "reject")
		{
			Friend::where('user1','=',$user)
			->where('user2','=',$log_username)
			->where('accepted','=','0')->delete();
			echo "<b>Request Rejected</b><br />You chose to reject friendship with this user...";
			exit();
		}
	}
?>
