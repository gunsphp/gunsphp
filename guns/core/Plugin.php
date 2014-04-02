<?php

class Plugin
{

    static $_plugins = array();

    /**
     * @param array $plugins
     */
    public static function setPlugins($pluginName, $pluginPath)
    {
        self::$_plugins[$pluginName] = $pluginPath;
    }

    /**
     * @return array
     */
    public static function getPlugins($pluginName = null)
    {
        if ($pluginName == null) {
            return self::$_plugins;
        } else {
            return self::$_plugins[$pluginName];
        }
    }

    /**
     * @param $pluginName
     */
    public static function load($pluginName)
    {
        if (is_dir(PLUGINS_DIR . DS . $pluginName)) {
            self::setPlugins($pluginName, PLUGINS_DIR . DS . $pluginName);
            $pluginDir = PLUGINS_DIR . DS . $pluginName;
            if (is_file($pluginDir . DS . 'config' . DS . 'config.php')) {
                App::uses('config', $pluginDir . DS . 'config');
            }
            if (is_file($pluginDir . DS . 'config' . DS . 'Routes.php')) {
                App::uses('Routes', $pluginDir . DS . 'config');
            }
        } else {
            die("Plugin $pluginName not found");
        }
    }

}