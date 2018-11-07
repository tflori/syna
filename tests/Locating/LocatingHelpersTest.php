<?php

namespace Syna\Test\Locating;

use Syna\HelperLocator;
use Syna\NotFound;
use Syna\Test\Examples;
use Syna\Test\Examples\DateTimeFormat;
use Syna\Test\Examples\Deeper;
use Syna\Test\TestCase;
use Syna\ViewHelper\CallableHelper;
use Syna\ViewHelper\Element;
use Syna\ViewHelperInterface;

class LocatingHelpersTest extends TestCase
{
    /** @test */
    public function loadsViewHelpersFromSynaByDefault()
    {
        $locator = new HelperLocator();

        $result = $locator->has('element');

        self::assertTrue($result);
    }

    /** @test */
    public function returnsViewHelper()
    {
        $locator = new HelperLocator();

        $helper = $locator->getHelper('element');

        self::assertInstanceOf(Element::class, $helper);
    }

    /** @test */
    public function defineCallablesAsHelper()
    {
        $locator = new HelperLocator();

        $locator->add('lower', 'strtolower');

        self::assertInstanceOf(CallableHelper::class, $locator->getHelper('lower'));
    }

    /** @test */
    public function defineViewHelperInstancesAsHelper()
    {
        $locator = new HelperLocator();
        $helper = new CallableHelper(function ($format, $dateTime) {
            return $dateTime instanceof \DateTime ? $dateTime->format($format) : date($format, strtotime($dateTime));
        });

        $locator->add('dtFormat', $helper);

        self::assertSame($helper, $locator->getHelper('dtFormat'));
    }

    /** @test */
    public function defineClassAsHelper()
    {
        $locator = new HelperLocator();

        $locator->add('dtFormat', DateTimeFormat::class);

        self::assertInstanceOf(DateTimeFormat::class, $locator->getHelper('dtFormat'));
    }

    /** @test */
    public function otherValuesAreNotAllowed()
    {
        $locator = new HelperLocator();

        self::expectException(\LogicException::class);
        self::expectExceptionMessage(
            'Helper has to be a callable, an instance of ' . ViewHelperInterface::class . ' or a class name'
        );

        $locator->add('dateFormat', 'Y-m-d');
    }

    /** @test */
    public function addNamespaceInConstructor()
    {
        $locator = new HelperLocator(Examples::class);

        $helper = $locator->getHelper('dateTimeFormat');

        self::assertInstanceOf(DateTimeFormat::class, $helper);
    }

    /** @test */
    public function laterNamespacesHaveHigherPriority()
    {
        $locator = new HelperLocator(Examples::class);

        $locator->addNamespace(Deeper::class);
        $helper = $locator->getHelper('dateTimeFormat');

        self::assertInstanceOf(Deeper\DateTimeFormat::class, $helper);
    }

    /** @test */
    public function underScoresRepresentNamespaceDivider()
    {
        $locator = new HelperLocator(Examples::class);

        $helper = $locator->getHelper('deeper_dateTimeFormat');

        self::assertInstanceOf(Deeper\DateTimeFormat::class, $helper);
    }

    /** @test */
    public function prependedNamespacesComeLast()
    {
        $locator = new HelperLocator(Examples::class);

        $locator->prependNamespace(Deeper::class);
        $helper = $locator->getHelper('dateTimeFormat');

        self::assertInstanceOf(DateTimeFormat::class, $helper);
    }

    /** @test */
    public function throwsWhenViewHelperIsNotFound()
    {
        $locator = new HelperLocator();

        self::expectException(NotFound::class);
        self::expectExceptionMessage('View helper dateTimeFormat not found');

        $locator->getHelper('dateTimeFormat');
    }
}