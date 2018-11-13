<?php

namespace Syna;

class Factory
{
    protected $sharedData = [];

    /** @var HelperLocator */
    protected $helperLocator;

    /** @var ViewLocator */
    protected $viewLocator;

    /** @var ViewLocator[] */
    protected $namedLocators = [];

    /**
     * @param ViewLocator   $viewLocator
     * @param HelperLocator $helperLocator
     * @param ViewLocator   $layoutLocator
     */
    public function __construct(
        ViewLocator $viewLocator,
        ?HelperLocator $helperLocator = null,
        ?ViewLocator $layoutLocator = null
    ) {
        $this->viewLocator = $viewLocator;
        $this->helperLocator = $helperLocator ?? new HelperLocator();
        !$layoutLocator ||
            $this->namedLocators['layout'] = $layoutLocator;
    }

    /**
     * Add a named ViewLocator $locator to this factory
     *
     * @param string $name
     * @param ViewLocator $locator
     * @return Factory
     */
    public function addLocator(string $name, ViewLocator $locator): self
    {
        $this->namedLocators[$name] = $locator;
        return $this;
    }

    /**
     * Get a named ViewLocator or the default ViewLocator
     *
     * @param string $name
     * @return ViewLocator
     */
    public function getLocator(?string $name = null): ?ViewLocator
    {
        if (!$name) {
            return $this->viewLocator;
        }
        return $this->namedLocators[$name] ?? null;
    }

    /**
     * Get the HelperLocator
     *
     * @return HelperLocator
     */
    public function getHelperLocator(): HelperLocator
    {
        return $this->helperLocator;
    }

    /**
     * Add shared data
     *
     * @param array $data
     * @return Factory
     */
    public function addSharedData(array $data): self
    {
        $this->sharedData = array_merge($this->sharedData, $data);
        return $this;
    }

    /**
     * Get all shared Data
     *
     * @return array
     */
    public function getSharedData(): array
    {
        return $this->sharedData;
    }

    /**
     * Create a view for $name with $data
     *
     * $name can be prefixed with a locator name followed by two colons (e. g. 'mail::activation') uses the locator
     * named mail and searches for 'activation'.
     *
     * @param string $name
     * @param array $data
     * @param string|View $layout
     * @return View
     * @throws \Exception
     */
    public function view(string $name, array $data = [], $layout = null): View
    {
        $viewLocator = $this->viewLocator;

        if (strpos($name, '::', 1) !== false) {
            list($locatorName, $name) = explode('::', $name);
            if (!isset($this->namedLocators[$locatorName])) {
                throw new \LogicException('No locator for ' . $locatorName . ' defined');
            }

            $viewLocator = $this->namedLocators[$locatorName];
        }

        if (!$viewLocator->has($name)) {
            throw new \Exception('View ' . $name . ' not found');
        }

        if (is_string($layout)) {
            $layout = $this->view('layout::' . $layout);
        }

        return new View($this, $viewLocator->getPath($name), $data, $layout);
    }

    /**
     * Execute $function with $arguments
     *
     * If the HelperLocator has $function this helper will be preferred but a 'strtoupper' is a valid callable and will
     * be executed if no helper is defined for this name.
     *
     * @param View $view
     * @param string $function
     * @param mixed ...$arguments
     * @return mixed
     */
    public function helper(View $view, string $function, ...$arguments)
    {
        if ($this->helperLocator->has($function)) {
            $helper = $this->helperLocator->getHelper($function);
            $helper->setView($view);
            /** @noinspection PhpMethodParametersCountMismatchInspection */
            return $helper(...$arguments);
        } elseif (is_callable($function)) {
            return call_user_func($function, ...$arguments);
        }

        throw new \Exception('$function has to be callable or a registered view helper');
    }

    /**
     * Alias for ->view()->render()
     *
     * @param string $name
     * @param array $data
     * @param string $layout
     * @return string
     * @see Factory::view()
     * @codeCoverageIgnore just an alias
     */
    public function render(string $name, array $data = [], string $layout = null): string
    {
        return $this->view($name, $data, $layout)->render();
    }
}
