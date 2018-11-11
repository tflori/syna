<?php

use Syna\Factory;
use Syna\HelperLocator;
use Syna\ViewLocator;

foreach ([__DIR__ . '/../../autoload.php', __DIR__ . '/vendor/autoload.php'] as $file) {
    if (file_exists($file)) {
        require_once $file;
    }
}

$viewLocator = new ViewLocator(__DIR__ . '/resources/views');
$layoutLocator = new ViewLocator(__DIR__ . '/resources/layouts');
$helperLocator = new HelperLocator();

$templates = new Factory($viewLocator, $helperLocator, $layoutLocator);

$templates->addSharedData([
    'menu' => [
        [
            'target' => '/home',
            'active' => true,
            'title' => 'Home',
            'icon' => 'home',
            'subitems' => [
                [
                    'target' => '/news',
                    'active' => false,
                    'title' => 'News',
                ],
                [
                    'target' => '/blog',
                    'active' => false,
                    'title' => 'Blog',
                ],
            ],
        ],
        [
            'target' => '/products',
            'active' => false,
            'title' => 'Products',
            'icon' => 'pages',
        ],
    ],
]);

try {
    echo $templates->render('index', [], 'fullPage');
} catch (Throwable $exception) {
    echo $exception;
}
