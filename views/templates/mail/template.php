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

    <style>
        * {
            margin: 0;
            padding: 0;
            font: 14px / 18px 'Verdana, Arial', sans-serif;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            display: flex;
            flex: 1 0 auto;
            align-items:center;
            justify-content: center;
            width: 100%;
        }
        h3 {
            margin: 10px 0;
            font-size: 16px;
            font-weight: bold;
        }
        p {
            margin-bottom: 10px;
        }
        b {
            font-weight: bold;
        }
        .btn {
            display: inline-block;
            margin-bottom: 10px;
            padding: 10px 15px;
            color: #ffffff;
            font-size: 13px;
            text-decoration: none;
            background-color: #4eac6d;
            border-radius: 3px;
        }
        .link {
            color: #4eac6d;
            text-decoration: underline;
        }
    </style>
</head>

<body>

<main>
    <?php echo $view ?? null; ?>
</main>

</body>
</html>
