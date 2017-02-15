<nav>
  @if (Auth::check())
      <a href="{{ url('/') }}">Home</a>
      <a href="{{ url('/') }}">Dashboard</a>
      <a href="/profile/{{Auth::user()->profile->id}}">Profile</a>
      <a href="{{ url('/stargazer') }}">Search</a>
      <a href="{{ url('/messages') }}">Messenger</a>
      <a href="{{ url('/settings') }}">Account Settings</a>
      <a href="{{ url('/logout') }}">Logout</a>
      <a href="#">{{Auth::user()->name}}</a>
  @else
      <a href="{{ url('/login') }}">Login</a>
      <a href="{{ url('/register') }}">Register</a>
  @endif
</nav>
