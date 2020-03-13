<?php

abstract class AController
{
    protected $viewVars = [];
    protected $viewName = "";
    protected $mappersClasses = [];
    protected $mappers = [];
    protected $renderer = null;
    protected $config = null;

    private $actions = [];

    public function __construct()
    {
    }

    protected function View(string $viewName = "", $params = [])
    {
        $viewName = !empty($viewName) ? $viewName : $this->viewName;
        $data = array_merge($this->viewVars, $params);
        return new ViewResult($this->renderer, $viewName, $data);
    }

    // Error prone: fatal error if constructor
    public final function loadComponent(string $componentName, string $className, IComponentsProvider $componentProvider)
    {
        if ($componentName == "renderer") {
            return false;
        }

        $this->$componentName = $componentProvider->getComponent($componentName, $className);
        if (!(is_subclass_of($this->$componentName, $className, false))) {
            throw new AppException("{$componentName} must implement {$className}");
        }

        return true;
    }

    public final function loadMetaData()
    {
        $metaData = new ReflectionClass(static::class);

        foreach ($metaData->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (
                $method->isStatic() ||
                !$method->hasReturnType() ||
                !($method->getReturnType()->getName() === IActionResult::class)
                || $method->getName() === "InvokeAction"
            ) {
                continue;
            }

            $this->actions[$method->getName()] = $method;
        }
    }

    public function loadConfiguration(IComponentsProvider $componentProvider)
    {
        $router = $componentProvider->getComponent('router', IRouterComponent::class);
        $globalConfiguration = $componentProvider->getComponent("globalConfig", AConfigComponent::class);
        $configurationClass = $componentProvider->getComponentType("controllerConfig");
        $configurationType = call_user_func($configurationClass . '::getConfigurationType');
        $controllerClass = static::class;
        $this->viewName = $router->getAction();
        $configPath = CNTRL_ROOT . "/{$router->getController()}/{$controllerClass}.meta.{$configurationType}";
        if (file_exists($configPath)) {
            $this->config = $componentProvider->getComponent("controllerConfig", AConfigComponent::class, [$configPath, $globalConfiguration]);
            $this->viewVars['ROOT'] = $this->config->url_root;
        }
    }

    public function loadRenderer(IRendererComponent $renderer)
    {
        $this->renderer = $renderer;
    }

    public function loadMappers(IComponentsProvider $componentProvider)
    {
        if (!empty($this->mappersClasses)) {
            foreach ($this->mappersClasses as $mapperClass) {
                $mapPath = MAP_ROOT . "/{$mapperClass}.php";
                if (!file_exists($mapPath)) {
                    throw new AppException("Attempt to connect a nonexistent mapper {$mapperClass}.php");
                }

                $mapper = lcfirst($mapperClass);
                $this->$mapper = new $mapperClass($componentProvider->getComponent("dataProvider", IDataComponent::class));
            }
        }
    }

    public function InvokeAction(string $actionName, $args = []): IActionResult
    {
        return isset($this->actions[$actionName]) ? $this->actions[$actionName]->invokeArgs($this, $args) : new NotFoundResult($this->renderer);
    }
}
