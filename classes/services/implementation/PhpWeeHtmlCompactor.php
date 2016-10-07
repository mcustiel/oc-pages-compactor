<?php

namespace Mcustiel\CompactPages\Classes\Services\Implementation;

use Mcustiel\CompactPages\Classes\Services\HtmlCompactorInterface;
use PHPWee\Minify;

class PhpWeeHtmlCompactor implements HtmlCompactorInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Mcustiel\CompactPages\Classes\Services\HtmlCompactorInterface::compactHtml()
     */
    public function compactHtml($html)
    {
        return str_replace("\n", '', Minify::html($html, true, false));
    }
}
