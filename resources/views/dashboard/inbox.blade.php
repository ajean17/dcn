<@extends('layouts.master')

@section('title')
  Inbox | DCN
@endsection

<?php
  use App\Dialogue;
  use App\Friend;
  use App\User;
?>

@section('content')
  <h1>Inbox</h1>
  <hr/>
  <div class="row">
    <div class="col-2 convoList">
      <h4>Conversations</h4>
      <hr/>
      <input type="text" name="startConvo" class="startConvo" id="startConvo"
      onkeydown="if (event.keyCode == 13) inboxSearch()"
      value=""
      placeholder="Start a dialogue...(Press enter)">
      <hr/>
        <?php
          foreach($conversations as $conversation)
          {
            $checkFriend = Friend::where('user1','=',$inboxOwner->name)->where('user2','=',$conversation->user1)->where('accepted','=','1')
      			->orWhere('user1','=',$conversation->user1)->where('user2','=',$inboxOwner->name)->where('accepted','=','1')
            ->orWhere('user1','=',$inboxOwner->name)->where('user2','=',$conversation->user2)->where('accepted','=','1')
      			->orWhere('user1','=',$conversation->user2)->where('user2','=',$inboxOwner->name)->where('accepted','=','1')->first();
            //dd($checkFriend);
            if($checkFriend == "")
            {
              $guy="";
              if($conversation->user1 == $inboxOwner->name)
                $guy =  User::where('name','=',$conversation->user2)->first();
              else if($conversation->user2 == $inboxOwner->name)
                $guy =  User::where('name','=',$conversation->user1)->first();

              $user1avatar = $guy->avatar;
              $user1pic = '<img src="/uploads/user/'.$guy->name.'/images'.'/'.$user1avatar.'" alt="'.$guy->name.'" class="talk_pic">';
              if($user1avatar == NULL)
                $user1pic = '<img src="/images/Default.jpg" alt="'.$guy->name.'" class="talk_pic">';
              echo '<div id="talks">'.$user1pic.'<div class="talkData"><a href="#" onclick="return false;" onmouseup="talkingTo(\''.$guy->name.'\')">'.$guy->name.'</a></div></div><br/>';
            }
          }
        ?>
    </div>
    <div class="col-8 convoContent">
      <div id="messageBox">
        <div id="conversationHead">
          <h4 id="talkingWith">Message {{$talkTo}}</h4>
        </div>
        <div id="appearMessage">

        </div>
        <div id="inputBox">
          <input type="text" name="msginput" class="messageInput" id="messageInput"
          onkeydown="if (event.keyCode == 13) sendmsg()"
          value=""
          placeholder="Enter your message here ... (Press enter to send message)">
        </div>
      </div>
    </div>
    <div class="col-2 frndConvoList">
      <h4>Friends</h4>
      <hr/>
        <?php
          foreach($friends as $friend)
          {
            $buddy = "";
            if($friend->user1 == $inboxOwner->name)
            {
              $buddy = $friend->user2;
            }
            else if($friend->user2 == $inboxOwner->name)
            {
              $buddy = $friend->user1;
            }
            $guy =  User::where('name','=',$buddy)->first();
            $user1avatar = $guy->avatar;
            $user1pic = '<img src="/uploads/user/'.$guy->name.'/images'.'/'.$user1avatar.'" alt="'.$guy->name.'" class="im_pic">';
            if($user1avatar == NULL)
              $user1pic = '<img src="/images/Default.jpg" alt="'.$guy->name.'" class="im_pic">';
            echo '<div id="talks"><div><a href="#" onclick="return false;" onmouseup="talkingTo(\''.$guy->name.'\')">'.$guy->name.'</a></div></div><br/>';
          }
        ?>
    </div>
  </div>

@endsection

@section('javascript')
  <script>
    var talkTo = "<?php echo $talkTo;?>";
    var token = '{{Session::token()}}';
    var urlm = '{{route('message')}}';
    var urls = '{{route('search')}}';

    function talkingTo(talk)
    {
      talkTo = talk;
      $('#talkingWith').html("Message "+talkTo);
      update(talkTo);
    }

    function inboxSearch()
    {
      var $startConvo = $('#startConvo');
      var inboxSearch = $startConvo.val();
      var whoSearched = "<?php echo Auth::user()->name;?>";
      if(inboxSearch != "")
      {
        $.ajax(
        {
          method: 'POST',
          url: urls,
          data: {whoSearched: whoSearched, inboxSearch: inboxSearch, _token: token}
        }).done(function (msg)
        {
          //console.log(msg['message']);
          if(msg['message'] == "new_dialogue")
          {
            $('#dialogues').append("<li><a href='#' onclick='return false;' onmouseup='talkingTo(\"" + inboxSearch + "\")''>" + inboxSearch +"</a></li><br/>");
          }
          else
            alert(msg['message']);
        });
      }
    }

    function sendmsg()
    {
      var msginput = $('#messageInput');
      var msgarea = $('#appearMessage');
      var message = msginput.val();
      if(message != "")
      {
        var username = "<?php echo Auth::user()->name;?>";
        $.ajax(
        {
          method: 'POST',
          url: urlm,
          data: {username: username, talkTo: talkTo, message: message, _token: token}
        }).done(function (msg)
        {
          //console.log(msg['message']);
          message = escapehtml(message);
          msgarea.append("<div class=\"msgc\" style=\"margin-bottom: 30px;\"><div class=\"msg msgfrom\">"	+ message + "</div><div class=\"msgarr msgarrfrom\"></div><div class=\"msgsentby msgsentbyfrom\">" + msg['message'] + " | Sent by " + username + "</div></div>");
          msginput.val("");
        });
      }

    }

    function escapehtml(text)
    {
      return text
          .replace(/&/g, "&amp;")
          .replace(/</g, "&lt;")
          .replace(/>/g, "&gt;")
          .replace(/"/g, "&quot;")
          .replace(/'/g, "&#039;");
    }

    function update(talkTo)
    {
      var msgarea = $('#appearMessage');
      var username = "<?php echo Auth::user()->name;?>";
      var output = "";

      $.ajax(
      {
        method: 'POST',
        url: urlm,
        data: {username: username, talkTo: talkTo, action: 'update', _token: token}
      }).done(function (msg)
      {
        //console.log(msg['message']);
        var response = msg['message'].split("\n");
        var rl = response.length;
        var item = "";

        for (var i = 0; i < rl; i++)
        {
          item = response[i].split("\\")
          if (item[2] != undefined)
          {
            if (item[0] == username)
            {
              output += "<div class=\"msgc\" style=\"margin-bottom: 30px;\"> <div class=\"msg msgfrom\">" + item[2] +"</div> <div class=\"msgarr msgarrfrom\"></div> <div class=\"msgsentby msgsentbyfrom\">"+ item[3] +" | Sent by " + item[0] + "</div></div>";
            }
            else
            {
              output += "<div class=\"msgc\"> <div class=\"msg\">" + item[2] + "</div> <div class=\"msgarr\"></div> <div class=\"msgsentby\">"+ item[3] +" | Sent by " + item[0] + "</div> </div>";
            }
          }
          msgarea.html(output);
        }
      });
    }

    setInterval(function()
    {
      update(talkTo)
    }, 1000);
  </script>
@endsection
