<?php
  $u = $user->name;
  $e = $user->email;
  $p = $user->password;
  $url = "?u=".$u."&e=".$e."&p=".$p;
?>
@component('mail::message')
  # Welcome to The Dream Catcher Network!

  {{$user->name}}, We are so glad you joined the network.  You are well on your
  way to find the connection that realized your business dream!

  Click the Link below to confirm your email and get started.
  <button>
    <a href="http://127.0.0.1:8000/activation{{$url}}">Activate Your Account!</a>
  </button>

  Thanks,<br>
  {{ config('app.name') }}
@endcomponent
