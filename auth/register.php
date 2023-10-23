<?php
require("script/config.php");

require("script/sessionCheckScript.php");
$auth = checkLogin();

if ($auth["status"]) {
	header("Location: " . redirectAfterLogin);
	die();
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Register</title>
	<link rel="stylesheet" href="content/css/bootstrap.min.css">
</head>
<body>
	<div class="container">
		<h1>Register</h1>
		<form class="row g-3">
			<div class="alert alert-danger" id="bannerError" role="alert" style="display: none;"></div>
			<div class="alert alert-success" id="bannerSuccess" role="alert" style="display: none;"></div>

			<div class="col-md-6">
				<label for="inputName" class="form-label">Name</label>
				<input type="text" class="form-control" id="inputName">
			</div>
			<div class="col-md-6">
				<label for="inputSurname" class="form-label">Surname</label>
				<input type="text" class="form-control" id="inputSurname">
			</div>
			<div class="col-md-12">
				<label for="inputEmail" class="form-label">Email</label>
				<input type="email" class="form-control" id="inputEmail">
			</div>
			<div class="col-md-6">
				<label for="inputPassword" class="form-label">Password</label>
				<input type="password" class="form-control" id="inputPassword">
			</div>
			<div class="col-md-6">
				<label for="inputCheckPassword" class="form-label">Password</label>
				<input type="password" class="form-control" id="inputCheckPassword">
				</div>
			<div class="col-12">
				<button class="btn btn-primary g-recaptcha" data-sitekey="<?php print(reCaptchaPublic) ?>" data-callback="register">Sign in</button>
			</div>

		</form>
	</div>

	<script src="content/js/bootstrap.min.js"></script>
	<script src="https://www.google.com/recaptcha/api.js"></script>
	<script src="script/register.js"></script>
</body>

</html>