@extends('layouts.mastertwo')
@section('content')
  <div class="col-md-8">
    </br>
    <h1>Sign in</h1>

  <form method="POST" action="/login">
    {{ csrf_field() }}
    <div class="form-group">
      <label for="email">Email Address:</label>
      <input type="email" class="form-control" id="email" name="email">
    </div>

    <div class="form-group">
      <label for="password">Password:</label>
      <input type="password" class="form-control" id="password" name="password">
    </div>

    <div class="form-group">
      <button type="submit" class="btn btn-default">Login</button>
    </div>
    @include ('layouts.errors')
</form>
  </div>
@endsection
