<?php

	use App\message;

	$username = stripslashes(htmlspecialchars($_GET['username']));
	$messages = Message::all();

	foreach ($messages as $message)
	{
		echo $message->username;
		echo "\\";
		echo $message->message;
		echo "\n";
  }
?>
