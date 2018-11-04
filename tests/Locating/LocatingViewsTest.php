<?php

namespace Syna\Test\Locating;

use Syna\Test\TestCase;
use Syna\ViewLocator;

class LocatingViewsTest extends TestCase
{
    /** @test */
    public function locatesPhtmlTemplatesByDefault()
    {
        $this->createTemplate('greeting.phtml', 'Hello <?= $name ?>!');
        $locator = new ViewLocator($this->templatePath);

        $result = $locator->has('greeting');

        self::assertTrue($result);
    }

    /** @test */
    public function returnsFullPathToTemplate()
    {
        $fullPath = $this->createTemplate('greeting.phtml', 'Hello <?= $name ?>!');
        $locator = new ViewLocator($this->templatePath);

        $path = $locator->getPath('greeting');

        self::assertSame($fullPath, $path);
    }

    /** @test */
    public function isNotConfusedByAdditionalSlashes()
    {
        $fullPath = $this->createTemplate('form/text.phtml', '<input type="text" name="<?= $e($name) ?>" />');
        $locator = new ViewLocator($this->templatePath);

        $path = $locator->getPath('/form//text');

        self::assertSame($fullPath, $path);
    }

    /** @test */
    public function throwsWhenViewIsNotAvailable()
    {
        $locator = new ViewLocator($this->templatePath);

        self::expectException(\Exception::class);
        self::expectExceptionMessage('View test not found');

        $locator->getPath('test');
    }

    /** @test */
    public function viewsCanManuallyBeAddedWithDifferentNames()
    {
        $fullPath = $this->createTemplate('greeting.phtml', 'Hello <?= $name ?>!');
        $locator = new ViewLocator('/etc');

        $locator->add('greet', $fullPath);

        self::assertSame($fullPath, $locator->getPath('greet'));
    }

    /** @test */
    public function throwsWhenFileDoesNotExist()
    {
        $locator = new ViewLocator('/');

        self::expectException(\LogicException::class);
        self::expectExceptionMessage('File /var/template.phtml does not exist');

        $locator->add('template', '/var/template.phtml');
    }

    /** @test */
    public function searchesTemplatesInReversedOrder()
    {
        $defaultPath = $this->createTemplate('default/greeting.phtml', 'Hello <?= $name ?>!');
        $themedPath = $this->createTemplate('theme/greeting.phtml', '<p>Hello <?= $name ?>!</p>');

        $locator = new ViewLocator($this->templatePath . '/default');
        $locator->addPath($this->templatePath . '/theme');

        $path = $locator->getPath('greeting');

        self::assertSame($themedPath, $path);
    }

    /** @test */
    public function addFallbackPathWithPrepend()
    {
        $defaultPath = $this->createTemplate('default/greeting.phtml', 'Hello <?= $name ?>!');
        $this->createTemplate('fallback/greeting.phtml', 'Hello <?= $name ?>!');
        $fallbackPath = $this->createTemplate('fallback/welcome.phtml', 'Welcome <?= $name ?>!');

        $locator = new ViewLocator($this->templatePath . '/default');
        $locator->prependPath($this->templatePath . '/fallback');

        self::assertSame($defaultPath, $locator->getPath('greeting'));
        self::assertSame($fallbackPath, $locator->getPath('welcome'));
    }
}
