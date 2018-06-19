<?php
include('../sulata/includes/config.php');
include('../sulata/includes/language.php');
include('../sulata/includes/functions.php');
include('../sulata/includes/get-settings.php');

$showManageIcon = TRUE;

//Check admin login.
//If user is not logged in, send to login page.
checkAdminLogin();

$sessionUserId = $_SESSION[SESSION_PREFIX . 'user_id'];

$mode = 'update';
$pageMode = $mode;
$table = suSegment(1);
$tableSegment = suSegment(1);
$rid = suSegment(2);
$s3 = suSegment(3);

//Stop unauthorised update access
$editAccess = suCheckAccess(suUnTablify($table), 'updateables');
if (!in_array(ADMIN_GROUP_NAME, $_SESSION[SESSION_PREFIX . 'user_group'])) {
    //Check IP restriction
    suCheckIpAccess();
    //Stop unauthorised access
    if ($s3 !== 'profile') {
        if ($editAccess == FALSE) {
            suExit(INVALID_ACCESS);
        }
    }
}

if ($s3 == 'duplicate') {
    $duplicate = TRUE;
} else {
    $duplicate = FALSE;
}

//For profile update handling
if ($s3 == 'profile') {
    $profile = TRUE;
} else {
    $profile = FALSE;
}

if (!isset($table) || $table == '') {
    suExit(INVALID_RECORD);
}


//Get title
$sql = "SELECT id,title,slug,label_add,label_update,display,save_for_later,structure FROM " . STRUCTURE_TABLE_NAME . " WHERE live='Yes' AND slug='" . suUnTablify($table) . "' LIMIT 0,1";
//Any actions desired at this point should be coded in this file
if (file_exists('includes/custom/update-a.php')) {
    include('includes/custom/update-a.php');
}
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
$saveForLater = $row['save_for_later'];


//Any actions desired at this point should be coded in this file
if (file_exists('includes/custom/update-b.php')) {
    include('includes/custom/update-b.php');
}

$structure = $row['structure'];

//Build sorting field
array_push($structure, array('Name' => 'sortOrder', 'Slug' => 'sortOrder', 'Type' => 'hidden'));
if ($duplicate == TRUE) {
    $h1 = 'Add ' . $title;
    $action = 'add';
} else {
    $h1 = 'Update ' . $title;
    $action = 'update';
}

//Get data
$sqlData = "SELECT id,data FROM " . suUnTablify($table) . " WHERE live='Yes' AND id='$rid' LIMIT 0,1";
//Any actions desired at this point should be coded in this file
if (file_exists('includes/custom/update-c.php')) {
    include('includes/custom/update-c.php');
}
$resultData = suQuery($sqlData);
$numRowsData = $resultData['num_rows'];
if ($numRowsData == 0) {
    suExit(INVALID_RECORD);
}
$resultData['result'] = suUnstrip($resultData['result']);

$rowData = $resultData['result'][0];
$data_id = $rowData['id'];
$data_data = $rowData['data'];

$data = $data_data;
//$data = html_entity_decode($data);
//$data = json_decode($data, 1);
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
        <div class="container-fluid" id="container-area">
            <div class="row">
                <main>
                    <div class="col-sm-10 content-area" id="working-area">
                        <!-- Add new -->
                        <?php if ($showManageIcon == TRUE) { ?>
                            <a title="<?php echo MANAGE . ' ' . $title; ?>" href="<?php echo ADMIN_URL; ?>manage<?php echo PHP_EXTENSION; ?>/<?php echo $table; ?>/" class="btn btn-circle"><i class="fa fa-table"></i></a>
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
//Any actions desired at this point should be coded in this file
                        if (file_exists('includes/custom/update-d.php')) {
                            include('includes/custom/update-d.php');
                        }
                        ?>
                        <form name="suForm" id="suForm" method="post" target="remote" action="<?php echo ADMIN_URL; ?>remote<?php echo PHP_EXTENSION; ?>/<?php echo $action; ?>/<?php echo suUnTablify($table); ?>/" enctype="multipart/form-data" data-parsley-validate>
                            <?php
                            //Previous
                            if ($duplicate == TRUE) {
                                $arg = array('type' => 'hidden', 'name' => '_____duplicate', 'id' => '_____duplicate', 'value' => 'duplicate');
                                echo suInput('input', $arg);
                            }
                            //Profile
                            if ($profile == TRUE) {
                                $arg = array('type' => 'hidden', 'name' => '_____profile', 'id' => '_____profile', 'value' => 'profile');
                                echo suInput('input', $arg);
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
                                        if (is_array($data[$value['Slug']])) {
                                            $value = array_merge($value, array('_____value' => $data[$value['Slug']]));
                                        } else {
                                            $value = array_merge($value, array('_____value' => $data[$value['Slug']]));
                                        }

                                        //$value = $rowData[];
                                        if ($value['Type'] == 'hidden' || $value['Type'] == 'ip_address') {
                                            //Any actions desired at this point should be coded in this file
                                            if (file_exists('includes/custom/update-e.php')) {
                                                include('includes/custom/update-e.php');
                                            }
                                            suBuildField($value, $mode);
                                        } else {
                                            if ($value['HideOnUpdate'] == 'yes') {
                                                //If hide on update
                                                //Any actions desired at this point should be coded in this file
                                                if (file_exists('includes/custom/update-f.php')) {
                                                    include('includes/custom/update-f.php');
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
                                                    //Any actions desired at this point should be coded in this file
                                                    if (file_exists('includes/custom/update-g.php')) {
                                                        include('includes/custom/update-g.php');
                                                    }
                                                    suBuildField($value, $mode, $label_update);
                                                    ?>
                                                    <div>&nbsp;</div>
                                                </div>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                </div>

                                <?php
//Any actions desired at this point should be coded in this file
                                if (file_exists('includes/custom/update-h.php')) {
                                    include('includes/custom/update-h.php');
                                }
                                ?>
                            </div>

                            <div>&nbsp;</div>

                            <div class="clearfix"></div>
                            <div>&nbsp;</div>

                            <p class="pull-right">
                                <?php
//ID in hidden
                                $arg = array('type' => 'hidden', 'name' => 'id', 'id' => 'id', 'value' => suCrypt($rid));
                                echo suInput('input', $arg);

//Redirect
                                if ($duplicate == TRUE) {
                                    $redirect = ADMIN_URL . "manage" . PHP_EXTENSION . "/" . suTablify($tableSegment) . "/";
                                    $arg = array('type' => 'hidden', 'name' => 'redirect', 'id' => 'redirect', 'value' => $redirect);
                                } else {
                                    if (suSegment(3) == 'profile') {
                                        $redirect = ADMIN_URL . "message" . PHP_EXTENSION . "?msg=" . PROFILE_UPDATE;
                                        $arg = array('type' => 'hidden', 'name' => 'redirect', 'id' => 'redirect', 'value' => $redirect);
                                    } else {
                                        $redirect = ADMIN_URL . "manage" . PHP_EXTENSION . "/" . suTablify($tableSegment) . "/?" . $_SERVER['QUERY_STRING'];
                                        $arg = array('type' => 'hidden', 'name' => 'redirect', 'id' => 'redirect', 'value' => $redirect);
                                    }
                                }

                                echo suInput('input', $arg);

//Hidden to store save for later action
                                $arg = array('type' => 'hidden', 'name' => 'save_for_later_use', 'id' => 'save_for_later_use', 'value' => 'No');
                                echo suInput('input', $arg);

//Submit
                                $arg = array('type' => 'submit', 'name' => 'Submit', 'id' => 'Submit', 'class' => 'btn btn-theme', 'title' => SUBMIT);
                                echo suInput('button', $arg, $submitButton, TRUE);

//If save for later
                                if ($saveForLater == 'Yes') {
                                    if ($data['save_for_later_use'] != 'No' || $duplicate != FALSE) {
                                        echo ' ';
                                        $arg = array('type' => 'submit', 'name' => 'save_for_later', 'id' => 'save_for_later', 'class' => 'btn btn-theme');
                                        echo suInput('button', $arg, $saveButton, TRUE);
                                    }
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
                        if (file_exists('includes/custom/update-i.php')) {
                            include('includes/custom/update-i.php');
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