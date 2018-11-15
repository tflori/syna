<?php

namespace Syna\ViewHelper;

use Syna\View;

interface ViewHelperInterface
{
    public function __invoke();

    public function setView(View $view);
}
