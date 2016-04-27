<?php

/**
 * Checks to see if a give value is truthy or not
 * 
 * @param mixed $value
 * @param bool $return_null
 * @return bool
 */
function is_true($value, $return_null = false){
    $boolval = (is_string($value) ? filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool) $value);
    return ($boolval === null && !$return_null ? false : $boolval);
}

/**
 * Checks if a given string is a valid email address
 * 
 * @param string $string
 * @return bool
 */
function isValidEmailAddress($string)
{
	if(filter_var($string, FILTER_VALIDATE_EMAIL))
		return true;

	return false;
}

/**
 * Generates a random string with the provided length
 * 
 * @param int $length
 * @return string
 */
function generateRandomString($length = 12, $numbers = true, $lowercase = true, $uppercase = true, $specials = true)
{
	$number_chars   = '0123456789';
	$lower_chars 	= 'abcdefghijklmnopqrstuvwxyz';
	$upper_chars 	= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$special_chars	= '!@#$%&*?';
	
	$characters  = $numbers? $number_chars : '';
	$characters .= $lowercase? $lower_chars : '';
	$characters .= $uppercase? $upper_chars : '';
	$characters .= $specials? $special_chars : '';

	return substr(str_shuffle($characters), 0, $length);
}

/**
 * Check strength of users password
 * 
 * @param string $password
 * @param int $min
 * @param int $max
 * @return mixed
 */
function isValidPassword($password, $min = 6, $max = 25) {

	$length = strlen($password);

    if ($length < $min || $length > $max)
        return "Password too short. Password must be between $min and $max characters.";

    if (!preg_match("#[0-9]+#", $password))
       return "Password must include at least one number.";

    if (!preg_match("#[a-zA-Z]+#", $password))
        return "Password must include at least one letter!";

    return true;
}