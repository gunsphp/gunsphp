<?php

class Request
{
    public function post($key = null)
    {
        if ($key == null) {
            return $_POST;
        } else {
            if (isset($_POST[$key])) {
                return $_POST[$key];
            } else {
                return false;
            }
        }
    }

    public function get($key = null)
    {
        if ($key == null) {
            return $_GET;
        } else {
            if (isset($_GET[$key])) {
                return $_GET[$key];
            } else {
                return false;
            }
        }
    }

    public function is($requestType)
    {
        return strtoupper($requestType) == $_SERVER['REQUEST_METHOD'];
    }
} 