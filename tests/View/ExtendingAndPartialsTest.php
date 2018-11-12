<?php

namespace Syna\Test\View;

use Syna\Factory;
use Syna\Test\TestCase;
use Mockery as m;
use Syna\ViewLocator;

class ExtendingAndPartialsTest extends TestCase
{
    /** @var m\Mock|Factory */
    protected $factory;

    protected function setUp()
    {
        parent::setUp();
        $this->factory = m::mock(Factory::class, [new ViewLocator($this->templatePath)])->makePartial();
    }

    /** @test */
    public function viewsCanExtendOtherViews()
    {
        $this->createTemplate('parent.php', '<div class="content"><?= $v->section("content") ?></div>');
        $this->createTemplate(
            'child.php',
            '<?php $v->extend("parent") ?>' . PHP_EOL .
            '<p>Lorem ipsum</p>'
        );

        $result = $this->factory->render('child');

        self::assertSame('<div class="content"><p>Lorem ipsum</p></div>', $result);
    }

    /** @test */
    public function parentsCanUseSectionsCreatedInChild()
    {
        $this->createTemplate('parent.php', '<h2><?= $v->section("title") ?></h2>');
        $this->createTemplate(
            'child.php',
            '<?php $v->extend("parent") ?>' . PHP_EOL .
            '<?php $v->provide("title", "Hello World!") ?>'
        );

        $result = $this->factory->render('child');

        self::assertSame('<h2>Hello World!</h2>', $result);
    }

    /** @test */
    public function parentsGetAllDataByDefault()
    {
        $this->createTemplate('parent.php', '<h2><?= $title ?></h2>');
        $this->createTemplate('child.php', '<?php $v->extend("parent") ?>');

        $result = $this->factory->render('child', ['title' => 'Hello World!']);

        self::assertSame('<h2>Hello World!</h2>', $result);
    }

    /** @test */
    public function changeTheDataAParentGets()
    {
        $this->createTemplate('parent.php', '<h2><?= $title ?? "" ?><small><?= $subTitle ?></small></h2>');
        $this->createTemplate('child.php', '<?php $v->extend("parent", ["subTitle" => "Foo Bar"]) ?>');

        $result = $this->factory->render('child', ['title' => 'Hello World!']);

        self::assertSame('<h2><small>Foo Bar</small></h2>', $result);
    }
}
