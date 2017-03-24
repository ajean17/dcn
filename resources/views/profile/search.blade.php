@extends('layouts.master')

@section('title')
  Star Gazer | DCN
@endsection
<?php
  use App\Category;

  $categories = Category::whereNull('parent')->orderBy('name','asc')->get();

?>
@section('content')
  <h1>Search Page</h1>
  <div class="row">
    <div id="searchCriteria" class="col-sm-3">
      <h4>Search Bar</h4>
      <hr/>
      <input type="text" id="searchBar" name="searchBar">
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
        <ul>
          @foreach($categories as $category)
            <li>{{$category->name}}</li>
            <?php
              $subCategory = Category::where('parent','=',$category->name)->orderBy('name','asc')->get();
              if($subCategory != "[]")
              {
                echo "<ul>";
                foreach($subCategory as $sub)
                {
                  echo "<li>".$sub->name."</li>";
                }
                echo "</ul>";
              }
            ?>
          @endforeach
          <!--li>Finance</li>
          <li>Marketing</li>
          <li>Agriculture</li>
          <li>Automotive/Mass Transit</li>
          <li>Engineering</li>
          <li>Electronics/Information Technology</li>
          <li>Food & Beverage</li>
          <li>Law</li>
          <li>Print Media</li>
          <li>Business</li>
          <li>Construction</li>
          <li>Politics</li>
          <li>Gaming</li>
          <li>Education</li>
          <li>Pharmacy</li>
          <li>Entertainment</li>
          <li>Environmental</li>
          <li>Philanthropy</li>
          <li>Social Issues</li>
          <li>Resources</li>
          <li>Fashion</li>
          <li>Religion</li>
          <li>Civil Services</li>
          <li>Medical</li>
          <li>Hospitality</li>
          <li>Travel</li>
          <li>Unions</li>
          <li>Retail</li>
          <li>Real Estate</li-->
        </ul>
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
        <ul>
        </ul>
      </div>
    </div>
  </div>
  <div class="row">

  </div>
@endsection

@section('javascript')
<script>
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
