<nav class="navbar navbar-toggleable-md navbar-inverse fixed-top bg-inverse">
  <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <a class="navbar-brand" href="#">Dream Catcher Network</a>
  <div class="collapse navbar-collapse" id="navbarCollapse">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="{{ url('/') }}">Home<span class="sr-only">(current)</span></a>
      </li>
      @if (Auth::check())
      <li class="nav-item">
        <a class="nav-link" href="/profile/{{Auth::user()->name}}">Profile</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ url('/stargazer') }}">Search</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/inbox/{{Auth::user()->name}}">Inbox</a>
      </li>
      <!--li class="nav-item">
        <a class="nav-link" href="/account/{{Auth::user()->name}}">Account Settings</a>
      </li-->
      <li class="nav-item">
        <a class="nav-link" href="{{ url('/logout') }}">Logout</a>
      </li>
      @else
        <li class="nav-item">
          <a class="nav-link" href="{{ url('/login') }}">Login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ url('/register') }}">Register</a>
        </li>
      @endif
      <li class="nav-item">
        <a style="color:pink" class="nav-link" href="https://www.surveymonkey.com/r/QN6KYN2">Take Our Survey!</a>
      </li>
    </ul>
    @if(Auth::check())
    <form class="form-inline mt-2 mt-md-0">
      <a class="nav-link" href="/notifications/{{Auth::user()->name}}">Notifications for {{ Auth::user()->name }}</a>
      <!--input class="form-control mr-sm-2" type="text" placeholder="Search">
      <button class="btn btn-outline-default my-2 my-sm-0" type="submit">Search</button-->
    </form>
    @endif
  </div>
</nav>
