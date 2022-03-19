<?php

namespace Syna\Test;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Syna\Factory;
use Syna\ViewLocator;

class TestCase extends MockeryTestCase
{
    protected $templatePath;

    protected function setUp(): void
    {
        $this->templatePath = '/tmp/syna-test';
        if (file_exists($this->templatePath)) {
            system('rm -Rf ' . $this->templatePath);
        }
        mkdir($this->templatePath);
    }

    protected function createView(string $name = 'test', string $content = '')
    {
        $this->createTemplate($name . '.php', $content);
        $factory = new Factory(new ViewLocator($this->templatePath));
        return $factory->view($name);
    }

    protected function createTemplate(string $path, string $content): string
    {
        $path = $this->templatePath . DIRECTORY_SEPARATOR . $path;
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        file_put_contents($path, $content);
        return $path;
    }

    protected static function accessProtected($obj, $prop)
    {
        $reflection = new \ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }
}
