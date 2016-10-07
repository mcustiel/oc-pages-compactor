# oc-pages-compactor
OctoberCMS plugin that provides HTML code minification and embedding of assets to pages.

## Twig tags

### inlineStyles

Embeds the content of the add css files into the HTML code. 

#### Example

* Component.php

```php
$this->addCss('my/style.css');
```

* my/style.css

```css
a {
    color: red;
}
```

* twig.htm:

```twig
<head>
    {% inlineStyles %}
</head>
```

* HTML result:
```html
<head>
    <style type="text/css">
    a {
        color: red;
    }
    </style>
</head>
```

### inlineScripts

The behaviour of this tag is analog to that of inlineStyles, for javascript assets added with addJs.

## Configuration

Default configuration for the plugin is:

* config.php:
```
return [
    'compactation' => [
        'enabled' => false,
        'compactor' => '\\Mcustiel\\InlineAssets\\Classes\\Services\\Implementation\\PhpWeeHtmlCompactor',
    ],
];
```

To activate page minification, you need to overwrite the config as explained in [october's documentation](https://octobercms.com/docs/plugin/settings#file-configuration) and set 'enabled' to true. 

If you want to add your own html minificator, you can specify it in the config. Your minificator must implement `\Mcustiel\InlineAssets\Classes\Services\HtmlCompactorInterface`:

```php
interface HtmlCompactorInterface
{
    /**
     * @param string $html
     * @return string
     */
    public function compactHtml($html);
}
```