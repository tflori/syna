<?php

namespace Syna\Test\Locating;

use Syna\NotFound;
use Syna\Test\TestCase;
use Syna\ViewLocator;

/**
 * Class LocatingViewsTest
 *
 * @package Syna\Test\Locating
 * @author Thomas Flori <thflori@gmail.com>
 * @covers \Syna\ViewLocator
 */
class LocatingViewsTest extends TestCase
{
    /** @test */
    public function locatesPhtmlTemplatesByDefault()
    {
        $this->createTemplate('greeting.php', 'Hello <?= $name ?>!');
        $locator = new ViewLocator($this->templatePath);

        $result = $locator->has('greeting');

        self::assertTrue($result);
    }

    /** @test */
    public function returnsFullPathToTemplate()
    {
        $fullPath = $this->createTemplate('greeting.php', 'Hello <?= $name ?>!');
        $locator = new ViewLocator($this->templatePath);

        $path = $locator->getPath('greeting');

        self::assertSame($fullPath, $path);
    }

    /** @test */
    public function isNotConfusedByAdditionalSlashes()
    {
        $fullPath = $this->createTemplate('form/text.php', '<input type="text" name="<?= $e($name) ?>" />');
        $locator = new ViewLocator($this->templatePath);

        $path = $locator->getPath('/form//text');

        self::assertSame($fullPath, $path);
    }

    /** @test */
    public function throwsWhenViewIsNotAvailable()
    {
        $locator = new ViewLocator($this->templatePath);

        self::expectException(NotFound::class);
        self::expectExceptionMessage('View test not found');

        $locator->getPath('test');
    }

    /** @test */
    public function viewsCanManuallyBeAddedWithDifferentNames()
    {
        $fullPath = $this->createTemplate('greeting.php', 'Hello <?= $name ?>!');
        $locator = new ViewLocator('/etc');

        $locator->add('greet', $fullPath);

        self::assertSame($fullPath, $locator->getPath('greet'));
    }

    /** @test */
    public function throwsWhenFileDoesNotExist()
    {
        $locator = new ViewLocator('/');

        self::expectException(\LogicException::class);
        self::expectExceptionMessage('File /var/template.php does not exist');

        $locator->add('template', '/var/template.php');
    }

    /** @test */
    public function searchesTemplatesInReversedOrder()
    {
        $defaultPath = $this->createTemplate('default/greeting.php', 'Hello <?= $name ?>!');
        $themedPath = $this->createTemplate('theme/greeting.php', '<p>Hello <?= $name ?>!</p>');

        $locator = new ViewLocator($this->templatePath . '/default');
        $locator->addPath($this->templatePath . '/theme');

        $path = $locator->getPath('greeting');

        self::assertSame($themedPath, $path);
    }

    /** @test */
    public function addFallbackPathWithPrepend()
    {
        $defaultPath = $this->createTemplate('default/greeting.php', 'Hello <?= $name ?>!');
        $this->createTemplate('fallback/greeting.php', 'Hello <?= $name ?>!');
        $fallbackPath = $this->createTemplate('fallback/welcome.php', 'Welcome <?= $name ?>!');

        $locator = new ViewLocator($this->templatePath . '/default');
        $locator->prependPath($this->templatePath . '/fallback');

        self::assertSame($defaultPath, $locator->getPath('greeting'));
        self::assertSame($fallbackPath, $locator->getPath('welcome'));
    }
}
