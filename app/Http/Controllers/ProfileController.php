<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Profile;
use App\Project;
use App\User;
use App\Friend;
use App\Block;
use \Storage;
use Carbon\Carbon;
use DB;

class ProfileController extends Controller
{
  public function __construct()//no access without being logged in
  {
    $this->middleware('auth');//->except(['index','show']);
  }

  public function setup(Request $request)
  {
    $user = $request['username'];
    $option = $request['option'];//0 = No selection 1 = Invent  2 = Invest

    if($option == 1)
    {
      User::where('name','=',$request['username'])->update(Array('userType' => $option));
    }
    else if($option == 2)
    {
      User::where('name','=',$request['username'])->update(Array('userType' => $option));
    }
    else
    {
      return back()->withErrors(['message' => 'Please select a profile path.']);
    }
    return redirect('/profile'.'/'.$request['username']);
  }

  public function show(User $profileOwner)
  {
   return view('profile.show',compact('profileOwner'));//'friend_check','block_check','block_check2'));
  }

  public function friend(Request $request)
  {
    $message = "Something went wrong";
    if($request->has('type') && $request->has('user'))
  	{
      //The profile owner being added
  		$user = $request['user'];//preg_replace('#[^a-z0-9]#i', '', $_GET['user']);
  		$log_username = $request['log'];//The one logged in
  		//Check to see if the user to befriend or block exists
  		$exists= User::where('name','=',$user)->first();/*->where('activated','=','1')*->get();*/

      if($exists == "")//If nothing matches in the DB stop everything and tell the user
  			$message = "$user does not exist.";

  		if($request['type'] == "friend")
  		//If friend request
  		{
  			//Check to see if the logged in user sent a request to the profile owner already that has been accepted
  			$row_count1 = Friend::where('user1','=',$log_username)
  			->where('user2','=',$user)
  			->where('accepted','=','1')
        ->orWhere('user1','=',$user)
  			->where('user2','=',$log_username)
  			->where('accepted','=','1')->get();

  			/*Check to see if the profile owner has sent a request to the logged in user already that has been accepted
  			$row_count2 = Friend::where('user1','=',$user)
  			->where('user2','=',$log_username)
  			->where('accepted','=','1')->get();*/

  			//Check to see if the logged in user sent a request to the profile owner already that has not been accepted
  			$row_count3 = Friend::where('user1','=',$log_username)
  			->where('user2','=',$user)
  			->where('accepted','=','0')->get();

  			//Check to see if the profile owner has sent a request to the logged in user already that has not been accepted
  			$row_count4 = Friend::where('user1','=',$user)
  			->where('user2','=',$log_username)
  			->where('accepted','=','0')->get();

  			if ($row_count1 != "[]")//If the profile owner and logged in user are already friends
          $message =  "You are already friends with $user.";

  			else if ($row_count3 != "[]")//If the logged in user has already sent request to the profile owner
  	      $message = "You have a pending friend request already sent to $user.";

  			else if ($row_count4 != "[]")//If the profile owner has already sent a request to the logged in user
  	      $message =  "$user has requested to friend with you first. Check your friend requests.";

  			else//Create a new friendship request between the logged in user and the profile owner
  			{
  				$newFriendship = Friend::create([
  					'user1' => $log_username,
  					'user2' => $user
  				]);
  	      $message = "friend_request_sent";
  			}
  		}
  		else if($request['type'] == "unfriend")
  		{
  			//Check to see if the logged in user and profile owner are currently friends
  			$row_count = Friend::where('user1','=',$user)
  			->where('user2','=',$log_username)
  			->where('accepted','=','1')
  			->orWhere('user1','=',$log_username)
  			->where('user2','=',$user)
  			->where('accepted','=','1')->get();

  			if ($row_count != '[]')//If the two are friends, delete their friendship record
  			{
  				//DB::table('friends')
  				Friend::where('user1','=',$user)
  				->where('user2','=',$log_username)
  				->where('accepted','=','1')
  				->orWhere('user1','=',$log_username)
  				->where('user2','=',$user)
  				->where('accepted','=','1')->delete();

  		    $message = "unfriend_ok";
  		  }
  			else//Otherwide notify the user that they are not even friends
          $message = "No friendship could be found between your account and $user, therefore we cannot unfriend you.";
  		}
  	}

    /*PARSING FOR ACCEPTING OR REJECTING FRIENDSHIPS*/
  	if($request->has('action') && $request->has('reqid') && $request->has('user1'))
  	{
  		$reqid = $request['reqid'];//preg_replace('#[^0-9]#', '', $_GET['reqid']);
  		$user = $request['user1'];//preg_replace('#[^a-z0-9]#i', '', $_GET['user1']);
  		$log_username = $request['log'];//The one logged in
  		$exists = User::where('name','=',$user)->first();//->where('activated','=','1')*->get();

  		if($exists == "")//If nothing matches in the DB stop everything and tell the user
  			$message = $user." does not exist.";

  		if($request['action'] == "accept")
  		{
  			$row_count = Friend::where('user1','=',$user)
  			->where('user2','=',$log_username)
  			->where('accepted','=','1')
  			->orWhere('user1','=',$log_username)
  			->where('user2','=',$user)
  			->where('accepted','=','1')->get();

  	    if ($row_count != "[]")
          $message = "You are already friends with $user.";
  			else
  			{
  				Friend::where('id','=',$reqid)
  				->where('user1','=',$user)
  				->where('user2','=',$log_username)
  				->update(array('accepted' => '1'));
          $message = "<b>Request Accepted!</b><br/>Your are now friends...";
  			}
  		}
  		else if($request['action'] == "reject")
  		{
  			Friend::where('user1','=',$user)
  			->where('user2','=',$log_username)
  			->where('accepted','=','0')->delete();
  			 $message = "<b>Request Rejected</b><br/>You chose to reject friendship with this user...";
  		}
  	}

    return response()->json(['message' => $message]);
  }

  public function block(Request $request)
  {
    $message = "Something went wrong";

    if($request->has('type') && $request->has('user'))
    {
      //The profile owner being blocked
      $blockee = $request['user'];//preg_replace('#[^a-z0-9]#i', '', $_GET['user']);
      $log_username = $request['log'];//The one logged in
      //Check to see if the user to befriend or block exists
      $exists= User::where('name','=',$blockee)->first();/*->where('activated','=','1')*->get();*/

      if($exists == "")
        $message = $blockee." does not exist";

      if($request['type'] == "block")
      {
        $block_check = Block::where('blocker','=',$log_username) //check if block exists between users
        ->where('blockee','=', $blockee)
        ->orWhere('blocker','=', $blockee)
        ->where('blockee','=',$log_username)->get();

        if($block_check != "[]") //if user is already blocked
          $message = "You have already blocked this user.";

        else
        {
          $block = Block::create([
          'blocker' => $log_username,
          'blockee' => $blockee
        ]);
          $message = "blocked_ok";
        }
      }
      else if ($request['type'] == "unblock")//If the request is to unblock
      {
        //Checks to see if they owner has been blocked yet
        $block_check = Block::where('blocker','=', $log_username)->where('blockee','=', $blockee)->get();
        if($block_check == "[]")//If they have not been, they can't unblock
          $message = "User is not blocked, unable to unblock them.";

        else
        {
          //same query as block_check2, individual variable
          $block_check3 = Block::where('blocker','=', $log_username)->where('blockee','=', $blockee)->delete();
          $message = "unblocked_ok";
        }
      }
    }

    return response()->json(['message' => $message]);
  }

  public function project(Request $request)
  {
    $name = "";
    $category = $request['category'];
    //echo $category;
    $subCategory = $request->input('subCategory');
    //echo $subCategory;
    $one = "one";     $oneType = "blank1";    $oneN = "1name";
    $two = "two";     $twoType = "blank2";    $twoN = "2name";
    $three = "three"; $threeType = "blank3";  $threeN = "3name";
    $four = "four";   $fourType = "blank4";   $fourN = "4name";
    $five = "five";   $fiveType = "blank5";   $fiveN = "5name";

    if($request->has('title'))
      $name = $request['title'];
    if($request->has('OneName'))
      $oneN = $request['OneName'];
    if($request->has('TwoName'))
      $twoN = $request['TwoName'];
    if($request->has('ThreeName'))
      $threeN = $request['ThreeName'];
    if($request->has('FourName'))
      $fourN = $request['FourName'];
    if($request->has('FiveName'))
      $fiveN = $request['FiveName'];
    if($request->has('OneType'))
    {
      $oneType = $request['OneType'];

      switch($oneType)
      {
        case "text":
          $one = $request['OneT'];
        break;
        case "embed":
          $one = $request['OneE'];
        break;
        case "upload":
          $one = $request->file('OneU');
        break;
      }
    }
    if($request->has('TwoType'))
    {
      $twoType = $request['TwoType'];
      switch($twoType)
      {
        case "text":
          $two = $request['TwoT'];
        break;
        case "embed":
          $two = $request['TwoE'];
        break;
        case "upload":
          $two = $request->file('TwoU');
        break;
      }
    }
    if($request->has('ThreeType'))
    {
      $threeType = $request['ThreeType'];
      switch($threeType)
      {
        case "text":
          $three = $request['ThreeT'];
        break;
        case "embed":
          $three =$request['ThreeE'];
        break;
        case "upload":
          $three = $request->file('ThreeU');
        break;
      }
    }
    if($request->has('FourType'))
    {
      $fourType = $request['FourType'];
      switch($fourType)
      {
        case "text":
          $four = $request['FourT'];
        break;
        case "embed":
          $four = $request['FourE'];
        break;
        case "upload":
          $four = $request->file('FourU');
        break;
      }
    }
    if($request->has('FiveType'))
    {
      $fiveType = $request['FiveType'];
      switch($fiveType)
      {
        case "text":
          $five = $request['FiveT'];
        break;
        case "embed":
          $five = $request['FiveE'];
        break;
        case "upload":
          $five = $request->file('FiveU');
        break;
      }
    }

    $types = Array($oneType => $one, $twoType => $two, $threeType => $three, $fourType => $four, $fiveType => $five);
    foreach ($types as $type => $element)
    {
       if($element == "" || $element == NULL)
       return back()->withErrors(['message' => 'You cannot submit empty elements.']);
    }

    $user = $request['userName'];
    $profile = Profile::where('username','=',$user)->first();

    if($profile == "")
    //If no profile exists for the current user, make one
    {
      $profile = Profile::create([
        'username' => $user
      ]);
    }
    if($profile->projectOneID == NULL)
    //If the profile has no project associated with it, make one using the data the user sent
    {
      $project = Project::create([
        'profileId' => $profile->id
      ]);

       Profile::where('id','=',$profile->id)->update(Array('projectOneID' => $project->id));
    }

    $profile = Profile::where('username','=',$user)->first();

    if($name != "" && $name != NULL)
      Project::where('id','=',$profile->projectOneID)->update(Array('name' => $name));
    if($oneN != "" && $oneN != NULL && $oneN != "1name")
      Project::where('id','=',$profile->projectOneID)->update(Array('oneName' => $oneN));
    if($twoN != "" && $twoN != NULL && $twoN != "2name")
      Project::where('id','=',$profile->projectOneID)->update(Array('twoName' => $twoN));
    if($threeN != "" && $threeN != NULL && $threeN != "3name")
      Project::where('id','=',$profile->projectOneID)->update(Array('threeName' => $threeN));
    if($fourN != "" && $fourN != NULL && $fourN != "4name")
      Project::where('id','=',$profile->projectOneID)->update(Array('fourName' => $fourN));
    if($fiveN != "" && $fiveN != NULL && $fiveN != "5name")
      Project::where('id','=',$profile->projectOneID)->update(Array('fiveName' => $fiveN));
    if($category != "" && $category != NULL)
    {
      Project::where('id','=',$profile->projectOneID)->update(Array('category' => $category));
      Project::where('id','=',$profile->projectOneID)->update(Array('subCategory' => $subCategory));
    }
    if($one != "one")
    {
      if($oneType == "upload")
        Project::where('id','=',$profile->projectOneID)->update(Array('elementOne' => $one->getClientOriginalName(),'oneType' => $oneType));
      else if($oneType == "text" || $oneType == "embed")
        Project::where('id','=',$profile->projectOneID)->update(Array('elementOne' => $one, 'oneType' => $oneType));
    }
    if($two != "two")
    {
      if($twoType == "upload")
        Project::where('id','=',$profile->projectOneID)->update(Array('elementTwo' => $two->getClientOriginalName(),'twoType' => $twoType));
      else if($twoType == "text" || $twoType == "embed")
        Project::where('id','=',$profile->projectOneID)->update(Array('elementTwo' => $two, 'twoType' => $twoType));
    }

    if($three != "three")
    {
      if($threeType == "upload")
        Project::where('id','=',$profile->projectOneID)->update(Array('elementThree' => $three->getClientOriginalName(),'threeType' => $threeType));
      else if($threeType == "text" || $threeType == "embed")
        Project::where('id','=',$profile->projectOneID)->update(Array('elementThree' => $three, 'threeType' => $threeType));
    }
    if($four != "four")
    {
      if($fourType == "upload")
        Project::where('id','=',$profile->projectOneID)->update(Array('elementFour' => $four->getClientOriginalName(),'fourType' => $fourType));
      else if($fourType == "text" || $fourType == "embed")
        Project::where('id','=',$profile->projectOneID)->update(Array('elementFour' => $four, 'fourType' => $fourType));
    }

    if($five != "five")
    {
      if($fiveType == "upload")
        Project::where('id','=',$profile->projectOneID)->update(Array('elementFive' => $five->getClientOriginalName(), 'fiveType' => $fiveType));
      else if($fiveType == "text" || $fiveType == "embed")
        Project::where('id','=',$profile->projectOneID)->update(Array('elementFive' => $five, 'fiveType' => $fiveType));
    }

    foreach ($types as $type => $element)
    {
      if($type == "upload")
      {
        //$this->saveUpload($element,$user);
        $fileName = $element->getClientOriginalName();
        //Grab the allowed file types and max file size from the config.app file keys
        $allowedUploadTypes = config('app.allowedUploadTypes');
        $maxUploadSize = config('app.maxUploadSize');
        //Assign the validation rules and run the command
        $rules = [
          'OneU' => 'nullable|mimes:'.$allowedUploadTypes.'|max:'.$maxUploadSize,
          'TwoU' => 'nullable|mimes:'.$allowedUploadTypes.'|max:'.$maxUploadSize,
          'ThreeU' => 'nullable|mimes:'.$allowedUploadTypes.'|max:'.$maxUploadSize,
          'FourU' => 'nullable|mimes:'.$allowedUploadTypes.'|max:'.$maxUploadSize,
          'FiveU' => 'nullable|mimes:'.$allowedUploadTypes.'|max:'.$maxUploadSize
        ];
        $this->validate($request,$rules);
        //Grab the destination path variable from the config.app file key
        $destinationPath = config('app.fileDestinationPath').'/'.$user.'/images'.'/'.$fileName;
        //Move the uploaded file from the temporary location to the folder of choice
        $moveResult = Storage::put($destinationPath, file_get_contents($element->getRealPath()));
      }
    }

    if($oneType != "embed" && $twoType != "embed" && $threeType != "embed" && $fourType != "embed" && $fiveType != "embed")
      Profile::where('username','=',$user)->update(Array('hasVideo' => '0'));
    else
      Profile::where('username','=',$user)->update(Array('hasVideo' => '1'));
    $message = "Your profile has been updated";
    return redirect()->to('/profile'.'/'.$user);;
  }
}
