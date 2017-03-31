<?php
  use App\Project;
  use App\Profile;
  use App\User;

  $name = $_POST['title'];
  $one = "one";
  $oneType = "";
  $two = "two";
  $twoType = "";
  $three = "three";
  $threeType = "";
  $four = "four";
  $fourType = "";
  $five = "five";
  $fiveType = "";
  if(isset( $_POST['OneType']))
  {
    $oneType = $_POST['OneType'];
    switch($oneType)
    {
      case "text":
        $one = $_POST['OneT'];
      break;
      case "embed":
        $one = $_POST['OneE'];
      break;
      case "upload":
        $one = $_FILES['OneU']['name'];
      break;
    }
  }
  if(isset($_POST['TwoType']))
  {
    $twoType = $_POST['TwoType'];
    switch($twoType)
    {
      case "text":
        $two = $_POST['TwoT'];
      break;
      case "embed":
        $two = $_POST['TwoE'];
      break;
      case "upload":
        $two = $_FILES['TwoU']['name'];
      break;
    }
  }
  if(isset($_POST['ThreeType']))
  {
    $threeType = $_POST['ThreeType'];
    switch($threeType)
    {
      case "text":
        $three = $_POST['ThreeT'];
      break;
      case "embed":
        $three = $_POST['ThreeE'];
      break;
      case "upload":
        $three = $_FILES['ThreeU']['name'];
      break;
    }
  }
  if(isset($_POST['FourType']))
  {
    $fourType = $_POST['FourType'];
    switch($fourType)
    {
      case "text":
        $four = $_POST['FourT'];
      break;
      case "embed":
        $four = $_POST['FourE'];
      break;
      case "upload":
        $four = $_FILES['FourU']['name'];
      break;
    }
  }
  if(isset($_POST['FiveType']))
  {
    $fiveType = $_POST['FiveType'];
    switch($fiveType)
    {
      case "text":
        $five = $_POST['FiveT'];
      break;
      case "embed":
        $five = $_POST['FiveE'];
      break;
      case "upload":
        $five = $_FILES['FiveU']['name'];
      break;
    }
  }



  //$elements = Array($one => $oneType, $two => $twoType, $three => $threeType, $four =>$fourType, $five => $fiveType);
  $types = Array($oneType => $one,
                 $twoType => $two,
                 $threeType => $three,
                 $fourType => $four,
                 $fiveType => $five);

  echo "The name is:".$name."<br/>";
  echo "The one is:".htmlentities($one)."<br/>";
  echo "The two is:".$two."<br/>";
  echo "The three is:".$three."<br/>";
  echo "The four is:".$four."<br/>";
  echo "The five is:".$five."<br/>";

  /*foreach ($types as $type => $element)
  {
    echo "The element is: ".$element.";<br/> The Type is: ".$type."<br/>";
  }*/


  /*
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

    Profile::where('username','=',$user)->update(Array('projectOneID' => $newProject->id));
  }
  else if($profile->projectOneID != NULL)
  {
    $project = Project::where('id','=',$profile->projectOneID)->first();

    if($name != "")
      $project->update(Array('name' => $name));
    if($one != "")
      $project->update(Array('elementOne' => $one, 'oneType' => $oneType));
    if($two != "")
      $project->update(Array('elementTwo' => $two, 'twoType' => $twoType));
    if($three != "")
      $project->update(Array('elementThree' => $three, 'threeType' => $threeType));
    if($four != "")
      $project->update(Array('elementFour' => $four, 'fourType' => $fourType));
    if($five != "")
      $project->update(Array('elementFive' => $five, 'fiveType' => $fiveType));

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
    echo "The file to save is ".$upload;
    /*if($upload != "")
    {

    }/
  }
  */
?>
