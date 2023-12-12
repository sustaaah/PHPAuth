<?php
require("lib/jwt/JWT.php");
require("lib/jwt/BeforeValidException.php");

/**
 * Summary of localizeIp
 * @param string $ip
 * @return mixed
 */
function localizeIp(string $ip): mixed
{
	require("config.php");

	$url = "http://ip-api.com/json/$ip?fields=16895";

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($ch);
	curl_close($ch);

	if ($response !== false) {
		$data = json_decode($response, true); // Converti in un array associativo

		if ($data['status'] == "success") {
			return $data;
		} else {
			error_log("\n" . __FILE__ . " : " . time() . " : Can't localize ip: " . $data['message'], errorLogMode, errorLogPath);
			return $data['status'] == "error";
		}
	} else {
		error_log("\n" . __FILE__ . " : " . time() . " : cURL request failed: " . curl_error($ch), errorLogMode, errorLogPath);
		return false;
	}
}

/**
 * @param string $userId
 * @param string $userUniqId
 * @param bool $remember
 * @return bool|array
 * @throws Exception
 */
// TODo fix first parameter
function sessionConstructor(string $userUniqId, bool $remember)
{
	require("config.php");

	try {
		$pdo = new PDO("mysql:host=" . dbHost . ";dbname=" . dbName, dbUsername, dbPassword);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$sql = "INSERT INTO " . tablePrefix . "session (jwtUniqId, sessionUniqId, loginTime, expireTime, isValid, userAgent, ip, userUniqId, countryName, countryCode, regionName, regionCode, city, latitude, longitude, timezone, zipCode) VALUES (:jwtUniqId, :sessionUniqId, :loginTime, :expireTime, :isValid, :userAgent, :ip, :userUniqId, :countryName, :countryCode, :regionName, :regionCode, :city, :latitude, :longitude, :timezone, :zipCode)";

		$stmt = $pdo->prepare($sql);

		// creating variables
		$jti = hash("sha256", time() . $userUniqId . bin2hex(random_bytes(40)));
		$sessionUniqId = generateRandomString();
		$loginTime = time();

		if ($remember) {
			$cookieExpire = time() + cookieExpire;
			$expireTime = $loginTime + sessionExpire;
		} else {
			$cookieExpire = 0;
			$expireTime = $loginTime + sessionExpireNotRememberMe;
		}

		$isValid = 1;
		$userAgent = $_SERVER["HTTP_USER_AGENT"];
		$ip = $_SERVER["REMOTE_ADDR"];

		$locationIp = localizeIp($_SERVER['REMOTE_ADDR']);

		if ($locationIp["status"] != "success") {
			$locationIp["country"] = $locationIp["countryCode"] = $locationIp["regionName"] = $locationIp["region"] = $locationIp["city"] = $locationIp["zip"] = $locationIp["lat"] = $locationIp["lon"] = $locationIp["timezone"] = "";
		}

		$response["locationIp"] = $locationIp;

		$stmt->bindParam(':countryName', $locationIp["country"]);
		$stmt->bindParam(':countryCode', $locationIp["countryCode"]);
		$stmt->bindParam(':regionName', $locationIp["regionName"]);
		$stmt->bindParam(':regionCode', $locationIp["region"]);
		$stmt->bindParam(':city', $locationIp["city"]);
		$stmt->bindParam(':zipCode', $locationIp["zip"]);
		$stmt->bindParam(':latitude', $locationIp["lat"]);
		$stmt->bindParam(':longitude', $locationIp["lon"]);
		$stmt->bindParam(':timezone', $locationIp["timezone"]);

		$stmt->bindParam(':jwtUniqId', $jti);
		$stmt->bindParam(':userUniqId', $userUniqId);
		$stmt->bindParam(':sessionUniqId', $sessionUniqId);
		$stmt->bindParam(':loginTime', $loginTime);
		$stmt->bindParam(':expireTime', $expireTime);
		$stmt->bindParam(':isValid', $isValid);
		$stmt->bindParam(':userAgent', $userAgent);
		$stmt->bindParam(':ip', $ip);

		$stmt->execute();
	} catch (PDOException $e) {
		//print($e);
		error_log("\n" . __FILE__ . " : " . time() . " : " . $e, errorLogMode, errorLogPath);
		return false;
	}

	$payloadJwt = [
		// jwt data
		"jti" => $jti,
		"iat" => time(),
		"exp" => time() + sessionExpire,
		// session data
		"sessionId" => $sessionUniqId
	];

	$jwt = Firebase\JWT\JWT::encode($payloadJwt, jwtSecret, 'HS256');

	setcookie(cookieName, $jwt, $cookieExpire, cookiePath, cookieDomain, cookieSecure, cookieHttpOnly);

	$pdo = null;

	$response["status"] = true;
	return $response;
}

/**
 * Summary of generateRandomString
 * @param int $length
 * @return string
 */
function generateRandomString(int $length = 128): string
{
	$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomCharIndex = rand(0, strlen($characters) - 1);
		$randomString .= $characters[$randomCharIndex];
	}

	return time() . "." . $randomString . "." . uniqid();
}

