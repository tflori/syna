# tflori/syna

[![Build Status](https://travis-ci.org/tflori/syna.svg?branch=master)](https://travis-ci.org/tflori/syna)
[![Coverage Status](https://coveralls.io/repos/github/tflori/syna/badge.svg?branch=master)](https://coveralls.io/github/tflori/syna?branch=master)
[![Latest Stable Version](https://poser.pugx.org/tflori/syna/v/stable.svg)](https://packagist.org/packages/tflori/syna) 
[![Total Downloads](https://poser.pugx.org/tflori/syna/downloads.svg)](https://packagist.org/packages/tflori/syna) 
[![License](https://poser.pugx.org/tflori/syna/license.svg)](https://packagist.org/packages/tflori/syna)

PHP library for rendering native php templates with sections, inheritance and helpers.  

## Installation

Like all my libraries: only with composer

```console
$ composer require tflori/syna
```

## Basic usage

```php
<?php

use Syna\Engine;
use Syna\HelperLocator;
use Syna\ViewLocator;

$viewLocator = new ViewLocator(__DIR__ . '/resources/views');
$layoutLocator = new ViewLocator(__DIR__ . '/resources/layouts');
$helperLocator = new HelperLocator();

$templates = new Engine($viewLocator, $helperLocator, $layoutLocator);

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

Please also have a look at the [example](example.php) 
