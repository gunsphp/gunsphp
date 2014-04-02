<?php

class Events implements ArrayAccess
{

    private $data = [];
    private $elementId = null;

    public function __call($functionName, $arguments)
    {
        if ($this->elementId !== null) {
            $params = array($this->elementId);
            $params = merge_array($params, $arguments);
            if ($functionName == 'render') {
                setHtml($this->elementId, renderajaxView($arguments[0]));
            } else {
                call_user_func_array($functionName, $params);
            }
        }
        return $this;
    }

    public function __construct($id = null)
    {
        $this->elementId = $id;
    }

    public function renderEvents()
    {
        $controllerName = $this->controllerName;
        $actionName = $this->name;
        $availableMethods = getMethods($actionName . 'Action');
        foreach ($availableMethods as $key => $method) {
            if ($method == 'onload') {
                if (isset($this->viewVars['post'][$method])) {
                    $options = array('data' => $this->viewVars['post'][$method]);
                }
                $options['data']['get'] = $this->Request->get();
                $controller = Controller::getInstance();
                if (!$controller->isFromPlugin == false) {
                    $pluginName = $controller->isFromPlugin;
                    ajax("$pluginName/$controllerName/$actionName?call=$method", $options);
                } else {
                    ajax("$controllerName/$actionName?call=$method", $options);
                }
            } else {
                $availableEvents = Configure::get('jQuery.events.render');
                foreach ($availableEvents as $event => $actualEvent) {
                    if (Text::endsWith($method, "_" . $event)) {
                        $elementName = explode('_', $method)[0];

                        ob_start();
                        isCallback(true);
                        $this->$method();
                        $scriptGenerated = ob_get_contents();
                        ob_end_clean();
                        $classEvents = isset($this->viewVars['classEvents']) ? $this->viewVars['classEvents'] : false;
                        $selector = '#';
                        if ($classEvents) {
                            foreach ($classEvents as $classEvent) {
                                if ($classEvent == $method) {
                                    $selector = '.';
                                }
                            }
                        }
                        bindEvent($selector . $elementName, $actualEvent, $scriptGenerated);
                        $scriptGenerated = ob_get_contents();
                        isCallback(false);
                        ob_end_clean();
                        if (ob_get_length() > 0)
                            ob_flush();


                        setScript($scriptGenerated);
                    }
                    if (Text::endsWith($method, "_" . $event . '_ajax')) {
                        $elementName = explode('_', $method)[0];
                        if (isset($this->viewVars['post'][$method])) {
                            $options = array('data' => $this->viewVars['post'][$method]);
                        }
                        $options['data']['get'] = $this->Request->get();
                        ob_start();
                        $classEvents = isset($this->viewVars['classEvents']) ? $this->viewVars['classEvents'] : false;
                        $selector = '#';
                        if ($classEvents) {
                            foreach ($classEvents as $classEvent) {
                                if ($classEvent == $method) {
                                    $selector = '.';
                                }
                            }
                        }
                        $controller = Controller::getInstance();
                        if (!$controller->isFromPlugin == false) {
                            $pluginName = $controller->isFromPlugin;
                            bindEvent($selector . $elementName, $actualEvent, callback('ajax', array("$pluginName/$controllerName/$actionName?call=$method", $options)));
                        } else {
                            bindEvent($selector . $elementName, $actualEvent, callback('ajax', array("$controllerName/$actionName?call=$method", $options)));
                        }
                        $scriptGenerated = ob_get_contents();
                        ob_end_clean();
                        if (ob_get_length() > 0)
                            ob_flush();

                        setScript($scriptGenerated);
                    }
                }
            }
        }
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    /**
     * Whether or not an offset exists
     *
     * @param string An offset to check for
     * @access public
     * @return boolean
     * @abstracting ArrayAccess
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * Unsets an offset
     *
     * @param string The offset to unset
     * @access public
     * @abstracting ArrayAccess
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->data[$offset]);
        }
    }

    /**
     * Returns the value at specified offset
     *
     * @param string The offset to retrieve
     * @access public
     * @return mixed
     * @abstracting ArrayAccess
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            if (!$this->offsetExists($offset)) {
                return new self($offset);
            } else {
                return $this->data[$offset];
            }
        }
    }
} 