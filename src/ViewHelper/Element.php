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
    public function __invoke($name = '', $attributes = [], $content = null, $escape = true)
    {
        $doc = new \DOMDocument('1.0', 'utf-8');
        $el = $doc->importNode($this->buildElement($name, $content, $escape), true);

        foreach ($attributes as $attribute => $value) {
            $attr = $doc->createAttribute($attribute);
            $attr->value = $value;
            $el->appendChild($attr);
        }

        $doc->appendChild($el);
        return $doc->saveHTML();
    }

    protected function buildElement($name, $content, $escape)
    {
        $doc = new \DOMDocument('1.0', 'utf-8');

        if (!$content) {
            // without content we can just create an element
            return $doc->createElement($name);
        } else {
            // otherwise we surround it with the element and load it as html
            $content = sprintf('<%s>%s</%s>', $name, $escape ? htmlspecialchars($content) : $content, $name);
            $doc->loadHTML($content);
            return $doc->getElementsByTagName('body')->item(0)->childNodes->item(0);
        }
    }
}
