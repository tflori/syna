<?php

namespace Syna;

use Syna\ViewHelper\CallableHelper;

class HelperLocator
{
    /** @var array */
    protected $map = [];

    /** @var array */
    protected $namespaces = [ViewHelper::class];

    /** @var callable */
    protected $resolver;

    public function __construct(string $namespace = null, callable $resolver = null)
    {
        !$namespace ||
            $this->namespaces[] = $namespace;
        $this->resolver = $resolver ?? function ($class) {
            return new $class;
        };
    }

    public function addNamespace(string $namespace): self
    {
        $this->namespaces[] = $namespace;
        return $this;
    }

    public function prependNamespace(string $namespace): self
    {
        array_unshift($this->namespaces, $namespace);
        return $this;
    }

    public function add(string $name, $helper)
    {
        if (isset($this->map[$name])) {
            throw new \LogicException('Helper ' . $name . ' already exists');
        }

        if (is_callable($helper) && !$helper instanceof ViewHelperInterface) {
            $this->map[$name] = $helper;
            return $this;
        }

        if ($helper instanceof ViewHelperInterface) {
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

    public function has(string $name): bool
    {
        if (isset($this->map[$name])) {
            return true;
        }

        // replace underscores with backslashes and ensure studly case
        $baseClassName = implode('\\', array_map('ucfirst', explode('_', $name)));
        foreach (array_reverse($this->namespaces) as $namespace) {
            $class = $namespace . '\\' . $baseClassName;
            if (class_exists($class)) {
                $this->map[$name] = $class;
                return true;
            }
        }

        return false;
    }

    public function getHelper(string $name): ViewHelperInterface
    {
        if (!$this->has($name)) {
            throw new \LogicException('View helper ' . $name . ' not found');
        }

        if (is_callable($this->map[$name]) && !$this->map[$name] instanceof ViewHelperInterface) {
            $this->map[$name] = new CallableHelper($this->map[$name]);
        } elseif (is_string($this->map[$name])) {
            $this->map[$name] = call_user_func($this->resolver, $this->map[$name]);
        }

        return $this->map[$name];
    }
}
