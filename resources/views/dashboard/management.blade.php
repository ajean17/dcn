@extends('layouts.master')

@section('title')
  Profile Management
@endsection
<?php
  use App\Category;

  $parent = "WHERE parent IS NULL";
  $categories = DB::select(DB::raw('SELECT * FROM categories '.$parent.' ORDER BY name ASC'));
  function elementUpload($number)
  {
    ?>
    <div class="form-group">
      <label for="element{{$number}}"><b>Element {{$number}}:</b></label><br/>
      Select the type of content you wish to place for this element.<br/>
      <input type="radio" name="{{$number}}Type" value="text" onclick="showType('{{$number}}')">Text &nbsp
      <input type="radio" name="{{$number}}Type" value="embed" onclick="showType('{{$number}}')">Embedding &nbsp
      <input type="radio" name="{{$number}}Type" value="upload" onclick="showType('{{$number}}')">Upload &nbsp<br/>
      <input type="text" class="form-control element" id="el-{{$number}}-text" name="{{$number}}T" placeholder="Enter Description">
      <input type="text" class="form-control element" id="el-{{$number}}-embed" name="{{$number}}E" placeholder="Enter Embed Link">
      <input type="file" class="form-control element" id="el-{{$number}}-upload" name="{{$number}}U" placeholder="Upload File">
    </div>
    <?php
  }
?>
@section('content')
  <?php
    if(isset($message))
    {
      echo $message;
    }
  ?>
  <form id="profileContentForm" enctype="multipart/form-data" method="post" action="/projectSystem"><!--onsubmit="return false;"-->
    {{csrf_field()}}

    <input type="hidden" name="userName" value="{{Auth::user()->name}}">
    <center>
      <h5>You may add up to five elements to your project's profile</h5>
      @include ('layouts.errors')
    </center>

    <div class="form-group">
      <label for="category"><b>Select a Category that Describes your Project</b></label><br/>
        <?php
          echo "<select id='categories' name=\"category\" onmouseup='showSub()'>";
          echo "<option value='dummy' disabled selected>Select your option</option>";
          foreach($categories as $category)
          {
            echo "<option value='".$category->name."'>".$category->name."</option>";
          }
          echo "</select><br/>";

          foreach($categories as $category)
          {
            echo "<div style='display:none;' id='".$category->name."'><select name='subCategory'>";
            echo "<option value='dummy2' disabled selected>Select an advanced category (Optional)</option>";
            $subCategory = Category::where('parent','=',$category->name)->orderBy('name','asc')->get();
            foreach($subCategory as $sub)
            {
              echo "<option value='".$sub->name."'>".$sub->name."</option>";
            }
            echo "</select></div>";
          }
        ?>
      </select>
    </div>

    <div class="form-group">
      <label for="title"><b>Please provide a title for your project.</b></label><br/>
      <input type="text" id="projectTitle" name="title">
    </div>

    <?php
      elementUpload("One");
      elementUpload("Two");
      elementUpload("Three");
      elementUpload("Four");
      elementUpload("Five");
    ?>

    <div class="form-group">
      <button type="submit" id="contentButton" onclick="addContent()" class="btn btn-default">
        Update Profile
      </button>
      <span id="status"></span>
    </div>
  </form>

  <div id="contentPreview">
  </div>
@endsection

@section('javascript')
  <script>
    function showSub()
    {
      var categories = document.getElementById('categories');
      var list = categories.value;
      if(list != "dummy")
      {
        var sub = document.getElementById(list);
        //console.log("List is: "+list);

        if(sub.style.display == "none")
          sub.style.display = "block";
        else if(sub.style.display == "block")
          sub.style.display = "none";
      }
    }

    function showType(select)
    {
      //console.log('Select = ' + select);
      var radios = document.getElementsByName(select + "Type");
      var textBox = document.getElementById("el-"+select+"-text");
      var embedBox = document.getElementById("el-"+select+"-embed");
      var uploadBox = document.getElementById("el-"+select+"-upload");
      var radioValue = "ok";

      for(var x = 0; x < radios.length; x ++)
      {
        if (radios[x].checked)
        {
         radioValue = radios[x].value;
        }
      }
      //console.log('Value=' + radioValue);
      switch(radioValue)
      {
        case "text":
          textBox.style.display = "block";
          embedBox.style.display = "none";
          uploadBox.style.display = "none";
        break;

        case "embed":
          textBox.style.display = "none";
          embedBox.style.display = "block";
          uploadBox.style.display = "none";
        break;
        case "upload":
          textBox.style.display = "none";
          embedBox.style.display = "none";
          uploadBox.style.display = "block";
        break;
      }
    }
      /*$(document).ready(function ()
      {
            $("#radioCheck").click(function ()
            {
                alert($("input[type=radio]:checked").val());
            });
      });*/

  </script>

  <style>
    .element
    {
      display:none;
    }
  </style>
@endsection
