# PHPAuth
Fast, simple and self hosted auth framework

## Features
- Login and logout
- Registration
- E-mail verification
- Forgot password
- reCaptcha verification
- 2FA(soon!)
- Setting page(soon!)
- csrf protection(soon!)
## Installation
Download files and upload them, run the [sql file](https://github.com/sustaaah/PHPAuth/blob/main/installation/database.sql) in your database and if you want you can add a prefix to your table(all the prefixes must be identical like "auth_"). You must to compile the [config file](https://github.com/sustaaah/PHPAuth/blob/main/auth/script/config.php)

## Implementation examples
### Verify access from a php file
This function check if the session is valid and return principal information about the session, like `status`(boolean value), `userUniqId`, `email`, `name` and `surname`.

You can utilize the 
```php
<?php
require("auth/script/sessionCheckScript.php");

$auth = checkLogin();

if (!$auth["status"]) {
	header("Location: " . $authResponse["redirectUrl"]);

	// if you want to run extra code, enter it here

	die();
}
?>
```

### Verify access from javascript
This function returns a boolean value: if `true` the session is valid, otherwise if the session is not valid the value will be `false`
```html
<script src="auth/script/sessionCheck.js"></script>
<script>
	// the "true" param means that the script is authorized to redirect
	sessionCheck(true);
</script>
```