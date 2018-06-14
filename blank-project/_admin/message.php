<?php
include('../sulata/includes/config.php');
include('../sulata/includes/language.php');
include('../sulata/includes/functions.php');
include('../sulata/includes/get-settings.php');
$showManageIcon = FALSE;

$title = 'System Message';
$h1 = $title;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $getSettings['site_name'] . ' - ' . $h1; ?></title>
        <?php include('includes/head.php'); ?>
        <script type="text/javascript">

            $(document).ready(function () {
                //Keep session alive
                $(function () {
                    window.setInterval("suStayAlive('<?php echo PING_URL; ?>')", 300000);
                });
                //Disable submit button
                suToggleButton(1);

            });
            //Set variable TRUE to save on CTRL + S
            var saveOnCtrlS = false;
        </script> 



    </head>
    <body>

        <p id="loading-area"></p>
        <div class="container-fluid" id="container-area">
            <div class="row">
                <main>
                    <div class="col-sm-10 content-area" id="working-area">
                        <!-- Add new -->
                        <?php if ($showManageIcon == TRUE) { ?>
                            <a href="<?php echo ADMIN_URL; ?>manage<?php echo PHP_EXTENSION; ?>/<?php echo $table; ?>/" class="btn btn-circle"><i class="fa fa-table"></i></a>
                        <?php } ?>
                        <?php
                        include('includes/header.php');
                        ?>

                        <div id="error-area">
                            <ul></ul>
                        </div>    
                        <div id="message-area">
                            <p></p>
                        </div>
                        <p class="system-message">
                            <?php
                            if (isset($_GET['msg'])) {
                                echo $_GET['msg'];
                            }
                            ?></p>

                    </div>

                </main>
                <?php include('includes/sidebar.php'); ?>
            </div>
            <?php include('includes/footer.php'); ?>
        </div>
        <?php include('includes/footer-js.php'); ?>
        <?php suIframe(); ?>
    </body>
</html>