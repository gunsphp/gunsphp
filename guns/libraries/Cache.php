<?php

App::uses('phpfastcache' . DS . 'phpfastcache', 'vendors');

/**
 * Cache
 *
 * @package SReports
 * @subpackage    Core
 * @author GunjanSoni
 * @copyright Copyright (c) 2014
 * @version $Id$
 * @access public
 */
class Cache
{

    /**
     * Cache::__construct()
     *
     * @return
     */
    public function __construct()
    {
        $Configure = array(
            'storage' => Configure::get('cache.engine'),
            'path' => ROOT . DS . Configure::get('cache.dir') . DS . 'cache',
            'securityKey' => Configure::get('security.applicationKey'),
            'fallback' => array('memcache' => Configure::get('cache.fallbackDriver'), 'apc' =>
                Configure::get('cache.fallbackDriver')),
            'htaccess' => true,
            'server' => Configure::get('cache.server'),
        );
        phpFastCache::setup($Configure);

        $GLOBALS['cache'] = phpFastCache();
    }

    /**
     * Cache::get()
     *
     * @param mixed $keyword
     * @return
     */
    public static function get($keyword)
    {
        if (is_array($keyword)) {
            return $GLOBALS['cache']->getMulti($keyword);
        } else {

            return $GLOBALS['cache']->get($keyword);
        }
    }

    /**
     * Cache::set()
     *
     * @param mixed $keyword
     * @param mixed $value
     * @param mixed $time
     * @return
     */
    public static function set($keyword, $value = null, $time = null)
    {
        if (is_array($keyword) && $value == null) {
            $arr = array();
            foreach ($keyword as $key => $value) {
                $arr[] = Cache::set($value[0], $value[1], isset($value[2]) ? $value[2] : null);
            }

            return $arr;
        } else {

            return $GLOBALS['cache']->set($keyword, $value, $time == null ? Configure::get('cache.refreshTime') :
                $time);
        }
    }

    /**
     * Cache::getInfo()
     *
     * @param mixed $keyword
     * @return
     */
    public static function getInfo($keyword)
    {
        if (is_array($keyword)) {

            return $GLOBALS['cache']->getInfoMulti($keyword);
        } else {

            return $GLOBALS['cache']->getInfo($keyword);
        }
    }

    /**
     * Cache::delete()
     *
     * @param mixed $keyword
     * @return
     */
    public static function delete($keyword)
    {
        if (is_array($keyword)) {
            return $GLOBALS['cache']->deleteMulti($keyword);
        } else {
            return $GLOBALS['cache']->delete($keyword);
        }
    }

    /**
     * Cache::clean()
     *
     * @return
     */
    public static function clean()
    {

        return $GLOBALS['cache']->clean();
    }

    /**
     * Cache::stats()
     *
     * @param mixed $keyword
     * @return
     */
    public static function stats($keyword = null)
    {
        if ($keyword == null) {
            return $GLOBALS['cache']->stats();
        } else {
            return $GLOBALS['cache']->stats($keyword);
        }
    }

    /**
     * Cache::exists()
     *
     * @param mixed $keyword
     * @return
     */
    public static function exists($keyword)
    {
        if (is_array($keyword)) {

            return $GLOBALS['cache']->isExistingMulti($keyword);
        } else {

            return $GLOBALS['cache']->isExisting($keyword);
        }
    }

    /**
     * Cache::increment()
     *
     * @param mixed $keyword
     * @param integer $incrementBy
     * @return
     */
    public static function increment($keyword, $incrementBy = 1)
    {
        if (is_array($keyword)) {

            return $GLOBALS['cache']->incrementMulti($keyword);
        } else {

            return $GLOBALS['cache']->increment($keyword, $incrementBy);
        }
    }

    /**
     * Cache::decrement()
     *
     * @param mixed $keyword
     * @param integer $decrementBy
     * @return
     */
    public static function decrement($keyword, $decrementBy = 1)
    {
        if (is_array($keyword)) {

            return $GLOBALS['cache']->decrementMulti($keyword);
        } else {

            return $GLOBALS['cache']->decrement($keyword, $decrementBy);
        }
    }

    /**
     * Cache::increaseLife()
     *
     * @param mixed $keyword
     * @param mixed $seconds
     * @return
     */
    public static function increaseLife($keyword, $seconds = null)
    {
        $seconds = $seconds == null ? Configure::get('cache.refreshTime') : $seconds;
        if (is_array($keyword)) {

            return $GLOBALS['cache']->touchMulti($keyword);
        } else {

            return $GLOBALS['cache']->touch($keyword, $seconds);
        }
    }

    public static function dump()
    {
        var_dump($GLOBALS['cache']);
    }
}
