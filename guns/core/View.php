<?php

class View
{

    public function __construct()
    {
        $twigAutoLoaderFile = 'Twig' . DS . 'Autoloader';
        App::uses($twigAutoLoaderFile, 'vendors');
        Twig_Autoloader::register(true);
    }

    function customFunctions($twig)
    {
        $twig->registerUndefinedFunctionCallback(function ($name) {
            if (function_exists($name)) {
                return new Twig_SimpleFunction($name, function () use ($name) {
                    return call_user_func_array($name, func_get_args());
                });
                return false;
            }
        });
    }
}
