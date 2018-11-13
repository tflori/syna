<?php

namespace Syna\Test\View;

use Syna\Factory;
use Syna\Test\TestCase;
use Mockery as m;
use Syna\ViewLocator;

/**
 * Class ExtendingAndPartialsTest
 *
 * @package Syna\Test\View
 * @author Thomas Flori <thflori@gmail.com>
 * @covers \Syna\View
 */
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
    public function sectionsCanBeAccessedViaMagicGetter()
    {
        $this->createTemplate(
            'parent.php',
            '<h2><?= $v->title ?></h2>' . PHP_EOL .
            '<div class="content"><?= $v->content ?></div>'
        );
        $this->createTemplate(
            'child.php',
            '<?php $v->extend("parent") ?>' . PHP_EOL .
            '<p>Lorem ipsum</p>' . PHP_EOL .
            '<?php $v->provide("title", "Hello World!") ?>'
        );

        $result = $this->factory->render('child');

        self::assertSame(
            '<h2>Hello World!</h2>' . PHP_EOL .
            '<div class="content"><p>Lorem ipsum</p></div>',
            $result
        );
    }

    /** @test */
    public function defineDefaultsUsingSectionMethod()
    {
        $this->createTemplate(
            'parent.php',
            '<h2><?= $v->section("title", "Foo") ?></h2>' . PHP_EOL .
            '<div class="content"><?= $v->content ?></div>'
        );
        $this->createTemplate(
            'child.php',
            '<?php $v->extend("parent") ?>' . PHP_EOL .
            '<p>Lorem ipsum</p>' . PHP_EOL
        );

        $result = $this->factory->render('child');

        self::assertSame(
            '<h2>Foo</h2>' . PHP_EOL .
            '<div class="content"><p>Lorem ipsum</p></div>',
            $result
        );
    }

    /** @test */
    public function defineDefaultsWithCoalesce()
    {
        $this->createTemplate(
            'parent.php',
            '<h2><?= $v->title ?? "Foo" ?></h2>' . PHP_EOL .
            '<div class="content"><?= $v->content ?></div>'
        );
        $this->createTemplate(
            'child.php',
            '<?php $v->extend("parent") ?>' . PHP_EOL .
            '<p>Lorem ipsum</p>' . PHP_EOL
        );

        $result = $this->factory->render('child');

        self::assertSame(
            '<h2>Foo</h2>' . PHP_EOL .
            '<div class="content"><p>Lorem ipsum</p></div>',
            $result
        );
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

    /** @test */
    public function fetchRendersAPartialTemplate()
    {
        $this->createTemplate('fetch.php', '<?= $v->fetch("partial", ["value" => "John Doe", "name" => "name"]) ?>');
        $this->createTemplate('partial.php', '<input type="text" name="<?= $e($name) ?>" value="<?= $e($value) ?>">');

        $result = $this->factory->render('fetch');

        self::assertSame('<input type="text" name="name" value="John Doe">', $result);
    }

    /** @test */
    public function theParentsParentGetsSectionsFromChild()
    {
        $this->createTemplate('grandParent.php', '<h1><?= $v->title ?></h1><?= $v->content ?>');
        $this->createTemplate(
            'parent.php',
            '<?php $v->extend("grandParent") ?>' . PHP_EOL .
            '<div class="content"><?= $v->content ?></div>'
        );
        $this->createTemplate(
            'child.php',
            '<?php $v->extend("parent") ?>' . PHP_EOL .
            '<?php $v->provide("title", "Foo Bar") ?>' . PHP_EOL .
            '<p>Lorem ipsum</p>'
        );

        $result = $this->factory->render('child');

        self::assertSame(
            '<h1>Foo Bar</h1>' .
            '<div class="content"><p>Lorem ipsum</p></div>',
            $result
        );
    }
}
