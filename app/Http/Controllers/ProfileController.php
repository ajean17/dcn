<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Profile;
use App\Summary;
use App\User;
use App\Friend;
use App\Block;
use App\Proof;
use App\Backer;
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
      User::where('name','=',$request['username'])->update(Array('role' => 'Creator'));
    else if($option == 2)
      User::where('name','=',$request['username'])->update(Array('role' => 'Investor'));
    else
      return back()->withErrors(['message' => 'Please select a profile path.']);
    return redirect('/profile'.'/'.$request['username']);
  }

  public function show(User $profileOwner)//This function is no longer needed/NEITHER is profile.show, it exists as a reference now
  {
    return view('profile.show',compact('profileOwner'));
  }

  public function profile(User $profileOwner)//THIS is the profile page that is currently in use, borrows heavily from profile.show
  {
    return view('profile.profile',compact('profileOwner'));
  }

  public function friend(Request $request)//Friend system parsing is done here, everything goes by user ID as opposed to username
  {
    $message = "Something went wrong";
    if($request->has('type') && $request->has('user'))
  	{
  		$user = $request['user'];//The profile owner being added
  		$log_userid = $request['log'];//The one logged in
  		//Check to see if the user to befriend or block exists
  		$exists= User::where('id','=',$user)->first();/*->where('activated','=','1')*->get();*/

      if($exists == "")//If nothing matches in the DB stop everything and tell the user
  			$message = "This user does not exist.";

  		if($request['type'] == "friend")//If friend request
  		{
  			//Check to see if the logged in user sent a request to the profile owner already that has been accepted
  			$row_count1 = Friend::where('user1','=',$log_userid)
  			->where('user2','=',$user)
  			->where('accepted','=','1')
        ->orWhere('user1','=',$user)
  			->where('user2','=',$log_userid)
  			->where('accepted','=','1')->get();
  			//Check to see if the logged in user sent a request to the profile owner already that has not been accepted
  			$row_count3 = Friend::where('user1','=',$log_userid)
  			->where('user2','=',$user)
  			->where('accepted','=','0')->get();
  			//Check to see if the profile owner has sent a request to the logged in user already that has not been accepted
  			$row_count4 = Friend::where('user1','=',$user)
  			->where('user2','=',$log_userid)
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
  					'user1' => $log_userid,
  					'user2' => $user
  				]);
  	      $message = "friend_request_sent";
  			}
  		}
  		else if($request['type'] == "unfriend")
  		{
  			//Check to see if the logged in user and profile owner are currently friends
  			$row_count = Friend::where('user1','=',$user)
  			->where('user2','=',$log_userid)
  			->where('accepted','=','1')
  			->orWhere('user1','=',$log_userid)
  			->where('user2','=',$user)
  			->where('accepted','=','1')->get();

  			if ($row_count != '[]')//If the two are friends, delete their friendship record
  			{
  				//DB::table('friends')
  				Friend::where('user1','=',$user)
  				->where('user2','=',$log_userid)
  				->where('accepted','=','1')
  				->orWhere('user1','=',$log_userid)
  				->where('user2','=',$user)
  				->where('accepted','=','1')->delete();

  		    $message = "unfriend_ok";
  		  }
  			else//Otherwide notify the user that they are not even friends
          $message = "You are not friends with this person yet.";
  		}
  	}

    /*PARSING FOR ACCEPTING OR REJECTING FRIENDSHIPS*/
  	if($request->has('action') && $request->has('reqid') && $request->has('user1'))
  	{
  		$reqid = $request['reqid'];
  		$user = $request['user1'];
  		$log_userid = $request['log'];//The one logged in
  		$exists = User::where('id','=',$user)->first();//->where('activated','=','1')*->get();

  		if($exists == "")//If nothing matches in the DB stop everything and tell the user
  			$message = $user." does not exist.";

  		if($request['action'] == "accept")
  		{
  			$row_count = Friend::where('user1','=',$user)
  			->where('user2','=',$log_userid)
  			->where('accepted','=','1')
  			->orWhere('user1','=',$log_userid)
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
  public function summary(Request $request)//Executive summary ssytem parsing is done here
  {
    $user = User::where('name','=',$request['user'])->first();
    $title = $request['title'];
    $market = $request['market'];
    $submarket = $request['submarket'];
    $age = $request['age'];
    $region = $request['region'];
    $markOther = $request['markother'];
    $compete1 = $request['compete1'];
    $compete2 = $request['compete2'];
    $compete3 = $request['compete3'];
    $risks = $request['risks'];
    $exit = $request['exit'];
    $roi = $request['roi'];
    $liquid = $request['liquid'];

    $summary = Summary::where('user_id','=',$user->id)->first();
    if($summary == "")
    {
      $summary = Summary::create([
        'user_id' => $user->id,
        'product_name' => 'incomplete',
        'market' => 'incomplete',
        'age_range' => 'incomplete',
        'exit_strategy' => 'incomplete',
        'ROI' => 'incomplete'
      ]);
    }
    if($request->has('title'))
      Summary::where('user_id','=',$user->id)->update(Array('product_name' => $title));
    if($request->has('market'))
      Summary::where('user_id','=',$user->id)->update(Array('market' => $market));
    //if($request->has('submarket'))
      //Summary::where('user_id','=',$user->id)->update(Array('market' => $submarket));
    if($request->has('age'))
      Summary::where('user_id','=',$user->id)->update(Array('age_range' => $age));
    if($request->has('region'))
      Summary::where('user_id','=',$user->id)->update(Array('region' => $region));
    if($request->has('markother'))
      Summary::where('user_id','=',$user->id)->update(Array('market_other' => $markOther));
    if($request->has('compete1'))
      Summary::where('user_id','=',$user->id)->update(Array('competitor1' => $compete1));
    if($request->has('compete2'))
      Summary::where('user_id','=',$user->id)->update(Array('competitor2' => $compete2));
    if($request->has('compete3'))
      Summary::where('user_id','=',$user->id)->update(Array('competitor3' => $compete3));
    if($request->has('risks'))
      Summary::where('user_id','=',$user->id)->update(Array('risks' => $risks));
    if($request->has('exit'))
      Summary::where('user_id','=',$user->id)->update(Array('exit_strategy' => $exit));
    if($request->has('roi'))
      Summary::where('user_id','=',$user->id)->update(Array('ROI' => $roi));
    if($request->has('liquid'))
      Summary::where('user_id','=',$user->id)->update(Array('liquid' => $liquid));

    return redirect()->to('/profile'.'/'.$user->name);
  }
  public function proof(Request $request)//Proof of Concept parsing
  {
    $user = User::where('name','=',$request['user'])->first();
    $proof = Proof::where('user_id','=',$user->id)->first();
    //dd($user->name);
    if($proof == "")
      $proof = Proof::create(['user_id' => $user->id]);

    if($request->has('newProof'))
      Proof::where('user_id','=',$user->id)->update(Array('embed' => $request['newProof']));
    if($request->has('newProofTitle'))
      Proof::where('user_id','=',$user->id)->update(Array('title' => $request['newProofTitle']));

    return redirect()->to('/profile'.'/'.$user->name);
  }
  public function backer(Request $request)//List of Backers parsing
  {
    $message = "Something went wrong, please try again later.";
    if($request->has('backing') && $request->has('backer'))
    {
      $backing = $request['backing'];
      $backer = $request['backer'];
      $backed = Backer::where('backing_id','=',$backing)->where('backer_name','=',$backer)->first();
      if($backed == "")
      {
        $backed = Backer::create([
          'backing_id' => $request['backing'],
          'backer_name' => $request['backer']
        ]);
        $message = "back_success";
      }
      else
        $message = "This person is already on your list.";
    }
    if($request->has('backer') && $request->has('delete'))
    {
      $backer = $request['backer'];
      $backed = Backer::where('id','=',$backer)->first();
      if($backed == "")
        $message = "This list operation could not be fulfilled. Please refresh the page.";
      else if($backed != "")
      {
        Backer::where('id','=',$backer)->delete();
        $message = "del_success";
      }
    }
    return response()->json(['message' => $message]);
  }
  public function project(Request $request)//OLD CODE for the project system but that is no longer in use/REFERENCE Material
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
    return redirect()->to('/profile'.'/'.$user);
  }
}
