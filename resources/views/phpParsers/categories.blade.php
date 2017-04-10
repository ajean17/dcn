<?php
  use App\Category;
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
    <button id="addBtn">Add Category</button><button id="delBtn">Delete Category</button>
  </form>
  <span id="status"></span>
  <hr>
  <h4>List of Categories</h4>
  <ul id="catList">
    <?php
      foreach($categories as $category)
      {
        echo "<li id='".$category->name."'>".$category->name."</li>";//<button id='".$category->name."Button' onclick='deleteCat(\"".$category->name."\",\"\")'>Delete Category</button>";
        $subCategory = Category::where('parent','=',$category->name)->orderBy('name','asc')->get();
        echo "<ul id='".$category->name."List'>";
        if($subCategory != "[]")
        {
          foreach($subCategory as $sub)
          {
            echo "<li id='".$sub->name."'>".$sub->name."</li>";//<button id='".$sub->name."Button' onclick='deleteCat(\"".$sub->name."\",\"".$sub->parent."\")'>Delete Category</button>";
          }
        }
        echo "</ul>";
      }
    ?>
  </ul>
@endsection

@section('javascript')
  <script>
    var token = '{{Session::token()}}';
    var url = '{{route('category')}}';

    $(document).ready(function()
    {
      var $name = $('#catName');
      var $parent = $('#parentName');
      var $status = $('#status');

      $('#delBtn').on('click',function()
      {
        console.log('delete');
        if($name.val() != "")
        {
          $.ajax(
          {
            method: 'POST',
            url: url,
            data: {name: $name.val(), parent: $parent.val(), type: 'delete', _token: token}
          }).done(function (msg)
          {
            console.log(msg['message']);
            if(msg['message'] == "cat_deleted")
            {
              $status.html($name.val() + " deleted.");
            }
            else if(msg['message'] != "cat_deleted")
            {
              $status.html(msg['message']);
            }
          });
        }
      });

      $('#addBtn').on('click',function()
      {
        console.log($name.val());
        if($name != "")
        {
          $.ajax(
          {
            method: 'POST',
            url: url,
            data: {name: $name.val(), parent: $parent.val(), type: 'add', _token: token}
          }).done(function (msg)
          {
            console.log(msg['message']);
            var name = $name.val();
            var parent = $parent.val();
            if(msg['message'] == "cat_added")
            {
              if($parent.val() == "")
              {
                console.log('name');
                $('#catList').append("<li id=\""+name+"\">"+name+"</li>");//<button id=\""+name+"Button\" onclick=\"deleteCat("+name+",\"\")\">Delete Category</button>");
              }
              else
              {
                console.log('parent');
                $('#'+parent+'List').append("<li id=\""+name+"\">"+name+"</li>");//<button id=\""+name+"Button\" onclick=\"deleteCat("+name+","+parent+")\">Delete Category</button>");
              }
            }
            else if(msg['message'] != "cat_added")
            {
              $status.html(msg['message']);
            }
          });
        }
      });
    });

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
