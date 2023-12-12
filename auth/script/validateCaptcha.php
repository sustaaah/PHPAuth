<?php
/* Usage

    $responseCaptcha = getResponseCaptcha($_POST["captcha"]);

    if ($responseCaptcha->success) {
		// success code here
	}

*/

/**
 * @param string $captchaToken
 * @return bool
 */
function getResponseCaptcha(string $captchaToken): bool
{
    $dataCaptcha = [
        'secret' => reCaptchaSecret,
        'response' => $captchaToken,
    ];
    $verifyCaptcha = curl_init();
    curl_setopt($verifyCaptcha, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($verifyCaptcha, CURLOPT_POST, true);
    curl_setopt($verifyCaptcha, CURLOPT_POSTFIELDS, http_build_query($dataCaptcha));
    curl_setopt($verifyCaptcha, CURLOPT_RETURNTRANSFER, true);
    $responseCaptcha = curl_exec($verifyCaptcha); // var_dump($responseCaptcha);
    $responseCaptcha = json_decode($responseCaptcha);

	if ($responseCaptcha->success){
		return true;
	}
	else{
		return false;
	}
}