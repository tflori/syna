<?php

namespace Syna\Test\View;

use Syna\Factory;
use Syna\Test\TestCase;
use Mockery as m;
use Syna\ViewLocator;

/**
 * Class LayoutsTest
 *
 * @package Syna\Test\View
 * @author Thomas Flori <thflori@gmail.com>
 * @covers \Syna\View
 */
class LayoutsTest extends TestCase
{
    /** @var m\Mock|Factory */
    protected $factory;

    protected function setUp()
    {
        parent::setUp();
        $this->factory = m::mock(Factory::class, [
            new ViewLocator($this->templatePath . '/views'),
            null,
            new ViewLocator($this->templatePath . '/layouts')
        ])->makePartial();
    }

    /** @test */
    public function itThrowsWhenNoLayoutLocatorIsDefined()
    {
        $factory = new Factory(new ViewLocator($this->templatePath));
        $this->createTemplate('view.php', '<p>Lorem ipsum</p>');

        self::expectException(\LogicException::class);
        self::expectExceptionMessage('No locator for layout defined');

        $factory->render('view', [], 'layout');
    }

    /** @test */
    public function layoutGetsTheContentAsSection()
    {
        $this->createTemplate('views/view.php', '<p>Lorem ipsum</p>');
        $this->createTemplate('layouts/layout.php', '<div class="content"><?= $v->section("content") ?></div>');

        $result = $this->factory->render('view', [], 'layout');

        self::assertSame('<div class="content"><p>Lorem ipsum</p></div>', $result);
    }

    /** @test */
    public function layoutGetsAllSectionsDefined()
    {
        $this->createTemplate('views/view.php', '<?php $v->provide("title", "Foo") ?><p>Lorem ipsum</p>');
        $this->createTemplate(
            'layouts/layout.php',
            '<h1><?= $v->section("title") ?></h1>' . PHP_EOL .
            '<div class="content"><?= $v->section("content") ?></div>'
        );

        $result = $this->factory->render('view', [], 'layout');

        self::assertSame(
            '<h1>Foo</h1>' . PHP_EOL .
            '<div class="content"><p>Lorem ipsum</p></div>',
            $result
        );
    }

    /** @test */
    public function provideSharedDataForLayouts()
    {
        $this->createTemplate('views/view.php', '<p>Lorem ipsum</p>');
        $this->createTemplate(
            'layouts/layout.php',
            '<h1><?= $title ?></h1>' . PHP_EOL .
            '<div class="content"><?= $v->section("content") ?></div>'
        );

        $this->factory->addSharedData(['title' => 'Foo Bar']);
        $result = $this->factory->render('view', [], 'layout');

        self::assertSame(
            '<h1>Foo Bar</h1>' . PHP_EOL .
            '<div class="content"><p>Lorem ipsum</p></div>',
            $result
        );
    }
}
