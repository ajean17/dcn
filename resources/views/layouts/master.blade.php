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
    @include('layouts.header')
    @if ($flash = session('message'))
      <div class="alert alert-success" role=alert>
        {{$flash}}
      </div>
    @endif
    <div class="container marketing">
      </br>
      </br>
      <div class="chat-sidebar">
        <div class="sidebar-name">
            <a href="javascript:register_popup('narayan-prusty', 'Narayan Prusty');">
        </br>
        </br>
        </br>
        </br>
          <?php
            use App\Friend;
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
          ?>
          </a>
        </div>
      </div>
      @yield('content')
      @include('layouts.footer')
    </div>

    <script>
                //this function can remove a array element.
                Array.remove = function(array, from, to) {
                    var rest = array.slice((to || from) + 1 || array.length);
                    array.length = from < 0 ? array.length + from : from;
                    return array.push.apply(array, rest);
                };

                //this variable represents the total number of popups can be displayed according to the viewport width
                var total_popups = 0;

                //arrays of popups ids
                var popups = [];

                //this is used to close a popup
                function close_popup(id)
                {
                    for(var iii = 0; iii < popups.length; iii++)
                    {
                        if(id == popups[iii])
                        {
                            Array.remove(popups, iii);

                            document.getElementById(id).style.display = "none";

                            calculate_popups();

                            return;
                        }
                    }
                }

                //displays the popups. Displays based on the maximum number of popups that can be displayed on the current viewport width
                function display_popups()
                {
                    var right = 220;

                    var iii = 0;
                    for(iii; iii < total_popups; iii++)
                    {
                        if(popups[iii] != undefined)
                        {
                            var element = document.getElementById(popups[iii]);
                            element.style.right = right + "px";
                            right = right + 320;
                            element.style.display = "block";
                        }
                    }

                    for(var jjj = iii; jjj < popups.length; jjj++)
                    {
                        var element = document.getElementById(popups[jjj]);
                        element.style.display = "none";
                    }
                }

                //creates markup for a new popup. Adds the id to popups array.
                function register_popup(id, name)
                {

                    for(var iii = 0; iii < popups.length; iii++)
                    {
                        //already registered. Bring it to front.
                        if(id == popups[iii])
                        {
                            Array.remove(popups, iii);

                            popups.unshift(id);

                            calculate_popups();


                            return;
                        }
                    }
                    var element = '<div class="popup-box chat-popup" id="'+ id +'">';
                    element = element + '<div class="popup-head">';
                    element = element + '<div class="popup-head-left">'+ name +'</div>';
                    element = element + '<div class="popup-head-right"><a href="javascript:close_popup(\''+ id +'\');">&#10005;</a></div>';
                    element = element + '<div style="clear: both"></div></div><div class="popup-messages"></div></div>';

                    document.getElementsByTagName("body")[0].innerHTML = document.getElementsByTagName("body")[0].innerHTML + element;

                    popups.unshift(id);

                    calculate_popups();
                }

                //calculate the total number of popups suitable and then populate the toatal_popups variable.
                function calculate_popups()
                {
                    var width = window.innerWidth;
                    if(width < 540)
                    {
                        total_popups = 0;
                    }
                    else
                    {
                        width = width - 200;
                        //320 is width of a single popup box
                        total_popups = parseInt(width/320);
                    }

                    display_popups();

                }

                //recalculate when window is loaded and also when window is resized.
                window.addEventListener("resize", calculate_popups);
                window.addEventListener("load", calculate_popups);
    </script>

  </body>
</html>
