<?php
use Models\User;
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Site</title>

    <link rel="stylesheet" href="/css/bootstrap.css" media="all">
    <link rel="stylesheet" href="/css/icons.min.css" media="all">
    <link rel="stylesheet" href="/css/styles.css" media="all">
    <link rel="stylesheet" href="/css/media.css" media="all">
    <link rel="stylesheet" href="/css/loader.css" media="all">

    <script src="/js/jquery-3.7.1.min.js"></script>
    <script src="/js/ondelay.jquery.js"></script>
    <script src="/js/functions.js"></script>

    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
</head>
<body class="d-flex flex-column">
<header class="text-center">
    header
</header>

<main class="d-flex flex-grow-1">
    <?php echo $view ?? null; ?>
</main>

<footer class="text-center">
    footer
</footer>

<div id="error"></div>

<div id="loader">
    <div class="footer-loader">
        <div class="loader-container" >
            <!--            <div class="loader-message">Подождите, Ваши данные обрабатываются</div>-->
            <div class='sk-fading-circle'>
                <div class='sk-circle sk-circle-1'></div>
                <div class='sk-circle sk-circle-2'></div>
                <div class='sk-circle sk-circle-3'></div>
                <div class='sk-circle sk-circle-4'></div>
                <div class='sk-circle sk-circle-5'></div>
                <div class='sk-circle sk-circle-6'></div>
                <div class='sk-circle sk-circle-7'></div>
                <div class='sk-circle sk-circle-8'></div>
                <div class='sk-circle sk-circle-9'></div>
                <div class='sk-circle sk-circle-10'></div>
                <div class='sk-circle sk-circle-11'></div>
                <div class='sk-circle sk-circle-12'></div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
