<?php

namespace Syna\Test;

use Syna\Factory;
use Mockery as m;
use Syna\HelperLocator;
use Syna\View;
use Syna\ViewHelper\CallableHelper;
use Syna\ViewLocator;

/**
 * Class FactoryTest
 *
 * @package Syna\Test
 * @author Thomas Flori <thflori@gmail.com>
 * @covers \Syna\Factory
 */
class FactoryTest extends TestCase
{
    /** @test */
    public function usesTheViewLocatorToFindViews()
    {
        $viewLocator = m::mock(ViewLocator::class);
        $factory = new Factory($viewLocator);

        $viewLocator->shouldReceive('has')->with('viewName')
            ->once()->ordered()->andReturn(true);
        $viewLocator->shouldReceive('getPath')->with('viewName')
            ->once()->ordered()->andReturn('/any/path');

        $view = $factory->view('viewName');

        self::assertSame('/any/path', self::accessProtected($view, 'path'));
    }

    /** @test */
    public function usesTheLayoutLocatorToFindLayouts()
    {
        $layoutLocator = m::mock(ViewLocator::class);
        $factory = new Factory(new ViewLocator($this->templatePath), null, $layoutLocator);
        $this->createTemplate('viewName.php', '');

        $layoutLocator->shouldReceive('has')->with('layoutName')
            ->once()->ordered()->andReturn(true);
        $layoutLocator->shouldReceive('getPath')->with('layoutName')
            ->once()->ordered()->andReturn('/any/path');

        $factory->view('viewName', [], 'layoutName');
    }

    /** @test */
    public function namedLocatorsCanBeDefinedAndUsed()
    {
        $mailLocator = m::mock(ViewLocator::class);
        $factory = new Factory(new ViewLocator($this->templatePath));

        $mailLocator->shouldReceive('has')->with('viewName')
            ->once()->ordered()->andReturn(true);
        $mailLocator->shouldReceive('getPath')->with('viewName')
            ->once()->ordered()->andReturn('/any/path');

        $factory->addLocator('mail', $mailLocator);
        $factory->view('mail::viewName');
    }

    /** @test */
    public function throwsWhenALocatorIsNotDefined()
    {
        $factory = new Factory(new ViewLocator($this->templatePath));

        self::expectException(\LogicException::class);
        self::expectExceptionMessage('No locator for mail defined');

        $factory->view('mail::viewName');
    }

    /** @test */
    public function throwsWhenViewDoesNotExist()
    {
        $viewLocator = m::mock(ViewLocator::class);
        $factory = new Factory($viewLocator);

        $viewLocator->shouldReceive('has')->with('viewName')
            ->once()->ordered()->andReturn(false);

        self::expectException(\Exception::class);
        self::expectExceptionMessage('View viewName not found');

        $factory->view('viewName');
    }

    /** @test */
    public function usesTheHelperLocatorToFindHelpers()
    {
        $helperLocator = m::mock(HelperLocator::class);
        $factory = new Factory(new ViewLocator($this->templatePath), $helperLocator);
        $view = new View($factory, '/any/path');

        $helperLocator->shouldReceive('has')->with('helperName')
            ->once()->ordered()->andReturn(true);
        $helperLocator->shouldReceive('getHelper')->with('helperName')
            ->once()->ordered()->andReturn(new CallableHelper('strtoupper'));

        $result = $factory->helper($view, 'helperName', 'argument1');

        self::assertSame('ARGUMENT1', $result);
    }

    /** @test */
    public function usesACallable()
    {
        $helperLocator = m::mock(HelperLocator::class);
        $factory = new Factory(new ViewLocator($this->templatePath), $helperLocator);
        $view = new View($factory, '/any/path');

        $helperLocator->shouldReceive('has')->with('strtoupper')
            ->once()->ordered()->andReturn(false);

        $result = $factory->helper($view, 'strtoupper', 'argument1');

        self::assertSame('ARGUMENT1', $result);
    }

    /** @test */
    public function throwsWhenHelperNotFoundAndNotCallable()
    {
        $helperLocator = m::mock(HelperLocator::class);
        $factory = new Factory(new ViewLocator($this->templatePath), $helperLocator);
        $view = new View($factory, '/any/path');

        $helperLocator->shouldReceive('has')->with('helperName')
            ->once()->ordered()->andReturn(false);

        self::expectException(\Exception::class);
        self::expectExceptionMessage('$function has to be callable or a registered view helper');

        $factory->helper($view, 'helperName', 'argument1');
    }

    /** @test */
    public function isAStorageForSharedData()
    {
        $factory = new Factory(new ViewLocator($this->templatePath));

        $factory->addSharedData(['var1' => 'foo bar']);
        self::assertSame(['var1' => 'foo bar'], $factory->getSharedData());

        $factory->addSharedData(['var1' => 'Foo Bar', 'var2' => 'John Doe']);
        self::assertSame(['var1' => 'Foo Bar', 'var2' => 'John Doe'], $factory->getSharedData());
    }

    /** @test */
    public function retrieveStoredLocators()
    {
        $factory = new Factory(new ViewLocator($this->templatePath));

        self::assertInstanceOf(ViewLocator::class, $factory->getLocator());
        self::assertInstanceOf(HelperLocator::class, $factory->getHelperLocator());

        $factory->addLocator('mail', $mailLocator = new ViewLocator($this->templatePath));
        self::assertSame($mailLocator, $factory->getLocator('mail'));
    }
}
