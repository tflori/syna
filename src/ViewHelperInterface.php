<?php

namespace Syna;

interface ViewHelperInterface
{
    public function __invoke();

    public function setView(View $view);
}
