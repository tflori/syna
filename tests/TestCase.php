<?php

namespace Syna\Test;

use Mockery\Adapter\Phpunit\MockeryTestCase;

class TestCase extends MockeryTestCase
{
    protected $templatePath;

    protected function setUp()
    {
        $this->templatePath = '/tmp/syna-test';
        if (file_exists($this->templatePath)) {
            system('rm -Rf ' . $this->templatePath);
        }
        mkdir($this->templatePath);
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
