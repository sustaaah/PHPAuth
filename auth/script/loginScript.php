<?php
require("config.php");

$jsonResponse = array();
$jsonResponse["status"] = "error";

if (isset($_POST["email"], $_POST["password"], $_POST["remember"], $_POST["captcha"])) {
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
		try {
			$pdo = new PDO("mysql:host=" . dbHost . ";dbname=" . dbName, dbUsername, dbPassword);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql = "SELECT * FROM " . tablePrefix . "user WHERE email = :email";
			$stmt = $pdo->prepare($sql);

			$inputEmail = $_POST["email"];
			$stmt->bindParam(":email", $inputEmail);
			$stmt->execute();

			$num_users_found = $stmt->rowCount();

			if ($num_users_found === 1) {
				$userData = $stmt->fetch(PDO::FETCH_ASSOC);

				if (password_verify($_POST["password"], $userData["password"])) {
					$rememberSession = ($_POST["remember"] === "remember-me");

					$sqlLastLogin = "UPDATE " . tablePrefix . "user SET lastLogin = :lastLogin WHERE userUniqId = :userUniqId";
					$stmtLastLogin = $pdo->prepare($sqlLastLogin);
					$lastLogin = time();

					$stmtLastLogin->bindParam(':lastLogin', $lastLogin);
					$stmtLastLogin->bindParam(':userUniqId', $userData["userUniqId"]);
					$stmtLastLogin->execute();

					require("sessionConstructor.php");

					$sessionConstructorStatus = sessionConstructor($userData["id"], $userData["userUniqId"], $rememberSession);

					if ($sessionConstructorStatus["status"]) {
						if ($sessionConstructorStatus["locationIp"]["status"] == "success") {
							$mailParam["location"] = $sessionConstructorStatus["locationIp"]["city"] . ", " . $sessionConstructorStatus["locationIp"]["country"];
						} else {
							$mailParam["location"] = "undefined";
						}

						$mailParam["device"] = $_SERVER["HTTP_USER_AGENT"];
						$mailParam["ip"] = $_SERVER["REMOTE_ADDR"];
						
						require("mailerScript.php");
						$sendMailStatus = mailer("loginNotification", $userData["email"], $userData["name"], $userData["surname"], $mailParam);

						$jsonResponse["status"] = "success";
						$jsonResponse["redirect"] = redirectAfterLogin;
					} else { // can't create a new session
						$jsonResponse["cause"] = "sessionCreationError";
					}
				} else {
					$jsonResponse["cause"] = "mailPassWrong";
				}
			} else {
				$jsonResponse["cause"] = "mailPassWrong";
			}
		} catch (PDOException $e) {
			$jsonResponse["cause"] = "serverError";
			error_log("\n" . __FILE__ . " : " . time() . " : " . $e, errorLogMode, errorLogPath);
		}
	} else {
		$jsonResponse["cause"] = "captchaFail";
	}
} else {
	$jsonResponse["cause"] = "missingData";
}

echo json_encode($jsonResponse);
?>