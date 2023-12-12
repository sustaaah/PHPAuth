<?php
require("script/config.php");

require("script/sessionCheckScript.php");
$auth = checkLogin();

if ($auth["status"]) {
	header("Location: " . redirectAfterLogin);
	die();
}

?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Create a new password</title>
	<link href="content/css/bootstrap.min.css" rel="stylesheet">
	<style>
		body {
			height: 100vh;
		}

		.form-signin {
			max-width: 330px;
			padding: 1rem;
		}

		.form-signin .form-floating:focus-within {
			z-index: 2;
		}

		#passwordInput {
			margin-bottom: -1px;
			border-bottom-right-radius: 0;
			border-bottom-left-radius: 0;
		}

		#repeatPasswordInput {
			margin-bottom: 10px;
			border-top-left-radius: 0;
			border-top-right-radius: 0;
		}

		.grecaptcha-badge {
			box-shadow: none !important;
			border-radius: 3px !important;
		}
	</style>
</head>
<body class="d-flex align-items-center py-4 ">
	<main class="form-signin w-100 m-auto border border-light-subtle rounded bg-secondary-subtle">
		<form>
			<h1 class="h3 mb-3 fw-normal">Create a new password</h1>
			<div class="alert alert-success" role="alert" id="bannerSuccess" style="display: none;"></div>
			<div class="alert alert-danger" role="alert" id="bannerError" style="display: none;"></div>
			<div class="form-floating">
				<input type="password" class="form-control" id="passwordInput" placeholder="Password">
				<label for="passwordInput">Password</label>
			</div>
			<div class="form-floating">
				<input type="password" class="form-control" id="repeatPasswordInput" placeholder="Repeat password">
				<label for="repeatPasswordInput">Repeat password</label>
			</div>
			<button class="btn btn-primary w-100 my-3 py-2 g-recaptcha" type="submit"
				data-sitekey="<?php echo reCaptchaPublic ?>" data-theme="dark"
				data-callback="resetPasswordFromLink">Send request</button>
			<p class="text-body-secondary fst-italic fs-6 mb-0">We will send an email with the instructions to this
				email address</p>
		</form>

	</main>
	<script src="script/forgotPassword.js"></script>
	<script src="content/js/bootstrap.bundle.min.js"></script>
	<script src="https://www.google.com/recaptcha/api.js"></script>
</body>

</html>