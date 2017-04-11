<@extends('layouts.master')

@section('title')
  Inbox | DCN
@endsection

<?php
  use App\Conversation;
  use App\Dialogue;

  if(isset($_GET['focus']))
  {
    $talkTo = stripcslashes(htmlspecialchars($_GET['focus']));
  }
  else
  {
    $mostRecent = "";
    $mostRecent1 = Dialogue::where('user1','=',$inboxOwner->name)->max('lastMessage');
    $mostRecent2 = Dialogue::where('user2','=',$inboxOwner->name)->max('lastMessage');
    if($mostRecent1 == "" && $mostRecent2 == "")
    {
      $talkTo = "";
    }
    else
    {
      if($mostRecent1 >= $mostRecent2)
      {
        $mostRecent = $mostRecent1;
      }
      else if ($mostRecent1 <= $mostRecent2)
      {
        $mostRecent = $mostRecent2;
      }
      //echo $mostRecent;
      $currentDialogue = Dialogue::where('lastMessage','=',$mostRecent)->first();

      if($currentDialogue->user1 == $inboxOwner->name)
      {
        $talkTo = $currentDialogue->user2;
      }
      else if($currentDialogue->user2 == $inboxOwner->name)
      {
        $talkTo = $currentDialogue->user1;
      }
    }
  }


  $conversations = Dialogue::where('user1','=',$inboxOwner->name)
  ->where('user2','!=',$inboxOwner->name)
  ->orWhere('user2','=',$inboxOwner->name)
  ->where('user1','!=',$inboxOwner->name)->get();

?>

@section('content')
  <h1>Inbox</h1>
  <div class="row">
    <div class="col-2 convoList">
      <h4>Conversations</h4>
      <input type="text" name="startConvo" class="startConvo" id="startConvo"
      onkeydown="if (event.keyCode == 13) inboxSearch()"
      value=""
      placeholder="Start a dialogue...(Press enter)">
      <ul id="dialogues">
        <?php
          foreach($conversations as $conversation)
          {
            if($conversation->user1 == $inboxOwner->name)
            {
              echo "<li><a href='#' onclick='return false;' onmouseup='talkingTo(\"".$conversation->user2."\")'>".$conversation->user2."</a></li><br/>";
            }
            if($conversation->user2 == $inboxOwner->name)
            {
              echo "<li><a href='#' onclick='return false;' onmouseup='talkingTo(\"".$conversation->user1."\")'>".$conversation->user1."</a></li><br/>";
            }

            //echo $conversation->user1;
          }
          //<li id="newConvo"></li>
        ?>
      </ul>
    </div>
    <div class="col-10 convoContent">
      <div id="messageBox">
        <div id="conversationHead">
          <h4> Message {{$talkTo}}</h4>
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
      $('#conversationHead').html("Message"+talkTo);
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
          msgarea.append("<div class=\"msgc\" style=\"margin-bottom: 30px;\"><div class=\"msg msgfrom\">"	+ message + "</div><div class=\"msgarr msgarrfrom\"></div><div class=\"msgsentby msgsentbyfrom\">Sent by " + username + "</div></div>");
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
              output += "<div class=\"msgc\" style=\"margin-bottom: 30px;\"> <div class=\"msg msgfrom\">" + item[2] + "</div> <div class=\"msgarr msgarrfrom\"></div> <div class=\"msgsentby msgsentbyfrom\">Sent by " + item[0] + "</div> </div>";
            }
            else
            {
              output += "<div class=\"msgc\"> <div class=\"msg\">" + item[2] + "</div> <div class=\"msgarr\"></div> <div class=\"msgsentby\">Sent by " + item[0] + "</div> </div>";
            }
          }
          msgarea.html(output);
        }
      });
    }

    setInterval(function()
    {
      update(talkTo)
    }, 2500);
  </script>
@endsection
