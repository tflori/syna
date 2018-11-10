<?php

namespace Syna;

use Syna\ViewHelper\CallableHelper;

class HelperLocator
{
    /** @var array */
    protected $map = [];

    /** @var array */
    protected $found = [];

    /** @var array */
    protected $namespaces = [ViewHelper::class];

    /** @var callable */
    protected $resolver;

    /**
     * HelperLocator constructor.
     *
     * If a resolver is given this resolver will be called to generate instances of the registered classes.
     *
     * @param string $namespace
     * @param callable $resolver
     */
    public function __construct(string $namespace = null, callable $resolver = null)
    {
        !$namespace ||
            $this->namespaces[] = $namespace;
        $this->resolver = $resolver ?? function ($class) {
            return new $class;
        };
    }

    /**
     * Add another $namespace to search for Helpers
     *
     * This Locator uses the last in first out principle. A later defined namespace will be searched first.
     *
     * An already resolved helper will not be overwritten with a later defined namespace unless you clear found helpers.
     *
     * @param string $namespace
     * @return $this
     */
    public function addNamespace(string $namespace): self
    {
        $this->namespaces[] = $namespace;
        return $this;
    }

    /**
     * Prepend a $namespace to search for Helpers
     *
     * This Locator uses the last in first out principle. A later prepended namespace will be searched last.
     *
     * @param string $namespace
     * @return $this
     */
    public function prependNamespace(string $namespace): self
    {
        array_unshift($this->namespaces, $namespace);
        return $this;
    }

    /**
     * Clear all helpers found in the registered namespaces
     *
     * @return HelperLocator
     */
    public function clearFound(): self
    {
        $this->found = [];
        return $this;
    }

    /**
     * Register $helper for $name
     *
     * We recommend to use a valid method name as $name to use $v->{$name}() syntax.
     * @param string $name
     * @param $helper
     * @return $this
     */
    public function add(string $name, $helper)
    {
        if (is_callable($helper)) {
            $this->map[$name] = $helper;
            return $this;
        }

        if (is_string($helper) && class_exists($helper)) {
            $this->map[$name] = $helper;
            return $this;
        }

        throw new \LogicException(
            'Helper has to be a callable, an instance of ' . ViewHelperInterface::class . ' or a class name'
        );
    }

    /**
     * Check if $name is available
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        if (isset($this->map[$name]) || isset($this->found[$name])) {
            return true;
        }

        // replace underscores with backslashes and ensure studly case
        $baseClassName = implode('\\', array_map('ucfirst', explode('_', $name)));
        foreach (array_reverse($this->namespaces) as $namespace) {
            $class = $namespace . '\\' . $baseClassName;
            if (class_exists($class)) {
                $this->found[$name] = $class;
                return true;
            }
        }

        return false;
    }

    /**
     * Get a helper for $name
     *
     * Creates CallableHelper for callback helpers and instantiates classes.
     *
     * @param string $name
     * @return ViewHelperInterface
     * @throws NotFound
     */
    public function getHelper(string $name): ViewHelperInterface
    {
        if (!$this->has($name)) {
            throw new NotFound('View helper ' . $name . ' not found');
        }

        $found = !isset($this->map[$name]);
        $helper = $this->map[$name] ?? $this->found[$name];
        if (is_callable($helper) && !$helper instanceof ViewHelperInterface) {
            $helper = new CallableHelper($this->map[$name]);
            $found ? $this->found[$name] = $helper : $this->map[$name] = $helper;
        } elseif (is_string($helper)) {
            $helper = call_user_func($this->resolver, $helper);
            $found ? $this->found[$name] = $helper : $this->map[$name] = $helper;
        }

        return $helper;
    }
}
