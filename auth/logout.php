<?php
require("script/logoutScript.php");

require("script/lib/jwt/JWT.php");
require("script/lib/jwt/BeforeValidException.php");
require("script/lib/jwt/Key.php");
use Firebase\JWT\Key;
use Firebase\JWT\JWT;

$logout = logout();

if ($logout['status'] == true) {
	header("Location: " . $logout["redirectUrl"]);
} elseif ($logout['status'] == false) {
	if ($logout['cause'] == "noCookieValue") {
		header("Location: " . $logout["redirectUrl"]);
	} else { // TODO rispondere che è stato impossibile eseguire il logout

	}
} else { // TODO rispondere con un errore indefinito

}
// TODO creare html
?>
