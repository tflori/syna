<?php

namespace Syna\ViewHelper;

/**
 * Class CallableViewHelper
 *
 * @package Syna\ViewHelper
 * @author Thomas Flori <thflori@gmail.com>
 */
class CallableViewHelper extends AbstractViewHelper
{
    /** @var callable */
    protected $callable;

    public function __construct(callable $callable, bool $bind = true)
    {
        $this->callable = $callable;
        if ($bind && $callable instanceof \Closure) {
            $this->callable = \Closure::bind($callable, $this, self::class);
        }
    }

    public function __invoke(...$args)
    {
        return call_user_func($this->callable, ...$args);
    }
}
