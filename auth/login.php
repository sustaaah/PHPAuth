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
	<title>Log in</title>
	<link href="content/css/bootstrap.min.css" rel="stylesheet">
	<style>
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

		.form-signin input[type="email"] {
			margin-bottom: -1px;
			border-bottom-right-radius: 0;
			border-bottom-left-radius: 0;
		}

		.form-signin input[type="password"] {
			margin-bottom: 10px;
			border-top-left-radius: 0;
			border-top-right-radius: 0;
		}

		.grecaptcha-badge {
			box-shadow: none !important;
			border-radius: 3px !important;
		}

		body {
			margin: 0;
			background: radial-gradient(ellipse at top left, rgba(13, 110, 253, 0.5), transparent 50%),
				radial-gradient(ellipse at top right, rgba(255, 228, 132, 0.5), transparent 50%),
				radial-gradient(ellipse at bottom right, rgba(112, 44, 249, 0.5), transparent 50%),
				radial-gradient(ellipse at bottom left, rgba(214, 51, 132, 0.5), transparent 50%);
			background-size: 200% 200%;
			background-position: 100% 0, 0 0, 100% 100%, 0 100%;
			background-repeat: no-repeat;
			animation: backgroundAnimation 25s ease-in-out infinite;
		}

		@keyframes backgroundAnimation {

			0% {
				background-position: 100% 0, 0 0, 100% 100%, 0 100%;
			}

			25% {
				background-position: 0 0, 100% 0, 100% 100%, 0 100%;
			}

			50% {
				background-position: 0 0, 100% 0, 0 100%, 0 100%;
			}

			75% {
				background-position: 0 0, 100% 0, 0 100%, 100% 100%;
			}

			100% {
				background-position: 100% 0, 0 0, 100% 100%, 0 100%;
			}
		}
	</style>
</head>

<body class="d-flex align-items-center py-4">
	<main class="w-100 m-auto">
		<div class="form-signin w-100 m-auto border border-light-subtle bg-secondary-subtle rounded">
			<form>
				<h1 class="h3 mb-3 fw-normal">Please sign in</h1>
				<div class="alert alert-success" role="alert" id="bannerSuccess" style="display: none;"></div>
				<div class="alert alert-danger" role="alert" id="bannerError" style="display: none;"></div>
				<div class="form-floating">
					<input type="email" class="form-control" id="inputEmail" placeholder="name@example.com"
						autocomplete="email">
					<label for="inputEmail">Email address</label>
				</div>
				<div class="form-floating">
					<input type="password" class="form-control" id="inputPassword" placeholder="Password"
						autocomplete="password">
					<label for="inputPassword">Password</label>
				</div>
				<div class="form-check text-start my-3">
					<input class="form-check-input" type="checkbox" value="remember-me" id="inputRemember" checked>
					<label class="form-check-label" for="inputRemember">
						Remember me
					</label>
				</div>
				<button class="btn btn-primary w-100 py-2 g-recaptcha" data-sitekey="<?php echo reCaptchaPublic ?>"
					data-theme="dark" data-callback="login" id="buttonSubmit">
					<span id="buttonLoginText">Sing in</span>
					<span id="buttonLoadingText" style="display: none;">
						<span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
						<span role="status">Loading...</span>
					</span>
				</button>
			</form>
			<div class="text-end pt-2">
				<a href="requestForgotPassword.php" style="display: inline-block">Forgot password?</a>
			</div>
		</div>
		<div class="text-center form-signin mt-3 w-100 m-auto border border-light-subtle bg-secondary-subtle rounded">
			New user? <a href="register.php">Create an account</a>
		</div>
	</main>
	<script src="content/js/bootstrap.bundle.min.js"></script>
	<script src="https://www.google.com/recaptcha/api.js"></script>
	<script src="script/login.js"></script>
</body>

</html>