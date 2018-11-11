<?php

namespace Syna\ViewHelper;

use Syna\View;
use Syna\ViewHelperInterface;

/**
 * Class AbstractViewHelper
 *
 * @package Syna\ViewHelper
 * @author Thomas Flori <thflori@gmail.com>
 * @method string escape(string $string)
 * @method mixed batch($var, string $functions)
 */
abstract class AbstractViewHelper implements ViewHelperInterface
{
    /** @var View */
    protected $view;

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->view, $name], $arguments);
    }

    public function setView(View $view)
    {
        $this->view = $view;
    }
}
