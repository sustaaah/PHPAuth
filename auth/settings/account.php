<?php
require("../script/config.php")
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Account settings</title>
	<link rel="stylesheet" href="../content/css/bootstrap.min.css">
	<link rel="stylesheet" href="../content/icons/bootstrap-icons.css">
	<style>
		body {
			min-height: 100vh;
			min-height: -webkit-fill-available;
		}

		html {
			height: -webkit-fill-available;
		}

		main {
			height: 100vh;
			height: -webkit-fill-available;
			max-height: 100vh;
			overflow-x: auto;
			overflow-y: hidden;
		}

		.btn-toggle-nav a {
			padding: .1875rem .5rem;
			margin-top: .125rem;
			margin-left: 1.25rem;
		}

		.btn-toggle-nav a:hover,
		.btn-toggle-nav a:focus {
			background-color: var(--bs-tertiary-bg);
		}

		.scrollarea {
			overflow-y: auto;
		}
	</style>
</head>

<body>
<nav class="nav d-lg-none border-bottom p-3">
	<button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSettings" aria-controls="offcanvasSettings" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
</nav>
<div class="row w-100">
	<div class="offcanvas-lg offcanvas-start col-12 col-lg-2 p-0 px-lg-2" tabindex="-1" id="offcanvasSettings" aria-labelledby="offcanvasSettingsLabel">
		<div class="offcanvas-body p-0 h-100">
			<div class="d-flex flex-column align-items-stretch flex-shrink-0 bg-body-tertiary w-100 h-100" style="min-height: 100vh;">
				<span class="d-inline flex-shrink-0 p-3 border-bottom">
					<span class="fs-5 fw-semibold" id="offcanvasSettingsLabel">Account settings</span>
					<button type="button" class="btn-close my-1 float-end d-inline d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#offcanvasSettings" aria-label="Close"></button>
				</span>
				<div class="list-group list-group-flush scrollarea h-100">
					<div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary h-100">
						<ul class="nav flex-column mb-auto">
							<li class="nav-item">
								<a href="account.php" class="nav-link active" aria-current="page">
									<i class="bi bi-person"></i>
									Account
								</a>
							</li>
							<li>
								<a href="security.php" class="nav-link link-body-emphasis">
									<i class="bi bi-shield-lock"></i>
									Security
								</a>
							</li>
							<li>
								<a href="session.php" class="nav-link link-body-emphasis">
									<i class="bi bi-broadcast-pin"></i>
									Sessions
								</a>
							</li>
							<li>
								<a href="notification.php" class="nav-link link-body-emphasis">
									<i class="bi bi-bell"></i>
									Notifications
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-12 col-lg-10 pe-0">
		<div class="container">
			<h1 class="mt-2">
				Account Settings
			</h1>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">Settings</li>
					<li class="breadcrumb-item active" aria-current="page">Account</li>
				</ol>
			</nav>
			<div class="alert alert-success" role="alert" id="bannerSuccess" style="display: none;"></div>
			<div class="alert alert-danger" role="alert" id="bannerError" style="display: none;"></div>
			<form class="row g-3">
				<div class="col-12">
					<label for="inputEmail" class="form-label">Email</label>
					<input type="email" class="form-control" id="inputEmail" placeholder="Email">
				</div>
				<div class="col-12 col-md-6">
					<label class="form-label" for="inputName">Name</label>
					<input type="text" class="form-control" id="inputName" placeholder="Name">
				</div>
				<div class="col-12 col-md-6">
					<label class="form-label" for="inputSurname">Surname</label>
					<input type="text" class="form-control" id="inputSurname" placeholder="Surname">
				</div>
				<div class="col-12">
					<button class="g-recaptcha btn btn-primary" data-sitekey="<?php echo reCaptchaPublic ?>" data-theme="dark" type="submit" data-callback="modifyAccount">Save</button>
				</div>
			</form>
			<!-- TODO add account deletion button -->
		</div>
	</div>
</div>

<script src="../content/js/bootstrap.bundle.min.js"></script>
<script src="script/account.js"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
    getUserInformation();
</script>
</body>

</html>