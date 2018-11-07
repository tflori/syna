<?php

namespace Syna\Test\Examples;

use Syna\ViewHelper\AbstractViewHelper;

class DateTimeFormat extends AbstractViewHelper
{
    protected $format = 'Y-m-d H:i:s';

    /**
     * DateTimeFormat constructor.
     *
     * @param string $format
     */
    public function __construct(string $format = 'Y-m-d H:i:s')
    {
        $this->format = $format;
    }

    public function setFormat(string $format): self
    {
        $this->format = $format;
        return $this;
    }

    public function __invoke($dateTime = null)
    {
        return $dateTime instanceof \DateTime ? $dateTime->format($this->format) :
            date($this->format, $dateTime);
    }
}
