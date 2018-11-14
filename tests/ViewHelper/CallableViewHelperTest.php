<?php

namespace Syna\Test\ViewHelper;

use Syna\Test\TestCase;
use Syna\ViewHelper\CallableViewHelper;
use Mockery as m;

class CallableViewHelperTest extends TestCase
{
    /** @test */
    public function executesTheCallableWithAllArguments()
    {
        $spy = m::mock(new m\ClosureWrapper(function () {
        }));
        $viewHelper = new CallableViewHelper($spy);

        $viewHelper('a', 'b', 'c');

        $spy->shouldHaveBeenCalled()->with('a', 'b', 'c')->once();
    }

    /** @test */
    public function bindsClosures()
    {
        $viewHelper = new CallableViewHelper(function () {
            return get_class($this);
        });

        $result = $viewHelper();

        self::assertSame(CallableViewHelper::class, $result);
    }

    /** @test */
    public function avoidBindingWithOptionalArgument()
    {
        $viewHelper = new CallableViewHelper(function () {
            return get_class($this);
        }, false);

        $result = $viewHelper();

        self::assertSame(self::class, $result);
    }
}
