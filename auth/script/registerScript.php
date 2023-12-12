<?php
require("config.php");
require("validateCaptcha.php");

/**
 * @param string $mail
 * @param int $length
 * @return string
 */
function generateUniqId(string $mail, int $length = 128): string
{
	$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomCharIndex = rand(0, strlen($characters) - 1);
		$randomString .= $characters[$randomCharIndex];
	}

    return time() . ".u." . $randomString . "." . md5($mail);
}

/**
 * @param string $name
 * @param string $surname
 * @param string $email
 * @param string $password
 * @return array
 */
function validateAll(string $name, string $surname, string $email, string $password): array
{
	require("validationScript.php");

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

	if (getResponseCaptcha($_POST["captcha"])){
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

					// check if the user has a unique id
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
						sessionConstructor($userUniqId, $rememberSession);

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
			} catch (Exception $e) {
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
