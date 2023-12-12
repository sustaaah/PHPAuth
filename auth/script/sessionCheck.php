<?php // file for sessionCheck.js
	require("sessionCheckScript.php");

	$checkLogin = checkLogin();
if (isset($checkLogin["userUniqId"])) {
	unset($checkLogin["userUniqId"]);
}
if (isset($checkLogin["name"])) {
	unset($checkLogin["name"]);
}
if (isset($checkLogin["surname"])) {
	unset($checkLogin["surname"]);
}
if (isset($checkLogin["email"])) {
	unset($checkLogin["email"]);
}

	print(json_encode($checkLogin));
