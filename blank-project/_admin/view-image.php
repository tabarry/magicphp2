<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <style>

            body{

                background: #EEE url(<?php echo base64_decode($_GET['path']); ?>) no-repeat center center fixed; 
                -webkit-background-size: contain;
                -moz-background-size: contain;
                -o-background-size: contain;
                background-size: contain;
            }
        </style>
    </head>
    <body>
    </body>
</html>
