<?php

class Exceptions
{

    public $obLevel;

    public $levels = array(
        E_ERROR => 'Error',
        E_WARNING => 'Warning',
        E_PARSE => 'Parsing Error',
        E_NOTICE => 'Notice',
        E_CORE_ERROR => 'Core Error',
        E_CORE_WARNING => 'Core Warning',
        E_COMPILE_ERROR => 'Compile Error',
        E_COMPILE_WARNING => 'Compile Warning',
        E_USER_ERROR => 'User Error',
        E_USER_WARNING => 'User Warning',
        E_USER_NOTICE => 'User Notice',
        E_STRICT => 'Runtime Notice'
    );

    public function __construct()
    {
        $this->obLevel = ob_get_level();
    }

    public function showErrors($severity, $message, $file, $line)
    {
        if (isset($this->levels[$severity])) {
            $severity = $this->levels[$severity];
        }

        if (false !== strpos($file, '/')) {
            $x = explode('/', $file);
            $file = $x[count($x) - 2] . '/' . end($x);
        }

        if (ob_get_level() > $this->obLevel + 1) {
            ob_end_flush();
        }

        $errorFileName = App::getFile('error', 'errors', Configure::get('view.extention'));
        $errorView = loadClass('View');

        if ($errorFileName !== false) {
            $loader = new Twig_Loader_Filesystem(ROOT);
            $twig = new Twig_Environment($loader, array(
                'cache' => ROOT . DS . Configure::get('cache.dir') . DS . Configure::get('view.cacheDir'),
                'auto_reload' => true,
                'debug' => Configure::get('debug.enabled')
            ));
            $errorView->customFunctions($twig);

            if (isAjax()) {
                return json_encode($twig->render($errorFileName, array(
                    'severity' => $severity,
                    'message' => $message,
                    'file' => $file,
                    'line' => $line
                )));
            } else {
                return $twig->render($errorFileName, array(
                    'severity' => $severity,
                    'message' => $message,
                    'file' => $file,
                    'line' => $line
                ));
            }
        }
    }
}
