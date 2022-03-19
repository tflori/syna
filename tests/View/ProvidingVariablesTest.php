<?php

namespace Syna\Test\View;

use Syna\Factory;
use Syna\Test\TestCase;
use Syna\View;
use Syna\ViewLocator;

/**
 * Class ProvidingVariablesTest
 *
 * @package Syna\Test\View
 * @author Thomas Flori <thflori@gmail.com>
 * @covers \Syna\View
 */
class ProvidingVariablesTest extends TestCase
{
    /** @var Factory */
    protected $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new Factory(new ViewLocator($this->templatePath));
    }

    /** @test */
    public function variablesAreExtracted()
    {
        $this->createTemplate('hello.php', 'Hello <?= $name ?>!');
        $view = $this->factory->view('hello');

        $result = $view->render(['name' => 'John']);

        self::assertSame('Hello John!', $result);
    }

    /** @test */
    public function provideToConstructor()
    {
        $this->createTemplate('hello.php', 'Hello <?= $name ?>!');

        $view = $this->factory->view('hello', ['name' => 'Jane']);

        self::assertSame('Hello Jane!', $view->render());
    }

    /** @test */
    public function shareDataBetweenViews()
    {
        $this->createTemplate('hello.php', 'Hello <?= $name ?>!');
        $this->createTemplate('login.php', 'Logged in as <?= $name ?>.');

        $this->factory->addSharedData(['name' => 'Max']);
        $hello = $this->factory->view('hello');
        $login = $this->factory->view('login');

        self::assertSame('Hello Max!', $hello->render());
        self::assertSame('Logged in as Max.', $login->render());
    }

    /** @test */
    public function preservedVariablesAreIgnored()
    {
        var_dump(get_defined_vars());
        $this->createTemplate('test.php', '<?= get_class($this) ?>,<?= get_class($v) ?>,<?= is_callable($e) ?>');

        $view = $this->factory->view('test', [
            'this' => $this,
            'v' => (object)[],
            'e' => 'not callable string'
        ]);

        self::assertSame(View::class . ',' . View::class . ',1', $view->render());
    }

    /** @test */
    public function overwriteVariables()
    {
        $this->createTemplate('hello.php', 'Hello <?= $name ?>!');
        $this->createTemplate('login.php', 'Logged in as <?= $name ?>.');

        $this->factory->addSharedData(['name' => 'Max']);
        $hello = $this->factory->view('hello')->render(['name' => 'John']);
        $login = $this->factory->view('login', ['name' => 'j.doe'])->render();

        self::assertSame('Hello John!', $hello);
        self::assertSame('Logged in as j.doe.', $login);
    }
}
