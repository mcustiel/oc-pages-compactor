<?php
namespace Mcustiel\CompactPages\Classes\Twig\Extensions;

use Html;
use Twig_Extension;
use Cms\Classes\Controller;
use Mcustiel\CompactPages\Classes\Twig\TokenParsers\InlineScript;
use Mcustiel\CompactPages\Classes\Twig\TokenParsers\InlineStyle;
use Illuminate\Support\Facades\Event;


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
        return '<style type="text/css">' . PHP_EOL
            . $this->assetsCode('css') . PHP_EOL
            . '</style>' .  PHP_EOL;
    }

    /**
     * Gets all added scripts and returns the code to inline it in the html.
     *
     * @return string
     */
    public function inlineScripts()
    {
        return '<script type="text/javascript">' . PHP_EOL
            . $this->assetsCode('js') . PHP_EOL
            . '</script>' .  PHP_EOL;
    }

    private function assetsCode($assetsType)
    {
        $html = '';
        foreach ($this->controller->getAssetPaths()[$assetsType] as $path) {
            $relativePath = parse_url($path)['path'];
            $assetCode = file_get_contents(base_path($relativePath));
            if (($response = Event::fire(
                'mcustiel.compactpages.assetInlining',
                [$relativePath, $assetCode, $assetsType]
            ))) {
                $html .= $response;
            } else {
                $html .= $assetsType == 'css' ? $this->fixCssPaths($relativePath, $assetCode) : $assetCode;
            }
        }
        return $html;
    }

    private function fixCssPaths($relativePath, $assetCode)
    {
        $pathArray = explode('/', $relativePath);
        $callback = function ($matches) use ($pathArray) {
            $replacement = '/';
            $amountOfDirectories = count($pathArray) - substr_count($matches[0], '../') - 1;
            for ($i = 1; $i < $amountOfDirectories; $i++) {
                $replacement .= $pathArray[$i] . '/';
            }
            return $replacement;
        };

        return preg_replace_callback('~(?:\.\./)+~', $callback, $assetCode);
    }
}
