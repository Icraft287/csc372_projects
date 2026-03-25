<?php
/*
    File: validate.php
    Author: Isaac Crft
    Date: March 25, 2026
    Description: Reusable validation functions for form data collected on destinations.php.
                 Included at the top of destinations.php via require_once.
*/

// ---------------------------------------------------------------
// FUNCTION 1: is_valid_text()
// Checks that a text input falls within a specified character range.
// Trims whitespace before checking so blank spaces don't count.
// @param string $value  The submitted text to check
// @param int    $min    Minimum number of characters allowed
// @param int    $max    Maximum number of characters allowed
// @return bool  True if valid, false if not
// ---------------------------------------------------------------
function is_valid_text($value, $min, $max) {
    $length = strlen(trim($value));
    return ($length >= $min && $length <= $max);
}

// ---------------------------------------------------------------
// FUNCTION 2: is_valid_number()
// Checks that a value is numeric and falls within a valid range.
// Uses is_numeric() so it catches non-number strings like "abc".
// @param mixed     $value  The submitted value to check
// @param int|float $min    Minimum allowed number
// @param int|float $max    Maximum allowed number
// @return bool  True if valid, false if not
// ---------------------------------------------------------------
function is_valid_number($value, $min, $max) {
    return (is_numeric($value) && $value >= $min && $value <= $max);
}

// ---------------------------------------------------------------
// FUNCTION 3: is_valid_option()
// Checks that a selected option exists in a predefined allowed list.
// Uses in_array() to compare the submitted value against valid options.
// @param string $value    The submitted option value
// @param array  $allowed  Array of accepted option values
// @return bool  True if valid, false if not
// ---------------------------------------------------------------
function is_valid_option($value, $allowed) {
    return in_array($value, $allowed);
}
?>