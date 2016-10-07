<?php

namespace Mcustiel\InlineAssets\Classes\Services\Implementation;

use Mcustiel\InlineAssets\Classes\Services\HtmlCompactorInterface;
use PHPWee\Minify;

class PhpWeeHtmlCompactor implements HtmlCompactorInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Mcustiel\InlineAssets\Classes\Services\HtmlCompactorInterface::compactHtml()
     */
    public function compactHtml($html)
    {
        return str_replace("\n", '', Minify::html($html, true, false));
    }
}
