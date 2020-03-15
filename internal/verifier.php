<?php

function verify(string $value, string $type)
{
    switch ($type) {
        case 'login':
            return preg_match('/[А-Яа-я -]{8,}/', $value);
            break;
        case 'password':
            return preg_match('/\w{8,}/', $value);
            break;
        case 'email':
            return filter_var($value, FILTER_VALIDATE_EMAIL);
            break;
    }
}
?>