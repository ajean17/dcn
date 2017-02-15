@extends('layouts.master')

@section('title')
  Login|BookSpace
@endsection

@section('content')
  <h3>Sign In</h3>
  <form method = "POST" action="/login">
    {{csrf_field()}}

    <label for="name">User Name:</label>
    <input type="text" name="name"><br/>

    <label for="password">Password:</label>
    <input type="password" name="password"><br/>

    <button type="submit">Log In</button><br/>
  </form>
  @include('layouts.errors')
@endsection
