<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\Reset;
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
use DB;

class ParseController extends Controller
{
  public function message(Request $request)
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
      $message = $c->created_at->format('h:i A F jS');
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
          $date= Carbon::parse($mezzage['created_at']);
          $message = $message.$mezzage->user1;
          $message = $message."\\";
          $message = $message.$mezzage->user2;
          $message = $message."\\";
          $message = $message.$mezzage->message;
          $message = $message."\\";
          $message = $message.$mezzage->created_at->format('h:i A F jS');
          $message = $message."\n";
        }
      }
    }
    return response()->json(['message' => $message]);
  }

  public function search(Request $request)
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
          $checkFriend = Friend::where('user1','=',$whoSearched)->where('user2','=',$criteria)->where('accepted','=','1')
          ->orWhere('user1','=',$criteria)->where('user2','=',$whoSearched)->where('accepted','=','1')->first();

          if($checkFriend != "")
            $message = "You are already friends with ".$criteria.", please check the list on the right.";
          else
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
      }
      else if($newTalkTo == "")
      {
          $message = "Sorry, That user does not exist yet.";
      }
    }

    if($request->has("search") && $request->has("video") && $request->has("mentor") && $request->has("investments") && $request->has("roi") && $request->has("category"))
    {
      $search = $request["search"];
      $video = $request["video"];
      $investments = $request["investments"];
      $mentor = $request["mentor"];
      $roi = $request["roi"];
      $category = $request["category"];
      //$message = "in search";
      $videoQ = "";
      $investmentsQ = "";
      $mentorQ = "";
      $roiQ = "";
      $categoryQ = "";
      $show = "";
      $isParent = false;

      if($search != "searching")//If there is a name in the search, look for the profile that matches
      {
        $user = User::where('name','=',$search)->first();
        if($user == "")
          $message = "Sorry, no users match your search.";
        else
        {
          $profile = Profile::where('username','=',$user->name)->first();
          $project = Project::where('id','=',$profile->projectOneID)->first();
          if($project != "")
            $message = $user->name."\\".$project->name."\n";
          else
            $message = $user->name."\\Under Construction\n";
        }
      }
      else if($video == "false" && $investments == "false" && $mentor == "false" && $roi == "false" && $category == "category")
      {
        $results = Profile::all();
        foreach($results as $result)
        {
          $project = Project::where('id','=',$result->projectOneID)->first();
          if($project != "" || $project != NULL)
            $show = $show.$result->username."\\".$project->name."\n";
          /*else
            $show = $show.$result->username."\\Under Construction\n";*/
        }
        $message = $show;
      }
      else//If the search bar is empty, search based on the boxes and category
      {
        if($video == "true")
          $videoQ= " WHERE hasVideo = '1'";

        if($investments == "true")
        {
          if($video == "true")
            $investmentsQ = " AND hasInvestments = '1'";
          else
            $investmentsQ = " WHERE hasInvestments = '1'";
        }

        if($mentor == "true")
        {
          if($video == "true" || $investments == "true")
            $mentorQ = " AND hasMentor = '1'";
          else
            $mentorQ = " WHERE hasMentor = '1'";
        }

        if($roi == "true")
        {
          if($video == "true" || $investments == "true" || $mentor == "true")
            $roiQ = " AND hasROI = '1'";
          else
            $roiQ = " WHERE hasROI = '1'";
        }

        if($category != "category")
        {
          $cat = Category::where('name','=',$category)->first();
          if($cat != "")
          {
            if($cat->parent == "" || $cat->parent == NULL)
              $isParent = true;
          }
          else
            $category = "category";
        }

        $results = DB::select(DB::raw('SELECT * FROM profiles'.$videoQ.$investmentsQ.$mentorQ.$roiQ.''));

        foreach($results as $result)
        {
          $project = Project::where('id','=',$result->projectOneID)->first();
          if($project != "" || $project != NULL)
          {
            if($category == "category")
              $show = $show.$result->username."\\".$project->name."\n";
            else if($category != "category")
            {
              //$message = "Parent? ".$isParent." Category: ".$category." Project Category: ".$project->category;
              if($isParent == true  && $category == $project->category)
                $show = $show.$result->username."\\".$project->name."\n";
              else if($isParent == false && $category == $project->subCategory)
                $show = $show.$result->username."\\".$project->name."\n";
            }
          }
          /*else
            $show = $show.$result->username."\\Under Construction\n";*/
        }
        $message = $show;
      }
      //echo "Here are the values".$videoQ.$investmentsQ.$mentorQ.$roiQ.$category;
    }

    return response()->json(['message' => $message]);
  }

  public function password(Request $request)
  {
    $message = "";
    if($request->has('email'))
    {
      $e = $request['email'];
      $user = User::where('email','=',$e)->first();
      if($user == "")
        $message = "Invalid email address.";
      else
      {
        //CONSIDER MAKING A TEMPORARY PASSWORD BRO!
        \Mail::to($user)->send(new Reset($user));
        $message = "email_success";
      }
    }
    if($request->has('u') && $request->has('e') && $request->has('p'))
    {
      $u = $request['u'];//preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
      $e = $request['e'];
      $p = $request['p'];

      $user = User::where('name','=', $u)->where('email','=', $e)->first();
      if($user == "")
        $message = "This account is invalid.  We apologize for the inconvenience and will resolve the issue promptly.";

      else
      {
        $pass = bcrypt($p);
        User::where('name','=', $u)->where('email','=', $e)->update(Array('password' => $pass));
        $check = User::where('name','=', $u)->where('password','=',$pass)->first();

        if($check == "")
          $message = "Unable to reset your password. We have been notified and are resolving the issue.  We apoligize for the inconvenience";
        else
          $message = "reset_success";
      }
    }
    return response()->json(['message' => $message]);
  }

  public function categories()
  {
    return view('phpParsers.categories');
  }

  public function cats(Request $request)
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
