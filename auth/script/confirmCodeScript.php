<?php
require("config.php");
require("validateCaptcha.php");
require("sessionCheckScript.php");
$auth = checkLogin();

if (!$auth["status"]) {
	$authResponse["status"] = false;
	$authResponse["cause"] = "notLoggedIn";
	$authResponse["redirectUrl"] = $auth["redirectUrl"];

	echo json_encode($authResponse);
	die();
}

$jsonResponse["status"] = false;

if (getResponseCaptcha($_POST["captcha"])) { // captcha success
	// your success code goes here
	if (isset($_POST["code"])) {
		if ($auth["needConfirm"]) {
			try {
				$pdo = new PDO("mysql:host=" . dbHost . ";dbname=" . dbName, dbUsername, dbPassword);
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				$sqlGet = "SELECT name, surname, email, confirmCode FROM " . tablePrefix . "user WHERE userUniqId = :userUniqId";
				$stmtGet = $pdo->prepare($sqlGet);
				$stmtGet->execute(['userUniqId' => $auth["userUniqId"]]);
				$num_users_found = $stmtGet->rowCount();

				if ($num_users_found === 1) {
					// Retrieve the results
					$responseDbGetCode = $stmtGet->fetch(PDO::FETCH_ASSOC);

					if ($responseDbGetCode["confirmCode"] == $_POST["code"]) {
						$sqlModify = "UPDATE " . tablePrefix . "user SET confirmCode = :newValue WHERE userUniqId = :userUniqId";
						$stmtModify = $pdo->prepare($sqlModify);

						$newValue = 0;
						$stmtModify->bindParam(':userUniqId', $auth["userUniqId"]);
						$stmtModify->bindParam(':newValue', $newValue);

						$stmtModify->execute();

						$jsonResponse["status"] = true;
						$jsonResponse["redirectUrl"] = redirectAfterLogin;

						require("mailerScript.php");
						$mailParam['startUrl'] = redirectAfterLogin;
						$responseMailer = mailer("confirmRegistrationSuccess", $responseDbGetCode["email"], $responseDbGetCode["name"], $responseDbGetCode["surname"], $mailParam);

					} else { // wrong code
						$jsonResponse["cause"] = "wrongCode";
					}


				} elseif ($num_users_found === 0) { // no user find
					$json_response["cause"] = "serverError";
					error_log("\n" . __FILE__ . " : " . time() . " : " . "No user found with the specified ID. userUniqId = " . $auth["userUniqId"], errorLogMode, errorLogPath);
				} else {
					$json_response["cause"] = "serverError";
					error_log("\n" . __FILE__ . " : " . time() . " : " . "More than one user found with the same ID. Something is wrong with the database! userUniqId = " . $auth["userUniqId"], errorLogMode, errorLogPath);
				}

			} catch (PDOException $e) {
				$jsonResponse["cause"] = "serverError";
				error_log("\n" . __FILE__ . " : " . time() . " : " . $e->getMessage(), errorLogMode, errorLogPath);
			}
		} else { // the account doesn't need to be confirmed
			$jsonResponse["cause"] = "accountDontNeedConfirmation";
			$jsonResponse["redirectUrl"] = redirectAfterLogin;
		}
	} else { // code post request not set
		$jsonResponse["cause"] = "codeNotSet";
	}
} else { // hCaptcha fail
	// return error to user; they did not pass
	$jsonResponse["cause"] = "hCaptchaFail";
}

// Convertire i booleani in stringhe
foreach ($jsonResponse as &$value) {
	if (is_bool($value)) {
		$value = $value ? 'true' : 'false';
	}
}


echo json_encode($jsonResponse);
