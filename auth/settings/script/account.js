const inputName = document.getElementById("inputName");
const inputSurname = document.getElementById("inputSurname");
const inputEmail = document.getElementById("inputEmail");

const bannerError = document.getElementById("bannerError");
const bannerSuccess = document.getElementById("bannerSuccess");

// TODO add validation script

function getUserInformation(){
    const url = "https://www.sustaaah.com/login-system/auth/settings/script/accountScript.php"; // Replace this with your API endpoint

    const data = new URLSearchParams();
    data.append("action", "getInfo");

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
            if (responseData["status"] === "success") {
                // successful response
                inputEmail.value = responseData["email"];
                inputName.value = responseData["name"];
                inputSurname.value = responseData["surname"];
            } else if (responseData["status"] === "error") {
                // error response
                bannerError.style.display = "block";

                switch (responseData["cause"]) {
                    case "serverError":
                        bannerError.innerHTML = "The server encountered an error";
                        break;
                    case "invalidAction":
                        bannerError.innerHTML = "Impossible to perform the requested action";
                        break;
                    case "notLoggedIn":
                        bannerError.innerHTML = "You're not logged in!";
                        window.location.href = responseData["redirectUrl"];
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

    return false;
}

function modifyAccount(captchaToken) {
    const url = "https://www.sustaaah.com/login-system/auth/settings/script/accountScript.php"; // Replace this with your API endpoint

    const data = new URLSearchParams();
    data.append("name", inputName.value);
    data.append("surname", inputSurname.value);
    data.append("email", inputEmail.value);
    data.append("captcha", captchaToken);
    data.append("action", "modify");

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
            if (responseData["status"] === "success") {
                // successful response
                bannerSuccess.style.display = "block";
                bannerSuccess.innerHTML = "Success! Your account information has been changed";
            } else if (responseData["status"] === "error") {
                // error response
                bannerError.style.display = "block";

                switch (responseData["cause"]) {
                    case "serverError":
                        bannerError.innerHTML = "The server encountered an error";
                        break;
                    case "validationError":
                        bannerError.innerHTML = "The submitted data is not valid";
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
                bannerError.style.display = "block";
                bannerError.innerHTML = "A logic error was found in the code";
            }
        })
        .catch((error) => {
            console.error("Error:", error);
        });

    return true;
}