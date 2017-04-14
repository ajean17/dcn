<nav>
<div class="container">
  <ul id="navigation">
      <li class="home"><a href="{{ url('/') }}" title="Home"><span><i class="fa fa-home"></i></span></a></li>
      @if (Auth::check())
      <li class="about"><a href="/profile/{{Auth::user()->name}}" title="About"><span><i class="fa fa-sign-in"></i></span></a></li>
      <li class="search"><a href="{{ url('/stargazer') }}" title="Search"><span><i class="fa fa-search"></i></span></a></li>
      <li class="contact"><a href="/inbox/{{Auth::user()->name}}" title="Contact"><span><i class="fa fa-commenting"></i></span></a></li>
      @else
      <li class="search"><a href="{{ url('/login') }}" title="Search"><span><i class="fa fa-search"></i></span></a></li>
      <li class="contact"><a href="{{ url('/register') }}" title="Contact"><span><i class="fa fa-commenting"></i></span></a></li>
      @endif
  </ul>
</div>
</nav>
