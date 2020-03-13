<?php

class App implements IComponentsProvider
{
    protected $components = [];
    protected $request = null;

    public function __construct()
    {
        if (isset($_SERVER['argv'])) {
            $this->request = new CliRequest();
        } elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->request = new PostRequest();
        } elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
            $this->request = new GetRequest();
        } else {
            throw new AppException("Undefined request");
        }

        $loader = new \Twig\Loader\FilesystemLoader(VIEW_ROOT);
        $twig = new \Twig\Environment($loader, [
            'cache' => VAR_ROOT . DIRECTORY_SEPARATOR . 'cache',
        ]);
        $this->registerComponent("globalConfig", new BasicXmlConfigComponent(CONFIG_ROOT . "/config.xml"));
        $this->registerConstructor("renderer", TwigRendererComponent::class);
        $this->registerComponent("router", new BasicRouterComponent($this->getComponent("globalConfig", AConfigComponent::class)));
        $this->registerComponent("dataProvider", MysqlDataComponent::createFromConfiguration($this->getComponent("globalConfig", AConfigComponent::class)));
        $this->registerConstructor("controllerConfig", BasicXmlConfigComponent::class);
    }

    public function getComponent(string $key, string $className, array $args = []): ?Component
    {
        $component = null;

        if (isset($this->components[$key])) {
            if ($this->components[$key]['provider'] == ComponentsProviderEnum::COMPONENT && is_subclass_of($this->components[$key]['instance'], $className)) {
                $component = $this->components[$key]['instance'];
            } elseif ($this->components[$key]['provider'] == ComponentsProviderEnum::CONSTRUCTOR && is_subclass_of($this->components[$key]['type'], $className)) {
                $component = new $this->components[$key]['type'](...$args);
            }
        }

        return $component;
    }

    public function getComponentType(string $key): ?string
    {
        if (!isset($this->components[$key]) || !is_subclass_of($this->components[$key]['type'], Component::class)) {
            return null;
        }

        return $this->components[$key]['type'];
    }

    public function registerComponent(string $key, Component $component)
    {
        if (!(is_subclass_of($component, Component::class, false))) {
            $baseClass = Component::class;
            $componentClass = get_class($component);
            throw new AppException("{$componentClass} must extend {$baseClass}");
        }

        $this->components[$key]['type'] = get_class($component);
        $this->components[$key]['provider'] = ComponentsProviderEnum::COMPONENT;
        $this->components[$key]['instance'] = $component;
    }

    public function registerConstructor(string $key, string $componentType)
    {
        if (!(is_subclass_of($componentType, Component::class))) {
            $baseClass = Component::class;
            throw new AppException("{$componentType} must extend {$baseClass}");
        }

        $this->components[$key]['type'] = $componentType;
        $this->components[$key]['provider'] = ComponentsProviderEnum::CONSTRUCTOR;
    }

    public function run()
    {
        try {
            $isDev = $this->getComponent("globalConfig", AConfigComponent::class)->dev_mode;
            $isDev = !empty($isDev) ? true : false;
            $router = $this->getComponent('router', IRouterComponent::class);
            $router->parseURL($this->request->url);
            $renderer = $this->getComponent("renderer", IRendererComponent::class, ["", ["devMode" => $isDev, "caching" => false]]);
            $controllerName = ucfirst($router->getController()) . "Controller";
            $pathValid = false;

            if (file_exists(CNTRL_ROOT . "/{$router->getController()}/{$controllerName}.php")) {
                require_once CNTRL_ROOT . "/{$router->getController()}/{$controllerName}.php";
                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    $controller->loadMetaData();
                    $controller->loadConfiguration($this);
                    $controller->loadMappers($this);
                    $controller->loadRenderer(
                        $this->getComponent("renderer", IRendererComponent::class, [$router->getController(), ["devMode" => $isDev, "caching" => !$isDev]])
                    );
                    $actionResult = $controller->InvokeAction($router->getAction(), array_merge($router->getParams(), [$this->request->data]));
                    $actionResult->execute();
                }
            }

            $notFoundResult = new NotFoundResult($renderer);
            $notFoundResult->execute();
        } catch (Throwable $ex) {
            $renderer->render("error", ["errorMessage" => $ex, "devMode" => $isDev, "ROOT" => $this->getComponent("globalConfig", AConfigComponent::class)->url_root]);
        }
    }
}
