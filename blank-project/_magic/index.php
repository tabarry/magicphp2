<?php
include('../sulata/includes/config.php');
include('../sulata/includes/language.php');
include('../sulata/includes/functions.php');
include('../sulata/includes/get-settings.php');

//Check magic login.
//If user is not logged in, send to login page.
checkMagicLogin();

//Get tables from database.
//Make select statement without the WHERE condition.
//Where will be built at a later stage, down the page.
//The $SqlFrom is also used in $sqlP below.
$sqlSelect = "SELECT id,title,slug,display,save_for_later  ";
$sqlFrom = " FROM " . STRUCTURE_TABLE_NAME . " WHERE live='Yes'";
$sql = $sqlSelect . $sqlFrom;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo MAGIC_TITLE . ' ' . MAGIC_VERSION; ?></title>
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
        </script>

    </head>
    <body>

        <div class="container">
            <div class="row">
                <main>
                    <div class="col-sm-12 content-area">
                        <!-- Add new -->
                        <a href="<?php echo MAGIC_URL; ?>add<?php echo PHP_EXTENSION; ?>/" class="btn btn-circle"><i class="fa fa-plus"></i></a>
                        <?php
                        $h1 = 'Formsets';
//Include header
                        include('includes/header.php');
                        ?>

                        <?php
                        //Instanciate variables
                        $where = '';
                        //If search field is not empty, then build a where condition.
                        //The 'q' parameter is the search parameter.
                        if ($_GET['q'] != '') {
                            $where .= " AND (title LIKE '%" . suStrip($_GET['q']) . "%' OR slug LIKE '%" . suStrip($_GET['q']) . "%') ";
                        }
                        //If query string does not contain 'start' parameter, set 'start' to 0.
                        //This 'start' parameter is the start point of LIMIT in query.
                        if (!$_GET['start']) {
                            $_GET['start'] = 0;
                        }
                        //If query string does not contain 'sr' parameter, set 'sr' to 0.
                        //This 'sr' parameter is the serial number display with records.
                        if (!$_GET['sr']) {
                            $sr = 0;
                        } else {
                            $sr = $_GET['sr'];
                        }
                        //If query string does not contain 'sort' parameter, make default order by to 'title' field.
                        if (!$_GET['sort']) {
                            $sort = " ORDER BY title";
                        } else {
                            //If 'sort' parameter is container in query string, use it.
                            //$_GET['f'] is the field to set and $_GET['sort'] is the sort order, ascending or descending.
                            $sort = " ORDER BY " . $_GET['f'] . " " . $_GET['sort'];
                        }

                        //Get records from database.
                        //$sql was built at the start of the page.
                        //WHERE condition, sorting and pagination added to query at this point.
                        $sql = "$sql $where $sort LIMIT " . $_GET['start'] . "," . $getSettings['page_size'];
                        $result = suQuery($sql);
                        //Get number of rows fetched and store in the $numRows variable.
                        $numRows = $result['num_rows'];
                        //If number of rows is greater than 0, show the records.
                        if ($numRows > 0) {
                            ?>
                            <!-- Search area -->
                            <form class="form-horizontal" name="searchForm" id="searchForm" method="get" action="">
                                <div class="row">

                                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                        <?php
                                        //If search parameter 'q' is set, assign the value to $q variable.
                                        if (isset($_GET['q'])) {
                                            $q = $_GET['q'];
                                        } else {
                                            //If search parameter 'q' is not set, assign an empty value to $q variable.
                                            $q = '';
                                        }
                                        //Make an array called $arg and then pass it on to suInput() function to create a textbox.
                                        $arg = array('type' => 'search', 'name' => 'q', 'id' => 'q', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Search by Title or Slug', 'value' => $q);
                                        echo suInput('input', $arg);
                                        ?>

                                    </div>
                                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">

                                        <?php
                                        //Make an array called $arg and then pass it on to suInput() function to create a submit button.
                                        $arg = array('type' => 'submit', 'name' => 'Submit', 'id' => 'Submit', 'class' => 'btn btn-theme');
                                        echo suInput('button', $arg, "<i class='fa fa-search'></i>", TRUE);
                                        ?>
                                        <?php if ($_GET['q']) { //If search parameter 'q' is set, display the 'Clear Search' link. ?>
                                            &nbsp;<a href="<?php echo MAGIC_URL; ?>index<?php echo PHP_EXTENSION; ?>/">Clear search.</a>
                                        <?php } ?>
                                    </div>

                                </div>
                            </form>
                            <?php
                            //Build the sorting dropdown from $fieldsArray to be passed on to suSort() function.
                            $fieldsArray = array('title', 'slug', 'display');
                            suSort($fieldsArray);
                            ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover tablex">
                                    <thead>
                                        <tr>
                                            <th style="width:5%"><?php echo SERIAL; ?></th>
                                            <th style="width:35%">Title</th>
                                            <th style="width:25%">Slug</th>
                                            <th style="width:20%">Display</th>
                                            <th style="width:10%">&nbsp;</th>
                                            <th style="width:5%"><i class="fa fa-trash"></i><sup><i class="fa fa-trash"></i></sup></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($result['result'] as $row) {
                                            $uid = RESERVED_TABLE_PREFEX . uniqid() . '_';
                                            ?>
                                            <tr id="row_<?php echo $row['id']; ?>">
                                                <td><?php echo $sr = $sr + 1; ?>.</td>
                                                <td><?php echo suUnstrip($row['title']); ?>
                                                <?php if (in_array($row['slug'], $reservedTables)) { ?>
                                                    <sup class="color-Crimson">Reserved Table</sup>
                                                <?php } ?>
                                                </td>
                                                <td><?php echo suUnstrip($row['slug']); ?></td>
                                                <td><?php echo $row['display']; ?></td>
                                                <td>
                                                    <!-- Edit Icon -->
                                                    <a title="<?php echo EDIT; ?>" id="edit_icon_<?php echo $row['id']; ?>" href="<?php echo MAGIC_URL; ?>update<?php echo PHP_EXTENSION; ?>/<?php echo $row['id']; ?>/"><i class="fa fa-edit"></i></a>
                                                    <!-- Duplicate Icon -->
                                                    <a title="<?php echo DUPLICATE; ?>" id="duplicate_icon_<?php echo $row['id']; ?>" href="<?php echo MAGIC_URL; ?>update<?php echo PHP_EXTENSION; ?>/<?php echo $row['id']; ?>/duplicate/"><i class="fa fa-copy"></i></a>
                                                    <?php if (!in_array($row['slug'], $reservedTables)) { ?>
                                                        <!-- Delete Icon -->
                                                        <a title="<?php echo DELETE; ?>" id="del_icon_<?php echo $row['id']; ?>" onclick="return delById('<?php echo $row['id']; ?>', '<?php echo CONFIRM_DELETE_RESTORE; ?>')" href="<?php echo MAGIC_URL; ?>remote<?php echo PHP_EXTENSION; ?>/delete/<?php echo $row['id']; ?>/<?php echo $uid; ?>/<?php echo suUnstrip($row['slug']); ?>/" target="remote"><i class="fa fa-trash"></i></a>
                                                        <!-- Restore Icon -->
                                                        <a title="<?php echo RESTORE; ?>" id="restore_icon_<?php echo $row['id']; ?>" href="<?php echo MAGIC_URL; ?>remote<?php echo PHP_EXTENSION; ?>/restore/<?php echo $row['id']; ?>/<?php echo $uid; ?>/<?php echo suUnstrip($row['slug']); ?>/" target="remote" style="display:none"><i class="fa fa-undo"></i></a>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if (!in_array($row['slug'], $reservedTables)) { ?>
                                                        <div class="pretty p-switch size-110" id="pretty_check_<?php echo $row['id']; ?>">


                                                            <?php
                                                            $delUrl = MAGIC_URL . 'remote' . PHP_EXTENSION . '/delete/' . $row['id'] . '/' . $uid . '/' . $uid . '/' . suUnstrip($row['slug']) . '/';
                                                            $restoreUrl = MAGIC_URL . 'remote' . PHP_EXTENSION . '/restore/' . $row['id'] . '/' . $uid . '/' . suUnstrip($row['slug']) . '/';
                                                            $arg = array('type' => 'checkbox', 'name' => 'delchk', 'id' => 'delchk_' . $row['id'], 'value' => $row['id'], 'onclick' => "delByIdCheckbox('" . $row['id'] . "','" . $delUrl . "','" . $restoreUrl . "')");
                                                            echo suInput('input', $arg);
                                                            ?>
                                                            <div class="state p-warning">
                                                                <label></label>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                        <?php
                        //Build query for pagination.
                        $sqlP = "SELECT COUNT(id) AS totalRecs $sqlFrom $where";
                        //Pass the $sqlP query to suPaginate() pagination function.
                        suPaginate($sqlP);
                        ?>
                    </div>
                </main>
            </div>
            <?php include('includes/footer.php'); ?>
        </div>
        <?php include('includes/footer-js.php'); ?>
    </body>
</html>
<?php suIframe(); ?>