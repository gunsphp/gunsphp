<?php

class Html
{

    public $printTags = false;

    public function js($jsFileName)
    {
        $url = loadClass('Url');
        if (!is_array($jsFileName)) {
            return "<script type='text/javascript' src='" . $url->baseUrl() . 'js/' . $jsFileName . '.js' . "'></script>";
        } else {
            $return = array();
            foreach ($jsFileName as $js) {
                $return[] = "<script type='text/javascript' src='" . $url->baseUrl() . 'js/' . $js . '.js' . "'></script>";
            }
            return implode("\n", $return);
        }
    }

    public function css($cssFileName, $options = array())
    {
        $url = loadClass('Url');
        $options = $this->convertOptions($options);
        if (!is_array($cssFileName)) {
            if (!Text::startsWith(strtolower($cssFileName), 'http')) {
                $cssFileName = $url->baseUrl() . 'css/' . $cssFileName . '.css';
            }
            return '<link rel="stylesheet" href="' . $cssFileName . '" ' . $options . '>';
        } else {
            $return = array();
            foreach ($cssFileName as $css) {
                if (!Text::startsWith(strtolower($css), 'http')) {
                    $css = $url->baseUrl() . 'css/' . $css . '.css';
                }
                return '<link rel="stylesheet" href="' . $css . '" ' . $options . '>';
            }
            return implode("\n", $return);
        }
    }

    public function link($url, $text, $options = array())
    {
        $prepend = isset($options['prefix']) ? $options['prefix'] : '';
        $postpend = isset($options['postfix']) ? $options['postfix'] : '';
        unset($options['prefix']);
        unset($options['postfix']);
        $urlClass = loadClass('Url');
        $element = $prepend . Text::formatString('<a href="%1$s" %2$s>%3$s</a>', array(
                Text::startsWith(strtolower($url), "http://") || Text::startsWith(strtolower($url), "javascript:") ? $url : $urlClass->baseUrl() . rtrim($url,
                        "/"),
                $this->convertOptions($options),
                $text)) . $postpend;
        e($element);
    }

    public function convertOptions($options)
    {
        $str = array();
        foreach ($options as $key => $value) {
            $str[] = "$key=\"$value\"";
        }
        $attributes = implode(" ", $str);
        return $attributes;
    }

    public function image($imageUrl, $options = array())
    {
        $urlClass = loadClass('Url');
        if (!Text::startsWith($imageUrl, 'http')) {
            $imageUrl = $urlClass->baseUrl() . 'img/' . ltrim($imageUrl, "/");
        }
        $options = $this->convertOptions($options);
        $element = '<img src="' . $imageUrl . '" ' . $options . ' />';
        return $element;
    }


} 