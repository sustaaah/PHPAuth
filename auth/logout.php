<?php
require("script/logoutScript.php");

require("script/lib/jwt/JWT.php");
require("script/lib/jwt/BeforeValidException.php");
require("script/lib/jwt/Key.php");

$logout = logout();

if ($logout['status']) {
	header("Location: " . $logout["redirectUrl"]);
	print("Logged out");
} elseif (!$logout['status']) {
	if ($logout['cause'] == "noCookieValue") {
		header("Location: " . $logout["redirectUrl"]);
		print("no cookie value");
	} else { // TODO rispondere che è stato impossibile eseguire il logout
		print("impossible to log out");
		print_r($logout);

	}
} else { // TODO rispondere con un errore indefinito
	print("logical error");

}
// TODO creare html
