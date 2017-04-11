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
  $profiles = Profile::all();

?>
@section('content')
  <h1>Search Page</h1>
  <div class="row">
    <div id="searchCriteria" class="col-sm-3">
      <h4>Search Bar</h4>
      <hr/>
      <input type="text" id="searchBar" name="searchBar" onkeydown="if (event.keyCode == 13) searchNow()">
      <button id="startSearch" onclick="searchNow()">Search</button>
      <div id="criteriaList">
        <hr/>
        <h4>Preferences</h4>
        <input type="checkbox" id="video" name="video"> Has a Video <br/>
        <input type="checkbox" id="mentor" name="mentor"> Has a Mentor <br/>
        <input type="checkbox" id="investments" name="investments"> Has Investments <br/>
        <input type="checkbox" id="ROI" name="ROI"> Has ROI forcast reports <br/>
        <hr/>
        <h4>Industries</h4>
          @foreach($categories as $category)
            <p class="accordion" onmouseup="toggleList(); grabCat('{{$category->name}}')">{{$category->name}}</p>
            <?php
              $subCategory = Category::where('parent','=',$category->name)->orderBy('name','asc')->get();
              if($subCategory != "[]")
              {
                echo "<ul class=\"panel\">";
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
      </div>
      <hr/>
      <div id="results">
        <h4>Search Results</h4>
        <hr/>
        <ul id = "resultList">
          <?php
            foreach($profiles as $profile)
            {
              echo "<li>Click to view <a href='\\profile\\".$profile->username."'>".$profile->username."'s</a> Profile</li>";
            }
          ?>
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
    console.log(cat);
  }

  function searchNow()
  {
    var video = $('#video').prop('checked');//document.getElementById("video").checked;
    var mentor = $('#mentor').prop('checked');//document.getElementById("mentor").checked;
    var investments = $('#investments').prop('checked');//document.getElementById("investments").checked;
    var roi = $('#ROI').prop('checked');//document.getElementById("ROI").checked;
    var search = $('#video').val();//document.getElementById("searchBar").value;

    console.log(category);
    console.log("video "+video+" mentor "+mentor+" investments "+investments+" roi "+roi);
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
        console.log(msg['message']);
        $('#resultList').html(output);
        var response = msg['message'].split("\n");
        var item = "";

        for (var i = 0; i < response.length; i++)
        {
          item = response[i].split("\\");
          if(item[0] != undefined && item[1] != undefined)
          {
            output += "<li>The profile owner is <a href='\\profile\\"+item[0]+"'>"+item[0]+"</a>, the name of their project is "+item[1]+".</li>";
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
              console.log("maxlength is " + panel.style.maxHeight);
            }
            else
            {
              panel.style.maxHeight = panel.scrollHeight + "px";
              console.log("maxlength is " + panel.scrollHeight);
            }
        }
    }
  }

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
