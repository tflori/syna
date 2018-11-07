<?php

namespace Syna\ViewHelper;

/**
 * Class CallableHelper
 *
 * @package Syna\ViewHelper
 * @author Thomas Flori <thflori@gmail.com>
 * @codeCoverageIgnore Just a wrapper
 */
class CallableHelper extends AbstractViewHelper
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
}
