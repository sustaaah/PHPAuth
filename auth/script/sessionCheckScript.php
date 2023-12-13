<?php
require("lib/jwt/BeforeValidException.php");
require("lib/jwt/ExpiredException.php");
require("lib/jwt/SignatureInvalidException.php");
require("lib/jwt/JWT.php");
require("lib/jwt/Key.php");

/* utilize this code to perform a correct authentication

require("auth/script/sessionCheckScript.php"); // fill this with your path
$auth = checkLogin();

if (!$auth["status"]){ 
	$authResponse["status"] = $auth["status"];
	$authResponse["redirectUrl"] = $auth["redirectUrl"];

	echo json_encode($authResponse);
	die();
}

*/

/**
 * Summary of checkLogin
 * @param bool $autoRedirect
 * @return array
 */
function checkLogin(bool $autoRedirect = false): array
{
	// require("config.php");

	$sessionData = [
		"status" => false,
		"statusUser" => false,
		"statusSession" => false
		// Inizializza le altre chiavi necessarie
	];

	if (isset($_COOKIE[cookieName])) {
		$sessionData = [];
		$sessionData["status"] = false;
		$sessionData["statusUser"] = false;
		$sessionData["statusSession"] = false;

		$cookieEncoded = $_COOKIE[cookieName];

		try {
			$cookieDecoded = Firebase\JWT\JWT::decode($cookieEncoded, new Firebase\JWT\Key(jwtSecret, 'HS256'));
			$cookieDecoded = json_decode(json_encode($cookieDecoded), true);
		} catch (Exception $e) { // can't decode jwt, invalid signature
			$sessionData["cause"] = "invalidJwt";
			error_log("\n" . __FILE__ . " : " . time() . " : " . $e, errorLogMode, errorLogPath);
		}

		try {
			// create a connection with pdo
			$pdo = new PDO("mysql:host=" . dbHost . ";dbname=" . dbName, dbUsername, dbPassword);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			/////////////////////////  session  /////////////////////////
			$sqlSession = "SELECT * FROM " . tablePrefix . "session WHERE sessionUniqId = :sessionUniqId";
			$stmtSession = $pdo->prepare($sqlSession);
			$stmtSession->bindParam(":sessionUniqId", $cookieDecoded["sessionId"]);
			$stmtSession->execute();
			$num_session_found = $stmtSession->rowCount();
			$sessionDbData = $stmtSession->fetch(PDO::FETCH_ASSOC);

			///////////////////////// user  /////////////////////////
			$sqlUser = "SELECT * FROM " . tablePrefix . "user WHERE userUniqId = :userUniqId";
			$stmtUser = $pdo->prepare($sqlUser);
			$stmtUser->bindParam(":userUniqId", $sessionDbData["userUniqId"]);
			$stmtUser->execute();
			$num_user_found = $stmtUser->rowCount();
			$userDbData = $stmtUser->fetch(PDO::FETCH_ASSOC);

			if ($num_session_found === 1 && $num_user_found === 1) {
				// check session
				if ($sessionDbData["jwtUniqId"] == $cookieDecoded["jti"]) { // the jti is correct
					if (time() < $sessionDbData["expireTime"]) { // && $sessionDbData["expireTime"] == $cookieDecoded["exp"]) { // session not expired
						if ($sessionDbData["isValid"] == 1) { // session valid
							if ($_SERVER["HTTP_USER_AGENT"] == $sessionDbData["userAgent"]) { // user agent ok
								$sessionData["statusSession"] = true;
							} else { // incorrect user agent
								$sessionData["cause"] = "userAgent";
							}
						} else { // session invalid
							$sessionData["cause"] = "invalidSession";
						}
					} else { // session expired
						$sessionData["cause"] = "expiredSession";
					}
				} else { // the jti is incorrect
					$sessionData["cause"] = "invalidJti";
				}

				// check user
				if ($userDbData["accountActive"] !== 0) { // account active
					if ($userDbData["flaggedTo"] < time()) { // account not flagged
						$sessionData["statusUser"] = true;
					} else { // account flagged
						$sessionData["cause"] = "accountFlagged";
					}
				} else { // account not active
					$sessionData["cause"] = "accountNotActive";
				}

				// check if the user has a confirmation code associated, that means that the user has to confirm their account
				if ($userDbData["confirmCode"] !== "0") {
					$sessionData["needConfirm"] = true;
					$sessionData["redirectConfirm"] = urlToAuth . "confirmCode.php";
				}

			} elseif ($num_session_found !== 1) {
				if ($num_session_found == 0) { // no session found
					$sessionData["cause"] = "noSessionFound";
					error_log("\n" . __FILE__ . " : " . time() . " : " . "no session found, sessionUniqId: " . $cookieDecoded["sessionId"], errorLogMode, errorLogPath);
				} else { // multiple session found
					$sessionData["cause"] = "multipleSessionFound";
					error_log("\n" . __FILE__ . " : " . time() . " : " . "multiple session found, sessionUniqId: " . $cookieDecoded["sessionId"], errorLogMode, errorLogPath);
				}
			} elseif ($num_user_found !== 1) {
				if ($num_user_found == 0) { // no user found
					$sessionData["cause"] = "noUserFound";
					error_log("\n" . __FILE__ . " : " . time() . " : " . "no user found, userUniqId: " . $sessionDbData["userUniqId"], errorLogMode, errorLogPath);
				} else { // multiple session found
					$sessionData["cause"] = "multipleUserFound";
					error_log("\n" . __FILE__ . " : " . time() . " : " . "multiple user found, userUniqId: " . $sessionDbData["userUniqId"], errorLogMode, errorLogPath);
				}
			} else {
				$sessionData["cause"] = "serverError";
			}
		} catch (PDOException $e) { // query or connection error
			$sessionData["cause"] = "serverError";
			error_log("\n" . __FILE__ . " : " . time() . " : " . $e, errorLogMode, errorLogPath);
		}
	} else { // cookie non set
		$sessionData["cause"] = "cookieNotSet";
		$sessionData["redirectUrl"] = redirectAfterLogout;
	}

	// if all is good, return the session data
	if ($sessionData["statusSession"] && $sessionData["statusUser"]) {
		// +++all good+++
		$sessionData["status"] = true;

		$sessionData["userUniqId"] = $userDbData["userUniqId"];
		$sessionData["name"] = $userDbData["name"];
		$sessionData["surname"] = $userDbData["surname"];
		$sessionData["email"] = $userDbData["email"];
	} elseif (!$sessionData["status"] && isset($_COOKIE[cookieName]) && $num_session_found === 1 && $num_user_found === 1) {
		require("logoutScript.php");

		$logoutResponse = logout();
		if ($logoutResponse['status']) {
			$sessionData["logout"] = true;
		} else {
			$sessionData["logout"] = false;
		}

		$sessionData["redirectUrl"] = redirectAfterLogout;

		if ($autoRedirect) {
			header("Location: " . $sessionData["redirectUrl"]);

			$pdo = null;

			die();
		}
	}

	$pdo = null;

	return $sessionData;
}
