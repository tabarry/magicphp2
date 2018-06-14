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

//fetch the table name which comes in segment 1.
$table = suSegment(1);

//Stop unauthorised sort access
$sortAccess = suCheckAccess(suUnTablify($table), 'sortables');
if (!in_array(ADMIN_GROUP_NAME, $_SESSION[SESSION_PREFIX . 'user_group'])) {
    //Check IP restriction
    suCheckIpAccess();
    //Stop unauthorised access
    if ($sortAccess == FALSE) {
        suExit(INVALID_ACCESS);
    }
}
//Any actions desired at this point should be coded in this file
if (file_exists('includes/custom/sort-a.php')) {
    include('includes/custom/sort-a.php');
}
//If table name is not available, exit the code.
if (!isset($table)) {
    suExit(INVALID_RECORD);
}

//Get title and structure for the form from structure table
$sql = "SELECT id,title,show_sorting_module,structure FROM " . STRUCTURE_TABLE_NAME . " WHERE live='Yes' AND slug='" . suUnTablify($table) . "' LIMIT 0,1";
$result = suQuery($sql);
//Get number of rows in the table and assign it to $numRows variable
$numRows = $result['num_rows'];
//If number of rows is zero, exit the code.
if ($numRows == 0) {
    suExit(INVALID_RECORD);
}
$result['result'] = suUnstrip($result['result']);

$row = $result['result'][0];
$id = $row['id'];
$title = 'Sort ' . $row['title'];
$showSortingModule = $row['show_sorting_module'];

$structure = $result['result'][0]['structure'];

$sortFieldToShow = $structure[0]['Slug'];

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
            var saveOnCtrlS = true;
            //Sort
            $(function () {
                $("#sortable").sortable();
                $("#sortable").disableSelection();
            });
        </script>

    </head>
    <body>

        <p id="loading-area"></p>
        <div class="container-fluid" id="container-area">
            <div class="row">
                <main>
                    <div class="col-sm-10 content-area" id="working-area">
                        <!-- Add new -->
                        <?php if ($_GET['overlay'] != 1) { ?>
                            <?php if ($showManageIcon == TRUE) { ?>
                                <a href="<?php echo ADMIN_URL; ?>manage<?php echo PHP_EXTENSION; ?>/<?php echo $table; ?>/" class="btn btn-circle"><i class="fa fa-table"></i></a>
                            <?php } ?>
                            <?php if ($addAccess == TRUE) { ?>
                                <a href="<?php echo ADMIN_URL; ?>add<?php echo PHP_EXTENSION; ?>/<?php echo $table; ?>/" class="btn btn-circle2"><i class="fa fa-plus"></i></a>
                            <?php } ?>
                        <?php } ?>

                        <?php
                        include('includes/header.php');
                        ?>
                        <?php
//Any actions desired at this point should be coded in this file
                        if (file_exists('includes/custom/sort-c.php')) {
                            include('includes/custom/sort-c.php');
                        }
                        ?>


                        <!-- Error area -->
                        <div id="error-area">
                            <ul></ul>
                        </div>
                        <div id="message-area">
                            <p></p>
                        </div>
                        <!-- Sort Area -->
                        <p>&nbsp;</p>
                        <form name="suForm" id="suForm" method="post" target="remote" action="<?php echo ADMIN_URL; ?>remote<?php echo PHP_EXTENSION; ?>/sort/<?php echo suUnTablify($table); ?>/" data-parsley-validate>                            <ul id="sortable">
                                <?php
                                $sortFieldToShow2 = suJsonExtract('data', $sortFieldToShow);
                                $sortOrderField = suJsonExtract('data', 'sortOrder', FALSE);
                                $sortOrderField3 = suJsonExtract('data', $sortFieldToShow, FALSE);

                                $sql = "SELECT id," . $sortFieldToShow2 . " FROM " . suUnTablify($table) . " WHERE live='Yes' ORDER BY " . $sortOrderField . ',' . $sortOrderField3;

                                $result = suQuery($sql);
                                $numRows = $result['num_rows'];
                                $result['result'] = suUnstrip($result['result']);

                                if ($numRows > 0) {
                                    $row = $result['result'];
                                    foreach ($row as $value) {
                                        ?>
                                        <li class="ui-state-default"><i class="fa fa-th color-lightSlateGray"></i> <?php echo $value['name']; ?><input type="hidden" name="sortOrder[]" value="<?php echo $value['id']; ?>"/></li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                            <p class="pull-right">
                                <button title="<?php echo SUBMIT; ?>" type="submit" id="Submit" name="Submit" class="btn btn-theme"><i class="fa fa-check"></i></button>
                            </p>
                            <div class="clearfix"></div>

                        </form>

                        <p>&nbsp;</p>
                        <div class="clearfix"></div>
                        <div class="table-responsive">


                        </div>


                        <div class="clearfix"></div>
                        <div id="post-table-placeholder"></div>
                    </div>
                    <?php
                    //Any actions desired at this point should be coded in this file
                    if (file_exists('includes/custom/sort-d.php')) {
                        include('includes/custom/sort-d.php');
                    }
                    ?>
                </main>
                <?php include('includes/sidebar.php'); ?>
            </div>
            <?php include('includes/footer.php'); ?>
        </div>
        <?php include('includes/footer-js.php'); ?>
        <?php suIframe(); ?>
    </body>
</html>