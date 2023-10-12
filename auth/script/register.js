var nameRegex = /^[a-zA-Z ',-]+$/u; // Allows only letters and spaces, minimum length is 2 characters
var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Basic email validation
var passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@#$%^&+=!]).{8,}$/;
var urlRegex = /^(https?:\/\/)?([\da-z.-]+)\.([a-z.]{2,6})([/\w .-]*)*\/?$/i;

var inputName = document.getElementById("inputName");
var inputSurname = document.getElementById("inputSurname");
var inputEmail = document.getElementById("inputEmail");
var inputPassword = document.getElementById("inputPassword");
var inputCheckPassword = document.getElementById("inputCheckPassword");

var bannerError = document.getElementById("bannerError");
var bannerSuccess = document.getElementById("bannerSuccess");

function validateInput() {
	// TODO compete function

	if (nameRegex.test(inputName.value)) {
	} else {
	}

	if (emailRegex.test(inputEmail.value)) {
	} else {
	}

	if (inputPassword === inputCheckPassword.value) { // both password are identical
		if (passwordRegex.test(inputPassword.value)) {
		} else {
		}
	}
	else{

	}
}

function register(tokenCaptcha) {
	const url = "https://www.sustaaah.com/login-system/auth/script/registerScript.php"; // Replace this with your API endpoint

	if (inputPassword.value == inputCheckPassword.value) {
		const data = new URLSearchParams();
		data.append("name", inputName.value);
		data.append("surname", inputSurname.value);
		data.append("email", inputEmail.value);
		data.append("password", inputPassword.value);
		data.append("passwordCheck", inputCheckPassword.value);
		data.append("captcha", tokenCaptcha);

		fetch(url, {
			method: "POST",
			headers: {
				"Content-Type": "application/x-www-form-urlencoded", // Change the Content-Type to send data as form data
			},
			body: data, // Use the encoded data as the body
		})
			.then((response) => {
				if (!response.ok) {
					throw new Error("Network response was not ok");
				}
				return response.json();
			})
			.then((responseData) => {
				// Process the responseData here
				console.log(responseData);

				if (responseData["status"] == "success") {
					// successful response
					if (urlRegex.test(responseData["redirect"])) {
						bannerSuccess.innerHTML = "Success! Your account has been created";
						window.location.href = responseData["redirect"];
					}
				} else if (responseData["status"] == "error") {
					// error response
					bannerError.style.display = "block";

					switch (responseData["cause"]) {
						case "serverError":
							bannerError.innerHTML = "The server encountered an error";
							break;
						case "existingAccount":
							bannerError.innerHTML = "An account associated with this email already exists";
							break;
						case "captchaFail":
							bannerError.innerHTML = "Your captcha response could not be verified, please try again";
							break;
						case "missingData":
							bannerError.innerHTML = "The form was not sent correctly and the server did not receive all the necessary data, please try again";
							break;
						default:
							bannerError.innerHTML = "The response was not compiled correctly by the server and it is not possible to know the problem";
							break;
					}
				} else {
					// logical error
					bannerError.innerHTML = "A logic error was found in the code";
				}
			})
			.catch((error) => {
				console.error("Error:", error);
			});
	} else {
		bannerError.style.display = "block";
		bannerError.innerHTML = "Both passwords must be identical";
	}

	return false;
}
