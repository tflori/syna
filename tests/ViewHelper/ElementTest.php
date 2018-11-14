<?php

namespace Syna\Test\ViewHelper;

use Syna\Factory;
use Syna\Test\TestCase;
use Syna\View;
use Syna\ViewHelper\Element;
use Syna\ViewLocator;

class ElementTest extends TestCase
{
    /** @test */
    public function createsEmptyElement()
    {
        $viewHelper = new Element();

        $element = $viewHelper('br');

        self::assertSame('<br />', $element);
    }

    /** @test */
    public function addsAttributesToElements()
    {
        $viewHelper = new Element();
        $viewHelper->setView($this->createView());

        $element = $viewHelper('input', [
            'name' => 'test',
            'value' => 'foo bar'
        ]);

        self::assertSame('<input name="test" value="foo bar" />', $element);
    }

    /** @test */
    public function escapesContentByDefault()
    {
        $viewHelper = new Element();
        $viewHelper->setView($this->createView());

        $element = $viewHelper('textarea', [], '<!DOCTYPE html>');

        self::assertSame('<textarea>&lt;!DOCTYPE html&gt;</textarea>', $element);
    }
}
