<?php
function logout(): array
{
	require("config.php");

	$response = array();
	$response["status"] = false;

	if (isset($_COOKIE[cookieName]) && $_COOKIE[cookieName] != '') {
		$cookieEncoded = $_COOKIE[cookieName];
		try {
			$cookieDecoded = Firebase\JWT\JWT::decode($cookieEncoded, new Firebase\JWT\Key(jwtSecret, 'HS256'));
		} catch (Exception $jwtException) { // can't decode jwt, invalid signature
			$sessionData["cause"] = "invalidJwt";
			error_log("\n" . __FILE__ . " : " . time() . " : " . $jwtException, errorLogMode, errorLogPath);
		}

		$cookieDecoded = json_decode(json_encode($cookieDecoded), true);

		setcookie(cookieName, "", time() - 3600, cookiePath, cookieDomain, cookieSecure, cookieHttpOnly);

		// Create a PDO instance
		try {
			$pdo = new PDO("mysql:host=" . dbHost . ";dbname=" . dbName, dbUsername, dbPassword);
			// Set the PDO error mode to exception
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			$response["cause"] = "dbError";
			error_log("\n" . __FILE__ . " : " . time() . " : " . $e, errorLogMode, errorLogPath);
		}

		$newValue = 0;

		// Prepare and execute the update statement
		try {
			// TODO check jti
			$stmt = $pdo->prepare("UPDATE " . tablePrefix . "session SET isValid = :loggedOut WHERE sessionUniqId = :sessionUniqId");
			$stmt->bindParam(':loggedOut', $newValue);
			$stmt->bindParam(':sessionUniqId', $cookieDecoded['sessionId']);
			$stmt->execute();

			$response["status"] = true;
			$response["redirectUrl"] = redirectAfterLogout;

		} catch (PDOException $e) {
			$response["cause"] = "dbError";
			error_log("\n" . __FILE__ . " : " . time() . " : " . $e, errorLogMode, errorLogPath);
		}
	} else {
		$response["cause"] = "noCookieValue";
		$response["redirectUrl"] = redirectAfterLogout;
	}

	$pdo = null;
	return $response;
}
