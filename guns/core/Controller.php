<?php

class Controller extends Events
{

    public $helpers = array();
    public $libraries = array();
    public static $instance;
    public $autorender = true;
    public $viewVars = array();
    public $layout = null;
    public $name = null;
    public $controllerName = null;
    public $roadPath = array();
    public $view = null;
    public $element = null;
    public $isFromPlugin = false;

    public function __construct()
    {
        //parent::__construct();
        self::$instance = $this;
        $this->name = str_replace('Action', '', get_class($this));
        $this->controllerName = str_replace('AppController', '', get_parent_class($this));
        $this->roadPath = App::returnFromFile('RoadPath', 'config');
        $this->view = $this->controllerName . ':' . $this->name;
        if (isAjax() == false) {
            if ($this->layout == null) {
                $this->layout = Configure::get('view.defaultLayout');
            }
        } else {
            $this->layout = Configure::get('view.defaultAjaxLayout');
        }
        $this->element = loadClass('Events');
    }

    public function initiate()
    {
        foreach ($this->helpers as $helper) {
            $this->$helper = loadClass($helper, 'helpers');
        }

        foreach ($this->libraries as $library) {
            $this->$library = loadClass($library, 'libraries');
        }

        $classes = loadedClasses();
        unset($classes[$this->name]);
        foreach ($classes as $key => $class) {
            $this->$key = loadClass($class);
            $this->viewVars[$key] = $this->$key;
        }
    }

    public function renderView($viewName = null, $returnView = false)
    {
        if (isAjax()) {
            $roadPath = $this->roadPath['ajax'];
        } else {
            $roadPath = $this->roadPath['http'];
        }

        if ($this->isFromPlugin) {
            $layout = Plugin::getPlugins($this->isFromPlugin) . DS . 'common' . DS . 'layouts' . DS . $this->layout . Configure::get('view.extention');
        } else {
            $layout = null;
        }

        if (!is_file($layout)) {
            $layout = Configure::get('view.layoutDir') . DS . $this->layout . Configure::get('view.extention');
        }

        $this->set('layout', $layout);

        if ($viewName == null) $viewName = $this->view;
        $explodedView = explode(':', $viewName);
        $viewName = $explodedView[0] . DS . 'views' . DS . $explodedView[1] . Configure::get('view.extention');
        if (isAjax() == false) {
            $this->renderEvents();
            $this->set('js_buffer', linkJs($this->controllerName, $this->name));
        }
        $viewPath = App::getFile($explodedView[1], 'app' . DS . $explodedView[0] . DS . 'views', Configure::get('view.extention'));
        if ($this->isFromPlugin) {
            $viewPath = App::getFile($explodedView[1],
                Plugin::getPlugins($this->isFromPlugin) . DS . 'app' . DS . $explodedView[0] . DS . 'views',
                Configure::get('view.extention'));
        }

        if (isAjax() && $returnView == false) {
            $viewPath = null;
        }

        if ($viewPath !== null) {
            if (!file_exists($viewPath)) {
                throw new Exception('Unable to Find View: ' . $viewPath);
            }
            $this->Html->printTags = true;
            $viewClass = loadClass('View');

            $loader = new Twig_Loader_Filesystem(ROOT);
            $twig = new Twig_Environment($loader, array(
                'cache' => ROOT . DS . Configure::get('cache.dir') . DS . Configure::get('view.cacheDir'),
                'auto_reload' => true,
                'debug' => Configure::get('debug.enabled')
            ));
            $viewClass->customFunctions($twig);
            $viewResult = $twig->render($viewPath, $this->viewVars);
            if ($returnView) {
                return $viewResult;
            } else {
                e($viewResult);
            }
        }
    }

    public function set($key, $value)
    {
        $this->viewVars[$key] = $value;
    }

    public static function getInstance()
    {
        return self::$instance;
    }
} 