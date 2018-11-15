<?php /** @var Syna\View $v */ /** @var callable $e */ ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Example</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Noto+Sans">
        <style type="text/css">
            nav .nav-wrapper {
                max-width: 992px;
                margin: 0 auto;
                padding: 0 20px;
            }

            .card .card-content p {
                margin-top: 1rem;
            }

            #left-col {
                min-height: 400px;
                position: relative;
            }

            #left-col .nav-wrapper {
                width: calc(100% - 1.5rem);
                position: absolute;
                z-index: 2;
            }

            #left-col li > ul {
                margin-left: 1rem;
            }

            #left-col .nav li {
                line-height: 48px;
                font-size: 14px;
                font-weight: 500;
            }

            #left-col .nav li > a {
                color: rgba(0, 0, 0, 0.87);
                display: block;
                padding: 0 32px;
            }

            #left-col .nav li > a > i {
                float: left;
                height: 48px;
                line-height: 48px;
                margin: 0 32px 0 0;
                width: 24px;
                color: rgba(0, 0, 0, 0.54);
            }
        </style>
    </head>
    <body class="grey lighten-4">

        <!-- Toolbar -->
        <div class="navbar-fixed">
            <nav class="teal darken-1">
                <div class="nav-wrapper">
                    <a href="#" class="sidenav-toggle left hide-on-large-only"><i class="material-icons menu-icon">menu</i><i class="material-icons close-icon">close</i></a>
                    <a href="/home" class="brand-logo left">Example <small><?= $e($v->section('subtitle')) ?></small></a>
                    <ul class="right">
                        <li class="icon"><a class="btnLogin"><i class="material-icons left">account_circle</i><span class="icon-text"> Login / Signup</span></a></li>
                    </ul>
                </div>
            </nav>
        </div>

        <div class="container">
            <div class="row">
                <div class="col l3 hide-on-med-and-down" id="left-col">
                    <div class="nav-wrapper">
                        <?= $v->fetch('partials/navigation', ['items' => $menu]) ?>
                        <?= $v->section('sidebar') ?>
                    </div>
                </div>
                <div class="col s12 l9" id="right-col">
                    <?= $v->section('content') ?>
                    <?= $v->varDump($menu) ?>
                </div>
            </div>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
        <script>
            $(window).on('resize', function() {
                let $leftNavWrapper = $('#left-col .nav-wrapper');
                $leftNavWrapper.css({
                    width: '',
                    position: '',
                });
                window.setTimeout(function() {
                    $leftNavWrapper.css({
                        width: $leftNavWrapper.width() + 'px',
                        position: 'fixed',
                    });
                }, 0);
            }).resize();
        </script>
    </body>
</html>
