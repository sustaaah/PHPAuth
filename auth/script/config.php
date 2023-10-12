<?php
// database
define("dbHost", ""); // database host
define("dbUsername", ""); // database username
define("dbPassword", ""); // database password
define("dbName", ""); // database name
define("tablePrefix", ""); // If there is no prefix, leave blank

// password
define("passwordSalt", ""); // get your code here: https://www.random.org/strings/?num=10&len=32&digits=on&upperalpha=on&loweralpha=on&unique=on&format=html&rnd=new

// cookie
define("cookieName", ""); // cookie name
define("cookieExpire", 3600 * 24 * 30 * 2); // two months
define("cookiePath", "/"); // cookie path validity
define("cookieDomain", ""); // cookie domain
define("cookieSecure", true);
define("cookieHttpOnly", true);
define("jwtSecret", ""); // get your code here: https://www.random.org/strings/?num=10&len=32&digits=on&upperalpha=on&loweralpha=on&unique=on&format=html&rnd=new

// mail settings
define("mailSmtpHostname", ""); // smtp server hostname
define("mailSmtpUsername", ""); // smtp username
define("mailSmtpPassword", ""); // smtp password
define("mailSmtpSecure", ""); // smtp secure setting
define("mailSmtpPort", ); // smtp port
define("mailSmtpFromName", ""); // smtp from name
define("mailSmtpFromMail", ""); // smtp from mail

// session
define("sessionExpire", 3600*24*30*1); // one month
define("sessionExpireNotRememberMe", 3600 * 24); // one day

// captcha v2
define("reCaptchaPublic", ""); // get your code here: https://www.google.com/recaptcha/admin/
define("reCaptchaSecret", ""); // get your code here: https://www.google.com/recaptcha/admin/

// website
define("urlToAuth", ""); // url to the folder that contains "auth" folder
define("redirectAfterLogin", ""); // url to redirect on login, like index page
define("redirectAfterLogout", ""); // url to redirect on logout, like login page

// error log
//
// script for error logging
// error_log("\n" . __FILE__ . " : " . time() . " : " . $e, errorLogMode, errorLogPath);
//
define("errorLogMode", 3); // DO NOT CHANGE THIS
define("errorLogPath", "errorLog.txt"); // DO NOT CHANGE THIS
?>