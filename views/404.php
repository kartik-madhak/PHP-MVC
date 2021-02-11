<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .centered {
            position: fixed; /* or absolute */
            top: 50%;
            left: 50%;
            /* bring your own prefixes */
            transform: translate(-50%, -50%);
        }
    </style>
</head>
<body>
<div class="centered">
    <h1 style="color: red; text-align: center">404 ERRORRRRRRRRRRRR</h1>
    <div style="text-align: center">
        <?php
        echo $error ?? '';
        ?>
    </div>
</div>
</body>
</html>