<?php

namespace Syna\Test\View;

use Syna\Factory;
use Syna\Test\TestCase;
use Mockery as m;
use Syna\View;
use Syna\ViewLocator;

/**
 * Class UsingHelpersTest
 *
 * @package Syna\Test\View
 * @author Thomas Flori <thflori@gmail.com>
 * @covers \Syna\View
 */
class UsingHelpersTest extends TestCase
{
    /** @var m\Mock|Factory */
    protected $factory;

    protected function setUp()
    {
        parent::setUp();
        $this->factory = m::mock(Factory::class, [new ViewLocator($this->templatePath)])->makePartial();
    }

    /** @test */
    public function callsHelperForUndefinedMethods()
    {
        $this->createTemplate('test.php', '<?= $v->upper($input) ?>');
        $this->factory->getHelperLocator()->add('upper', 'strtoupper');

        $result = $this->factory->render('test', ['input' => 'Hello World!']);

        self::assertSame('HELLO WORLD!', $result);
        $this->factory->shouldHaveReceived('helper')->with(m::type(View::class), 'upper', 'Hello World!')->once();
    }

    /** @test */
    public function batchProcessingHelpers()
    {
        $this->factory->getHelperLocator()->add('lower', 'strtolower');
        $this->factory->getHelperLocator()->add('ucwords', 'ucwords'); // not needed
        $this->createTemplate('title.php', '<?= $v->batch($title, \'lower|ucwords\') ?>');
        $view = $this->factory->view('title', ['title' => 'welcome to SYNA']);

        $this->factory->shouldReceive('helper')->with($view, 'lower', 'welcome to SYNA')
            ->once()->ordered()->passthru();
        $this->factory->shouldReceive('helper')->with($view, 'ucwords', 'welcome to syna')
            ->once()->ordered()->passthru();

        $result = $view->render();

        self::assertSame('Welcome To Syna', $result);
    }

    /** @test */
    public function escapingHelpersOutput()
    {
        $var = (object) ['id' => 23, 'name' => 'John Doe'];
        $this->factory->getHelperLocator()->add('dump', function ($var) {
            return print_r($var, true);
        });
        $this->createTemplate('dump.php', '<?= $e($var, \'dump\') ?>');
        $view = $this->factory->view('dump', ['var' => $var]);

        $this->factory->shouldReceive('helper')->with($view, 'dump', $var)
            ->once()->passthru();

        $result = $view->render();

        self::assertSame(
            'stdClass Object' . PHP_EOL .
            '(' . PHP_EOL .
            '    [id] =&gt; 23' . PHP_EOL .
            '    [name] =&gt; John Doe' . PHP_EOL .
            ')',
            $result
        );
    }
}
