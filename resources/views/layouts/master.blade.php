<!DOCTYPE html>

<html>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>@yield('title')</title>
    @include('layouts.style')
    @yield('javascript')
  </head>

  <body>
    <!--
      Change to include('layouts.header') for the old nav bar
    -->
    @include('layouts.sidenav')
    @if ($flash = session('message'))
      <div class="alert alert-success" role=alert>
        {{$flash}}
      </div>
    @endif
    <div class="container marketing">

      <!--div class="chat-sidebar">
        <div class="sidebar-name">
        <a href="javascript:register_popup('narayan-prusty', 'Narayan Prusty');">
          <?php
            /*use App\Friend;
            use App\User;
            if(Auth::check())
            {
              $loggedUser = Auth::user()->name;
              $friends = Friend::where('user2','=', $loggedUser)->where('accepted','=','1')
              ->orWhere('user1','=', $loggedUser)->where('accepted','=','1')->get();

                foreach($friends as $friend)
                {
                  $buddy = "";
                  if($friend->user1 == Auth::user()->name)
                  {
                    $buddy = $friend->user2;
                  }
                  else if($friend->user2 == Auth::user()->name)
                  {
                    $buddy = $friend->user1;
                  }
                  $guy =  User::where('name','=',$buddy)->first();
                  $user1avatar = $guy->avatar;
                  $user1pic = '<img src="/uploads/user/'.$guy->name.'/images'.'/'.$user1avatar.'" alt="'.$guy->name.'" class="user_pic">';
                  if($user1avatar == NULL)
                  {
                    $picURL = "/images/Default.jpg";
                    $user1pic = '<img src="'.$picURL.'" alt="'.$guy->name.'" class="user_pic">';
                  }
                  echo '<div><a href="/profile/'.$guy->name.'">'.$user1pic.'</a><b><p>'.$guy->name.'</p></b></div>';
                }
            }
          */?>
          </a>
        </div>
      </div-->
      @yield('content')
      @include('layouts.footer')
    </div>

  </body>
</html>
