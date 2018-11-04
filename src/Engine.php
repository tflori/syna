<?php

namespace Syna;

class Engine
{
    protected $sharedData = [];

    /** @var HelperLocator */
    protected $helperLocator;

    /** @var ViewLocator */
    protected $viewLocator;

    /** @var ViewLocator[] */
    protected $namedLocators = [];

    /**
     * Engine constructor.
     * @param ViewLocator   $viewLocator
     * @param HelperLocator $helperLocator
     * @param ViewLocator   $layoutLocator
     */
    public function __construct(
        ViewLocator $viewLocator,
        HelperLocator $helperLocator = null,
        ViewLocator $layoutLocator = null
    ) {
        $this->viewLocator = $viewLocator;
        $this->helperLocator = $helperLocator ?? new HelperLocator();
        !$layoutLocator ||
            $this->namedLocators['layout'] = $layoutLocator;
    }

    public function addLocator(string $name, HelperLocator $locator): self
    {
        $this->namedLocators[$name] = $locator;
        return $this;
    }

    public function addSharedData(array $data): self
    {
        $this->sharedData = array_merge($this->sharedData, $data);
        return $this;
    }

    public function getSharedData()
    {
        return $this->sharedData;
    }

    public function view(string $name, array $data = []): View
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

        return new View($this, $viewLocator->getPath($name), $data);
    }

    public function render(string $name, array $data = [], string $layout = null): string
    {
        $view = $this->view($name, $data);
        $content = $view->render();

        if ($layout && isset($this->namedLocators['layout'])) {
            $layout = $this->view('layout::' . $layout);
            $layout->setSections(array_merge($view->getSections(), ['content' => $content]));
            $content = $layout->render();
        }

        return $content;
    }

    public function helper(View $view, $function, ...$arguments)
    {
        if ($this->helperLocator->has($function)) {
            $helper = $this->helperLocator->getHelper($function);
            $helper->setView($view);
            /** @noinspection PhpMethodParametersCountMismatchInspection */
            return $helper(...$arguments);
        } elseif (is_callable($function)) {
            return call_user_func($function, ...$arguments);
        }

        throw new \LogicException('$function has to be callable or a registered view helper');
    }
}
