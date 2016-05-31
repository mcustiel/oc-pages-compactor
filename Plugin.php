<?php namespace Mcustiel\CompactPages;

use App;
use Event;
use Config;
use System\Classes\PluginBase;
use System\Classes\MarkupManager;
use Mcustiel\CompactPages\Classes\Twig\TokenParsers\InlineStyle;
use Cms\Classes\Controller;
use Mcustiel\CompactPages\Classes\Twig\Extensions\InlineAssets;
use Mcustiel\CompactPages\Classes\Twig\TokenParsers\NativeOverwriteInlineStyle;
use Mcustiel\CompactPages\Classes\Twig\TokenParsers\InlineScript;
use Mcustiel\CompactPages\Classes\Twig\TokenParsers\NativeOverwriteInlineScript;
use Cms\Classes\Page;

/**
 * compactPages Plugin Information File
 */
class Plugin extends PluginBase
{
    public function boot()
    {
        Event::listen('cms.page.beforeDisplay', function (Controller $controller, $url, Page $page) {
            $controller->getTwig()->addExtension(App::make(InlineAssets::class, [$controller]));
        });

        if (Config::get('mcustiel.compactpages::compactation.enabled')) {
            Event::listen('cms.page.postprocess', function($controller, $url, $page, $dataHolder) {
                $compactor = App::make(
                    Config::get('mcustiel.compactpages::compactation.compactor')
                );
                $dataHolder->content = $compactor->compactHtml($dataHolder->content);
            });
        }
    }

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Compact Pages',
            'description' => 'Provides HTML compactation/minifaction and inline assets',
            'author'      => 'mcustiel',
            'icon'        => 'icon-cogs'
        ];
    }

    /**
     * {@inheritDoc}
     * @see \System\Classes\PluginBase::registerMarkupTags()
     */
    public function registerMarkupTags()
    {
        $tokenParsers =  [
            MarkupManager::EXTENSION_TOKEN_PARSER => [
                App::make(InlineStyle::class),
                App::make(InlineScript::class),
            ]
        ];
        if (Config::get('mcustiel.compactpages::inline_assets.overwrite_native.styles')) {
            $tokenParsers[MarkupManager::EXTENSION_TOKEN_PARSER][] = App::make(
                NativeOverwriteInlineStyle::class
            );
        }
        if (Config::get('mcustiel.compactpages::inline_assets.overwrite_native.scripts')) {
            $tokenParsers[MarkupManager::EXTENSION_TOKEN_PARSER][] = App::make(
                NativeOverwriteInlineScript::class
            );
        }
        return $tokenParsers;
    }
}
