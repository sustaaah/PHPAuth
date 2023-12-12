function getUrlParam(paramToSearch) {
	var queryString = window.location.search;
	queryString = queryString.substring(1);
	var params = queryString.split("&");
	let valoreParametro = null;

	for (var i = 0; i < params.length; i++) {
		var param = params[i].split("=");
		if (param[0] === paramToSearch) {
			valoreParametro = param[1];
			break;
		}
	}

	return valoreParametro;
}

function resetPasswordFromLink(captchaToken) {
	var inputPassword = document.getElementById("passwordInput");
	var inputRepeatPassword = document.getElementById("repeatPasswordInput");

	var bannerSuccess = document.getElementById("bannerSuccess");
	var bannerError = document.getElementById("bannerError");
	var url = "https://www.sustaaah.com/login-system/auth/script/forgotPasswordScript.php"; // Replace this with your API endpoint

	// TODO let this work
	// modify button script
	//var buttonSubmit = document.getElementById("buttonSubmit");
	//var buttonLoginText = document.getElementById("buttonLoginText");
	//var buttonLoadingText = document.getElementById("buttonLoadingText");

	const dataForm = new URLSearchParams();
	dataForm.append("password", inputPassword.value);
	dataForm.append("repeatPassword", inputRepeatPassword.value);
	dataForm.append("token", getUrlParam("token"));
	dataForm.append("captcha", captchaToken);

	fetch(url, {
		method: "POST",
		headers: {
			"Content-Type": "application/x-www-form-urlencoded", // Change the Content-Type to send data as form data
		},
		body: dataForm.toString(), // Use the encoded data as the body
	})
		.then((response) => {
			if (!response.ok) {
				throw new Error("Network response was not ok");
			}
			return response.json();
		})
		.then((responseData) => {
			if (responseData["status"] === "success") {
				// successful response
				bannerSuccess.style.display = "block";

				bannerSuccess.innerHTML = "<strong>Success!</strong> Password has been changed successfully, <a class='alert-link' href='" + responseData["redirectUrl"] + "'>back to login</a>.";
			} else if (responseData["status"] === "error") {
				// error response
				bannerError.style.display = "block";

				switch (responseData["cause"]) {
					case "serverError":
						bannerSuccess.innerHTML = "<strong>Error!</strong> The server encountered an error.";
						break;
					case "captchaFail":
						bannerError.innerHTML = "<strong>Error!</strong> Your captcha response could not be verified, please try again.";
						break;
					case "missingData":
						bannerError.innerHTML = "<strong>Error!</strong> The form was not sent correctly and the server did not receive all the necessary data, please try again.";
						break;
					case "invalidToken":
						bannerError.innerHTML = "<strong>Error!</strong> Token does not appear to be valid.";
						break;
					case "expiredToken":
						bannerError.innerHTML = "<strong>Error!</strong> The token appears to have expired.";
						break;
					case "passwordNotIdentical":
						bannerError.innerHTML = "<strong>Error!</strong> Passwords are not identical.";
						break;
					case "invalidPassword":
						bannerError.innerHTML = "<strong>Error!</strong> The password must be at least 8 characters long and contain a capital, lowercase and a number.";
						break;
					default:
						bannerError.innerHTML = "<strong>Error!</strong> The response was not compiled correctly by the server and it is not possible to know the problem.";
						break;
				}
			} else {
				// logical error
				bannerError.style.display = "block";
				bannerError.innerHTML = "A logic error was found in the code";
			}
		})
		.catch((error) => {
			console.error("Error:", error);
			bannerError.style.display = "block";
			bannerError.innerHTML = "Generic error occurred";
		});
}

function initializeToken() {}
