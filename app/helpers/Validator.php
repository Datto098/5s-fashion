<?php
/**
 * Input Validation Helper
 * Standardize input validation across the application
 */

class Validator {

    public static function validateEmail($email) {
        return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
    }

    public static function validatePassword($password) {
        return !empty($password) && strlen($password) >= 6;
    }

    public static function validatePhone($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        return preg_match('/^[0-9]{10,11}$/', $phone);
    }

    public static function sanitizeString($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    public static function validateRequired($fields, $data) {
        $errors = [];
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                $errors[] = ucfirst($field) . ' is required';
            }
        }
        return $errors;
    }
}
?>
