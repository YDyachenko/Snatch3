<?php

namespace Core\View\Helper;

use Core\View\View;

class AbstractHelper
{

    protected $view;

    public function setView(View $view)
    {
        $this->view = $view;

        return $this;
    }

    public function getView()
    {
        return $this->view;
    }

}
