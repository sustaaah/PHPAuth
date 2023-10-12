<?php
require("script/config.php");
require("script/sessionCheckScript.php");

$auth = checkLogin();

if (!$auth["status"]) {
	$authResponse["status"] = false;
	header("Location: " . $authResponse["redirectUrl"]);

	echo json_encode($authResponse);
	die();
}

if (!$auth["needConfirm"]){
	header("Location: " . redirectAfterLogin);
}

?>
<!doctype html>
<html lang="en" data-bs-theme="dark">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<title>Confirm code</title>
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

		.form-signin input[type="number"] {}

		input[type="number"]::-webkit-inner-spin-button,
		input[type="number"]::-webkit-outer-spin-button {
			-webkit-appearance: none;
			appearance: none;
			margin: 0;
			/* Rimuove il margine predefinito in alcuni browser */
		}
	</style>

</head>
<body class="d-flex align-items-center py-4 bg-body-tertiary">
	<main class="form-signin w-100 m-auto">
		<form>
			<h2 class="h3 mb-3 fw-normal">Insert the code that you recived on your mail to continue</h2>
			<div class="alert alert-success" role="alert" id="bannerSuccess" style="display: none;"></div>
			<div class="alert alert-danger" role="alert" id="bannerError" style="display: none;"></div>
			<div class="form-floating">
				<input type="number" class="form-control" id="inputCode" placeholder="XXXXXX">
				<label for="inputCode">Code</label>
			</div>
			<button class="btn btn-success w-100 py-2 mt-3 g-recaptcha" data-sitekey="6LfCz4IoAAAAAGjw3tYyovt9bslexl1f9DY2aXoT" data-theme="dark" data-callback="sendCode" id="buttonSubmit">
				<span id="buttonLoginText">Confirm code</span>
				<span id="buttonLoadingText" style="display: none;">
					<span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
					<span role="status">Loading...</span>
				</span>
			</button>
		</form>
	</main>

	<script src="script/confimCode.js"></script>
	<script src="https://www.google.com/recaptcha/api.js"></script>
	<script src="content/js/bootstrap.bundle.min.js"></script>
</body>

</html>