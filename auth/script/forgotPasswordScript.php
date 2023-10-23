<?php
require("config.php");

require("sessionCheckScript.php");
$auth = checkLogin();

if ($auth["status"]) {
	$authResponse["status"] = "userLoggedIn";
	$authResponse["redirectUrl"] = redirectAfterLogin;

	echo json_encode($authResponse);
	die();
}

$response["status"] = "error";

if (isset($_POST["captcha"], $_POST["token"], $_POST["password"], $_POST["repeatPassword"])) {
	$dataCaptcha = array(
		'secret' => reCaptchaSecret,
		'response' => $_POST['captcha'],
	);
	$verifyCaptcha = curl_init();
	curl_setopt($verifyCaptcha, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
	curl_setopt($verifyCaptcha, CURLOPT_POST, true);
	curl_setopt($verifyCaptcha, CURLOPT_POSTFIELDS, http_build_query($dataCaptcha));
	curl_setopt($verifyCaptcha, CURLOPT_RETURNTRANSFER, true);
	$responseCaptcha = curl_exec($verifyCaptcha); // var_dump($responseCaptcha);
	$responseCaptcha = json_decode($responseCaptcha);

	if ($responseCaptcha->success) {
		// your success code goes here
		$requestToken = $_POST['token'];

		try {
			$pdo = new PDO("mysql:host=" . dbHost . ";dbname=" . dbName, dbUsername, dbPassword);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "SELECT * FROM " . tablePrefix . "requestForgotPassword WHERE requestUniqId = :requestUniqId";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(['requestUniqId' => $requestToken]);
			$numTokenFound = $stmt->rowCount();

			if ($numTokenFound === 1) {
				$tokenInfoDb = $stmt->fetch(PDO::FETCH_ASSOC);

				// check token validity
				if ($tokenInfoDb["isValid"] == 1) { // validToken
					if ($tokenInfoDb["creationTime"] + 900 > time()) { // not expired
						$inputPassword = $_POST["password"];
						$inputRepeatPassword = $_POST['repeatPassword'];

						if ($inputPassword === $inputRepeatPassword) { // password are identical
							require("validationScript.php");

							if (validatePassword($inputPassword)) { // password is valid
								// set the token as invalid
								$sqlInvalidate = "UPDATE " . tablePrefix . "requestForgotPassword SET isValid = :isValid WHERE requestUniqId = :requestUniqId";
								$stmtInvalidate = $pdo->prepare($sqlInvalidate);

								$invalidateToken = 0;

								$stmtInvalidate->bindParam(':isValid', $invalidateToken);
								$stmtInvalidate->bindParam(':requestUniqId', $requestToken);

								$stmtInvalidate->execute();

								// modify the password in the database
								$hashedPassword = password_hash($inputPassword, PASSWORD_BCRYPT, ['cost' => 12, 'salt' => passwordSalt]);

								$sqlModify = "UPDATE " . tablePrefix . "user SET password = :password WHERE userUniqId = :userUniqId";
								$stmtModify = $pdo->prepare($sqlModify);

								// Bind parameters
								$stmtModify->bindParam(':password', $hashedPassword);
								$stmtModify->bindParam(':userUniqId', $tokenInfoDb["userUniqId"]);

								// Execute the query
								$stmtModify->execute();

								$response["status"] = "success";
								$response["redirectUrl"] = urlToAuth . "login.php";
							} else {
								$response["cause"] = "invalidPassword";
							}
						} else {
							$response["cause"] = "passwordNotIdentical";
						}
					} else {
						$response["cause"] = "expiredToken";
					}
				} else {
					$response["cause"] = "invalidToken";
				}
			} elseif ($numTokenFound === 0) {
				$response["cause"] = "invalidToken";
			} else { // multiple token found
				error_log("\n" . __FILE__ . " : " . time() . " : multiple token found for password reset. Token:" . $_POST["token"], errorLogMode, errorLogPath);
				$response["cause"] = "serverError";
			}
		} catch (PDOException $e) {
			error_log("\n" . __FILE__ . " : " . time() . " : " . $e->getMessage(), errorLogMode, errorLogPath);
			$response["cause"] = "serverError";
		}
	} else {
		$response["cause"] = "captchaFail";
	}
} else {
	$response["cause"] = "missingData";
}

echo json_encode($response);
?>