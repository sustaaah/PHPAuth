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
		.bd-placeholder-img {
			font-size: 1.125rem;
			text-anchor: middle;
			-webkit-user-select: none;
			-moz-user-select: none;
			user-select: none;
		}

		@media (min-width: 768px) {
			.bd-placeholder-img-lg {
				font-size: 3.5rem;
			}
		}

		.b-example-divider {
			width: 100%;
			height: 3rem;
			background-color: rgba(0, 0, 0, .1);
			border: solid rgba(0, 0, 0, .15);
			border-width: 1px 0;
			box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
		}

		.b-example-vr {
			flex-shrink: 0;
			width: 1.5rem;
			height: 100vh;
		}

		.bi {
			vertical-align: -.125em;
			fill: currentColor;
		}

		.nav-scroller {
			position: relative;
			z-index: 2;
			height: 2.75rem;
			overflow-y: hidden;
		}

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

		.btn-bd-primary {
			--bd-violet-bg: #712cf9;
			--bd-violet-rgb: 112.520718, 44.062154, 249.437846;

			--bs-btn-font-weight: 600;
			--bs-btn-color: var(--bs-white);
			--bs-btn-bg: var(--bd-violet-bg);
			--bs-btn-border-color: var(--bd-violet-bg);
			--bs-btn-hover-color: var(--bs-white);
			--bs-btn-hover-bg: #6528e0;
			--bs-btn-hover-border-color: #6528e0;
			--bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
			--bs-btn-active-color: var(--bs-btn-hover-color);
			--bs-btn-active-bg: #5a23c8;
			--bs-btn-active-border-color: #5a23c8;
		}

		.bd-mode-toggle {
			z-index: 1500;
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
					<input type="email" class="form-control" id="inputEmail" placeholder="name@example.com" autocomplete="email">
					<label for="inputEmail">Email address</label>
				</div>
				<div class="form-floating">
					<input type="password" class="form-control" id="inputPassword" placeholder="Password" autocomplete="password">
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