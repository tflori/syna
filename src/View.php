<?php

namespace Syna;

class View
{
    /** @var Engine */
    protected $engine;

    /** @var array  */
    protected $data = [];

    /** @var string  */
    protected $path;

    /** @var array  */
    protected $sections = [];

    /** @var View */
    protected $parentTemplate;

    /** @var string */
    protected $sectionName;

    /** @var bool */
    protected $appendSection = false;

    public function __construct(Engine $engine, string $path, array $data = [])
    {
        $this->engine = $engine;
        $this->path = $path;

        $this->data = array_merge($engine->getSharedData(), $data);
    }

    public function __toString()
    {
        return $this->render();
    }

    public function __call($name, $arguments)
    {
        return $this->engine->helper($this, $name, ...$arguments);
    }

    public function addData(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    public function setSections(array $sections): self
    {
        $this->sections = $sections;
        return $this;
    }

    public function getSections(): array
    {
        return $this->sections;
    }

    public function render(array $data = null): string
    {
        if ($data) {
            $this->addData($data);
        }
        unset($data);

        $v = $this;
        $e = [$this, 'escape'];
        extract($this->data, EXTR_SKIP);

        $level = ob_get_level();
        try {
            ob_start();
            include $this->path;
            $content = trim(ob_get_clean());

            if ($this->parentTemplate) {
                $this->parentTemplate->setSections(array_merge($this->sections, ['content' => $content]));
                $content = $this->parentTemplate->render();
            }

            return $content;
        } catch (\Throwable $exception) {
            while (ob_get_level() >  $level) {
                ob_end_clean();
            }
            throw $exception;
        }
    }

    public function extend(string $path, array $data = null)
    {
        $this->parentTemplate = $this->engine->view($path, $data ?? $this->data);
    }

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

    public function end()
    {
        if (is_null($this->sectionName)) {
            throw new \LogicException('You must start a section before you can end it.');
        }

        $this->provide($this->sectionName, ob_get_clean(), $this->appendSection);

        $this->sectionName = null;
        $this->appendSection = false;
    }

    public function provide(string $name, string $content, bool $append = false)
    {
        $content = trim($content, "\t\n\r\0\x0b");
        if ($append && isset($this->sections[$name])) {
            $this->sections[$name] .= $content;
        } else {
            $this->sections[$name] = $content;
        }
    }

    public function section(string $name, string $default = null): string
    {
        if (!isset($this->sections[$name])) {
            return $default;
        }

        return $this->sections[$name];
    }

    public function fetch($name, array $data = array())
    {
        return $this->engine->render($name, $data);
    }

    public function insert($name, array $data = [])
    {
        echo $this->engine->render($name, $data);
    }

    /**
     * Apply multiple functions to variable.
     * @param  mixed  $var
     * @param  string $functions
     * @return mixed
     */
    public function batch($var, $functions)
    {
        foreach (explode('|', $functions) as $function) {
            $var = $this->engine->helper($this, $function, $var);
        }

        return $var;
    }

    /**
     * Escape string.
     * @param  string      $string
     * @param  null|string $functions
     * @return string
     */
    public function escape($string, $functions = null)
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
