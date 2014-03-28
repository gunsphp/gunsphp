<?php

/**
 * GunsPHP : An Event Based Javascript embeded PHP Framework (http://www.gunsphp.com)
 * Copyright (c) GunZdb Pvt Ltd. (http://www.gunzdb.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) GunZdb Pvt Ltd. Project
 * @link        http://www.gunsphp.com GunsPHP Project
 * @package     Guns.Core
 * @since       GunsPHP v 0.1
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Configure allows you to set different configuration options to your application. It allows you to store application
 * wise configuration options that could be used for managing runtime configuration information
 *
 * @package Guns.Core
 */
class Configure
{

    /**
     * Array of values stored in Configure Class
     *
     * @var array
     */
    static $_configItem = array();

    /**
     * Configure::set()
     *
     * @param mixed $keyword
     * @param mixed $value
     * @return mixed $_configItem
     */
    public static function set($keyword, $value)
    {
        if (strpos($keyword, ".") > 0) {
            $explodedKeywords = explode(".", $keyword);
            $firstKey = $explodedKeywords[0];
            unset($explodedKeywords[0]);
            $explodedKeywords = array_reverse($explodedKeywords);
            $temparray = $value;
            foreach ($explodedKeywords as $k => $v) {
                $s = $temparray;
                $temparray = array();
                $temparray[$v] = $s;
            }
            //e($temparray);
            if (self::check($firstKey)) {
                $t = self::$_configItem[$firstKey];

                self::$_configItem[$firstKey] = array_replace_recursive($t, $temparray);
            } else {
                self::$_configItem[$firstKey] = $temparray;
            }
        } else {
            self::$_configItem[$keyword] = $value;
        }
    }

    /**
     * Configure::get()
     *
     * @param mixed $keyword
     * @return
     */
    public static function get($keyword)
    {
        $returnVar = false;
        if (strpos($keyword, ".") > 0) {
            $explodedKeywords = explode(".", $keyword);
            $firstKey = $explodedKeywords[0];
            unset($explodedKeywords[0]);
            $explodedKeywords = array_reverse($explodedKeywords);
            $temparray = '';
            //e($explodedKeywords);
            foreach ($explodedKeywords as $k => $v) {
                $s = $temparray;
                $temparray = array();
                $temparray[$v] = $s;
            }
            $returnVar = ((self::check($firstKey) == true) ? self::getAssociative(self::$_configItem[$firstKey],
                $temparray) : false);
        } else {
            $returnVar = isset(self::$_configItem[$keyword]) ? self::$_configItem[$keyword] : false;
        }
        return $returnVar;
    }

    /**
     * Configure::check()
     *
     * @param mixed $keyword
     * @return
     */
    public static function check($keyword)
    {
        return isset(self::$_configItem[$keyword]) ? true : false;
    }

    /**
     * Configure::getAssociative()
     *
     * @param mixed $sourcearray
     * @param mixed $associativearray
     * @return
     */
    public static function getAssociative($sourcearray, $associativearray)
    {
        $returnVar = '';
        foreach ($associativearray as $key => $value) {
            if (!is_array($associativearray[$key])) {
                $returnVar = isset($sourcearray[$key]) ? $sourcearray[$key] : false;
                break;
            } else {
                $returnVar = self::getAssociative($sourcearray[$key], $associativearray[$key]);
            }
        }
        return $returnVar;
    }
}
