<?php

require("auth/script/sessionCheckScript.php"); // fill this with your path
$auth = checkLogin();

if (!$auth["status"]){
	$authResponse["status"] = $auth["status"];
	$authResponse["redirectUrl"] = $auth["redirectUrl"];

	echo json_encode($authResponse);
	die();
}

?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Home</title>
</head>
<body>
	HI!!
	<?php
		print_r($auth);
	?>
</body>
</html>
