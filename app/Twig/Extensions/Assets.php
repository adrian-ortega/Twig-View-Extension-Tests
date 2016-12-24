<?php

namespace App\Twig\Extensions;

use Interop\Container\ContainerInterface;

class Assets
{
    protected $container;

    public function __construct(&$container) {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('get_image', [$this, 'getImageUrl']),
            new \Twig_SimpleFunction('get_script', [$this, 'getScriptUrl']),
            new \Twig_SimpleFunction('get_stylesheet', [$this, 'getStylesheetUrl']),
            new \Twig_SimpleFunction('get_vendor_script', [$this, 'getVendorScriptUrl']),
        ];
    }

    /**
     * Returns the base url for the app
     * @return string
     */
    protected function baseUrl()
    {
        $uri = $this->container->get('request')->getUri();

        if (is_string($uri)) {
            return $uri;
        }

        if (method_exists($uri, 'getBaseUrl')) {
            return $uri->getBaseUrl();
        }

        return '/';
    }

    /**
     * Returns the url for a directory
     * @param string $dir
     * @return string
     */
    protected function assetDir($dir = '')
    {
        return $this->baseUrl() . '/assets/' . (!empty($dir) ? $dir . '/' : '');
    }

    /**
     * Returns the path for a particular asset directory
     * @param string $dir
     * @return string
     */
    protected function assetPath($dir = '')
    {
        return $this->container->get('abs_path') . '/assets/' . (!empty($dir) ? $dir . '/' : '');
    }

    /**
     * Returns a version to use with a query string
     * @param null|string $file
     * @param null|string $type
     * @return string
     */
    protected function getAssetVersion($file = null, $type = null)
    {
        if (empty($type)) {
            if (strpos($file, '.js') !== false) {
                $type = 'js';
            } elseif (strpos($file, '.css')) {
                $type = 'css';
            }
        }

        $path = $this->assetPath($type);
        $version = date('YmdHis');

        if (file_exists($path . $file)) {
            $version = date('YmdHis', filemtime($path . $file));
        }

        return base_convert($version, 10, 36);
    }

    /**
     * Returns the url for an image
     * @param string $file
     * @return string
     */
    public function getImageUrl($file = '')
    {
        $base_url = $this->assetDir('images');
        return $base_url . $file;
    }

    /**
     * Returns the path for a particular script
     * @param string $file
     * @param null|string $sub
     * @return string
     */
    public function getScriptUrl($file = '', $sub = null)
    {
        if (empty($file)) return '';

        $base_url = $this->assetDir('js');
        $file = empty($sub) ? $file : "$sub/$file";

        if (strpos($file, '.min') === false) {

            $asset_path = $this->assetPath('js');

            $_file = str_replace('.js', '.min.js', $file);

            if (file_exists($asset_path . $_file)) {
                $file = $_file;
            }
        }

        return $base_url . $file . '?v=' . $this->getAssetVersion($file, 'js');
    }

    /**
     * Returns the url for a vendor javascript
     * @param $file
     * @return string
     */
    public function getVendorScriptUrl($file)
    {
        return $this->getScriptUrl($file, 'vendor');
    }

    /**
     * Returns the url for a particular stylesheet
     * @param string $file
     * @return string
     */
    public function getStylesheetUrl($file = '')
    {
        if (empty($file)) return '';

        $base_url = $this->assetDir('css');

        if (strpos($file, '.min') === false) {

            $asset_path = $this->assetPath('css');
            $_file = str_replace('.css', '', $file);
            $_file = $_file . '.min.css';

            if (file_exists($asset_path . $_file)) {
                $file = $_file;
            }
        }

        return $base_url . $file . '?v=' . $this->getAssetVersion($file, 'css');
    }
}