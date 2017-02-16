@extends('layouts.master')

@section('title')
  {{$profile->user->name}}'s Profile
@endsection

@section('content')
  <h1>This is {{$profile->user->name}}</h1>
  <h4>{{$profile->id}}</h4>
@endsection
