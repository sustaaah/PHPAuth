function sendCode(tokenCaptcha) {
	const url = "https://www.sustaaah.com/login-system/auth/script/confirmCodeScript.php";

	const inputCode = document.getElementById("inputCode");

	const data = new URLSearchParams();
	data.append("code", inputCode.value);
	data.append("captcha", tokenCaptcha);

	fetch(url, {
		method: "POST",
		headers: {
			"Content-Type": "application/x-www-form-urlencoded", // Change the Content-Type to send data as form data
		},
		body: data.toString(), // Use the encoded data as the body
	})
		.then((response) => {
			if (!response.ok) {
				throw new Error("Network response was not ok");
			}
			return response.json();
		})
		.then((responseData) => {
			console.log(responseData);

			bannerSuccess = document.getElementById("bannerSuccess");
			bannerError = document.getElementById("bannerError");

			if (responseData.status == "true") {
				// success
				bannerSuccess.style.display = "block";

				bannerSuccess.innerHTML = "<strong>Success!</strong> If the redirect is not done automatically, <a  class='alert-link' href='" + responseData.redirectUrl + "'>click here</a>";
				window.location.replace = responseData.redirectUrl;
			} else if (responseData.status == "false") {
				// fail
				bannerError.style.display = "block";

				switch (responseData.cause) {
					case "wrongCode":
						bannerError.innerHTML = "<strong>Error!</strong> The entered code is incorrect.";
						break;
					case "serverError":
						bannerError.innerHTML = "<strong>Error!</strong> The server encountered an error.";
						break;
					case "codeNotSet":
						bannerError.innerHTML = "<strong>Error!</strong> The code was not sent correctly.";

						break;
					case "hCaptchaFail":
						bannerError.innerHTML = "<strong>Error!</strong> Error with the captcha.";
						break;
					case "accountDontNeedConfirmation":
						bannerError.innerHTML = "<strong>Error!</strong> Your account does not need a confirmation.";
						break;
					case "notLoggedIn":
						bannerError.innerHTML = "<strong>Error!</strong> You are not currently logged in.";
						window.location.replace = responseData.redirectUrl;
						break;
					default:
						bannerError.innerHTML = "<strong>Error!</strong> A generic error has been found.";
						break;
				}
			} else {
				// unknown fail
				bannerError.style.display = "block";
				bannerError.innerHTML = "<strong>Error!</strong> Unknown error.";
			}
		})
		.catch((error) => {
			console.error(error);
		});
}
