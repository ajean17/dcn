<?php

	use App\message;

	$username = stripcslashes(htmlspecialchars($_GET['username']));
	$message = stripslashes(htmlspecialchars($_GET['message']));

	echo $username;
	echo $message;

	if ($message == "" || $username == "")
	{
		die();
	}

	Message::create([
		'username' => $username,
		'message' => $message
	]);

?>
