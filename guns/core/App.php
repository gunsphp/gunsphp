<?php

class App
{

    static $paths = array(COMMON_DIR, GUNSPHP_DIR, APP_ROOT);

    public static function uses($fileName, $directory = 'core', $extention = '.php')
    {
        $fileExists = false;
        foreach (self::$paths as $path) {
            $file = is_dir($directory) ? $directory . DS . $fileName . $extention : $path . DS . $directory . DS . $fileName . $extention;
            //if ($fileName == 'GunsphpAppController') echo "$file <br/>";
            if (file_exists($file)) {
                $fileExists = true;
                if (class_exists($fileName) === false) {
                    require_once $file;
                    break;
                }
            }
        }
        return $fileExists;
    }

    public static function import($fileName, $directory = 'core', $extention = '.php')
    {
        $fileExists = false;
        foreach (self::$paths as $path) {
            $file = is_dir($directory) ? $directory . DS . $fileName . $extention : $path . DS . $directory . DS . $fileName . $extention;
            if (file_exists($file)) {
                $fileExists = true;
                if (class_exists($fileName) === false) {
                    include_once $file;
                    //echo "<hr/>Found file $file<hr/>";
                    break;
                }
            }
            //echo "Cannot find the Specified file $file<br/>";
        }
        return $fileExists;
    }

    public static function getFile($fileName, $directory = 'core', $extention = '.php')
    {
        foreach (self::$paths as $path) {
            $file = is_dir($directory) ? $directory . DS . $fileName . $extention : $path . DS . $directory . DS . $fileName . $extention;
            if (file_exists($file)) {
                return $file;
            }
        }
        return false;
    }

    public static function returnFromFile($fileName, $directory = 'core', $extention = '.php')
    {
        foreach (self::$paths as $path) {
            $file = is_dir($directory) ? $directory . DS . $fileName . $extention : $path . DS . $directory . DS . $fileName . $extention;
            if (file_exists($file)) {
                return include_once $file;
            }
        }
        return false;
    }
}