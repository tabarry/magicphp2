<?php
include('../sulata/includes/config.php');
include('../sulata/includes/language.php');
include('../sulata/includes/functions.php');
include('../sulata/includes/get-settings.php');
//instantiate Add more counter
$_SESSION[SESSION_PREFIX . 'add_more_counter'] = '';

$showManageIcon = TRUE;
//Check admin login.
//If user is not logged in, send to login page.
checkAdminLogin();

$sessionUserId = $_SESSION[SESSION_PREFIX . 'user_id'];

$mode = 'add';
$table = suSegment(1);
$tableSegment = suSegment(1);
//Stop unauthorised add access
$addAccess = suCheckAccess(suUnTablify($table), 'addables');
$viewAccess = suCheckAccess(suUnTablify($table), 'viewables');
if (!in_array(ADMIN_GROUP_NAME, $_SESSION[SESSION_PREFIX . 'user_group'])) {
    //Check IP restriction
    suCheckIpAccess();
    //Stop unauthorised access
    if ($addAccess == FALSE) {
        suExit(INVALID_ACCESS);
    }
}
//Any actions desired at this point should be coded in this file
if (file_exists('includes/custom/add-a.php')) {
    include('includes/custom/add-a.php');
}


if (!isset($table) || $table == '') {
    suExit(INVALID_RECORD);
}



$sql = "SELECT id,title,slug,label_add,label_update,display,save_for_later,structure FROM " . STRUCTURE_TABLE_NAME . " WHERE live='Yes' AND slug='" . suUnTablify($table) . "' LIMIT 0,1";
$result = suQuery($sql);
$numRows = $result['num_rows'];
if ($numRows == 0) {
    suExit(INVALID_RECORD);
}
$result['result'] = suUnstrip($result['result']);

$row = $result['result'][0];
$id = $row['id'];
$title = $row['title'];

$label_add = $row['label_add'];
$label_update = $row['label_update'];
$display = $row['display'];
$save_for_later = $row['save_for_later'];

$structure = $row['structure'];
//$structure= html_entity_decode($structure);
//$structure = json_decode($structure, 1);
//Create heading
$h1 = 'Add ' . $title;
//Required for date picker
$date_format = $getSettings['date_format'];
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
            var saveOnCtrlS = true;

        </script> 



    </head>
    <body>
        <p id="loading-area"></p>
        <div class="container-fluid" id="container-area1">
            <div class="row">
                <main>
                    <div class="col-sm-10 content-area" id="working-area">
                        <!-- Add new -->
                        <?php if ($_GET['overlay'] != 1) { ?>
                            <?php if ($showManageIcon == TRUE) { ?>
                                <a title="<?php echo MANAGE . ' ' . $title; ?>" href="<?php echo ADMIN_URL; ?>manage<?php echo PHP_EXTENSION; ?>/<?php echo $table; ?>/" class="btn btn-circle"><i class="fa fa-table"></i></a>
                            <?php } ?>
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
                        <?php
                        if ($_GET['reload'] == yes) {
                            $reload = '?reload=yes';
                        } else {
                            $reload = '';
                        }
                        ?>
                        <form name="suForm" id="suForm" method="post" target="remote" action="<?php echo ADMIN_URL; ?>remote<?php echo PHP_EXTENSION; ?>/add/<?php echo suUnTablify($table); ?>/<?php echo $reload; ?>" enctype="multipart/form-data"  data-parsley-validate>
                            <?php
                            //Any actions desired at this point should be coded in this file
                            if (file_exists('includes/custom/add-b.php')) {
                                include('includes/custom/add-b.php');
                            }
                            ?>

                            <div class="row">
                                <div class="form-group">
                                    <?php
                                    $uniqueArray = array();
                                    $tabIndex = 0;
                                    foreach ($structure as $value) {
                                        $tabIndex++;
                                        $value['TabIndex'] = $tabIndex;
                                        if ($value['Type'] == 'hidden' || $value['Type'] == 'ip_address') {
                                            //Any actions desired at this point should be coded in this file
                                            if (file_exists('includes/custom/add-c.php')) {
                                                include('includes/custom/add-c.php');
                                            }
                                            suBuildField($value, $mode);
                                        } elseif ($value['Type'] == 'line_break') {
                                            ?>
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 zero-height" id="data_div_<?php echo $value['Slug']; ?>">
                                                <?php
                                                suBuildField($value, $mode, $label_add);
                                                //Any actions desired at this point should be coded in this file
                                                if (file_exists('includes/custom/add-d.php')) {
                                                    include('includes/custom/add-d.php');
                                                }
                                                ?>
                                                <div>&nbsp;</div>
                                            </div>
                                            <?php
                                        } else {
                                            ?>
                                            <div class="col-xs-12 col-sm-12 col-md-<?php echo $value['Width']; ?> col-lg-<?php echo $value['Width']; ?>" id="data_div_<?php echo $value['Slug']; ?>">
                                                <?php
                                                suBuildField($value, $mode, $label_add);
                                                //Any actions desired at this point should be coded in this file
                                                if (file_exists('includes/custom/add-d.php')) {
                                                    include('includes/custom/add-d.php');
                                                }
                                                ?>
                                                <div>&nbsp;</div>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>


                                <?php
                                //Any actions desired at this point should be coded in this file
                                if (file_exists('includes/custom/add-e.php')) {
                                    include('includes/custom/add-e.php');
                                }
                                ?>
                            </div>

                            <div>&nbsp;</div>

                            <div class="clearfix"></div>
                            <div>&nbsp;</div>
                            <p class="pull-right">
                                <?php
                                //Build sorting field
                                $arg = array('type' => 'hidden', 'name' => 'sortOrder', 'id' => 'sortOrder', 'value' => '10000');
                                echo suInput('input', $arg);
                                //If reloadField
                                if (isset($_GET['reloadField'])) {
                                    $arg = array('type' => 'hidden', 'name' => 'reloadField', 'id' => 'reloadField', 'value' => $_GET['reloadField']);
                                    echo suInput('input', $arg);
                                    $arg = array('type' => 'hidden', 'name' => 'sourceField', 'id' => 'sourceField', 'value' => $_GET['sourceField']);
                                    echo suInput('input', $arg);
                                }

                                //Hidden to store save for later action
                                $arg = array('type' => 'hidden', 'name' => 'save_for_later_use', 'id' => 'save_for_later_use', 'value' => 'No');
                                echo suInput('input', $arg);

                                //Submit
                                $arg = array('type' => 'submit', 'name' => 'Submit', 'id' => 'Submit', 'class' => 'btn btn-theme', 'title' => SUBMIT);
                                echo suInput('button', $arg, $submitButton, TRUE);

                                //If save for later
                                if ($save_for_later == 'Yes') {
                                    echo ' ';
                                    $arg = array('type' => 'submit', 'name' => 'save_for_later', 'id' => 'save_for_later', 'class' => 'btn btn-theme');
                                    echo suInput('button', $arg, $saveButton, TRUE);
                                }
                                ?>   

                            </p>
                            <p>&nbsp;</p>
                        </form>
                        <script>
                            $("#suForm").parsley({"validationThreshold": 0});
                            //On modal window close, reset modal iframe url
                            $("#modal-close-btn").click(function () {
                                window.top.overlayFrame.location.href = "<?php echo PING_URL; ?>";
                            });
                            $("#modal-close-btn2").click(function () {
                                window.top.overlayFrame.location.href = "<?php echo PING_URL; ?>";
                            });


                        </script>
                        <?php
                        //Any actions desired at this point should be coded in this file
                        if (file_exists('includes/custom/add-f.php')) {
                            include('includes/custom/add-f.php');
                        }
                        ?>
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