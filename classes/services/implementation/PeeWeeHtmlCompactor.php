<?php
namespace Mcustiel\InlineAssets\Classes\Services\Implementation;

use Mcustiel\InlineAssets\Classes\Services\HtmlCompactorInterface;
use PHPWee\PHPWee;

class PeeWeeHtmlCompactor implements HtmlCompactorInterface
{
    /**
     *
     * {@inheritdoc}
     * @see \Mcustiel\InlineAssets\Classes\Services\HtmlCompactorInterface::compactHtml()
     */
    public function compactHtml($html, $minifyJs = false, $minifyCss = false)
    {
        return PHPWee::html($html, $minifyJs, $minifyCss);
    }
}
