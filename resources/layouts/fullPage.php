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
            #left-col {
                position: relative;
                min-height: 400px;
            }
            #left-col ul {
                width: calc(100% - 1.5rem);
                position: absolute;
                z-index: 2;
            }
        </style>
    </head>
    <body class="grey lighten-4">

        <!-- Toolbar -->
        <div class="navbar-fixed">
            <nav class="teal darken-1">
                <div class="nav-wrapper">
                    <a href="#" class="sidenav-toggle left hide-on-large-only"><i class="material-icons menu-icon">menu</i><i class="material-icons close-icon">close</i></a>
                    <a href="/home" class="brand-logo left"><div id="logo-icon"></div><div id="logo-name"></div><div id="logo-subtitle"></div></a>
                    <ul class="right">
                        <li class="icon"><a class="btnLogin"><i class="material-icons left">account_circle</i><span class="icon-text"> Login / Signup</span></a></li>
                    </ul>
                    <div class="account">

                    </div>
                </div>
            </nav>
        </div>

        <div class="container">
            <div class="row">
                <div class="col l2 hide-on-med-and-down" id="left-col">
                    <ul class="nav">
                        <li><a href="/home"><i class="material-icons">home</i> Home</a></li>
                        <li><a href="/blog"><i class="material-icons">rss_feed</i> Blog</a></li>
                        <li><a href="/guide"><i class="material-icons">toc</i> Guide</a></li>
                        <li><a href="/docs"><i class="material-icons">library_books</i> Documentation</a></li>
                        <li><a href="/exchange"><i class="material-icons">question_answer</i> Exchange</a></li>
                    </ul>
                </div>
                <div class="col s12 l10" id="right-col">

                    <?= $v->section('content') ?>
                    <?= $v->varDump($menu) ?>

                </div>
            </div>
        </div>
    </body>
</html>
