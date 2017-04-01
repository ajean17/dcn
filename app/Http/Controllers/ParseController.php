<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Project;
use App\Profile;
use \Storage;

class ParseController extends Controller
{
  //***Find a way to place all parser content within this controller***//
  public function friend()
  {
    //dd(request('type'));
    return view('/phpParsers.friendSystem');
  }

  public function block()
  {
    //dd(request('type'));
    return view('/phpParsers.blockSystem');
  }

  public function message()
  {
    //dd(request('msg'));
    return view('phpParsers.messageSystem');
  }

  public function search()
  {
    return view('phpParsers.searchSystem');
  }

  public function password()
  {
    return view('phpParsers.passwordSystem');
  }

  public function categories()
  {
    return view('phpParsers.categories');
  }

  public function project(Request $request)
  {
    $name = $request->input('title');
    $one = "one";     $oneType = "blank1";
    $two = "two";     $twoType = "blank2";
    $three = "three"; $threeType = "blank3";
    $four = "four";   $fourType = "blank4";
    $five = "five";   $fiveType = "blank5";

    if($request->has('OneType'))
    {
      $oneType = $request->input('OneType');

      switch($oneType)
      {
        case "text":
          $one = $request->input('OneT');
        break;
        case "embed":
          $one = $request->input('OneE');
        break;
        case "upload":
          $one = $request->file('OneU');
        break;
      }
    }
    if($request->has('TwoType'))
    {
      $twoType = $request->input('TwoType');
      switch($twoType)
      {
        case "text":
          $two = $request->input('TwoT');
        break;
        case "embed":
          $two = $request->input('TwoE');
        break;
        case "upload":
          $two = $request->file('TwoU');
        break;
      }
    }
    if($request->has('ThreeType'))
    {
      $threeType = $request->input('ThreeType');
      switch($threeType)
      {
        case "text":
          $three = $request->input('ThreeT');
        break;
        case "embed":
          $three =$request->input('ThreeE');
        break;
        case "upload":
          $three = $request->file('ThreeU');
        break;
      }
    }
    if($request->has('FourType'))
    {
      $fourType = $request->input('FourType');
      switch($fourType)
      {
        case "text":
          $four = $request->input('FourT');
        break;
        case "embed":
          $four = $request->input('FourE');
        break;
        case "upload":
          $four = $request->file('FourU');
        break;
      }
    }
    if($request->has('FiveType'))
    {
      $fiveType = $request->input('FiveType');
      switch($fiveType)
      {
        case "text":
          $five = $request->input('FiveT');
        break;
        case "embed":
          $five = $request->input('FiveE');
        break;
        case "upload":
          $five = $request->file('FiveU');
        break;
      }
    }

    $types = Array($oneType => $one,
                   $twoType => $two,
                   $threeType => $three,
                   $fourType => $four,
                   $fiveType => $five);
    foreach ($types as $type => $element)
    {
       if($element == "" || $element == NULL)
       return back()->withErrors(['message' => 'You cannot submit empty elements.']);
    }

    $user = $_POST['userName'];
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
        'profileId' => $profile->id,
        'name' => $name,
        'elementOne' => $one,
        'oneType' => $oneType,
        'elementTwo' => $two,
        'twoType' => $twoType,
        'elementThree' => $three,
        'threeType' => $threeType,
        'elementFour' => $four,
        'fourType' => $fourType,
        'elementFive' => $five,
        'fiveType' => $fiveType
      ]);

      $profile->update(Array('projectOneID' => $newProject->id));
    }
    else if($profile->projectOneID != NULL)
    {
      $project = Project::where('id','=',$profile->projectOneID)->first();

      if($name != "" || $name != NULL)
        $project->update(Array('name' => $name));

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
