@extends('layouts.master')

@section('title')
  Login|Dream Catcher Network
@endsection

@section('content')
  <h3>Sign In</h3>
  <form method = "POST" action="/login">
    {{csrf_field()}}

    <label for="name">User Name:</label>
    <input type="text" id="name" name="name"><br/>

    <!--label for="email">Email:</label>
    <input type="text" id = "email" name="email"><br/-->

    <label for="password">Password:</label>
    <input type="password" id="password" name="password"><br/>

    <button type="submit">Log In</button><br/>
  </form>
  @include('layouts.errors')
@endsection
