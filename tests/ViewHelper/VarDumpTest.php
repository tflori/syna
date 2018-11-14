<?php

namespace Syna\Test\ViewHelper;

use Syna\Test\TestCase;
use Syna\ViewHelper\VarDump;

class VarDumpTest extends TestCase
{
    /** @test */
    public function usesVarExportToDumpVariables()
    {
        $viewHelper = new VarDump();
        $var = ['foo' => 'bar'];

        $result = $viewHelper($var, false);

        self::assertContains(var_export($var, true), $result);
    }

    /** @test */
    public function highlightsUsingHighlightString()
    {
        $viewHelper = new VarDump();
        $var = ['foo' => 'bar'];

        $result = $viewHelper($var, true);

        self::assertContains('<code><span style="color: #000000">', $result);
        self::assertContains('</span>' . PHP_EOL . '</code>', $result);
    }

    /** @test */
    public function surroundsOutputWithPre()
    {
        $viewHelper = new VarDump();
        $var = ['foo' => 'bar'];

        $result = $viewHelper($var, false);

        self::assertStringStartsWith('<pre>', $result);
        self::assertStringEndsWith('</pre>', $result);
    }
}
