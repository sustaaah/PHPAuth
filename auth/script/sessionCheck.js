const urlCheck = "https://www.sustaaah.com/login-system/auth/script/sessionCheck.php";

function sessionCheck(canRedirect) {
	// Create a configuration object for the fetch request
	const requestOptions = {
		method: "POST", // Use the POST method
	};

	// Use fetch() to make the POST request
	fetch(urlCheck , requestOptions)
		.then((response) => {
			// Check if the response status is OK (status code 200)
			if (!response.ok) {
				throw new Error(`HTTP error! Status: ${response.status}`);
			}
			// Parse the response body as JSON
			return response.json();
		})
		.then((data) => {
			// Handle the response data here
			console.log(data);

			if (data.status == true) {
				return true;
			}
			else {
				if (canRedirect && data.redirectUrl !== ""){
					window.location.replace = data.redirectUrl;
				}
				
				return false;
			}
		})
		.catch((error) => {
			// Handle any errors that occurred during the fetch
			console.error("Fetch error:", error);
		});
}
