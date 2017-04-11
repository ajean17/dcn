<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Dialogue;
use App\Project;
use App\Profile;
use App\Category;
use App\Friend;
use App\Block;
use App\Message;
use \Storage;
use Carbon\Carbon;

class ParseController extends Controller
{
  public function friend(\Illuminate\Http\Request $request)
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

  			if ($row_count1 != "[]" || $row_count2 != "[]")//If the profile owner and logged in user are already friends
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
          $message = "<b>Request Accepted!</b><br />Your are now friends...";
  			}
  		}
  		else if($request['action'] == "reject")
  		{
  			Friend::where('user1','=',$user)
  			->where('user2','=',$log_username)
  			->where('accepted','=','0')->delete();
  			 $message = "<b>Request Rejected</b><br />You chose to reject friendship with this user...";
  		}
  	}

    return response()->json(['message' => $message]);
  }

  public function block(\Illuminate\Http\Request $request)
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

  public function message(\Illuminate\Http\Request $request)
  {
    $message = "Something is wrong";

    if($request->has('username') && $request->has('talkTo') && $request->has('message'))//SEND MESSAGE
    {
      //Save message
      $username = stripcslashes(htmlspecialchars($request['username']));
      $talkTo = stripcslashes(htmlspecialchars($request['talkTo']));
      $message = stripslashes(htmlspecialchars($request['message']));

      if ($message == "" || $username == "" || $talkTo == "")
        $message = "";

      $c = Message::create([
        'user1' => $username,
        'user2'=> $talkTo,
        'message' => $message
      ]);

      $d = Dialogue::where('user1','=',$username)
      ->where('user2','=',$talkTo)
      ->orWhere('user2','=',$username)
      ->where('user1','=',$talkTo)->get();

      if($d == "[]")//If no dialogue exists, make one
      {
        Dialogue::create([
          'user1' => $username,
          'user2'=> $talkTo,
          'lastMessage' => $c->created_at
        ]);
      }
      else//Otherwise update the last message
      {
        Dialogue::where('user1','=',$username)
        ->where('user2','=',$talkTo)
        ->orWhere('user2','=',$username)
        ->where('user1','=',$talkTo)->update(Array('lastMessage' => $c->created_at));
      }
    }

    if($request->has('username') && $request->has('talkTo') && $request->has('action'))//UPDATE MESSAGES
    {
      //Load Message
      if($request['action']=="update")
      {
        $username = stripslashes(htmlspecialchars($request['username']));
        $talkTo = stripcslashes(htmlspecialchars($request['talkTo']));
        $messages = Message::where('user1','=',$username)->where('user2','=',$talkTo)
        ->where('message','!=','')
        ->orWhere('user1','=',$talkTo)->where('user2','=',$username)
        ->where('message','!=','')->orderBy('created_at','asc')->get();
        $message = "";
        foreach ($messages as $mezzage)
        {
          $message = $message.$mezzage->user1;
          $message = $message."\\";
          $message = $message.$mezzage->user2;
          $message = $message."\\";
          $message = $message.$mezzage->message;
          $message = $message."\n";
        }
      }
    }

    return response()->json(['message' => $message]);
  }

  public function search(\Illuminate\Http\Request $request)
  {
    $message = "Something is wrong.";

    if($request->has('whoSearched') && $request->has('inboxSearch'))
    {
      $criteria = stripcslashes(htmlspecialchars($request['inboxSearch']));
      $whoSearched = stripcslashes(htmlspecialchars($request['whoSearched']));
      $newTalkTo = User::where('name','=',$criteria)->first();

      if($newTalkTo != "")
      //If the person being searched for does exist
      {
          $d = Dialogue::where('user1','=',$whoSearched)->where('user2','=',$criteria)
          ->orWhere('user1','=',$criteria)->where('user2','=',$whoSearched)->first();

          if($d != "")
            $message = "You already have a dialogue with ".$criteria;

          else
          {
            Dialogue::create([
              'user1' => $whoSearched,
              'user2'=> $criteria,
              'lastMessage' => Carbon::now()
            ]);

            $message = "new_dialogue";
          }
      }
      else if($newTalkTo == "")
      {
          $message = "Sorry, That user does not exist yet.";
      }
    }

    return response()->json(['message' => $message]);
  }

  public function password()
  {
    return view('phpParsers.passwordSystem');
  }

  public function categories()
  {
    return view('phpParsers.categories');
  }

  public function cats(\Illuminate\Http\Request $request)
  {
    $message = "Working...";

    if($request->has('type') && $request->has('name'))
    {
      //Assing the values from the ajax request
      $name = $request['name'];
      $parent = $request['parent'];
      $type = $request['type'];
      //Check to see if there is already a category with the name sent
      $exists = Category::where('name','=',$name)->first();
      $message = "Not working well though...";

      if($type == "add")//If we are adding a category
      {
        if($exists != "")//If one already exists
          $message = "That category already exists in the system.";
        else
        {
          if($parent == "")//If there is not a category with that name yet and the parent field is empty
          { //Make the category
            Category::create([
              'name' => $name
            ]);
          }
          else if($parent != "")//If the parent field is not empty
          { //Check to see if a category with the parent name exists
            $exists = Category::where('name','=',$parent)->first();
            if($exists != "")//If the parent is there, create the subcategory
            {
              Category::create([
                'name' => $name,
                'parent' => $parent
              ]);
            }
            else
              $message = "You entered an invalid category name as the parent. Please select another";
          }
          $message = "cat_added"; //Add complete
        }
      }
      else if($type == "delete")
      {
        if($exists == "")//If the category does not exist it cannot be deleted
        {
          $message = "That category does not exist, therefore cannot be deleted.";
        }
        else
        {
          Category::where('name','=',$name)->delete();
          Category::where('parent','=',$name)->delete();
          $message = "cat_deleted";
        }
      }
    }
    return response()->json(['message' => $message]);
  }

  public function project(Request $request)
  {
    $name = "Need to name me at some point...";
    $category = $request['category'];
    echo $category;
    $subCategory = $request->input('subCategory');
    echo $subCategory;
    $one = "one";     $oneType = "blank1";
    $two = "two";     $twoType = "blank2";
    $three = "three"; $threeType = "blank3";
    $four = "four";   $fourType = "blank4";
    $five = "five";   $fiveType = "blank5";

    if($request->has('title'))
    {
      $name = $request['title'];
    }
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
      $newProject = Project::create([
        'profileId' => $profile->id
      ]);

       Profile::where('username','=',$user)->update(Array('projectOneID' => $newProject->id));
    }

    $project = Project::where('id','=',$profile->projectOneID)->first();

    if($name != "" || $name != NULL)
      Project::where('id','=',$profile->projectOneID)->update(Array('name' => $name));
    if($category != "" || $category != NULL)
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

    $message = "Your profile has been updated";
    return redirect()->to('/management'.'/'.$user);
    //return redirect()->to('/profile'.'/'.$user);
  }

  function saveUpload($upload,$user)
  {
    $fileName = $upload->getClientOriginalName();
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
    $this->validate($upload,$rules);
    //Grab the destination path variable from the config.app file key
    $destinationPath = config('app.fileDestinationPath').'/'.$user.'/images'.'/'.$fileName;
    //Move the uploaded file from the temporary location to the folder of choice
    $moveResult = Storage::put($destinationPath, file_get_contents($upload->getRealPath()));
  }

  public function photoHandle(User $User, Request $request)
  {
    //Pull the request object named avatar, and assign its original filename to a variable
    $file = $request->file('avatar');
    $fileName = $file->getClientOriginalName();
    $userName = $User->name;
    //Grab the allowed file types and max file size from the config.app file keys
    $allowedFileTypes = config('app.allowedFileTypes');
    $maxFileSize = config('app.maxFileSize');
    //Assign the validation rules and run the command
    $rules = [
      'avatar' => 'required|mimes:'.$allowedFileTypes.'|max:'.$maxFileSize
    ];
    $this->validate($request,$rules);
    //Grab the destination path variable from the config.app file key
    $destinationPath = config('app.fileDestinationPath').'/'.$userName.'/images'.'/'.$fileName;
    //Move the uploaded file from the temporary location to the folder of choice
    $moveResult = Storage::put($destinationPath, file_get_contents($file->getRealPath()));

    if($User->avatar != NULL)
    //If the user already has an avatar, delete it and replace with the uploaded file
    {
      //delete current photo in its place
      Storage::delete(config('app.fileDestinationPath').'/'.$userName.'/images'.'/'.$User->avatar);
    }
    User::where('name','=',$userName)->update(Array('avatar' => $fileName));
    //Back to the profile page
    return redirect()->to('/profile'.'/'.$userName);
  }
}
