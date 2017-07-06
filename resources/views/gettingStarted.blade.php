@extends('layouts.master')
<?php
  if(Auth::user()->role != 'none')
    return redirect('/profile'.'/'.Auth::user()->name);
  else
    echo Auth::user()->role;
?>
@section('title')
  Getting Started | Dream Catcher Network
@endsection

@section('content')
  <form method="POST" action="/gettingStarted" id="gettingStartedPanel">
    {{ csrf_field() }}
    <input type="hidden" value="{{Auth::user()->name}}" name="username">
    <input type="hidden" value="0" id="option" name="option">
    <h1>Please select a path</h1>
    <hr/>
    <input id="invent" type="button" name="invent" value="Creator" class="pathOption">
    &nbsp;&nbsp;
    <input id="invest" type="button" name="invest" value="Investor" class="pathOption">
    <hr>
    <button id="next1" type="submit" class="continue"s>Continue</button>
    <br><br>
  </form>
@endsection

@section('javascript')
  <script>
    $(document).ready(function()
    {
      var invent = $('#invent');
      var invest = $('#invest');
      var option = $('#option');

      invest.on('click', function()
      {
        option.val('2').change();
      });
      invent.on('click', function()
      {
        option.val('1').change();
      });
    });
  </script>
@endsection
