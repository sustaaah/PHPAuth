<?php
// database
const dbHost = "31.11.39.61";
const dbUsername = "Sql1565861";
const dbPassword = "khE(ZbQ6m=sZ;5";
const dbName = "Sql1565861_1";
const tablePrefix = "auth_";

// password
const passwordSalt = "m41sTEbhG63fDw6LMiwW8B9MT8xVukpd";

// cookie
const cookieName = "authSustaaah";
const cookieExpire = 3600 * 24 * 30 * 2; // two months
const cookiePath = "/";
const cookieDomain = ".sustaaah.com";
const cookieSecure = true;
const cookieHttpOnly = true;
const jwtSecret = "7agB4g9XQnNcFojPd6ftw3nlP70teATf";

// mail settings
const mailSmtpHostname = "smtps.aruba.it";
const mailSmtpUsername = "noreply@sustaaah.com";
const mailSmtpPassword = "tezby8-jigkic-Nunxyf";
const mailSmtpSecure = "ssl";
const mailSmtpPort = 465;
const mailSmtpFromName = "noreply";
const mailSmtpFromMail = "noreply@sustaaah.com";

// session
const sessionExpire = 3600 * 24 * 30; // one month
// TODO controllare se viene effettivamente utilizzata questa variabile
const sessionExpireNotRememberMe = 3600 * 24; // one day

// captcha
const reCaptchaPublic = "6LfCz4IoAAAAAGjw3tYyovt9bslexl1f9DY2aXoT";
const reCaptchaSecret = "6LfCz4IoAAAAAJLoz-NoWkDmqsbPVqOoJYA8Tbzt";

// website
const urlToAuth = "https://www.sustaaah.com/login-system/auth/"; // url to the folder that contains "auth" folder
const redirectAfterLogin = "https://www.sustaaah.com/login-system/"; // url to redirect on login, es. index page
const redirectAfterLogout = "https://www.sustaaah.com/login-system/auth/login.php"; // url to redirect on logout, es. index page

// error log
//
// script for error logging
// error_log("\n" . __FILE__ . " : " . time() . " : " . $e, errorLogMode, errorLogPath);
//
const errorLogMode = 3;
const errorLogPath = "errorLog.txt";
