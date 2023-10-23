function requestForgotPassword(captchaToken) {
	var inputEmail = document.getElementById("inputEmail");

	var bannerSuccess = document.getElementById("bannerSuccess");
	var bannerError = document.getElementById("bannerError");
	var url = "https://www.sustaaah.com/login-system/auth/script/requestForgotPasswordScript.php"; // Replace this with your API endpoint

	// TODO let this work
	// modify button script
	//var buttonSubmit = document.getElementById("buttonSubmit");
	//var buttonLoginText = document.getElementById("buttonLoginText");
	//var buttonLoadingText = document.getElementById("buttonLoadingText");

	const dataForm = new URLSearchParams();
	dataForm.append("email", inputEmail.value);
	dataForm.append("captcha", captchaToken);

	fetch(url, {
		method: "POST",
		headers: {
			"Content-Type": "application/x-www-form-urlencoded",
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
				bannerSuccess.innerHTML = "<strong>Success!</strong> We sent an email with the necessary instructions.";
				document.getElementById("formRequest").disabled = true;
				document.getElementById("formRequest").readOnly = true;
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
					case "missinData":
						bannerError.innerHTML = "<strong>Error!</strong> The form was not sent correctly and the server did not receive all the necessary data, please try again.";
						break;
					case "noUsersFound":
						bannerError.innerHTML = "<strong>Error!</strong> No user was found with this email.";
						break;
					case "userLoggedIn":
						bannerError.innerHTML = "<strong>Error!</strong> You can not be logged in to request a password change.";
						window.location.replace = responseData.redirectUrl;
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