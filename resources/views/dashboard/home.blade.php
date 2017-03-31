@extends('layouts.master')

@section('title')
  Dashboard | Home
@endsection

@section('content')
  <div class="row dashboard">
    <div class="col-sm-4 dashOption">
      <h1>
        <a href="#">Connections</a>
      </h1>
    </div>
    <div class="col-sm-4 dashOption">
      <h1>
        <a href="#">Inbox</a>
      </h1>
    </div>
    <div class="col-sm-4 dashOption">
      <h1>
        <a href="/management/{{Auth::user()->name}}">Manage your Profile</a>
      </h1>
    </div>
  </div>
  <hr>
  <div class="row dashboard">
    <div class="col-sm-6 dashOption">
      <h1>
        <a href="#">Business Diagrams</a>
      </h1>
    </div>
    <div class="col-sm-6 dashOption">
      <h1>
        <a href="#">Additional Content</a>
      </h1>
    </div>
  </div>
@endsection
