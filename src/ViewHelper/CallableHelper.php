<?php

namespace Syna\ViewHelper;

use Syna\View;
use Syna\ViewHelperInterface;

class CallableHelper implements ViewHelperInterface
{
    /** @var callable */
    protected $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function __invoke(...$args)
    {
        return call_user_func($this->callable, ...$args);
    }

    public function setView(View $view)
    {
    }
}
