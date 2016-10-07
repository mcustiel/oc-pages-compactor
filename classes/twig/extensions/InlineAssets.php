<?php

namespace Mcustiel\InlineAssets\Classes\Twig\Extensions;

use Html;
use Twig_Extension;
use Cms\Classes\Controller;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Request;

class InlineAssets extends Twig_Extension
{
    /**
     * @var \Cms\Classes\Controller
     */
    protected $controller;

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'McustielCompactPages';
    }

    /**
     * Gets all added styles and returns the code to inline it in the html.
     *
     * @return string
     */
    public function inlineStyles()
    {
        $code = $this->assetsCode('css');
        if ($code) {
            return '<style type="text/css">' . PHP_EOL
                . $code . PHP_EOL
                . '</style>';
        }
        return $code;
    }

    /**
     * Gets all added scripts and returns the code to inline it in the html.
     *
     * @return string
     */
    public function inlineScripts()
    {
        $code = $this->assetsCode('js');
        if ($code) {
            return '<script type="text/javascript">' . PHP_EOL
                . $code . PHP_EOL
                . '</script>';
        }
        return $code;
    }

    private function getPath($path)
    {
        return parse_url($path)['path'];
    }

    private function convertToRealPath($relativePath)
    {
        if (strpos($relativePath, 'combine/') !== false) {
            return url($relativePath);
        }
        return base_path($relativePath);
    }

    private function assetsCode($assetsType)
    {
        $html = '';
        foreach ($this->controller->getAssetPaths()[$assetsType] as $path) {
            $relativePath = '';
            $assetCode = $this->getAssetCode($path, $relativePath);
            if (($response = Event::fire(
                'mcustiel.compactpages.assetInlining',
                [$relativePath, $assetCode, $assetsType],
                true
            ))) {
                $html .= $response;
            } else {
                $html .= $assetsType == 'css' ? $this->fixCssPaths($relativePath, $assetCode) : $assetCode;
            }
        }
        return $html;
    }

    private function getAssetCode($path, &$relativePath) {
        if (strpos($path, Request::root()) !== 0) {
            $relativePath = '';
            return file_get_contents($path);
        }
        $relativePath = $this->getPath($path);
        return file_get_contents($this->convertToRealPath($relativePath));
    }

    private function fixCssPaths($relativePath, $assetCode)
    {
        $pathArray = explode('/', $relativePath);
        $callback = function ($matches) use ($pathArray) {
            $replacement = $matches[1] . '/';
            $amountOfDirectories = count($pathArray) - substr_count($matches[0], '../') - 1;
            for ($i = 1; $i < $amountOfDirectories; ++$i) {
                $replacement .= $pathArray[$i] . '/';
            }
            return $replacement;
        };

        return preg_replace_callback('~([^/])(?:\\.\\./)+~', $callback, $assetCode);
    }
}
