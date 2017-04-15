<nav>
  <div class="container">
    <ul id="navigation">
        <li class="home"><a href="{{ url('/') }}" title="Home"><b>Home</b> &nbsp;&nbsp;<span><i class="fa fa-home"></i></span></a></li>
        @if (Auth::check())
        <li class="about"><a href="/profile/{{Auth::user()->name}}" title="Profile"><b>Profile</b>  &nbsp;&nbsp;<span><i class="fa fa-sign-in"></i></span></a></li>
        <li class="search"><a href="{{ url('/stargazer') }}" title="Search"><b>Search</b>  &nbsp;&nbsp;<span><i class="fa fa-search"></i></span></a></li>
        <li id="chat" class="chat dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="Chat"><b>Chat</b>  &nbsp;&nbsp;<span><i class="fa fa-commenting"></i></span></a>
          <ul id="chatList" class="dropdown-menu">
            <li><a href="#" title="chatOps"><b>Friends</b>  &nbsp;&nbsp;<span><i class="fa fa-smile-o"></i></span></a></li>
            <li><a href="#" title="chatOps"><b>Inbox</b>  &nbsp;&nbsp;<span><i class="fa fa-comments-o"></i></span></a></li>
          </ul>
        </li>
        @else
        <li class="search"><a href="{{ url('/login') }}" title="Search"><span><i class="fa fa-search"></i></span></a></li>
        <li class="contact"><a href="{{ url('/register') }}" title="Contact"><span><i class="fa fa-commenting"></i></span></a></li>
        @endif
    </ul>
  </div>
</nav>


<!--div class="sidebar-name">
    < Pass username and display name to register popup >
    <a href="javascript:register_popup('narayan-prusty', 'Narayan Prusty');">
        <img width="30" height="30" src="https://fbcdn-profile-a.akamaihd.net/hprofile-ak-xap1/v/t1.0-1/p50x50/1510656_10203002897620130_521137935_n.jpg?oh=572eaca929315b26c58852d24bb73310&oe=54BEE7DA&__gda__=1418131725_c7fb34dd0f499751e94e77b1dd067f4c" />
        <span>Narayan Prusty</span>
    </a>
</div-->
