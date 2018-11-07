<?php

namespace Syna\ViewHelper;

use Syna\View;
use Syna\ViewHelperInterface;

abstract class AbstractViewHelper implements ViewHelperInterface
{
    protected $view;

    public function __call($name, $arguments)
    {
        return $this->view->__call($name, $arguments);
    }

    public function setView(View $view)
    {
        $this->view = $view;
    }
}
