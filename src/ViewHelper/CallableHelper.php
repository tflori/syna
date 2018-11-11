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
        if ($callable instanceof \Closure) {
            $this->callable = \Closure::bind($callable, $this, self::class);
        }
    }

    public function __invoke(...$args)
    {
        return call_user_func($this->callable, ...$args);
    }
}
