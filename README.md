# oc-pages-compactor
OctoberCMS plugin that provides embedding of assets to pages.

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
