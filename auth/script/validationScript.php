<?php
/**
 * Summary of validateName
 * @param mixed $name
 * @return bool
 */
function validateName($name)
{
	$pattern = "/^[a-zA-Z ',-]+$/u";
	$name = trim($name);

	if (preg_match($pattern, $name)) {
		return true;
	} else {
		return false;
	}
}

/**
 * Summary of validateEmail
 * @param mixed $email
 * @return bool
 */
function validateEmail($email)
{
	$email = trim($email);

	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return true;
	} else {
		return false;
	}
}

/**
 * Summary of validatePassword
 * @param mixed $password
 * @return bool
 */
function validatePassword($password)
{
	// $pattern = '/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,16}$/';

	/*
		  if (preg_match($pattern, $password)) {
			  return true;
		  } else {
			  return false;
		  }
		  */

	$minLength = 8;

	if (strlen($password) < $minLength) {
		return false;
	}

	if (!preg_match('/[a-z]/', $password)) {
		return false;
	}

	if (!preg_match('/[A-Z]/', $password)) {
		return false;
	}

	if (!preg_match('/[0-9]/', $password)) {
		return false;
	}

	return true;
}