<?php

class TwigRendererComponent extends Component implements IRendererComponent
{
    private $loader = null;
    private $twig = null;

    public function __construct(string $templatesDir, array $params = [])
    {
        $this->loader = new \Twig\Loader\FilesystemLoader([
            VIEW_ROOT . DIRECTORY_SEPARATOR . "_shared",
            VIEW_ROOT
        ]);

        if (!empty($templatesDir) && is_dir(VIEW_ROOT . DIRECTORY_SEPARATOR . $templatesDir)) {
            $this->loader->prependPath(VIEW_ROOT . DIRECTORY_SEPARATOR . $templatesDir);
        }

        $this->twig = new \Twig\Environment($this->loader, [
            'cache' => VAR_ROOT . DIRECTORY_SEPARATOR . 'cache',
            'auto_reload' => !empty($params['caching']) ? false : true,
            'debug' => !empty($params['devMode']) ? true : false,
        ]);

        $this->twig->addExtension(new \Twig\Extension\DebugExtension());
    }

    public function render(string $view, array $args)
    {
        $view .= ".html";
        echo $this->twig->render($view, $args);
        exit;
    }
}
