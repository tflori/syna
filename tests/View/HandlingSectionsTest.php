<?php

namespace Syna\Test\View;

use Syna\Factory;
use Syna\Test\TestCase;
use Mockery as m;
use Syna\ViewLocator;

/**
 * Class HandlingSectionsTest
 *
 * @package Syna\Test\View
 * @author Thomas Flori <thflori@gmail.com>
 * @covers \Syna\View
 */
class HandlingSectionsTest extends TestCase
{
    /** @var m\Mock|Factory */
    protected $factory;

    protected function setUp()
    {
        parent::setUp();
        $this->factory = m::mock(Factory::class, [new ViewLocator($this->templatePath)])->makePartial();
    }

    /** @test */
    public function contentBetweenStartAndEndBelongsToSection()
    {
        $this->createTemplate('section.php', '<?php $v->start("foo") ?>Foo<?php $v->end() ?>');
        $view = $this->factory->view('section');

        $content = $view->render();

        self::assertSame('', $content);
        self::assertSame('Foo', $view->getSections()['foo']);
    }

    /** @test */
    public function sectionNameContentIsNotAllowed()
    {
        $this->createTemplate('section.php', '<?php $v->start("content") ?>Foo<?php $v->end() ?>');
        $view = $this->factory->view('section');

        self::expectException(\LogicException::class);
        self::expectExceptionMessage('The section name "content" is reserved.');

        $view->render();
    }

    /** @test */
    public function sectionsCanNotBeNested()
    {
        $this->createTemplate(
            'section.php',
            '<?php $v->start("foo") ?>
                Foo
                <?php $v->start("bar"); ?>
                    Bar
                <?php $v->end() ?>
            <?php $v->end() ?>'
        );
        $view = $this->factory->view('section');

        self::expectException(\LogicException::class);
        self::expectExceptionMessage('You cannot nest sections within other sections.');

        $view->render();
    }

    /** @test */
    public function simpleSectionsCanBeCreatedWithProvide()
    {
        $this->createTemplate('section.php', '<?php $v->provide("foo", "Foo") ?>');
        $view = $this->factory->view('section');

        $view->render();

        self::assertSame('Foo', $view->getSections()['foo']);
    }

    /** @test */
    public function sectionsAreTrimmedExceptSpacesAndTabs()
    {
        $this->createTemplate(
            'section.php',
            '<?php $v->start("foo") ?>' . PHP_EOL .
            '  Foo' . PHP_EOL .
            '<?php $v->end() ?>'
        );

        $view = $this->factory->view('section');

        $view->render();

        self::assertSame('  Foo', $view->getSections()['foo']);
    }

    /** @test */
    public function sectionsCanBeAccessedUsingSection()
    {
        $this->createTemplate('parent.php', '<?= $this->section("foo") ?>');
        $view = $this->factory->view('parent');
        $view->setSections(['foo' => 'Foo']);

        $result = $view->render();

        self::assertSame('Foo', $result);
    }

    /** @test */
    public function undefinedSectionsAreEmpty()
    {
        $this->createTemplate('parent.php', '<?= $this->section("foo") ?>');
        $view = $this->factory->view('parent');

        $result = $view->render();

        self::assertSame('', $result);
    }

    /** @test */
    public function alternativeUsedForUndefinedSection()
    {
        $this->createTemplate('parent.php', '<?= $this->section("foo", "Unknown") ?>');
        $view = $this->factory->view('parent');

        $result = $view->render();

        self::assertSame('Unknown', $result);
    }

    /** @test */
    public function alternativeIsNotUsedForEmptySections()
    {
        $this->createTemplate('parent.php', '<?= $this->section("foo", "Unknown") ?>');
        $view = $this->factory->view('parent');
        $view->setSections(['foo' => '']);

        $result = $view->render();

        self::assertSame('', $result);
    }

    /** @test */
    public function sectionContentCanBeAppended()
    {
        $this->createTemplate(
            'section.php',
            '<?php $v->provide("foo", "Bar", true) ?>' . PHP_EOL .
            '<?php $v->start("foo", true) ?>Baz<?php $v->end() ?>'
        );
        $view = $this->factory->view('section');
        $view->setSections(['foo' => 'Foo']);

        $view->render();

        self::assertSame('FooBarBaz', $view->getSections()['foo']);
    }

    /** @test */
    public function sectionHasToBeStartedBeforeEnding()
    {
        $this->createTemplate('section.php', '<?php $v->end() ?>');
        $view = $this->factory->view('section');

        self::expectException(\LogicException::class);
        self::expectExceptionMessage('You must start a section before you can end it.');

        $view->render();
    }
}
