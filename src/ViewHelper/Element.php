<?php

namespace Syna\ViewHelper;

/**
 * Create an element with attributes and content
 *
 * @package App\View\Helper
 */
class Element extends AbstractViewHelper
{
    /**
     * @param string $name
     * @param array $attributes
     * @param string $content Text or html (set escape to false)
     * @param bool $escape Escape the text or insert html (XSS?)
     * @return string
     */
    public function __invoke(string $name = '', array $attributes = [], string $content = null, bool $escape = true)
    {
        if ($escape && !empty($content)) {
            $content = $this->escape($content);
        }

        $tagContent = '';
        if (count($attributes)) {
            $renderedAttributes = [];
            foreach ($attributes as $attribute => $value) {
                $renderedAttributes[] = $attribute . '="' . $this->view->escape($value) . '"';
            }
            $tagContent = ' ' . implode(' ', $renderedAttributes);
        }

        if (empty($content)) {
            return sprintf('<%s%s />', $name, $tagContent);
        }

        return sprintf('<%s%s>%s</%s>', $name, $tagContent, $content, $name);
    }
}
