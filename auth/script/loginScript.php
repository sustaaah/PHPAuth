<?php
require("config.php");
require("validateCaptcha.php");

$jsonResponse = array();
$jsonResponse["status"] = "error";

if (isset($_POST["email"], $_POST["password"], $_POST["remember"], $_POST["captcha"])) {

	if (getResponseCaptcha($_POST["captcha"])){
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

					$sessionConstructorStatus = sessionConstructor($userData["userUniqId"], $rememberSession);

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
		} catch (Exception $e) {
		}
	} else {
		$jsonResponse["cause"] = "captchaFail";
	}
} else {
	$jsonResponse["cause"] = "missingData";
}

echo json_encode($jsonResponse);
