<?php

namespace Syna;

class ViewLocator
{
    /** @var array  */
    protected $paths = [];

    /** @var array */
    protected $namedPaths = [];

    /** @var array */
    protected $map = [];

    /** @var string */
    protected $extension = '.phtml';

    public function __construct(string $path, string $extension = '.phtml')
    {
        $this->paths[] = $path;
        $this->extension = $extension;
    }

    public function addPath(string $path): self
    {
        $this->paths[] = $path;
        return $this;
    }

    public function prependPath(string $path): self
    {
        array_unshift($this->paths, $path);
        return $this;
    }

    public function add($name, $path): self
    {
        if (file_exists($path)) {
            throw new \LogicException('File ' . $path . ' does not exist');
        }

        $this->map[$name] = $path;
        return $this;
    }

    public function has($name): bool
    {
        if (isset($this->map[$name])) {
            return true;
        }

        foreach (array_reverse($this->paths) as $path) {
            $viewPath = $path . DIRECTORY_SEPARATOR . $name . $this->extension;
            if (file_exists($viewPath)) {
                $this->map[$name] = $viewPath;
                return true;
            }
        }

        return false;
    }

    public function getPath($name): string
    {
        if (!$this->has($name)) {
            throw new \Exception('View ' . $name . ' not found');
        }

        return $this->map[$name];
    }
}
