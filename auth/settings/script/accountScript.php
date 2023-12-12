<?php
require("../../script/sessionCheckScript.php");

$auth = checkLogin();

if (!$auth["status"]){
	$authResponse["status"] = "notLoggedIn";
	$authResponse["redirectUrl"] = $auth["redirectUrl"];

	echo json_encode($authResponse);
	die();
}

function getInfo(array $data): array
{
	return [
		"status" => "success",
		"name" => $data["name"],
		"surname" => $data["surname"],
		"email" => $data["email"]
	];
}

if(isset($_POST["action"])){
	if ($_POST["action"] == "getInfo"){
		$response = getInfo($auth);
	}
	elseif ($_POST["action"] == "modify"){
		require("../../script/config.php");
		require("../../script/validateCaptcha.php");
		require("../../script/validationScript.php");

		$response["status"] = "error";

		if (isset($_POST['name'], $_POST["surname"], $_POST["email"], $_POST["captcha"])){
			if (getResponseCaptcha($_POST["captcha"])){
				$inputName = $_POST["name"];
				$inputSurname = $_POST["surname"];
				$inputEmail = $_POST["email"];

				if (validateName($inputName) && validateName($inputSurname) && validateEmail($inputEmail)){
					try {
						$pdo = new PDO("mysql:host=" . dbHost . ";dbname=" . dbName, dbUsername, dbPassword);
						$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

						if ($inputEmail !== $auth["email"]){
							// Prepare the query with a prepared statement using the placeholder :id
							$sql = "SELECT * FROM " . tablePrefix . "user WHERE email = :email";

							$stmt = $pdo->prepare($sql);

							$stmt->execute(['email' => $inputEmail]);

							$num_users_found = $stmt->rowCount();

							if ($num_users_found === 0) {
								// TODO execute the query


							} elseif ($num_users_found === 1) {
								// TODO report error to user

							} else {
								error_log("\n" . __FILE__ . " : " . time() . " : " . "more than an user found with the same email: $inputEmail", errorLogMode, errorLogPath);
								$response["cause"] = "serverError";
							}



						}

						// SQL query with a prepared statement
						$sql = "UPDATE " . tablePrefix . "user SET name = :name, surname = :surname, email = :email WHERE userUniqId = :userUniqId";
						$stmt = $pdo->prepare($sql);

						// Bind parameters
						$stmt->bindParam(':name', $inputName);
						$stmt->bindParam(':surname', $inputSurname);

						// TODO put this query into the if statement
						$stmt->bindParam(':email', $inputEmail);

						$stmt->bindParam(':userUniqId', $auth["userUniqId"]);

						// Execute the query
						$stmt->execute();

						$response["status"] = "success";
					} catch (PDOException $e) {
						$response["cause"] = "serverError";
						error_log("\n" . __FILE__ . " : " . time() . " : " . $e->getMessage(), errorLogMode, errorLogPath);
					}
				}
				else{
					$response["cause"] = "validationError";
				}
			}
			else{
				$response["cause"] = "captchaFail";
			}
		}
		else{
			$response["cause"] = "missingData";
		}


	}
}
else{
	$response["status"] = "error";
	$response["cause"] = "undefinedAction";
}

echo json_encode($response);
