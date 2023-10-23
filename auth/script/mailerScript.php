<?php
require(__DIR__ . '/lib/PHPMailer/Exception.php');
require(__DIR__ . '/lib/PHPMailer/PHPMailer.php');
require(__DIR__ . '/lib/PHPMailer/SMTP.php');

/**
 * Summary of mailler
 * @param mixed $template
 * @param mixed $mailTo
 * @param mixed $nameTo
 * @param mixed $surnameTo
 * @param array $params
 * @return array
 */
function mailer($template, $mailTo, $nameTo, $surnameTo, array $params = array())
{
	require('config.php');
	$responseStatus = array();
	$responseStatus['status'] = false;

	switch ($template) {
		case "confirmAccount":
			$message = file_get_contents(__DIR__ . '/mailTemplate/confirmRegistration.html');
			$message = str_replace("$[name]", $nameTo, $message);
			$message = str_replace("$[code]", $params['confirmationCode'], $message);

			$subject = "Confirm Registration";
			break;
		case "confirmRegistrationSuccess":
			$message = file_get_contents(__DIR__ . '/mailTemplate/confirmRegistrationSuccess.html');
			$message = str_replace("$[name]", $nameTo, $message);
			$message = str_replace("$[startUrl]", $params["startUrl"], $message);

			$subject = "Account successfully confirmed!";
			break;
		case "loginNotification":
			$message = file_get_contents(__DIR__ . '/mailTemplate/loginNotification.html');
			$message = str_replace("$[name]", $nameTo, $message);
			$message = str_replace("$[ip]", $params["ip"], $message);
			$message = str_replace("$[email]", $mailTo, $message);
			$message = str_replace("$[location]", $params["location"], $message);
			$message = str_replace("$[device]", $params["device"], $message);
			// TODO finish this
			$subject = "Account Login Notification";
			break;
		case "passwordResetLink":
			$message = file_get_contents(__DIR__ . '/mailTemplate/passwordResetLink.html');
			$message = str_replace("$[name]", $nameTo, $message);
			$message = str_replace("$[resetLink]", $params["resetLink"], $message);

			$subject = "Password Reset - Reset Link";
			break;
		default:
			return $responseStatus;
			break;
	}

	try {
		$PHPMailer = new PHPMailer\PHPMailer\PHPMailer;

		$PHPMailer->isSMTP();
		$PHPMailer->Host = mailSmtpHostname;
		$PHPMailer->SMTPAuth = true;
		$PHPMailer->Username = mailSmtpUsername;
		$PHPMailer->Password = mailSmtpPassword;
		$PHPMailer->SMTPSecure = mailSmtpSecure;
		$PHPMailer->Port = mailSmtpPort;

		$PHPMailer->setFrom(mailSmtpFromMail, mailSmtpFromName);
		$PHPMailer->addAddress($mailTo, $nameTo . ' ' . $surnameTo);
		$PHPMailer->Subject = $subject;
		$PHPMailer->Body = $message;
		$PHPMailer->isHTML(true);

		if ($PHPMailer->send()) {
			$responseStatus['status'] = true;
		} else {
			error_log("\n" . __FILE__ . " : " . time() . " : " . $PHPMailer->ErrorInfo, errorLogMode, errorLogPath);
		}
	} catch (Exception $e) {
		error_log("\n" . __FILE__ . " : " . time() . " : " . $e, errorLogMode, errorLogPath);
	}

	return $responseStatus;
}