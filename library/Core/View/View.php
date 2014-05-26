<?php

namespace Core\View;

class View implements ViewInterface
{

    protected $script;
    protected $params           = array();
    protected $prefix           = 'view';
    protected $invokableHelpers = array(
        'escapehtml' => 'Core\View\Helper\EscapeHtml',
    );

    public function __construct($helpers = null)
    {
        if (is_array($helpers)) {
            foreach ($helpers as $key => $value) {
                $this->invokableHelpers[strtolower($key)] = $value;
            }
        }
    }

    public function setScript($script, $prefix = 'templates')
    {
        $this->script = 'Application/View/' . $prefix . '/' . $script . '.phtml';
        return $this;
    }

    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }

    public function render(array $params = array())
    {
        if (!file_exists($this->script))
            throw new Exception\ScriptNotFound('View script not found: ' . $this->script);

        $params = array_merge($params, $this->params);
        extract($params);

        ob_start();
        include $this->script;
        $return = ob_get_clean();

        return $return;
    }

    public function __call($name, $arguments)
    {
        $name = strtolower($name);

        if (!array_key_exists($name, $this->invokableHelpers))
            throw new \Exception('Helper not found');

        $helper = new $this->invokableHelpers[$name]();

        $helper->setView($this);

        return call_user_func_array($helper, $arguments);
    }

}
