<?php


if (!function_exists('isCallback')) {
    /**
     * @param null $setTo
     * @return null
     */
    function isCallback($setTo = null)
    {
        static $callback = false;
        static $level = 0;
        if ($setTo == null) {
            return $callback;
        } else {
            if ($level <= 1) {
                $callback = $setTo;
            }
            $level = $setTo == false ? $level - 1 : $level + 1;
        }
    }
}

if (!function_exists('parseScript')) {
    /**
     * @param $script
     */
    function parseScript($script)
    {
        static $isCallBack = false;
        if (isAjax()) {
            echo $script . "\n";
        } else {
            if (isCallback()) {
                echo $script . "\n";
            } else {
                setScript($script . "\n");
            }
        }
    }
}

if (!function_exists('setScript')) {
    /**
     * @param null $script
     * @return array
     */
    function setScript($script = null)
    {
        static $_script = array();
        if ($script == null) {
            return $_script;
            $_script = array();
        } else {
            $_script[] = $script . "\n";
        }
    }
}

if (!function_exists('linkJs')) {
    /**
     * @param $controllerName
     * @param $actionName
     * @return string
     */
    function linkJs($controllerName, $actionName)
    {
        $useInlineScripting = Configure::get('jQuery.script.useInline');

        if ($useInlineScripting) {
            $script = "$(document).ready(function() {\n" . implode("\n", setScript()) . "});";
            return "<script type='text/javascript'>\n\n$script\n</script>";
        } else {
            $jsFileName = randomString(40) . '.js';
            $folderName = PUBLIC_DIR . DS . 'generated';
            if (!is_dir($folderName)) {
                mkdir($folderName);
                chmod($folderName, 777);

            }
            $fileName = $folderName . DS . $jsFileName;
            $availableFiles = scandir($folderName);
            unset($availableFiles[0]);
            unset($availableFiles[1]);
            foreach ($availableFiles as $file) {
                $file = $folderName . DS . $file;
                if (is_file($file)) unlink($file);
            }
            $script = "$(document).ready(function() {\n" . implode("\n", setScript()) . "});";
            file_put_contents($fileName, $script);
            $url = loadClass('Url', 'helpers');
            return "<script type='text/javascript' src='" . $url->baseUrl() . 'public/generated/' . $jsFileName . "'></script>";
        }
    }
}

if (!function_exists('parseElement')) {
    /**
     * @param $elementName
     * @return string
     */
    function parseElement($elementName)
    {
        if ($elementName == 'document' || $elementName == 'window' || $elementName == 'this') {
            return "$elementName";
        } else {
            return "'$elementName'";
        }
    }
}

if (!function_exists('alert')) {
    /**
     * @param $message
     */
    function alert($message)
    {
        $message = json_encode($message);
        $return = "\nalert($message);\n";
        parseScript($return);
    }
}

if (!function_exists('ajax')) {
    /**
     * @param $url
     * @param $options
     */
    function ajax($url, $options)
    {
        $defaultOptions = Configure::get('jQuery.ajax.default.options');
        $mergedOptions = merge_array($options, $defaultOptions);
        $urlClass = loadClass('Url');
        if (Text::startsWith(strtolower($url), 'http://') == false) {
            $url = $urlClass->baseUrl() . rtrim($url, "/");
        }
        $mergedOptions['url'] = $url;
        if (isset($mergedOptions['beforeSend'])) {
            $mergedOptions['beforeSend'] = "function() {\n\t" . $mergedOptions['beforeSend'] . "\n}";
        }
        if (isset($mergedOptions['error'])) {
            $mergedOptions['error'] = "function(jqXHR, textStatus, errorThrown) {\n\t" . $mergedOptions['error'] . "\n}";
        }
        if (isset($mergedOptions['successVariable'])) {
            $successVariable = $mergedOptions['successVariable'];
            unset($mergedOptions['successVariable']);
        } else {
            $successVariable = 'data';
        }
        if (Configure::get('jQuery.ajax.logging.logfile') == true) {
            $jslogAjax = "$.post(\n\t\t'" . $urlClass->baseUrl() .
                "gunsphp/writejslog?call=performwrite',\n\t\t{\n\t\t\tlogdata: $successVariable,
                \n\t\t\tcalledfromUrl: '$url'\n\t\t}\n\t);";
        } else {
            $jslogAjax = null;
        }
        $consoleLog = Configure::get('jQuery.ajax.logging.consolelog') == true ?
            "console.log($successVariable);" : null;
        if (isset($mergedOptions['successCallBack'])) {
            $successCallBack = "function($successVariable) {\n\t\t" . $mergedOptions['successCallBack'] . "\n}";
            unset($mergedOptions['successCallBack']);
        } else {
            $successCallBack = "function($successVariable) {\n\t\t" . $jslogAjax . "\n";
            $successCallBack .= $consoleLog . "\n";
            $successCallBack .= "eval($successVariable);\n}";
        }
        $mergedOptions['success'] = $successCallBack;
        $return = Text::formatString("$.ajax(%s);\n", array(encode($mergedOptions)));
        parseScript($return);
    }
}

if (!function_exists('setHtml')) {
    /**
     * @param $elementName
     * @param $dataToSet
     * @param bool $isVariable
     */
    function setHtml($elementName, $dataToSet, $isVariable = false)
    {
        $elementName = parseElement($elementName);
        if (Text::startsWith($dataToSet, '$')) $isVariable = true;
        if ($isVariable == false) {
            $dataToSet = json_encode($dataToSet);
        }
        $script = "$($elementName).html($dataToSet);";
        parseScript($script);
    }
}

if (!function_exists('setValue')) {
    /**
     * @param $elementName
     * @param $valueToSet
     * @param bool $isVariable
     */
    function setValue($elementName, $valueToSet, $isVariable = false)
    {
        if (Text::startsWith($valueToSet, '$')) $isVariable = true;
        $elementName = parseElement($elementName);
        if ($isVariable == false) {
            $valueToSet = json_encode($valueToSet);
        }
        $script = "$($elementName).val($valueToSet);";
        parseScript($script);
    }
}

if (!function_exists('getValue')) {
    /**
     * @param $elementName
     * @param null $returnToVariableName
     */
    function getValue($elementName, $returnToVariableName = null)
    {
        $elementName = parseElement($elementName);
        if ($returnToVariableName == null) {
            $script = "$($elementName).val()";
        } else {
            $script = "var $returnToVariableName = $($elementName).val();\n";
        }
        parseScript($script);
    }
}

if (!function_exists('click')) {
    /**
     * @param $elementName
     */
    function click($elementName)
    {
        $elementName = parseElement($elementName);
        $script = "$($elementName).click();";
        parseScript($script);
    }
}

if (!function_exists('onClick')) {
    /**
     * @param $elementName
     * @param $callBackFunction
     * @param bool $preventDefault
     */
    function onClick($elementName, $callBackFunction, $preventDefault = true)
    {
        $elementName = parseElement($elementName);
        if ($preventDefault) $callBackFunction = $callBackFunction . "\nreturn false;";
        $callBackFunction = "function(e) {\n$callBackFunction\n}";
        $script = "$($elementName).click($callBackFunction);";
        parseScript($script);
    }
}

if (!function_exists('doubleClick')) {
    /**
     * @param $elementName
     */
    function doubleClick($elementName)
    {
        $elementName = parseElement($elementName);
        $script = "$($elementName).dblclick();";
        parseScript($script);
    }
}

if (!function_exists('onDoubleClick')) {
    /**
     * @param $elementName
     * @param $callBackFunction
     * @param bool $preventDefault
     */
    function onDoubleClick($elementName, $callBackFunction, $preventDefault = true)
    {
        $elementName = parseElement($elementName);
        if ($preventDefault) $callBackFunction = $callBackFunction . "\nreturn false;";
        $callBackFunction = "function(e) {\n$callBackFunction\n}";
        $script = "$($elementName).dblclick($callBackFunction);";
        parseScript($script);
    }
}

if (!function_exists('blur')) {
    /**
     * @param $elementName
     */
    function blur($elementName)
    {
        $elementName = parseElement($elementName);
        $script = "$($elementName).blur();";
        parseScript($script);
    }
}

if (!function_exists('onBlur')) {
    /**
     * @param $elementName
     * @param $callBackFunction
     */
    function onBlur($elementName, $callBackFunction)
    {
        $elementName = parseElement($elementName);
        $callBackFunction = "function() {\n$callBackFunction\n}";
        $script = "$($elementName).blur($callBackFunction);";
        parseScript($script);
    }
}

if (!function_exists('change')) {
    /**
     * @param $elementName
     */
    function change($elementName)
    {
        $elementName = parseElement($elementName);
        $script = "$($elementName).change();";
        parseScript($script);
    }
}

if (!function_exists('onChange')) {
    /**
     * @param $elementName
     * @param $callBackFunction
     */
    function onChange($elementName, $callBackFunction)
    {
        $elementName = parseElement($elementName);
        $callBackFunction = "function() {\n$callBackFunction\n}";
        $script = "$($elementName).change($callBackFunction);";
        parseScript($script);
    }
}

if (!function_exists('error')) {
    /**
     * @param $elementName
     */
    function error($elementName)
    {
        $elementName = parseElement($elementName);
        $script = "$($elementName).error();";
        parseScript($script);
    }
}

if (!function_exists('onError')) {
    /**
     * @param $elementName
     * @param $callBackFunction
     */
    function onError($elementName, $callBackFunction)
    {
        $elementName = parseElement($elementName);
        $callBackFunction = "function() {\n$callBackFunction\n}";
        $script = "$($elementName).error($callBackFunction);";
        parseScript($script);
    }
}

if (!function_exists('focus')) {
    /**
     * @param $elementName
     */
    function focus($elementName)
    {
        $elementName = parseElement($elementName);
        $script = "$($elementName).focus();";
        parseScript($script);
    }
}

if (!function_exists('onFocus')) {
    /**
     * @param $elementName
     * @param $callBackFunction
     */
    function onFocus($elementName, $callBackFunction)
    {
        $elementName = parseElement($elementName);
        $callBackFunction = "function() {\n$callBackFunction\n}";
        $script = "$($elementName).focus($callBackFunction);";
        parseScript($script);
    }
}

if (!function_exists('focusIn')) {
    /**
     * @param $elementName
     */
    function focusIn($elementName)
    {
        $elementName = parseElement($elementName);
        $script = "$($elementName).focusin();";
        parseScript($script);
    }
}

if (!function_exists('onFocusIn')) {
    /**
     * @param $elementName
     * @param $callBackFunction
     */
    function onFocusIn($elementName, $callBackFunction)
    {
        $elementName = parseElement($elementName);
        $callBackFunction = "function() {\n$callBackFunction\n}";
        $script = "$($elementName).focusin($callBackFunction);";
        parseScript($script);
    }
}

if (!function_exists('OnfocusOut')) {
    /**
     * @param $elementName
     * @param $inCallBackFunction
     * @param null $outCallBackFunction
     */
    function OnfocusOut($elementName, $inCallBackFunction, $outCallBackFunction = null)
    {
        $elementName = parseElement($elementName);
        if ($outCallBackFunction == null) {
            $script = "$($elementName).focusout(function() { $inCallBackFunction });";
        } else {
            $script = "$($elementName).focusout(function() { $inCallBackFunction }, function() { $outCallBackFunction });";
        }
        parseScript($script);
    }
}

if (!function_exists('toggleClass')) {
    /**
     * @param $elementName
     * @param $classNameToToggle
     * @param null $condition
     */
    function toggleClass($elementName, $classNameToToggle, $condition = null)
    {
        $elementName = parseElement($elementName);
        $classNameToToggle = json_encode($classNameToToggle);
        if ($condition == null) {
            $script = "$($elementName).toggleClass($classNameToToggle);";
        } else {
            $condition = json_encode($condition);
            $script = "$($elementName).toggleClass($classNameToToggle, $condition);";
        }
        parseScript($script);
    }
}

if (!function_exists('triggerEvent')) {
    /**
     * @param $elementName
     * @param $eventName
     */
    function triggerEvent($elementName, $eventName)
    {
        $elementName = parseElement($elementName);
        $eventName = json_encode($eventName);
        $script = "$($elementName).trigger($eventName);";
        parseScript($script);
    }
}

if (!function_exists('appendTo')) {
    /**
     * @param $elementName
     * @param $whatToAppend
     */
    function appendTo($elementName, $whatToAppend)
    {
        $elementName = parseElement($elementName);
        if (!Text::startsWith($whatToAppend, '$')) {
            $whatToAppend = json_encode($whatToAppend);
        }
        $script = "$($whatToAppend).appendTo($elementName);";
        parseScript($script);
    }
}

if (!function_exists('addClass')) {
    /**
     * @param $elementName
     * @param $className
     */
    function addClass($elementName, $className)
    {
        $elementName = parseElement($elementName);
        $className = json_encode($className);
        $script = "$($elementName).addClass($className);";
        parseScript($script);
    }
}

if (!function_exists('getAttribute')) {
    /**
     * @param $elementName
     * @param $attributeName
     * @param null $returnToVariableName
     */
    function getAttribute($elementName, $attributeName, $returnToVariableName = null)
    {
        $elementName = parseElement($elementName);
        $attributeName = json_encode($attributeName);
        if ($returnToVariableName == null) {
            $script = "$($elementName).attr($attributeName)";
        } else {
            $script = "var $returnToVariableName = $($elementName).attr($attributeName);\n";
        }
        parseScript($script);
    }
}

if (!function_exists('setAttribute')) {
    /**
     * @param $elementName
     * @param $attributeName
     * @param $valueToSet
     * @param null $returnToVariableName
     */
    function setAttribute($elementName, $attributeName, $valueToSet, $returnToVariableName = null)
    {
        $elementName = parseElement($elementName);
        $attributeName = json_encode($attributeName);
        if (!Text::startsWith($valueToSet, '$')) {
            $valueToSet = json_encode($valueToSet);
        }
        $script = "$($elementName).attr($attributeName, $valueToSet);\n";
        parseScript($script);
    }
}

if (!function_exists('getCssProperty')) {
    /**
     * @param $elementName
     * @param string $cssAttribute
     * @param null $returnToVariable
     */
    function getCssProperty($elementName, $cssAttribute = 'width', $returnToVariable = null)
    {
        $elementName = parseElement($elementName);
        $cssAttribute = json_encode($cssAttribute);
        if ($returnToVariable == null) {
            $script = "$($elementName).css($cssAttribute)";
        } else {
            $script = "var $returnToVariable = $($elementName).css($cssAttribute);";
        }
        parseScript($script);
    }
}

if (!function_exists('setCssProperty')) {
    /**
     * @param $elementName
     * @param string $cssAttribute
     * @param $newValue
     */
    function setCssProperty($elementName, $cssAttribute = 'width', $newValue)
    {
        $elementName = parseElement($elementName);
        $cssAttribute = json_encode($cssAttribute);
        $newValue = json_encode($newValue);
        $script = "$($elementName).css($cssAttribute, $newValue);";
        parseScript($script);
    }
}

if (!function_exists('removeClass')) {
    /**
     * @param $elementName
     * @param $classNameToRemove
     */
    function removeClass($elementName, $classNameToRemove)
    {
        $elementName = parseElement($elementName);
        $classNameToRemove = json_encode($classNameToRemove);
        $script = "$($elementName).removeClass($classNameToRemove);";
        parseScript($script);
    }
}

if (!function_exists('encode')) {
    /**
     * @param $str
     * @param int $initialTab
     * @return string
     */
    function encode($str, $initialTab = 0)
    {
        $tabs = '';
        for ($i = 0; $i <= $initialTab; $i++) {
            $tabs .= "\t";
        }
        $encodedString = "{\n$tabs%s\n$tabs}";

        if (!is_array($str)) {
            return skipQuotes($str) ? $str : Text::formatString('"%s"', array($str));
        }

        $returnVal = array();

        if ($str === array_values($str)) {
            foreach ($str as $value) {
                if (is_array($value)) {
                    $returnVal[] = encode($value, $initialTab + 1);
                } else {
                    $returnVal[] = skipQuotes($value) ? $value : Text::formatString('"%s"',
                        array($value));
                }
            }
            $encodedString = Text::formatString("[%s]", array(implode(",\n$tabs", $returnVal)));
            return $encodedString;
        } else {
            foreach ($str as $key => $value) {
                $returnVal[] = $key . ":" . encode($value, $initialTab + 1);
            }

            return Text::formatString($encodedString, array(implode(",\n$tabs", $returnVal)));
        }
    }
}

if (!function_exists('skipQuotes')) {
    /**
     * @param $str
     * @return bool
     */
    function skipQuotes($str)
    {
        $str = str_replace("\n", "", $str);
        $str = str_replace("\t", "", $str);
        if (Text::startsWith($str, 'function')) {
            return true;
        }
        if (Text::startsWith($str, '$')) {
            return true;
        }
        if (is_numeric($str)) {
            return true;
        }
        if (Text::startsWith($str, '[') && Text::endsWith($str, ']')) {
            return true;
        }
        if (Text::startsWith($str, '{') && Text::endsWith($str, '}')) {
            return true;
        }
        if (is_bool($str)) {
            return true;
        }
        if ($str == 'true' || $str == 'false') {
            return true;
        }

        return false;
    }
}

if (!function_exists('bindEvent')) {
    /**
     * @param $elementName
     * @param $eventName
     * @param $callBack
     */
    function bindEvent($elementName, $eventName, $callBack)
    {
        $elementName = parseElement($elementName);
        $script = "$($elementName).bind('$eventName', function() {\n\t$callBack\n})";
        parseScript($script);
    }
}

if (!function_exists('callback')) {
    /**
     * @param $function
     * @param array $params
     * @return string
     */
    function callback($function, $params = array())
    {
        isCallback(true);
        $result = return_result($function, $params);
        isCallback(false);
        return $result;
    }
}

if (!function_exists('fadeOut')) {
    /**
     * @param $elementName
     * @param string $duration
     * @param null $callBack
     */
    function fadeOut($elementName, $duration = 'short', $callBack = null)
    {
        $elementName = parseElement($elementName);
        $duration = json_encode($duration);
        if ($callBack == null) {
            $script = "$($elementName).fadeOut($duration);\n";
        } else {
            $script = "$($elementName).fadeOut($duration, function(){\n\t$callBack\n});\n";
        }
        parseScript($script);
    }
}

if (!function_exists('fadeIn')) {
    /**
     * @param $elementName
     * @param string $duration
     * @param null $callBack
     */
    function fadeIn($elementName, $duration = 'short', $callBack = null)
    {
        $elementName = parseElement($elementName);
        $duration = json_encode($duration);
        if ($callBack == null) {
            $script = "$($elementName).fadeIn($duration);\n";
        } else {
            $script = "$($elementName).fadeIn$duration, function(){\n\t$callBack\n});\n";
        }
        parseScript($script);
    }
}

if (!function_exists('submit')) {
    /**
     * @param $elementName
     * @param null $callBack
     */
    function submit($elementName, $callBack = null)
    {
        $elementName = parseElement($elementName);
        if ($callBack == null) {
            $return = "$($elementName).submit();\n";
        } else {
            $return = "$($elementName).submit(function(){\n\t$callBack\nevent.preventDefault();\n});\n";
        }
        parseScript($return);
    }
}

if (!function_exists('serialize_form')) {
    /**
     * @param $elementName
     * @param null $variableToRetrun
     */
    function serialize_form($elementName, $variableToRetrun = null)
    {
        $elementName = parseElement($elementName);
        if ($variableToRetrun == null) {
            $script = "$($elementName).serialize()\n";
        } else {
            $script = "var $variableToRetrun = $($elementName).serialize();\n";
        }
        parseScript($script);
    }
}

if (!function_exists('iif')) {
    /**
     * @param $condition
     * @param $successCallBack
     * @param null $failedCallBack
     */
    function iif($condition, $successCallBack, $failedCallBack = null)
    {
        $script = "if($condition) {\n\t";
        $script .= $successCallBack . "\n}";
        if ($failedCallBack !== null) {
            $script .= "\n else {\n\t";
            $script .= $failedCallBack . "\n}";
        }
        $script .= "\n";
        parseScript($script);
    }
}

if (!function_exists('return_false')) {
    /**
     *
     */
    function return_false()
    {
        $script = "return false;\n";
        parseScript($script);
    }
}

if (!function_exists('renderajaxView')) {
    /**
     * @param $viewName
     * @return string
     */
    function renderajaxView($viewName)
    {
        $controller = Controller::getInstance();
        $view = $controller->renderView($viewName, true);
        return $view;
    }
}

if (!function_exists('is')) {
    /**
     * @param $elementName
     * @param $whatToCheck
     * @param null $returnToVariableName
     */
    function is($elementName, $whatToCheck, $returnToVariableName = null)
    {
        $elementName = parseElement($elementName);
        if ($returnToVariableName == null) {
            $script = "$($elementName).is('$whatToCheck')";
        } else {
            $script = "var $returnToVariableName = $($elementName).is('$whatToCheck');\n";
        }
        parseScript($script);
    }
}