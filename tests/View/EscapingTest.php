<?php

namespace Syna\Test\View;

use Syna\Factory;
use Syna\Test\TestCase;
use Mockery as m;
use Syna\ViewLocator;

/**
 * Class EscapingTest
 *
 * @package Syna\Test\View
 * @author Thomas Flori <thflori@gmail.com>
 * @covers \Syna\View
 */
class EscapingTest extends TestCase
{
    /** @var m\Mock|Factory */
    protected $factory;

    protected function setUp()
    {
        parent::setUp();
        $this->factory = m::mock(Factory::class, [new ViewLocator($this->templatePath)])->makePartial();
    }

    /** @test */
    public function usingEscapeMethod()
    {
        $this->createTemplate('escape.php', '<?= $v->escape("<p class=\\"intro\\">Lorem ipsum</p>") ?>');
        $view = $this->factory->view('escape');

        $result = $view->render();

        self::assertSame('&lt;p class=&quot;intro&quot;&gt;Lorem ipsum&lt;/p&gt;', $result);
    }

    /** @test */
    public function usingDollarE()
    {
        $this->createTemplate('escape.php', '<?= $e("<p class=\\"intro\\">Lorem ipsum</p>") ?>');
        $view = $this->factory->view('escape');

        $result = $view->render();

        self::assertSame('&lt;p class=&quot;intro&quot;&gt;Lorem ipsum&lt;/p&gt;', $result);
    }

    /** @test */
    public function batchProcessingBeforeEscaping()
    {
        $this->createTemplate('escape.php', '<?= $e($title, "strtoupper") ?>');
        $view = $this->factory->view('escape');

        $result = $view->render(['title' => 'Lorem & Ipsum']);

        self::assertSame('LOREM &amp; IPSUM', $result);
    }
}
