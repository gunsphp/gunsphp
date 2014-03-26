<?php

class Validator
{

    public function isEmail($string)
    {
        $result = filter_var($string, FILTER_VALIDATE_EMAIL);
        return $result;
    }

    public function isBoolean($string)
    {
        $result = filter_var($string, FILTER_VALIDATE_BOOLEAN);
        return $result;
    }

    public function minLength($string, $length)
    {
        if (strlen($string) >= $length) {
            return true;
        } else {
            return false;
        }
    }

    public function maxLength($string, $length)
    {
        if (strlen($string) <= $length) {
            return true;
        } else {
            return false;
        }
    }

    public function isIp($string)
    {
        $result = filter_var($string, FILTER_VALIDATE_IP);
        return $result;
    }

    public function isUrl($string)
    {
        $result = filter_var($string, FILTER_VALIDATE_URL);
        return $result;
    }

    public function isNumeric($string)
    {
        return is_numeric($string);
    }
} 