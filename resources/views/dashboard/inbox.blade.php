<?php
  setcookie("messengerUname",Auth::user()->name,time()+60*60*24,"/");
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Messenger</title>
    @include('layouts.style');
  </head>
  <body onload="update();">
    @include('layouts.header')
		<div class="msg-container">
      </br>
			<div class="header">Messenger</div>
      </br>
			<div class="msg-area" id="msg-area"></div>
			<div class="bottom"><input type="text" name="msginput" class="msginput" id="msginput"
        onkeydown="if (event.keyCode == 13) sendmsg()" value=""
        placeholder="Enter your message here ... (Press enter to send message)">
      </div>
		</div>
    @include('layouts.footer')
  </body>
</html>
