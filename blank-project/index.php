<?php
include('sulata/includes/config.php');
include('sulata/includes/language.php');
include('includes/functions.php');
include('sulata/includes/get-settings.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $getSettings['site_name'] . ' - ' . $h1; ?></title>
        <?php include('_admin/includes/head.php'); ?>
        <script type="text/javascript">

            $(document).ready(function () {
                //Keep session alive
                $(function () {
                    window.setInterval("suStayAlive('<?php echo PING_URL; ?>')", 300000);
                });
                //Disable submit button
                suToggleButton(1);

            });
        </script> 



    </head>
    <body>

        <?php include('_admin/includes/footer-js.php'); ?>
    </body>
</html>
<?php suIframe(); ?>