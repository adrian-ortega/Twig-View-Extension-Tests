<?php

namespace App\Twig\Extensions;

use Slim\Http\Request;

class Urls
{
    protected $container;

    public function __construct($container) {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('site_url', [$this, 'getSiteUrl']),
            new \Twig_SimpleFunction('current_url', [$this, 'getCurrentUrl']),
            new \Twig_SimpleFunction('current_path', [$this, 'getCurrentPath']),
        ];
    }

    public function getSiteUrl($path = '')
    {
        return $this->container->get('site_url') . (!empty($path) ? "/$path" : "");
    }

    public function getCurrentPath()
    {
        return $this->container->get('request')->getUri()->getPath();
    }

    public function getCurrentUrl($withQueryString = true)
    {
        $request = $this->container->get('request');

        $uri = $this->container->get('site_url') . $request->getUri()->getPath();

        if ($withQueryString) {
            $env = $this->container->get('environment');
            if ($env['QUERY_STRING']) {
                $uri .= '?' . $env['QUERY_STRING'];
            }
        }
        return $uri;
    }
}