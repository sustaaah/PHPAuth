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
	<title>Forgot password</title>
	<link href="content/css/bootstrap.min.css" rel="stylesheet">
	<style>
        .nav-scroller .nav {
			display: flex;
			flex-wrap: nowrap;
			padding-bottom: 1rem;
			margin-top: -1px;
			overflow-x: auto;
			text-align: center;
			white-space: nowrap;
			-webkit-overflow-scrolling: touch;
		}

		html,
		body {
			height: 100%;
		}

		.form-signin {
			max-width: 330px;
			padding: 1rem;
		}

		.form-signin .form-floating:focus-within {
			z-index: 2;
		}

		.grecaptcha-badge {
			box-shadow: none !important;
			border-radius: 3px !important;
		}
	</style>
</head>

<body class="d-flex align-items-center py-4 ">
	<main class="form-signin w-100 m-auto border border-light-subtle rounded bg-secondary-subtle">
		<form id="formRequest">
			<h1 class="h3 mb-3 fw-normal">Forgot password</h1>
			<div class="alert alert-success" role="alert" id="bannerSuccess" style="display: none;"></div>
			<div class="alert alert-danger alert-dismissible fade show" role="alert" id="bannerError" style="display: none;"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>

			<div class="form-floating">
				<input type="email" class="form-control" id="inputEmail" placeholder="name@example.com" autocomplete="off">
				<label for="inputEmail">Email address</label>
			</div>
			<button class="btn btn-primary w-100 py-2 g-recaptcha mt-3 mb-2"
				data-sitekey="<?php echo reCaptchaPublic ?>" data-theme="dark" data-callback="requestForgotPassword"
				id="buttonSubmit">
				<span id="buttonLoginText">Send request</span>
				<span id="buttonLoadingText" style="display: none;">
					<span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
					<span role="status">Loading...</span>
				</span>
			</button>
			<p class="text-body-secondary fst-italic fs-6 mb-0">We will send an email with the instructions to this
				email address</p>
		</form>
	</main>
	<script src="script/requestForgotPassword.js"></script>
	<script src="content/js/bootstrap.bundle.min.js"></script>
	<script src="https://www.google.com/recaptcha/api.js"></script>
</body>

</html>