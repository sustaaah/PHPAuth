function login(tokenCaptcha) {
	var inputEmail = document.getElementById("inputEmail");
	var inputPassword = document.getElementById("inputPassword");
	var inputRemember = document.getElementById("inputRemember");

	var bannerSuccess = document.getElementById("bannerSuccess");
	var bannerError = document.getElementById("bannerError");

	var url = "https://www.sustaaah.com/login-system/auth/script/loginScript.php"; // Replace this with your API endpoint

	// modify button script
	var buttonSubmit = document.getElementById("buttonSubmit");
	var buttonLoginText = document.getElementById("buttonLoginText");
	var buttonLoadingText = document.getElementById("buttonLoadingText");

	buttonSubmit.setAttribute("disabled", "true");
	buttonLoginText.style.display = "none";
	buttonLoadingText.style.display = "inline";

	const data = new URLSearchParams();
	data.append("email", inputEmail.value);
	data.append("password", inputPassword.value);
	data.append("remember", inputRemember.value);
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
			// Process the responseData here

			if (responseData["status"] === "success") {
				// successful response
				bannerSuccess.style.display = "block";
				bannerSuccess.innerHTML = "<strong>Success!</strong> Logging in successfully, if you have not been redirected automatically, <a class='alert-link' href='" + responseData["redirect"] + "'>click here<a>";
				window.location.href = responseData["redirect"];
			} else if (responseData["status"] === "error") {
				// error response
				bannerError.style.display = "block";

				if (responseData["cause"] === "serverError") {
					bannerError.innerHTML = "The server encountered an error";
				} else if (responseData["cause"] === "captchaFail") {
					bannerError.innerHTML = "Your captcha response could not be verified, please try again";
				} else if (responseData["cause"] === "missingData") {
					bannerError.innerHTML = "The form was not sent correctly and the server did not receive all the necessary data, please try again";
				} else if (responseData["cause"] === "mailPassWrong") {
					bannerError.innerHTML = "Mail or password wrong";
				} else {
					bannerError.innerHTML = "The response was not compiled correctly by the server and it is not possible to know the problem";
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

	// normalize button
	buttonSubmit.removeAttribute("disabled");
	buttonLoginText.style.display = "inline";
	buttonLoadingText.style.display = "none";

	return false;
}
