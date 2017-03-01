@extends('layouts.master')

@section('title')
  {{$profile->user->name}} | DCN
@endsection

@section('content')
  @if($profile->id == Auth::user()->id)
    <?php $edit = true;?>
    <h1>Your Profile</h1>
  @else
    <h1>{{$profile->user->name}}'s Profile</h1>
  @endif
  <hr>
  <div class="row profileHead">
    <div class="col-sm-3 profilePic">
      <h1>Profile Picture</h1>
      <img class="featurette-image img-fluid mx-auto" data-src="holder.js/500x500/auto" alt="Generic placeholder image">
    </div>
    <div class="col-sm-9 banner">
      <h1>Banner</h1>
      <div class="bannerTabs">
        <a href="#">Project One</a>
        <a href="#">| Project Two</a>
        <a href="#">| Project Three</a>
      </div>
    </div>
  </div>
  <hr>
  <div class="row profileBody">
    <div class="col-sm-2 quickNav">
      <h1>Quick Nav</h1>
      <ul>
        <li>Element One</li>
        <li>Element Two</li>
        <li>Element Three</li>
        <li>Element Four</li>
      </ul>
    </div>
    <div class="col-sm-10 projectContent">
      <h1>Project Content</h1>
    </div>
  </div>
  <hr>
  <div class="row profileFoot">
    <div class="col-sm-12">
      <h1>Reserved for more</h1>
    </div>
  </div>
@endsection
