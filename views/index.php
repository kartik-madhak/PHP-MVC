<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<h1>Welcome to website!</h1>

<div style="border: blue solid thin; border-radius: 4px; padding: 10px">
    <form action="/home" method="get">
        <div>
        <label>
            Type a username to search
            <input type="text" name="name">
        </label>
        </div>

        <div>
        <button type="submit">Submit</button>
        </div>
    </form>
</div>

<div style="border: blue solid thin; border-radius: 4px; padding: 10px">
    <form action="/add" method="post">
        <div>
        <label>
            Type a username to add
            <input type="text" name="name">
        </label>
        </div>
        <div>
        <button type="submit">Submit</button>
        </div>
    </form>
    <?php
        echo $msg ?? '';
    ?>
</div>
</body>
</html>