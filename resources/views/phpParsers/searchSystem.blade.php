<?php
  use App\User;
  use App\Dialogue;
  use App\Project;

  if(isset($_GET['whoSearched']) && isset($_GET['inboxSearch']))
  {
    $criteria = stripcslashes(htmlspecialchars($_GET['inboxSearch']));
    $whoSearched = stripcslashes(htmlspecialchars($_GET['whoSearched']));
    $newTalkTo = User::where('name','=',$criteria)->first();

    if($newTalkTo != "")
    //If the person being searched for does exist
    {
        //echo $newTalkTo->name;
        //echo $newTalkTo;
        $d = Dialogue::where('user1','=',$whoSearched)->where('user2','=',$criteria)
        ->orWhere('user1','=',$criteria)->where('user2','=',$whoSearched)->first();

        if($d != "")
        {
          echo "You already have a dialogue with ".$criteria;
        }
        else
        {
          Dialogue::create([
            'user1' => $whoSearched,
            'user2'=> $criteria,
            'lastMessage' => Carbon\Carbon::now()
          ]);

          echo "new_dialogue";
        }
    }
    else if($newTalkTo == "")
    {
        echo "Sorry, That user does not exist yet.";
    }
  }

  //if(isset($_GET['whoSearch']) && isset($_GET['searchCriteria']))

  if(isset($_GET["search"]) && isset($_GET["video"]) && isset($_GET["mentor"]) && isset($_GET["investments"]) && isset($_GET["roi"]) && isset($_GET["category"]))
  {
    $search = $_GET["search"];
    $video = $_GET["video"];
    $investments = $_GET["investments"];
    $mentor = $_GET["mentor"];
    $roi = $_GET["roi"];
    $category = $_GET["category"];

    $videoQ = "";
    $investmentsQ = "";
    $mentorQ = "";
    $roiQ = "";

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

    $results = DB::select(DB::raw('SELECT * FROM profiles'.$videoQ.$investmentsQ.$mentorQ.$roiQ.''));
    $show = "";
    $count = 0;
    foreach($results as $result)
    {
      $project = Project::where('id','=',$result->projectOneID)->first();
      if($project != "" || $project != NULL)
        $show = $show.$result->username."\\".$project->name."\n";
      //$count ++;
    }
    echo $show;
    //echo "Here are the values".$videoQ.$investmentsQ.$mentorQ.$roiQ.$category;
  }
?>
