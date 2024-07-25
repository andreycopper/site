<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Mesigo</title>

    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta name="description" content="Chat"/>
    <meta name="keywords" content="chat, web chat, communication, group chat, message, messenger"/>
    <meta name="author" content="Andrew Copper"/>

    <link rel="shortcut icon" href="/favicon.ico" id="tabIcon">

    <link href="/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/css/fonts.css" rel="stylesheet" type="text/css"/>
    <link href="/css/icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="/css/styles.css" rel="stylesheet" type="text/css"/>
    <link href="/css/loader.css" rel="stylesheet" type="text/css"/>
    <link href="/css/media.css" rel="stylesheet" type="text/css"/>

    <script src="/js/jquery-3.7.1.min.js"></script>
</head>

<body class="simple flex-lg-row">

<div class="auth-bg">
    <div class="d-flex flex-column authentication-page-content">
        <div class="d-flex flex-column h-100 px-4 pt-4 flex-grow-1">
            <div class="row justify-content-center my-auto flex-grow-1">
                <div class="row col-sm-8 col-lg-6 col-xl-5 col-xxl-4">
                    <?php echo $view ?? null; ?>
                </div>
            </div>

            <div class="row flex-grow-0">
                <div class="col-xl-12">
                    <div class="text-center text-muted p-2">
                        <p class="mb-0 font-size-13">
                            &copy; <?= date('Y') ?> Mesigo by Andrew Copper
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

<script src="/js/bootstrap.bundle.min.js"></script>
<script src="/js/glightbox.min.js"></script>
<script src="/js/scripts.js"></script>

</body>
</html>
