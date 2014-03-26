<?php


class Text
{
    /**
     * Text::formatString()
     *
     * @param mixed $string
     * @param mixed $args
     * @return
     */
    public static function formatString($string, $args = array())
    {
        return vsprintf($string, $args);
    }

    /**
     * Text::startsWith()
     *
     * @param mixed $haystack
     * @param mixed $needle
     * @return
     */
    public static function startsWith($haystack, $needle)
    {
        return $needle === "" || strpos($haystack, $needle) === 0;
    }

    /**
     * Text::endsWith()
     *
     * @param mixed $haystack
     * @param mixed $needle
     * @return
     */
    public static function endsWith($haystack, $needle)
    {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }

    /**
     * Text::contains()
     *
     * @param mixed $haystack
     * @param mixed $needle
     * @return
     */
    public static function contains($haystack, $needle)
    {
        return strpos($haystack, $needle) > 0 ? true : false;
    }

    /**
     * Text::format()
     *
     * @param mixed $string
     * @param mixed $args
     * @return
     */
    public static function format($string, $args = array())
    {
        foreach ($args as $key => $value) {
            $string = str_replace($key, $value, $string);
        }
        return $string;
    }

    /**
     * Text::left()
     *
     * @param mixed $str
     * @param mixed $length
     * @return
     */
    public static function left($str, $length)
    {
        return substr($str, 0, $length);
    }

    /**
     * Text::right()
     *
     * @param mixed $str
     * @param mixed $length
     * @return
     */
    public static function right($str, $length)
    {
        return substr($str, -$length);
    }

    public static function isEmail($string)
    {
        return filter_var($string, FILTER_VALIDATE_EMAIL);
    }

    public static function checkRegEx($checkWith, $text, $regExValidator = '*')
    {
        $textExploded = explode($regExValidator, $text);
        foreach ($textExploded as $key => $value) {
            $textExploded[$key] = "($value)";
        }
        $text = implode('(.*)', $textExploded);

        $regEx = "/^$text$/D";
        preg_match($regEx, $checkWith, $matches);
        if (count($matches) > 0) {
            return true;
        } else {
            return false;
        }
    }
}