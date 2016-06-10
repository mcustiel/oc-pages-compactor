<?php
namespace Mcustiel\CompactPages\Classes\Services\Implementation;

use Mcustiel\CompactPages\Classes\Services\HtmlCompactorInterface;
use PHPWee\PHPWee;

class PeeWeeHtmlCompactor implements HtmlCompactorInterface
{
    /**
     *
     * {@inheritdoc}
     * @see \Mcustiel\CompactPages\Classes\Services\HtmlCompactorInterface::compactHtml()
     */
    public function compactHtml($html, $minifyJs = false, $minifyCss = false)
    {
        return PHPWee::html($html, $minifyJs, $minifyCss);
    }
}
