<?php

namespace Syna;

class View
{
    /** @var Factory */
    protected $factory;

    /** @var array  */
    protected $data = [];

    /** @var string  */
    protected $path;

    /** @var View */
    protected $layout;

    /** @var View */
    protected $parentView;

    /** @var array  */
    protected $sections = [];

    /** @var string */
    protected $sectionName;

    /** @var bool */
    protected $appendSection = false;

    public function __construct(Factory $factory, string $path, array $data = [], View $layout = null)
    {
        $this->factory = $factory;
        $this->path = $path;
        $this->layout = $layout;

        $this->data = array_merge($factory->getSharedData(), $data);
    }

    /**
     * The string representation of a view is it's rendered content
     *
     * @return string
     * @codeCoverageIgnore Just a wrapper
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Calls to undefined methods are executed as helper
     *
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->factory->helper($this, $name, ...$arguments);
    }

    public function __get($name)
    {
        return $this->section($name);
    }

    /**
     * Add $data for the view
     *
     * Later defined data overwrites current data.
     *
     * @param array $data
     * @return View
     */
    public function addData(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Predefine section content
     *
     * @param string[] $sections
     * @return View
     */
    public function setSections($sections): self
    {
        $this->sections = $sections;
        return $this;
    }

    /**
     * Get the current sections
     *
     * @return array
     */
    public function getSections(): array
    {
        return $this->sections;
    }

    /**
     * Render the view
     *
     * @param array|null $data
     * @return string
     */
    public function render(array $data = null): string
    {
        if ($data) {
            $this->addData($data);
        }
        unset($data);

        if (isset($this->data['this'])) {
            unset($this->data['this']); // avoid fatal error
        }
        $v = $this;             // provide the view and it's method under $v
        $e = [$this, 'escape']; // provide the escape method under $e
        extract($this->data, EXTR_SKIP); // preserves $v, $e and $this

        $level = ob_get_level();
        try {
            ob_start();
            include $this->path;
            $content = trim(ob_get_clean());

            if ($this->parentView || $this->layout) {
                $parent = $this->parentView ?? $this->layout;
                $content = $parent->setSections(array_merge($this->sections, ['content' => $content]))
                    ->render();
            }

            return $content;
        } finally {
            while (ob_get_level() >  $level) {
                ob_end_clean();
            }
        }
    }

    /**
     * Extend the view $name with this view
     *
     * When no data is given all data in this view will be provided.
     *
     * @param string $name
     * @param array $data
     */
    public function extend(string $name, array $data = null)
    {
        $this->parentView = $this->factory->view($name, $data ?? $this->data, $this->layout);
    }

    /**
     * Start content for section $name
     *
     * If $append is true the content will be added to the section - otherwise existing content will be replaced.
     *
     * Every whitespace except spaces will be trimmed from the content.
     *
     * @param string $name
     * @param bool $append
     */
    public function start(string $name, bool $append = false)
    {
        if ($name === 'content') {
            throw new \LogicException('The section name "content" is reserved.');
        }

        if ($this->sectionName) {
            throw new \LogicException('You cannot nest sections within other sections.');
        }

        $this->appendSection = $append;
        $this->sectionName = $name;
        ob_start();
    }

    /**
     * End content of the current opened section
     */
    public function end()
    {
        if (is_null($this->sectionName)) {
            throw new \LogicException('You must start a section before you can end it.');
        }

        $this->provide($this->sectionName, ob_get_clean(), $this->appendSection);

        $this->sectionName = null;
        $this->appendSection = false;
    }

    /**
     * Provide $content as section $name
     *
     * If $append is true $content will be added to the section - otherwise existing content will be replaced.
     *
     * Every whitespace except spaces will be trimmed from $content.
     *
     * @param string $name
     * @param string $content
     * @param bool $append
     */
    public function provide(string $name, string $content, bool $append = false)
    {
        $content = trim($content, "\n\r\0\x0b");
        if ($append && isset($this->sections[$name])) {
            $this->sections[$name] .= $content;
        } else {
            $this->sections[$name] = $content;
        }
    }

    /**
     * Get the content of section $name or $alternative
     *
     * @param string $name
     * @param string|null $alternative
     * @return string
     */
    public function section(string $name, string $alternative = null): ?string
    {
        if (!isset($this->sections[$name])) {
            return $alternative;
        }

        return $this->sections[$name];
    }

    /**
     * Fetch template $name with $data
     *
     * @param $name
     * @param array $data
     * @return string
     */
    public function fetch(string $name, array $data = array())
    {
        return $this->factory->render($name, $data);
    }

    /**
     * Apply multiple functions to variable
     *
     * @param  mixed  $var
     * @param  string $functions
     * @return mixed
     */
    public function batch($var, string $functions)
    {
        foreach (explode('|', $functions) as $function) {
            $var = $this->factory->helper($this, $function, $var);
        }

        return $var;
    }

    /**
     * Escape string
     *
     * Uses batch processing for $functions before escaping.
     *
     * If $string is no string the $functions have to convert it to string
     *
     * @param  string|mixed $string
     * @param  string $functions
     * @return string
     */
    public function escape($string, string $functions = null)
    {
        static $flags;
        if (!isset($flags)) {
            $flags = ENT_QUOTES | (defined('ENT_SUBSTITUTE') ? ENT_SUBSTITUTE : 0);
        }

        if ($functions) {
            $string = $this->batch($string, $functions);
        }

        return htmlspecialchars($string, $flags, 'UTF-8');
    }
}
