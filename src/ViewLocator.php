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
    protected $extension = '.php';

    public function __construct(string $path, string $extension = '.php')
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

    public function add(string $name, string $path): self
    {
        if (!file_exists($path)) {
            throw new \LogicException('File ' . $path . ' does not exist');
        }

        $this->map[$name] = $path;
        return $this;
    }

    public function has(string $name): bool
    {
        if (isset($this->map[$name])) {
            return true;
        }

        foreach (array_reverse($this->paths) as $path) {
            $viewPath = $path . DIRECTORY_SEPARATOR . $name . $this->extension;
            if (file_exists($viewPath)) {
                $this->map[$name] = realpath($viewPath);
                return true;
            }
        }

        return false;
    }

    /**
     * Get the path to view $name
     *
     * @param $name
     * @return string
     * @throws NotFound
     */
    public function getPath(string $name): string
    {
        if (!$this->has($name)) {
            throw new NotFound('View ' . $name . ' not found');
        }

        return $this->map[$name];
    }
}
