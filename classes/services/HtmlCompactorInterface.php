<?php

namespace Mcustiel\CompactPages\Classes\Services;

interface HtmlCompactorInterface
{
    /**
     * Compresses the given HTML.
     *
     * @param string $html The HTML to minify
     *
     * @return string The minified HTML
     */
    public function compactHtml($html);
}
