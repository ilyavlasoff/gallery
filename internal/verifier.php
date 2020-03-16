<?php

function verify(string $value, string $type)
{
    switch ($type) {
        case 'login':
            return preg_match('/[А-Яа-я -]{8,}/', $value);
            break;
        case 'password':
            //return preg_match('/\w{8,}/', $value);
            return true;
            break;
        case 'email':
            return filter_var($value, FILTER_VALIDATE_EMAIL);
            break;
        case 'name':
            //return preg_match('/[А-Я][а-я]+\s[А-Я][а-я]+/', $value);
            return true;
            break;
        default:
            throw new Exception('Type is undefined');
    }
}

function filter(string $str, string $type): string {
    $str = trim($str);
    return $str;
}

