# tflori/syna

[![Build Status](https://travis-ci.org/tflori/syna.svg?branch=master)](https://travis-ci.org/tflori/syna)
[![Coverage Status](https://coveralls.io/repos/github/tflori/syna/badge.svg?branch=master)](https://coveralls.io/github/tflori/syna?branch=master)
[![Latest Stable Version](https://poser.pugx.org/tflori/syna/v/stable.svg)](https://packagist.org/packages/tflori/syna) 
[![Total Downloads](https://poser.pugx.org/tflori/syna/downloads.svg)](https://packagist.org/packages/tflori/syna) 
[![License](https://poser.pugx.org/tflori/syna/license.svg)](https://packagist.org/packages/tflori/syna)

PHP library for rendering native php templates with sections, inheritance and helpers.

This library is inspired by [aura/view](https://packagist.org/packages/aura/view) and 
[league/plates](https://packagist.org/packages/league/plates). Both have advantages against each other and both are
lacking some major features.

> Að sýna means to show in Icelandic.

### Helpers

Both libraries are missing a functionality to not register each helper. Syna has the ability to register namespaces
where your helpers are placed. Adding the namespace `App\ViewHelper` would load `App\ViewHelper\Date` for
`$view->date()`.

### Layouts

Syna also provides the ability to use layouts as described by the TwoStepView pattern. Other then extending views from
inside a view (suggested by other libraries like league/plates, illuminate/blade etc.) you should define the layout
in your controller. The separation of concerns between extending views and wrapping a html snipped into another one
is logic that should not be decided from views (e. g. loading the content of a modal dialog or loading a full page with
navigation, header and footer).

### Named Locators

### Extending Views 

## Installation

Like all my libraries: only with composer

```console
$ composer require tflori/syna
```

## Basic usage

```php
<?php

use Syna\Factory;
use Syna\HelperLocator;
use Syna\ViewLocator;

$viewLocator = new ViewLocator(__DIR__ . '/resources/views');
$layoutLocator = new ViewLocator(__DIR__ . '/resources/layouts');
$helperLocator = new HelperLocator();

$templates = new Factory($viewLocator, $helperLocator, $layoutLocator);

echo $templates->render('pages/home', [], 'fullPage');
```

**layouts/fullPage.phtml**

```php
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Example</title>
    </head>
    <body>
        <?= $v->fetch('partials/navbar') ?>
        
        <div id="content">
            <?= $v->section('content') ?>
        </div>
    </body>
</html>
```


**views/pages/home**

```php
<?php $v->extends('pageWithTeaser'); ?>

<?php $v->start('teaser') ?>

<img src="teaser.jpg" />

<div class="teaser-content">
    <h2>Title for teaser</h2>
    <p>Lorem ipsum dolor sit amet...</p>
</div>

<?php $v->end(); ?>

<div class="card">Lorem ipsum dolor sit amet...</div>
<div class="card">Lorem ipsum dolor sit amet...</div>

<p>what ever...</p>
```

**views/pageWithTeaser**

```php
<div class="teaser">
    <?= $v->section('teaser') ?>
</div>

<?= $v->section('content') ?>
```

Please also have a look at the [example](example.php) for a more concrete example and at the [tests](tests) for the
documented examples.
