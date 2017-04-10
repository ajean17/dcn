<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Project;
use App\Profile;
use App\Category;
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

    $message = "Your profile has been updated";*/
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
