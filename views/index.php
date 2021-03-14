<!doctype html>
<html lang="en">
<?php include('layout/head.php') ?>
<body>

<div class="container">
    <div class="display-4 text-center">Welcome to website!</div>

    <form action="/home">
        <div class="text-center">
            <button class="btn btn-outline-primary" type="submit">Login</button>
        </div>
    </form>

    <button class="btn btn-outline-secondary" type="submit" onclick="ajaxTest()">Click me to test an AJAX request
    </button>
    <div id="result"></div>


    <div>
        <form action="/home" method="post">
            <button class="btn btn-outline-warning" type="submit">Test a POST request</button>
        </form>
        <?php echo $msg ?? ''; ?>
    </div>
</div>

</body>
<script>
    function ajaxTest() {
        console.log('CALLED');
        $.ajax({
            type: "GET",
            url: '/testingAjax',
            success: function (response) {
                console.log(JSON.parse(response));
                document.getElementById("result").innerHTML = 'AJAX SUCCESS, SEE CONSOLE';
            },
            error: function (response) {
                alert('AJAX FAILED!');
                console.log(response);
            }
        });
    }
</script>
</html>