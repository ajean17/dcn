<?php

  use App\Category;

  if(isset($_GET['type']) && isset($_GET['name']) && isset($_GET['parent']))
  {
    $name = $_GET['name'];
    $parent = $_GET['parent'];
    $type = $_GET['type'];
    $exists = Category::where('name','=',$name)->first();

    if($type == "add")
    {
      if($exists != "")
      {
        echo "That category already exists in the system.";
        exit();
      }
      else
      {
        if($parent=="")
        {
          Category::create([
            'name' => $name
          ]);
        }
        else if($parent!="")
        {
          $exists = Category::where('name','=',$parent)->first();
          if($exists != "")
          {
            Category::create([
              'name' => $name,
              'parent' => $parent
            ]);
          }
          else
          {
            echo "You entered an invalid category name as the parent. Please select another";
            exit();
          }
        }
        echo "cat_added";
        exit();
      }
    }
    else if($type == "delete")
    {
      if($exists == "")
      {
        echo "That category does not exist, therefore cannot be deleted.";
        exit();
      }
      else
      {
        Category::where('name','=',$name)->delete();
        if($parent=="")
        {
          Category::where('parent','=',$name)->delete();
        }
        echo "cat_deleted";
        exit();
      }
    }
  }

  $categories = Category::whereNull('parent')->orderBy('name','asc')->get();
?>

@extends('layouts.master')

@section('title')
  Add or Delete Categories
@endsection

@section('content')
  <form onsubmit="return false;">
    <input type="text" id="catName" placeholder="enter the category name here"><br/>
    <input type="text" id="parentName" placeholder="enter the parent category name here"><br/>
    <button onclick="addCat()">Add Category</button>
  </form>
  <span id="status"></span>
  <hr>
  <h4>List of Categories</h4>
  <ul id="catList">
    <?php
      foreach($categories as $category)
      {
        echo "<li id='".$category->name."'>".$category->name."</li><button id='".$category->name."Button' onclick='deleteCat(\"".$category->name."\",\"\")'>Delete Category</button>";
        $subCategory = Category::where('parent','=',$category->name)->orderBy('name','asc')->get();
        echo "<ul id='".$category->name."List'>";
        if($subCategory != "[]")
        {
          foreach($subCategory as $sub)
          {
            echo "<li id='".$sub->name."'>".$sub->name."</li><button id='".$sub->name."Button' onclick='deleteCat(\"".$sub->name."\",\"".$sub->parent."\")'>Delete Category</button>";
          }
        }
        echo "</ul>";
      }
    ?>
  </ul>
@endsection

@section('javascript')
  <script>
    function addCat()
    {
      var name = document.getElementById('catName').value;
      var parent = document.getElementById('parentName').value;
      var type = "add";
      var status = document.getElementById('status');
      if(name != "")
      {
        var ajax = ajaxObj("GET", "/categories?type="+type+"&name="+name+"&parent="+parent);
        status.innerHTML = 'please wait ...';
        ajax.onreadystatechange = function()
        {
          if(ajaxReturn(ajax) == true)
          {
            if(ajax.responseText == "cat_added")
            {
              status.innerHTML = "Category Added";
              if(parent=="")
              {
                  document.getElementById("catList").innerHTML += "<li id=\""+name+"\">"+name+"</li><button id=\""+name+"Button\" onclick=\"deleteCat("+name+",\"\")\">Delete Category</button>";
              }
              else
              {
                document.getElementById(parent+"List").innerHTML += "<li id=\""+name+"\">"+name+"</li><button id=\""+name+"Button\" onclick=\"deleteCat("+name+","+parent+")\">Delete Category</button>";
              }
            }
            else if(ajax.responseText != "cat_added")
            {
              status.innerHTML = ajax.responseText;
            }
          }
        }
        ajax.send();
      }
    }

    function deleteCat(cat,par)
    {
      var type = "delete";
      var status = document.getElementById('status');
      var ajax = ajaxObj("GET", "/categories?type="+type+"&name="+cat+"&parent="+par);
      ajax.onreadystatechange = function()
      {
        if(ajaxReturn(ajax) == true)
        {
          if(ajax.responseText == "cat_deleted")
          {
            status.innerHTML = "Category Deleted";
            document.getElementById(cat).style.display = "none";
            document.getElementById(cat+"Button").style.display = "none";
          }
          else if(ajax.responseText != "cat_deleted")
          {
            status.innerHTML = ajax.responseText;
          }
        }
      }
      ajax.send();
    }
  </script>
@endsection

<?php
/*
  **Finance
    Accountants
    Mortgage
    Commercial Banks
    Credit Unions
    Credit Companies
    Insurance & Real Estate
    Hedge Funds
    Money Lending
    Investment Firms
    Savings & Loans
    Securities & Investment
    Stock Brokerage
    Student Loan Companies
    Venture Capital
  **Marketing
    Advertising/Public Relations
  **Agriculture
    Agribusiness
    Agricultural Services & Products
    Agriculture
    Architectural Services
    Dairy
    Crop Production & Basic Processing
    Farm Bureaus
    Farming
    Livestock
    Meat processing & products
    Poultry & Eggs
    Sugar Cane & Sugar Beets
    Tobacco
    Vegetables & Fruits
  **Automotive/Mass Transit
    Air Transport
    Airlines
    Auto Dealers
    Auto Manufacturers
    Automotive
    Car Dealers/Imports
    Car Manufacturers
    Cruise Ships & Lines
    Marine Transport
    Railroads
    Transportation
    Trucking
  **Engineering
    Defense
    Defense Aerospace
    Defense Electronics
    Defense Contractors
  **Electronics/Information Technology
    Phone Companies
    Electronics Manufacturing & Equipment
    Internet
    Telecom Services & Equipment
    Telephone Utilities
    Computer Software
    Communications
  **Food and Beverage
    Alcoholic Beverages
    Food & Beverage
    Food Processing & Sales
    Food Products Manufacturing
    Food Stores
    Restaurants & Drinking Establishments
  **Law
    Attorneys/Law Firms
    Lobbyists
  **Print Media
    Books, Magazines & Newspapers
    Printing & Publishing
  **Business
    Business Associations
    Business Services
  **Construction
    Builders/General Contractors
    Builders/Residential
    Building Materials & Equipment
    Commercial Construction
    Construction Services
    General Contractors
    Residential Construction
  **Politics
    Candidate Committees
    Conservative/Republican
    Democratic Leadership PACs
    Democratic/Liberal
    Defense/Foreign Policy Advocates
    Lobbyists
    Leadership PACs
    Pro-Israel
    Progressive
    Republican Leadership PACs
  **Gaming
    Gambling & Casinos
  **Education
    Education
    For-profit Education
    Universities, Colleges & Schools
  **Pharmacy
    Chiropractors
    Drug Manufacturers
  **Entertainment
    Entertainment Industry
    Video Gaming
    Motion Picture Production & Distribution
    Music Production
    Professional Sports
    Sports Arenas
    Record Companies
    Rappers/Singers/Songwriters
    Recreation/Live Entertainment
    *Radio
      Broadcasters, Radio/TV Stations
    *Television
      Cable & Satellite TV Production & Distribution
      Commercial Television
      Movies
      Movie Production
      TV Production
  **Environmental
    Environment
    Forestry & Forest Products
  **Philanthropy
    Foundations, Philanthropists & Non-Profits
  **Social Issues
    Gun Control
    Gun Rights
    Gay & Lesbian Rights & Issues
    Human Rights
    Ideological/Single-Issue
    Women's Issues
  **Resources
    Alternative Energy Production & Services
    Chemical & Related Manufacturing
    Gas & Oil
    Logging, Timber & Paper Mills
    Energy & Natural Resources
    Mining
    Natural Gas Pipelines
    Oil & Gas
    Steel Production
  **Fashion
    Clothing Manufacturing
    Textiles
  **Religion
    Clergy & Religious Organizations
    Religious Organizations/Clergy
  **Civil Services
    Funeral Services
    Government Employees
    Labor Services
    Postal Services
    Power Utilities
    Trash Collection/Waste Management
    Civil Servants/Public Officials
  **Medical
    Health
    Health Professionals
    Health Services/HMOs
    Hospitals & Nursing Homes
    Medical Supplies
    Nursing
    Nutritional & Dietary Supplements
    Pharmaceutical Manufacturing
    Pharmaceuticals/Health Products
    Dentistry
    Chiropracting
  **Hospitality
    Hotels, Motels & Tourism
  **Travel
    Lodging / Tourism
  **Unions
    Industrial Unions
    Unions
  **Retail
  **Real Estate
*/
?>
