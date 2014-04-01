<?php

class Loader
{

    public $roadPath = array();
    static $params = array();

    public function __construct()
    {
        $autoloader = Configure::get('autoload');
        foreach ($autoloader as $classType => $classes) {
            foreach ($classes as $class) {
                $this->$class = loadClass($class, $classType);
            }
        }
        $this->roadPath = App::returnFromFile('RoadPath', 'config');
    }

    public function loadController($controllerName, $actionName, $params = array())
    {
        if ($params == null) $params = array();
        self::$params = $params;
        if (isAjax()) {
            $roadPath = $this->roadPath['ajax'];
        } else {
            $roadPath = $this->roadPath['http'];
        }

        if (Text::contains($controllerName, '.')) {
            $explodedControllerName = explode('.', $controllerName);
            $pluginName = $explodedControllerName[0];
            $ctrlName = $explodedControllerName[1];
            $mainAppControllerPath = Plugin::getPlugins($pluginName) . DS . 'app';
            $controllerPath = $mainAppControllerPath . DS . $ctrlName;
            $actionPath = $controllerPath . DS . 'actions';
            $mainAppController = $pluginName . 'AppController';
            $controller = $ctrlName . 'AppController';
            if (!is_file($mainAppControllerPath . DS . ucwords($ctrlName) . DS . $controller . '.php')) {
                if (is_file(Plugin::getPlugins($pluginName) . DS . 'public' . DS . $ctrlName . DS . strtolower($actionName))) {
                    if (strtolower($ctrlName) == 'css') {
                        header("Content-type: text/css; charset: UTF-8");
                    }
                    App::uses(strtolower($actionName), Plugin::getPlugins($pluginName) . DS . 'public' . DS . strtolower($ctrlName), '');
                    die();
                }
            }
        } else {
            $pluginName = null;
            $mainAppControllerPath = 'app';
            $controllerPath = $mainAppControllerPath . DS . $controllerName;
            $actionPath = $controllerPath . DS . 'actions';
            $mainAppController = 'AppController';
            $controller = $controllerName . 'AppController';
        }

        if ($actionName == '') {
            $actionName = Configure::get('router.defaultAction');
        }
        $action = $actionName . 'Action';

        $baseController = loadClass('Controller');
        $currentAppController = loadClass($mainAppController, $mainAppControllerPath);
        $currentController = loadClass($controller, $controllerPath);

        $currentAction = loadClass($action, $actionPath);

        $controllerHirarchy = array($currentAction, $currentController, $currentAppController, $baseController);

        if (method_exists($currentAction, 'initiate')) {
            call_user_func_array(array($currentAction, 'initiate'), array());
        }

        if (isset($roadPath['beforeMainController']) && is_array($roadPath['beforeMainController'])) {
            foreach ($roadPath['beforeMainController'] as $functionName => $executionConditions) {
                if (isset($executionConditions['allowedControllers'])) {
                    $allowedControllers = $executionConditions['allowedControllers'];
                } else {
                    $allowedControllers = '*';
                }

                if (isset($executionConditions['allowedActions'])) {
                    $allowedActions = $executionConditions['allowedActions'];
                } else {
                    $allowedActions = '*';
                }

                if (Text::checkRegEx($controllerName, $allowedControllers) && Text::checkRegEx($actionName, $allowedActions)) {
                    $acceptsParams = isset($executionConditions['acceptsParams']) ? $executionConditions['acceptsParams'] : false;
                    foreach ($controllerHirarchy as $ctrl) {
                        if (method_exists($ctrl, $functionName)) {
                            call_user_func_array(array($ctrl, $functionName), $acceptsParams ? $params : array());
                            break;
                        }
                    }
                }
            }
        }

        if (method_exists($currentAction, 'main') && isAjax() == false) {
            call_user_func_array(array($currentAction, 'main'), $params);
        }

        if (isAjax() && isset($_GET['call'])) {
            call_user_func_array(array($currentAction, $this->Request->get('call')), $this->Request->get());
        }

        if ($currentAction->autorender == true) {
            if (isset($roadPath['beforeViewRender']) && is_array($roadPath['beforeViewRender'])) {
                foreach ($roadPath['beforeViewRender'] as $functionName => $executionConditions) {
                    if (isset($executionConditions['allowedControllers'])) {
                        $allowedControllers = $executionConditions['allowedControllers'];
                    } else {
                        $allowedControllers = '*';
                    }

                    if (isset($executionConditions['allowedActions'])) {
                        $allowedActions = $executionConditions['allowedActions'];
                    } else {
                        $allowedActions = '*';
                    }

                    if (Text::checkRegEx($controllerName, $allowedControllers) && Text::checkRegEx($actionName, $allowedActions)) {
                        $acceptsParams = isset($executionConditions['acceptsParams']) ? $executionConditions['acceptsParams'] : false;
                        foreach ($controllerHirarchy as $ctrl) {
                            if (method_exists($ctrl, $functionName)) {
                                call_user_func_array(array($ctrl, $functionName), $acceptsParams ? $params : array());
                                break;
                            }
                        }
                    }
                }
            }

            if (isAjax() == false) {
                if (!$pluginName == null) {
                    $currentAction->isFromPlugin = $pluginName;
                }
                $currentAction->renderView();
            }

            if (isset($roadPath['afterViewRender']) && is_array($roadPath['afterViewRender'])) {
                foreach ($roadPath['afterViewRender'] as $functionName => $executionConditions) {
                    if (isset($executionConditions['allowedControllers'])) {
                        $allowedControllers = $executionConditions['allowedControllers'];
                    } else {
                        $allowedControllers = '*';
                    }

                    if (isset($executionConditions['allowedActions'])) {
                        $allowedActions = $executionConditions['allowedActions'];
                    } else {
                        $allowedActions = '*';
                    }

                    if (Text::checkRegEx($controllerName, $allowedControllers) && Text::checkRegEx($actionName, $allowedActions)) {
                        $acceptsParams = isset($executionConditions['acceptsParams']) ? $executionConditions['acceptsParams'] : false;
                        foreach ($controllerHirarchy as $ctrl) {
                            if (method_exists($ctrl, $functionName)) {
                                call_user_func_array(array($ctrl, $functionName), $acceptsParams ? $params : array());
                                break;
                            }
                        }
                    }
                }
            }
        }

        if (isset($roadPath['afterMainController']) && is_array($roadPath['afterMainController'])) {
            foreach ($roadPath['afterMainController'] as $functionName => $executionConditions) {
                if (isset($executionConditions['allowedControllers'])) {
                    $allowedControllers = $executionConditions['allowedControllers'];
                } else {
                    $allowedControllers = '*';
                }

                if (isset($executionConditions['allowedActions'])) {
                    $allowedActions = $executionConditions['allowedActions'];
                } else {
                    $allowedActions = '*';
                }

                if (Text::checkRegEx($controllerName, $allowedControllers) && Text::checkRegEx($actionName, $allowedActions)) {
                    $acceptsParams = isset($executionConditions['acceptsParams']) ? $executionConditions['acceptsParams'] : false;
                    foreach ($controllerHirarchy as $ctrl) {
                        if (method_exists($ctrl, $functionName)) {
                            call_user_func_array(array($ctrl, $functionName), $acceptsParams ? $params : array());
                            break;
                        }
                    }
                }
            }
        }
    }

    public static function getParams()
    {
        return self::$params;
    }

} 