<?php
  use App\Project;
  use App\Profile;
  use App\User;
  //use \Storage;

  $name = $request->input('title');
  $one = "one";
  $oneType = "blank1";
  $two = "two";
  $twoType = "blank2";
  $three = "three";
  $threeType = "blank3";
  $four = "four";
  $fourType = "blank4";
  $five = "five";
  $fiveType = "blank5";

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

  //$elements = Array($one => $oneType, $two => $twoType, $three => $threeType, $four =>$fourType, $five => $fiveType);
  $types = Array($oneType => $one,
                 $twoType => $two,
                 $threeType => $three,
                 $fourType => $four,
                 $fiveType => $five);
  echo "Name is: ".$name."<hr/>";

  foreach ($types as $type => $element)
  {
    if($type == "embed")
      echo "The element is: ".htmlentities($element)."<br/> The Type is: ".$type."<hr/>";
    else if($type == "upload")
      echo "The element is: ".$element->getClientOriginalName()."<br/> The Type is: ".$type."<hr/>";
    else
      echo "The element is: ".$element."<br/> The Type is: ".$type."<hr/>";
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

    if($one != "one" && $oneType != "blank1")
    {
      if($oneType == "upload")
        $project->update(Array('elementOne' => $one->getClientOriginalName(),'oneType' => $oneType));
      else
        $project->update(Array('elementOne' => $one, 'oneType' => $oneType));
    }
    if($two != "two")
    {
      if($twoType == "upload")
        $project->update(Array('elementTwo' => $two->getClientOriginalName(),'twoType' => $twoType));
      else
        $project->update(Array('elementTwo' => $two, 'twoType' => $twoType));
    }

    if($three != "three")
    {
      if($threeType == "upload")
        $project->update(Array('elementThree' => $three->getClientOriginalName(),'threeType' => $threeType));
      else
        $project->update(Array('elementThree' => $three, 'threeType' => $threeType));
    }
    if($four != "four")
    {
      if($fourType == "upload")
        $project->update(Array('elementFour' => $four->getClientOriginalName(),'fourType' => $fourType));
      else
        $project->update(Array('elementFour' => $four, 'fourType' => $fourType));
    }

    if($five != "five")
    {
      if($fiveType == "upload")
        $project->update(Array('elementFive' => $five->getClientOriginalName(), 'fiveType' => $fiveType));
      else
        $project->update(Array('elementFive' => $five, 'fiveType' => $fiveType));
    }
  }

  foreach ($types as $type => $element)
  {
    if($type == "upload")
    {
      saveUpload($element);
    }
  }

  $message = "Your profile has been updated";
  return redirect()->to('/management');

  function saveUpload($upload)
  {
    //echo "The file to save is ".$upload['name'];
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
    Request::validate($upload,$rules);
    //Grab the destination path variable from the config.app file key
    $destinationPath = config('app.fileDestinationPath').'/'.$user.'/images'.'/'.$fileName;
    //Move the uploaded file from the temporary location to the folder of choice
    $moveResult = Storage::put($destinationPath, file_get_contents($upload->getRealPath()));
  }

?>
