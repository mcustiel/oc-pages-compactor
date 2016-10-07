<?php

namespace Mcustiel\InlineAssets;

use App;
use Event;
use Config;
use System\Classes\PluginBase;
use System\Classes\MarkupManager;
use Mcustiel\InlineAssets\Classes\Twig\TokenParsers\InlineStyle;
use Cms\Classes\Controller;
use Mcustiel\InlineAssets\Classes\Twig\Extensions\InlineAssets;
use Mcustiel\InlineAssets\Classes\Twig\TokenParsers\InlineScript;
use Cms\Classes\Page;

/**
 * compactPages Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * {@inheritdoc}
     *
     * @see \System\Classes\PluginBase::boot()
     */
    public function boot()
    {
        Event::listen(
            'cms.page.beforeDisplay',
            function (Controller $controller, $url, Page $page = null) {
                $controller->getTwig()->addExtension(App::make(InlineAssets::class, [$controller]));
            }
        );

        if (Config::get('mcustiel.compactpages::compactation.enabled')) {
            Event::listen('cms.page.postprocess', function ($controller, $url, $page, $dataHolder) {
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
            'icon'        => 'icon-cogs',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \System\Classes\PluginBase::registerMarkupTags()
     */
    public function registerMarkupTags()
    {
        return [
            MarkupManager::EXTENSION_TOKEN_PARSER => [
                App::make(InlineStyle::class),
                App::make(InlineScript::class),
            ],
        ];
    }
}
