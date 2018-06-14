<?php
include('../sulata/includes/config.php');
include('../sulata/includes/language.php');
include('../sulata/includes/functions.php');
include('../sulata/includes/get-settings.php');
$showManageIcon = FALSE;
//Check admin login.
//If user is not logged in, send to login page.
checkAdminLogin();
$sessionUserId = $_SESSION[SESSION_PREFIX . 'user_id'];
$title = 'Welcome';
$h1 = $title;

//Check IP restriction
if (!in_array(ADMIN_GROUP_NAME, $_SESSION[SESSION_PREFIX . 'user_group'])) {
    suCheckIpAccess();
}
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
                        <div>
                            <div class="row">
                                <div class="form-group">

                                    <!-- Line Chart -->
                                    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
                                        <h4>Line Chart</h4>
                                        <?php
                                        $labelsArray = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                                        $dataArray = array("10", "60", "20", "40", "30", "50", "70", "90", "100", "80", "10", "30");
                                        $title = urlencode("Sales in Millions");
                                        $sizeArray = array('90%', '90%');
                                        $labelsArray = urlencode(json_encode($labelsArray));
                                        $dataArray = urlencode(json_encode($dataArray));
                                        $clickUrl = '';
                                        $type = 'line';
                                        ?>
                                        <iframe class="alpha-border" width="100%" height="350" frameborder="0" src="<?php echo BASE_URL; ?>chartjs/index.php?title=<?php echo $title; ?>&labels=<?php echo $labelsArray; ?>&data=<?php echo $dataArray; ?>&click_url=<?php echo $clickUrl; ?>&type=<?php echo $type; ?>"></iframe>
                                    </div>
                                    <!-- Pie Chart -->
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                        <h4>Pie Chart</h4>
                                        <?php
                                        $labelsArray = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                                        $dataArray = array("10", "60", "20", "40", "30", "50", "70", "90", "100", "80", "10", "30");
                                        $title = urlencode("Sales in Millions");
                                        $sizeArray = array('90%', '90%');
                                        $labelsArray = urlencode(json_encode($labelsArray));
                                        $dataArray = urlencode(json_encode($dataArray));
                                        $clickUrl = '';
                                        $type = 'pie';
                                        ?>
                                        <iframe class="alpha-border" width="100%" height="350" frameborder="0" src="<?php echo BASE_URL; ?>chartjs/index.php?title=<?php echo $title; ?>&labels=<?php echo $labelsArray; ?>&data=<?php echo $dataArray; ?>&click_url=<?php echo $clickUrl; ?>&type=<?php echo $type; ?>"></iframe>
                                    </div>
                                    <!-- Bar Chart -->
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                        <h4>Bar Chart</h4>
                                        <?php
                                        $labelsArray = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                                        $dataArray = array("10", "60", "20", "40", "30", "50", "70", "90", "100", "80", "10", "30");
                                        $title = urlencode("Sales in Millions");
                                        $sizeArray = array('90%', '90%');
                                        $labelsArray = urlencode(json_encode($labelsArray));
                                        $dataArray = urlencode(json_encode($dataArray));
                                        $clickUrl = '';
                                        $type = 'bar';
                                        ?>
                                        <iframe class="alpha-border" width="100%" height="300" frameborder="0" src="<?php echo BASE_URL; ?>chartjs/index.php?title=<?php echo $title; ?>&labels=<?php echo $labelsArray; ?>&data=<?php echo $dataArray; ?>&click_url=<?php echo $clickUrl; ?>&type=<?php echo $type; ?>"></iframe>
                                    </div>
                                    <!-- Pie Chart -->
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                        <h4>Horizontal Bar Chart</h4>
                                        <?php
                                        $labelsArray = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                                        $dataArray = array("10", "60", "20", "40", "30", "50", "70", "90", "100", "80", "10", "30");
                                        $title = urlencode("Sales in Millions");
                                        $sizeArray = array('90%', '90%');
                                        $labelsArray = urlencode(json_encode($labelsArray));
                                        $dataArray = urlencode(json_encode($dataArray));
                                        $clickUrl = '';
                                        $type = 'horizontalBar';
                                        ?>
                                        <iframe class="alpha-border" width="100%" height="300" frameborder="0" src="<?php echo BASE_URL; ?>chartjs/index.php?title=<?php echo $title; ?>&labels=<?php echo $labelsArray; ?>&data=<?php echo $dataArray; ?>&click_url=<?php echo $clickUrl; ?>&type=<?php echo $type; ?>"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
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