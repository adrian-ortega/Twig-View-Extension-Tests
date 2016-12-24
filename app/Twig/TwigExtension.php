<?php

namespace App\Twig;

use Interop\Container\ContainerInterface;
use Slim\Views\TwigExtension as BaseTwigExtension;

class TwigExtension extends BaseTwigExtension
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Array of custom extensions
     * @var array
     */
    protected $extensions;

    /**
     * CustomTwigExtension constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface &$container)
    {
        $this->container = $container;
        $this->extensions = [];

        foreach($this->getCustomExtensions() as $ext) {
            $this->extensions[] = new $ext($container);
        }

        parent::__construct(
            $container->get('router'),
            $container->get('request')->getUri()
        );
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        $filters = parent::getFilters();
        foreach($this->extensions as $ext) {
            if(method_exists($ext, 'getFilters')) {
                $filters = array_merge($filters, $ext->getFilters());
            }
        }

        return $filters;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        $functions = parent::getFunctions();

        foreach($this->extensions as $ext) {
            if(method_exists($ext, 'getFunctions')) {
                $functions = array_merge($functions, $ext->getFunctions());
            }
        }

        return $functions;
    }

    /**
     * Goes through our custom extensions directory and returns all files to be added
     * @return array
     */
    protected function getCustomExtensions()
    {
        $prefix = '\\App\\Twig\\Extensions\\';
        $path = __DIR__ . '/Extensions/';
        $ignore = [
            '.', '..',
            '.DS_Store', '.AppleDouble', '.LSOverride', '.trash',
            'Thumbs.db', 'ehthumbs.db', '$RECYCLE.BIN'
        ];
        $files = [];

        if(file_exists($path)) {
            $_handle = opendir($path);

            while ($file = readdir($_handle)) {
                if (!in_array($file, $ignore)) {
                    $files[] = $prefix . str_replace('.php', '', $file);
                }
            }
        }

        return $files;
    }
}