@extends('layouts.master')

@section('title')
  Registration|BookSpace
@endsection

@section('content')
  <h3>Register</h3>

  <form method="POST" action="/register">
    {{csrf_field()}}

    <label for="name">User Name:</label>
    <input type="text" name="name" required><br/>

    <label for="email">Email:</label>
    <input type="email" name="email" required><br/>

    <label for="password">Password:</label>
    <input type="password" name="password" required><br/>

    <label for="password_confirmation">Confirm Password:</label>
    <input type="password" name="password_confirmation" required><br/>

    <button type="submit">Create User</button><br/>
  </form>

  @include('layouts.errors')
@endsection
