<?php

require("config.php");

/**
 * Summary of generateUniqId
 * @param mixed $mail
 * @param mixed $length
 * @return string
 */
function generateUniqId($mail, $length = 128)
{
	$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomCharIndex = rand(0, strlen($characters) - 1);
		$randomString .= $characters[$randomCharIndex];
	}

	$output = time() . ".u." . $randomString . "." . md5($mail);
	return $output;
}

/**
 * Summary of validateName
 * @param mixed $name
 * @return bool
 */
function validateName($name)
{
	$pattern = "/^[a-zA-Z ',-]+$/u";
	$name = trim($name);

	if (preg_match($pattern, $name)) {
		return true;
	} else {
		return false;
	}
}

/**
 * Summary of validateEmail
 * @param mixed $email
 * @return bool
 */
function validateEmail($email)
{
	$email = trim($email);

	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return true;
	} else {
		return false;
	}
}

/**
 * Summary of validatePassword
 * @param mixed $password
 * @return bool
 */
function validatePassword($password)
{
	// $pattern = '/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,16}$/';

	/*
	   if (preg_match($pattern, $password)) {
		   return true;
	   } else {
		   return false;
	   }
	   */

	$minLength = 8;

	if (strlen($password) < $minLength) {
		return false;
	}

	if (!preg_match('/[a-z]/', $password)) {
		return false;
	}

	if (!preg_match('/[A-Z]/', $password)) {
		return false;
	}

	if (!preg_match('/[0-9]/', $password)) {
		return false;
	}

	return true;
}

/**
 * Summary of validateAll
 * @param mixed $name
 * @param mixed $surname
 * @param mixed $email
 * @param mixed $password
 * @return array
 */
function validateAll($name, $surname, $email, $password)
{
	$validationName = validateName($name);
	$validationSurname = validateName($surname);
	$validateEmail = validateEmail($email);
	$validationPassword = validatePassword($password);

	if ($validationName && $validationSurname && $validateEmail && $validationPassword) {
		$response["status"] = true;
	} else {
		$response["status"] = false;
	}

	$response["name"] = $validationName;
	$response["surname"] = $validationSurname;
	$response["email"] = $validateEmail;
	$response["password"] = $validationPassword;

	return $response;
}

// initialize response array
$jsonResponse = array(); // status, cause(only if error), redirect(can be empty)
$jsonResponse['status'] = "error";

if (isset($_POST["name"]) && isset($_POST["surname"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["captcha"])) {
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
		$inputName = trim($_POST['name']);
		$inputSurname = trim($_POST['surname']);
		$inputEmail = trim($_POST['email']);
		$inputPassword = $_POST['password'];
		$inputCheckPassword = $_POST["passwordCheck"];

		$validationResponse = validateAll($inputName, $inputSurname, $inputEmail, $inputPassword);

		if ($inputPassword === $inputCheckPassword && $validationResponse["status"]) {

			try { // check if account already exists
				$pdo = new PDO("mysql:host=" . dbHost . ";dbname=" . dbName, dbUsername, dbPassword);
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$sqlCheck = "SELECT COUNT(*) as total FROM " . tablePrefix . "user WHERE email = :email";
				$stmtCheck = $pdo->prepare($sqlCheck);
				$stmtCheck->bindParam(':email', $inputEmail);
				$stmtCheck->execute();
				$result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
				$rowCountAccount = $result['total'];


				if ($rowCountAccount == 0) { // no account found, create a new account
					$sqlNew = "INSERT INTO " . tablePrefix . "user (name, surname, email, password, userUniqId, tfaActive, accountActive, flaggedTo, confirmCode, lastLogin, lastPasswordChange, loginAttempt, registrationDate) VALUES (:name, :surname, :email, :password, :userUniqId, :tfaActive, :accountActive, :flaggedTo, :confirmCode, :lastLogin, :lastPasswordChange, :loginAttempt, :registrationDate)";
					$stmtNew = $pdo->prepare($sqlNew);

					// Genera l'hash della password utilizzando l'algoritmo bcrypt
					$hashedPassword = password_hash($_POST["password"], PASSWORD_BCRYPT, ['cost' => 12, 'salt' => passwordSalt]);

					$stmtNew->bindParam(':name', $inputName);
					$stmtNew->bindParam(':surname', $inputSurname);
					$stmtNew->bindParam(':email', $inputEmail);
					$stmtNew->bindParam(':password', $hashedPassword);

					// generated data
					$userUniqId = generateUniqId($inputEmail);
					$tfaActive = "0";
					$accountActive = "1";
					$flaggedTo = "0";
					$confirmCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
					$lastLogin = "0";
					$lastPasswordChange = "0";
					$loginAttempt = "0";
					$registrationDate = time();

					$stmtNew->bindParam(':userUniqId', $userUniqId);
					$stmtNew->bindParam(':tfaActive', $tfaActive);
					$stmtNew->bindParam(':accountActive', $accountActive);
					$stmtNew->bindParam(':flaggedTo', $flaggedTo);
					$stmtNew->bindParam(':confirmCode', $confirmCode);
					$stmtNew->bindParam(':lastLogin', $lastLogin);
					$stmtNew->bindParam(':lastPasswordChange', $lastPasswordChange);
					$stmtNew->bindParam(':loginAttempt', $loginAttempt);
					$stmtNew->bindParam(':registrationDate', $registrationDate);

					$stmtNew->execute();

					// check if the user has an unique id
					$sqlCheck = "SELECT * FROM " . tablePrefix . "user WHERE userUniqId = :userUniqId";
					$stmtCheck = $pdo->prepare($sqlCheck);
					$stmtCheck->execute(['userUniqId' => $userUniqId]);
					$numUserFoundCheck = $stmtCheck->rowCount();

					if ($numUserFoundCheck === 1) {
						// Retrieve the results
						$resultCheck = $stmtCheck->fetch(PDO::FETCH_ASSOC);

						require("sessionConstructor.php");
						require("mailerScript.php");

						$mailParam = ["confirmationCode" => $confirmCode];
						$mailConfirmAccount = mailer("confirmAccount", $inputEmail, $inputName, $inputSurname, $mailParam);

						$jsonResponse["mailSend"] = $mailConfirmAccount["status"];

						$rememberSession = true;
						// TODO aggiungi un handling della risposta
						sessionConstructor($resultCheck['id'], $userUniqId, $rememberSession);

						//
						// add here your script for the registration (es. create a table for the user)
						//

						$jsonResponse['status'] = "success";
						$jsonResponse['redirect'] = urlToAuth . "confirmCode.php";
					} elseif ($numUserFoundCheck === 0) { // no user found
						$jsonResponse['cause'] = "userRegistrationFailure";
					} else { // more than one user found
						$jsonResponse['cause'] = "serverError";
					}
				} else { // existing account
					$jsonResponse['cause'] = "existingAccount";
				}
			} catch (PDOException $e) { // connection error
				$jsonResponse['cause'] = "serverError";
				error_log("\n" . __FILE__ . " : " . time() . " : " . $e, errorLogMode, errorLogPath);
			}
		} else {
			$jsonResponse['cause'] = "validationError";
			$jsonResponse["validationResult"] = $validationResponse;
		}


	} else { // captcha failed
		$jsonResponse['status'] = "error";
		$jsonResponse['cause'] = "captchaFail";
	}
} else { // missing post data
	$jsonResponse['status'] = "error";
	$jsonResponse['cause'] = "missingData";
}

$pdo = null;

echo json_encode($jsonResponse);
?>