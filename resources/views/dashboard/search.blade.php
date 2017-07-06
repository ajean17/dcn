@extends('layouts.master')

@section('title')
  Star Gazer | DCN
@endsection
<?php
  use App\Category;
  use App\User;
  use App\Project;
  use App\Profile;

  //$categories = Category::whereNull('parent')->orderBy('name','asc')->get();
  $parent = "WHERE parent IS NULL";
  $categories = DB::select(DB::raw('SELECT * FROM categories '.$parent.' ORDER BY name ASC'));
  $users = User::all();

?>
@section('content')
  <!--h1>Search Page</h1-->
  <div class="row">
    <div id="searchCriteria" class="col-sm-3">
      <h4>Search Bar</h4>
      <hr/>
      <input type="text" id="searchBar" name="searchBar" onkeydown="if (event.keyCode == 13) searchNow()"
      placeholder="Search for users by name">
      <button id="startSearch" onclick="searchNow()">Search</button>
      <div id="criteriaList">
        <hr/>
        <h4>Preferences</h4>
        <div id = "checks">
          Has a Video <input type="checkbox" id="video" name="video"><br/>
          Has a Mentor <input type="checkbox" id="mentor" name="mentor"><br/>
          Has Investments <input type="checkbox" id="investments" name="investments"><br/>
          Has ROI forcast reports <input type="checkbox" id="ROI" name="ROI"><br/>
        </div>
        <hr/>
        <h4>Categories</h4>
        <h6 id="catChoose"></h6>
        <button class="btn btn-sm" onclick="grabCat('category')">None</button></br>
        <hr/>
          @foreach($categories as $category)
            <p class="accordion" onmouseup="toggleList(); grabCat('{{$category->name}}')">{{$category->name}}</p>
            <?php
              $subCategory = Category::where('parent','=',$category->name)->orderBy('name','asc')->get();
              if($subCategory != "[]")
              {
                echo "<ul class=\"panell\">";
                //echo "<div class=\"panel\">";
                foreach($subCategory as $sub)
                {
                  echo "<li onclick='grabCat(\"".$sub->name."\")'>".$sub->name."</li>";
                }
                //echo "</div>";
                echo "</ul>";
              }
            ?>
          @endforeach
      </div>
    </div>
    <div class="col-sm-9">
      <div id="starMap">
        <h4>Animated Star Map</h4>
        <hr/>
        <div id="map"><img src="/images/galaxy.jpg" width="245px" height="245px" alt="Map Picture"></div>
      </div>
      <div id="results">
        <hr/>
        <h4>Search Results</h4>
        <hr/>
        <ul id = "resultList">
          @foreach($users as $user)
            <?php
              $userPic = "";
              if($user->avatar != NULL)
                $userPic = '<img src="/uploads/user/'.$user->name.'/images'.'/'.$user->avatar.'" alt="'.$user->name.'" class="searchPic">';
              else
                $userPic = '<img src="/images/Default.jpg" alt="'.$user->name.'" class="searchPic">';
            ?>
            <li><?php echo $userPic;?><p><a href='\profile\{{$user->name}}'>{{$user->name}}</a> | {{$user->userType}} | {{$user->profile->project->name}}</p></li><br/>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
  <div class="row">

  </div>
@endsection

@section('javascript')
<script>
  var category = "category";
  var token = '{{Session::token()}}';
  var url= '{{route('search')}}';

  function grabCat(cat)
  {
    category = cat;
    //console.log(cat);
    if(category == "category")
      $('#catChoose').html("Chosen: None");
    else
    $('#catChoose').html("Chosen: "+ category);
  }
  function searchNow()
  {
    var video = $('#video').prop('checked');//document.getElementById("video").checked;
    var mentor = $('#mentor').prop('checked');//document.getElementById("mentor").checked;
    var investments = $('#investments').prop('checked');//document.getElementById("investments").checked;
    var roi = $('#ROI').prop('checked');//document.getElementById("ROI").checked;
    var search = $('#searchBar').val();//document.getElementById("searchBar").value;
    if(search == "")
      search = "searching";
    //console.log(category);
    //console.log("video "+video+" mentor "+mentor+" investments "+investments+" roi "+roi);
    //if(search != "")
    //{
      var output = "";
      $.ajax(
      {
        method: 'POST',
        url: url,
        data: {search: search, video: video, mentor: mentor, investments: investments, roi: roi, category: category, _token: token}
      }).done(function (msg)
      {
        //console.log(msg['message']);
        $('#resultList').html(output);

        if(msg['message']=="")
          output += "<li>Sorry, no users or projects matched your search.</li>";
        else
        {
          var response = msg['message'].split("\n");
          var item = "";

          for (var i = 0; i < response.length; i++)
          {
            item = response[i].split("\\");
            if(item[0] != undefined && item[1] != undefined)
            {
              output += "<li>The profile owner is <a href='\\profile\\"+item[0]+"'>"+item[0]+"</a>, their project is "+item[1]+".</li><br/>";
            }
          }
        }
        $('#resultList').html(output);
      });
  }
  function toggleList()
  {
    var acc = document.getElementsByClassName("accordion");
    var i;
    for (i = 0; i < acc.length; i++)
    {
        acc[i].onclick = function()
        {
            /* Toggle between adding and removing the "active" class,
            to highlight the button that controls the panel*/
            this.classList.toggle("active");

            /* Toggle between hiding and showing the active panel*/
            var panel = this.nextElementSibling;
            if (panel.style.maxHeight)
            {
              panel.style.maxHeight = null;
              //console.log("maxlength is " + panel.style.maxHeight);
            }
            else
            {
              panel.style.maxHeight = panel.scrollHeight + "px";
              //console.log("maxlength is " + panel.scrollHeight);
            }
        }
    }
  }
  $(document).ready(function()
  {
    $("#starMap").click(function()
    {
        $("#map").slideToggle("slow");
    });
  });
//ajax rq
/*
given category c1, find the most similar category
Best score variable set to minimum value o
for each other category c2
  set matchscore to 0
  for each Feature
    compare c1 and c2 on Feature
    if match, matchscore++
  if matchscore > bestscore
  then bestmatch = c2 and bestscore = matchscore
return bestmatch
*/
</script>
@endsection
