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
<div class="container w-75">
    <div class="display-4 text-center">
        Welcome to Migration Helper
    </div>
    <div class="body">
        <?php

        use Lib\database\FluentDB;

        $files = array_diff(scandir(__DIR__ . '/../models'), array('.', '..'));
        foreach ($files as $file) {
            if (strpos($file, '.php') != -1) {
                $modelName = rtrim($file, ".php");
                $fluentDB = new FluentDB($modelName);
                $arr = get_class_vars($modelName);
                foreach ($arr as $k => $v) {
                    try {
                        $arr[$k] = (string)(new \ReflectionProperty($modelName, $k))->getType();
                    } catch (ReflectionException $e) {
                    }
                }
                echo $fluentDB->createTable($arr) == true ? 'Created table ' : 'Table already exists<br>';
            }
        }
        ?>
    </div>
</div>
</body>
</html>