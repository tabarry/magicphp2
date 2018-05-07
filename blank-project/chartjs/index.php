<?php
include('../sulata/includes/config.php');
include('../sulata/includes/language.php');
include('../sulata/includes/functions.php');
include('../sulata/includes/get-settings.php');
$labelsArray = json_decode($_GET['labels'], 1);
$dataArray = json_decode($_GET['data'], 1);
$title = $_GET['title'];
$type = $_GET['type'];
$clickUrl = $_GET['click_url'];
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <?php suInitChartJs(); ?>
    </head>
    <body>
        <?php suRenderChartJs('suChart', $type, $labelsArray, $dataArray, $title, $clickUrl); ?>
    </body>
</html>
