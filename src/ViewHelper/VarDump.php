<?php

namespace Syna\ViewHelper;

class VarDump extends AbstractViewHelper
{
    public function __invoke($var = null, $highlight = true)
    {
        $definition = var_export($var, true);

        if ($highlight) {
            $highlighted = highlight_string('<?php ' . $definition, true);
            $definition = str_replace('&lt;?php&nbsp;', '', $highlighted);
        }

        return '<pre>' . $definition . '</pre>';
    }
}
