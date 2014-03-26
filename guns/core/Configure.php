<?php

/**
 * Configure
 * 
 * @package SMART
 * @author gunjansoni
 * @copyright 2014
 * @version $Id$
 * @access public
 */
class Configure
{

	static $_configItem = array();

	/**
	 * Configure::set()
	 * 
	 * @param mixed $keyword
	 * @param mixed $value
	 * @return
	 */
	public static function set($keyword, $value)
	{
		if (strpos($keyword, ".") > 0)
		{
			$explodedKeywords = explode(".", $keyword);
			$firstKey = $explodedKeywords[0];
			unset($explodedKeywords[0]);
			$explodedKeywords = array_reverse($explodedKeywords);
			$temparray = $value;
			foreach ($explodedKeywords as $k => $v)
			{
				$s = $temparray;
				$temparray = array();
				$temparray[$v] = $s;
			}
			//e($temparray);
			if (self::check($firstKey))
			{
				$t = self::$_configItem[$firstKey];

				self::$_configItem[$firstKey] = array_replace_recursive($t, $temparray);
			} else
			{
				self::$_configItem[$firstKey] = $temparray;
			}
		} else
		{
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
		if (strpos($keyword, ".") > 0)
		{
			$explodedKeywords = explode(".", $keyword);
			$firstKey = $explodedKeywords[0];
			unset($explodedKeywords[0]);
			$explodedKeywords = array_reverse($explodedKeywords);
			$temparray = '';
			//e($explodedKeywords);
			foreach ($explodedKeywords as $k => $v)
			{
				$s = $temparray;
				$temparray = array();
				$temparray[$v] = $s;
			}
			$returnVar = ((self::check($firstKey) == true) ? self::getAssociative(self::$_configItem[$firstKey],
				$temparray) : false);
		} else
		{
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
		foreach ($associativearray as $key => $value)
		{
			if (!is_array($associativearray[$key]))
			{
				$returnVar = isset($sourcearray[$key]) ? $sourcearray[$key] : false;
				break;
			} else
			{
				$returnVar = self::getAssociative($sourcearray[$key], $associativearray[$key]);
			}
		}
		return $returnVar;
	}
}
