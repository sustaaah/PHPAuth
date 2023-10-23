<?php
require("config.php");

require("sessionCheckScript.php");
$auth = checkLogin();

if ($auth["status"]) {
	$authResponse["status"] = "error";
	$authResponse["cause"] = "userLoggedIn";
	$authResponse["redirectUrl"] = redirectAfterLogin;

	echo json_encode($authResponse);
	die();
}

function generateRequestId($mail, $length = 256)
{
	$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomCharIndex = rand(0, strlen($characters) - 1);
		$randomString .= $characters[$randomCharIndex];
	}

	$output = uniqid('', true) . $randomString . md5($mail);
	return $output;
}

$response["status"] = "error";

if (isset($_POST["captcha"], $_POST["email"])) {
	$inputEmail = trim($_POST["email"]);

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
		try {
			$pdo = new PDO("mysql:host=" . dbHost . ";dbname=" . dbName, dbUsername, dbPassword);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sqlFindUser = "SELECT * FROM " . tablePrefix . "user WHERE email = :email";
			$stmtFindUser = $pdo->prepare($sqlFindUser);
			$stmtFindUser->execute(['email' => $inputEmail]);
			$num_users_found = $stmtFindUser->rowCount();

		} catch (PDOException $e) {
			// If there's an error in the connection or query, it will be caught and handled here
			error_log("\n" . __FILE__ . " : " . time() . " : " . $e, errorLogMode, errorLogPath);
			$response["cause"] = "serverError";
		}

		if ($num_users_found === 1) {
			// Retrieve the results
			$dbUser = $stmtFindUser->fetch(PDO::FETCH_ASSOC);

			try {
				$pdo->beginTransaction();

				do {
					$requestUniqId = generateRequestId($inputEmail);

					$existing_string_query = $pdo->prepare("SELECT COUNT(*) FROM " . tablePrefix . "requestForgotPassword WHERE requestUniqId = :requestUniqId");
					$existing_string_query->bindParam(':requestUniqId', $requestUniqId);
					$existing_string_query->execute();

					$count = $existing_string_query->fetchColumn();
				} while ($count > 0);

				$isValidNew = 1;
				$nowTime = time();

				$insert_query = $pdo->prepare("INSERT INTO " . tablePrefix . "requestForgotPassword (requestUniqId, userUniqId, creationTime, isValid) VALUES (:requestUniqId, :userUniqId, :creationTime, :isValid)");
				$insert_query->bindParam(':requestUniqId', $requestUniqId);
				$insert_query->bindParam(':userUniqId', $dbUser["userUniqId"]);
				$insert_query->bindParam(':creationTime', $nowTime);
				$insert_query->bindParam(':isValid', $isValidNew);
				$insert_query->execute();

				$pdo->commit();

				$mailParam["resetLink"] = urlToAuth . "forgotPassword.php?token=" . $requestUniqId;

				require("mailerScript.php");
				mailer("passwordResetLink", $dbUser["email"], $dbUser["name"], $dbUser["surname"], $mailParam);

				$response["status"] = "success";
			} catch (PDOException $e) {
				$pdo->rollBack();
				error_log("\n" . __FILE__ . " : " . time() . " : " . $e, errorLogMode, errorLogPath);
				$response["cause"] = "serverError";
				$response["debug"] = $e ->getMessage();
			}
		} elseif ($num_users_found === 0) {
			$response["cause"] = "noUsersFound";
		} else {
			error_log("\n" . __FILE__ . " : " . time() . " : " . "more than an user found with the same email: $mailInput", errorLogMode, errorLogPath);
			$response["cause"] = "serverError";

		}
	} else {
		// return error to user; they did not pass
		$response["cause"] = "captchaFail";
	}
} else {
	$response["cause"] = "missingData";
}

echo (json_encode($response));
?>