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

//fetch the table name which comes in segment 1.
$table = suSegment(1);
//Stop unauthorised manage access
$addAccess = suCheckAccess(suUnTablify($table), 'addables');
$viewAccess = suCheckAccess(suUnTablify($table), 'viewables');
$previewAccess = suCheckAccess(suUnTablify($table), 'previewables');
$editAccess = suCheckAccess(suUnTablify($table), 'updateables');
$deleteAccess = suCheckAccess(suUnTablify($table), 'deleteables');
$duplicateAccess = suCheckAccess(suUnTablify($table), 'duplicateables');
$downloadAccessCSV = suCheckAccess(suUnTablify($table), 'csv_downloadables');
$downloadAccessPDF = suCheckAccess(suUnTablify($table), 'pdf_downloadables');

if (!in_array(ADMIN_GROUP_NAME, $_SESSION[SESSION_PREFIX . 'user_group'])) {
    //Check IP restriction
    suCheckIpAccess();
    //Stop unauthorised access
    if ($viewAccess == FALSE) {
        suExit(INVALID_ACCESS);
    }
}
//==
//Any actions desired at this point should be coded in this file
if (file_exists('includes/custom/manage-a.php')) {
    include('includes/custom/manage-a.php');
}
//If table name is not available, exit the code.
if (!isset($table)) {
    suExit(INVALID_RECORD);
}

//Get title and structure for the form from structure table
$sql = "SELECT id,title,show_form_on_manage, show_sorting_module ,structure,extrasql_on_view,save_for_later FROM " . STRUCTURE_TABLE_NAME . " WHERE live='Yes' AND slug='" . suUnTablify($table) . "' LIMIT 0,1";
$result = suQuery($sql);
//Get number of rows in the table and assign it to $numRows variable
$numRows = $result['num_rows'];
//If number of rows is zero, exit the code.
if ($numRows == 0) {
    suExit(INVALID_RECORD);
}
$row = $result['result'][0];

$id = suUnstrip($row['id']);
$title = suUnstrip($row['title']);
$saveForLater = suUnstrip($row['save_for_later']);
$showFormOnManage = suUnstrip($row['show_form_on_manage']);
$showSortingModule = suUnstrip($row['show_sorting_module']);
if($showSortingModule=='Yes') {
    $sortAccess = suCheckAccess(suUnTablify($table), 'sortables');
}
$extrasqlOnView = html_entity_decode(suUnstrip($row['extrasql_on_view']));
//Eval the string if $ in string
if (stristr($extrasqlOnView, '$')) {
    eval("\$extrasqlOnView = \"$extrasqlOnView\";");
}


$result = $result['result'][0]['structure'];
$structure = json_decode($result, 1);
//Instantiate variables
$fields = array(); //Fields to display
$searchBy = array(); //Fields to search by
$orderBy = array(); //Fields to order by
$f = '';
$sb = '';

$dates = array(); //Array to hold date fields
$pictures = array(); //Array to hold pictures
$attachments = array(); //Array to hold attachments
$source = array(); //Array to hold source
$miniStructure = array(); //Array to hold mini structure
$compositeUnique = array(); //Array to hold composite uniques
//Loop through the structure array to build fields to display in query
for ($i = 0; $i <= sizeof($structure) - 1; $i++) {
    if ($structure[$i]['Show'] == 'yes') {

        //Check data type as date
        if ($structure[$i]['Type'] == 'date') {
            //Push the field name in date array
            array_push($dates, $structure[$i]['Slug']);
            //Apply date format and build an extra aliased field to display date.
            //One is used for display and other for soring.
            $f .= " DATE_FORMAT(" . suJsonExtract('data', $structure[$i]['Slug'], FALSE) . ",'%d-%b-%Y') AS " . $structure[$i]['Slug'] . '2' . ',';
            $f .= " DATE_FORMAT(" . suJsonExtract('data', $structure[$i]['Slug'], FALSE) . ",'%d-%b-%Y <sup class=\"color-dimGray\">%h:%i %p</sup>') AS " . $structure[$i]['Slug'] . '3' . ',';
            $f .= suJsonExtract('data', $structure[$i]['Slug']) . ',';
            //If field type is attachment
        } elseif ($structure[$i]['Type'] == 'attachment_field') {
            //Push the field name in $attachments array
            array_push($attachments, $structure[$i]['Slug']);
            $f .= suJsonExtract('data', $structure[$i]['Slug']) . ',';
            //If field type is picture
        } elseif ($structure[$i]['Type'] == 'picture_field') {
            //Push the field name in $pictures array
            array_push($pictures, $structure[$i]['Slug']);
            $f .= suJsonExtract('data', $structure[$i]['Slug']) . ',';
        } else {
            $f .= suJsonExtract('data', $structure[$i]['Slug']) . ',';
        }
        array_push($fields, $structure[$i]['Name']);
    }
    //If the SearchBy structure is set to yes
    if ($structure[$i]['SearchBy'] == 'yes') {
        //Push the field name in searchBy array
        array_push($searchBy, suUnstrip($structure[$i]['Name']));
        $sb .= $structure[$i]['Name'] . ' OR ';
    }
    //If the OrderBy structure is set to yes
    if ($structure[$i]['OrderBy'] == 'yes') {
        //Push the field name in searchBy array
        array_push($orderBy, $structure[$i]['Name']);
    }
    //If the Source is empty, hold it to make it inline editable
    if ($structure[$i]['Source'] == '' && $structure[$i]['Type'] == 'textbox') {
        //Push the field name in searchBy array
        array_push($source, suSlugifyStr($structure[$i]['Name'], '_'));
    }
    //If the field is composite unique field
    if ($structure[$i]['CompositeUnique'] == 'yes') {
        //Push the field name in compositeUnique array
        array_push($compositeUnique, suSlugifyStr($structure[$i]['Name'], '_'));
    }
    //Rebuild mini structure
    $miniStructure[suSlugifyStr($structure[$i]['Name'], '_')] = array('name' => $structure[$i]['Name'], 'type' => $structure[$i]['Type'], 'length' => $structure[$i]['Length'], 'onchange' => suUnstrip($structure[$i]['OnChange']), 'onclick' => suUnstrip($structure[$i]['OnClick']), 'onkeyup' => suUnstrip($structure[$i]['OnKeyUp']), 'onkeypress' => suUnstrip($structure[$i]['OnKeyPress']), 'onblur' => suUnstrip($structure[$i]['OnBlur']));
}

//Substring to remove the last characters
$f = substr($f, 0, -1);
$sb = substr($sb, 0, -4);

//Make Save for Later option
if ($saveForLater == 'Yes') {
    $saveForLaterSql = "," . suJsonExtract('data', 'save_for_later_use');
} else {
    $saveForLaterSql = "";
}
//Make select statement. The $SqlFrom is also used in $sqlP below.
$sqlSelect = "SELECT id,$f $saveForLaterSql "; //$f is the fields built above for sql
$sqlFrom = " FROM " . suUnTablify($table) . " WHERE live='Yes' $extrasqlOnView ";

//Any actions desired at this point should be coded in this file
if (file_exists('includes/custom/manage-b.php')) {
    include('includes/custom/manage-b.php');
}

$sql = $sqlSelect . $sqlFrom;




$h1 = $title;

//Download CSV
if (suSegment(2) == 'stream-csv' && $downloadAccessCSV == TRUE) {
    $receivedSql = suDecrypt($_GET['s']);
    suSqlToCSV($receivedSql, $fields, suUnTablify($table));
    exit;
}
//Download PDF
if (suSegment(2) == 'stream-pdf' && $downloadAccessPDF == TRUE) {
    if ($getSettings['pdf_format'] == 'table') {
        $receivedSql = suDecrypt($_GET['s']);
        suSqlToPDF($receivedSql, $fields, suUnTablify($table), $dates);
    } else {
        $receivedSql = suDecrypt($_GET['s']);
        suSqlToPDF2($receivedSql, $fields, suUnTablify($table), $dates);
    }
    exit;
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
                        <?php if ($addAccess == TRUE) { ?>
                            <a href="<?php echo ADMIN_URL; ?>add<?php echo PHP_EXTENSION; ?>/<?php echo $table; ?>/" class="btn btn-circle"><i class="fa fa-plus"></i></a>
                        <?php } ?>
                        <?php if ($sortAccess == TRUE) { ?>
                            <a href="<?php echo ADMIN_URL; ?>sort<?php echo PHP_EXTENSION; ?>/<?php echo $table; ?>/" class="btn btn-circle2"><i class="fa fa-sort-alpha-asc"></i></a>
                        <?php } ?>
                        <?php
                        include('includes/header.php');
                        ?>
                        <?php
//Any actions desired at this point should be coded in this file
                        if (file_exists('includes/custom/manage-c.php')) {
                            include('includes/custom/manage-c.php');
                        }
                        ?>
                        <?php
                        //If the querystring contains the search parameter, build the WHERE condition for sql query
                        if ($_GET['q'] != '') {
                            //Build the $where variable
                            //The search string and searched data is to be converted to lower case to return match
                            $where .= " AND lcase(" . suJsonExtract('data', suSlugifyStr($_GET['s'], '_'), FALSE) . ") LIKE '%" . suStrip(strtolower($_GET['q'])) . "%' ";
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
                        //If query string does not contain 'sort' parameter, make default order by to 'id' field.
                        if (!$_GET['sort']) {
                            if (sizeof($orderBy) == 0) {
                                $sort = " ORDER BY id ";
                            } else {
                                $sort = " ORDER BY " . suSlugifyStr($orderBy[0], '_') . " ";
                            }
                        } else {
                            $sort = " ORDER BY " . suSlugifyStr($_GET['f'], '_') . " " . $_GET['sort'];
                        }
                        //Get records from database.
                        //$sql was built at the start of the page.
                        //WHERE condition, sorting and pagination added to query at this point.
                        $sql = "$sql $where $sort LIMIT " . $_GET['start'] . "," . $getSettings['page_size'];
                        $result = suQuery($sql);
                        $numRows = $result['num_rows'];

                        if ($numRows > 0) {
                            ?>

                            <!-- Error area -->
                            <div id="error-area">
                                <ul></ul>
                            </div>
                            <div id="message-area">
                                <p></p>
                            </div>
                            <!-- Add Form Area -->
                            <?php if ($showFormOnManage == 'Yes') { ?>
                                <iframe name="add-on-manage" id="add-on-manage" width="100%" frameborder="0" src="<?php echo ADMIN_URL; ?>add<?php echo PHP_EXTENSION; ?>/<?php echo $table; ?>/?overlay=1&reload=yes" scrolling="no" onload="doResizeIframe(this)"></iframe>

                                <h4>Search <?php echo $title; ?></h4>
                            <?php } ?>
                            <!-- Search area -->

                            <!-- If search fields are greater than 1 than show the click search field-->
                            <?php if (sizeof($searchBy) > 1) { ?>
                                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4" id="search-click">
                                    <div class="row">
                                        <?php
                                        $arg = array('type' => 'search', 'name' => 'searchClick', 'id' => 'searchClick', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Search..', 'title' => 'Search..', 'onclick' => 'toggleSearch(\'show\');');
                                        echo suInput('input', $arg);
                                        ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <div id="search-area">
                                <?php
                                for ($i = 0; $i <= sizeof($searchBy) - 1; $i++) {
                                    $searchField = suSlugifyStr($searchBy[$i], '_');
                                    ?>

                                    <form class="form-horizontal" name="searchForm" id="searchForm-<?php echo $searchField; ?>" method="get" action="">
                                        <div class="row">

                                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                                <?php
                                                if (isset($_GET['q'])) {
                                                    $q = $_GET['q'];
                                                } else {
                                                    $q = '';
                                                }

                                                $arg = array('type' => 'search', 'name' => 'q', 'id' => 'q_' . $searchField, 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Search by ' . suUnstrip($searchBy[$i]), 'title' => 'Search by ' . $searchBy[$i]);
                                                if ($_GET['s'] != '' && $_GET['s'] == $searchBy[$i]) {
                                                    $arg = array_merge($arg, array('value' => $_GET['q']));
                                                }
                                                echo suInput('input', $arg);
                                                ?>
                                                <?php if ($_GET['s'] != '' && $_GET['s'] == $searchBy[$i]) {
                                                    ?>
                                                    <div><i class="fa fa-angle-up color-gray"></i> <small class='color-gray'>Search by <?php echo suUnstrip($searchBy[$i]); ?> <i class="fa fa-angle-up color-gray"></i> <a href="<?php echo ADMIN_URL; ?>manage<?php echo PHP_EXTENSION; ?>/<?php echo $table; ?>/">Clear search.</a></small></div>

                                                <?php } ?>
                                            </div>
                                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">

                                                <?php
                                                //Search field
                                                $arg = array('type' => 'hidden', 'name' => 's', 'id' => 's', 'value' => $searchBy[$i]);
                                                echo suInput('input', $arg);

                                                //Button
                                                $arg = array('type' => 'submit', 'name' => 'Submit', 'id' => 'Submit', 'class' => 'btn btn-theme');
                                                echo suInput('button', $arg, "<i class='fa fa-search'></i>", TRUE);
                                                ?>

                                            </div>

                                        </div>
                                    </form>
                                    <script>
                                        //Autocomplete code
                                        jQuery(document).ready(function () {
                                            $('#q_<?php echo $searchField; ?>').autocomplete(
                                                    {source: '<?php echo ADMIN_URL; ?>remote.php?do=autocomplete&source=<?php echo urlencode($table); ?>.<?php echo urlencode($searchField); ?>', minLength: 2}
                                                            );
                                                        });
                                    </script>
                                <?php } ?>
                                <!-- If search fields are greater than 1 -->
                                <?php if (sizeof($searchBy) > 1) { ?>
                                    <div><a href="javascript:;" onclick="toggleSearch('hide');"><i class="fa fa-angle-up"></i><i class="fa fa-search"></i><i class="fa fa-angle-up"></i></a></div>
                                <?php } else { ?>
                                    <script>
                                        toggleSearch('show');
                                    </script>
                                <?php } ?>
                            </div>

                            <!-- Clear Search -->
                            <?php if (isset($_GET['q']) && $_GET['q'] != '') {
                                ?>
                                <div class="pull-right hidden"><small class='color-gray'><a href="<?php echo ADMIN_URL; ?>manage<?php echo PHP_EXTENSION; ?>/<?php echo $table; ?>/"><i class="fa fa-close"></i> Clear search.</a></small></div>
                                <script>
                                    toggleSearch('show');
                                </script>
                            <?php } ?>
                            <div class="clearfix"></div>
                            <!-- Sort Area -->
                            <div id="sort-area">
                                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4" id="search-click">
                                    <div class="row">
                                        <?php
                                        //The sortable fields array
                                        $fieldsArray = $orderBy;

                                        if ($saveForLater == 'Yes') {
                                            $saveForLaterSort = array('Save for Later Use');
                                            $fieldsArray = array_merge($fieldsArray, $saveForLaterSort);
                                        }
                                        suSort($fieldsArray);
                                        ?>
                                    </div>
                                </div>

                            </div>
                            <div class="clearfix"></div>
                            <div class="table-responsive">

                                <table class="table table-striped table-hover tablex" id="records-table">
                                    <thead>
                                        <tr>
                                            <th style="width:5%"><?php echo SERIAL; ?></th>
                                            <?php
                                            $th = '';
                                            //Set equal width of each column
                                            if ($getSettings['multi_delete'] == 1) {//If multi delete allowed
                                                $toBeRounded = 75;
                                            } else {
                                                $toBeRounded = 70;
                                            }
                                            $tdWidth = round($toBeRounded / sizeof($fields));
                                            $thAlignClass = '';
                                            //Build table field headers
                                            for ($i = 0; $i <= sizeof($fields) - 1; $i++) {
                                                $tdType = $miniStructure[suSlugifyStr($fields[$i], '_')]['type'];
                                                if ($tdType == 'integer' || $tdType == 'decimal' || $tdType == 'currency') {
                                                    $thAlignClass = ' class="number-right" ';
                                                }
                                                $th .= '<th ' . $thAlignClass . ' style="width:' . $tdWidth . '%">' . suUnstrip($fields[$i]) . $thSpace . '</th>';
                                            }
                                            //Any actions desired at this point should be coded in this file
                                            if (file_exists('includes/custom/manage-d.php')) {
                                                include('includes/custom/manage-d.php');
                                            }
                                            echo $th;
                                            ?>
                                            <!-- delete -->
                                            <th style="width:10%">&nbsp;</th>
                                            <?php if ($getSettings['multi_delete'] == 1 && $deleteAccess == TRUE) {//If multi delete allowed     ?>
                                                <th style="width:5%">
                                                    <div class="pretty p-switch size-110" id="pretty_check_bulk">


                                                        <?php
                                                        $arg = array('type' => 'checkbox', 'name' => 'delAll', 'id' => 'delAll', 'value' => '1', 'onclick' => 'doDelAll()', 'title' => DELETE_ALL);
                                                        echo suInput('input', $arg);
                                                        ?>
                                                        <div class="state p-warning">

                                                            <label><i class="fa fa-trash"></i></label>
                                                        </div>
                                                    </div>

                                                <?php } ?>
                                                <!-- save for later -->
                                                <?php if ($saveForLater == 'Yes') { ?>
                                                <th style="width:5%">&nbsp;</th>
                                            <?php } ?>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($result['result'] as $row) {
                                            $uid = RESERVED_TABLE_PREFEX . uniqid() . '_';
                                            ?>
                                            <tr id="row_<?php echo $row['id']; ?>">
                                                <td><span class="badge"><?php echo $sr = $sr + 1; ?></span></td>

                                                <?php
                                                $td = '';
                                                $align = '';
                                                //Build td to display data

                                                for ($i = 0; $i <= sizeof($fields) - 1; $i++) {
//Any actions desired at this point should be coded in this file
                                                    if (file_exists('includes/custom/manage-e.php')) {
                                                        include('includes/custom/manage-e.php');
                                                    }
                                                    $hiddenField = '';
                                                    $fld = suSlugifyStr($fields[$i], '_');


                                                    //If it is a date field, display the aliased field that shows date iin English
                                                    if (in_array($fld, $dates)) {
                                                        if (strstr($row[$fld], ' ')) {//If also has time
                                                            $td .= '<td>' . $row[$fld . '3'] . '</td>'; //Print with time
                                                        } else {
                                                            $td .= '<td>' . $row[$fld . '2'] . '</td>'; //Print without time
                                                        }
                                                        //Any actions desired at this point should be coded in this file
                                                        if (file_exists('includes/custom/manage-f.php')) {
                                                            include('includes/custom/manage-f.php');
                                                        }
                                                        //If it is an attachment field, make hyperlink
                                                    } elseif ((in_array($fld, $attachments)) && (file_exists(ADMIN_UPLOAD_PATH . suUnstrip($row[$fld])))) {

                                                        $td .= '<td><a target="_blank" href="' . UPLOAD_URL . suUnstrip($row[$fld]) . '">' . suUnMakeUploadPath(suUnstrip($row[$fld])) . '</a></td>';
                                                        //Any actions desired at this point should be coded in this file
                                                        if (file_exists('includes/custom/manage-g.php')) {
                                                            include('includes/custom/manage-g.php');
                                                        }
                                                        //If it is a picture field, display picture
                                                    } elseif ((in_array($fld, $pictures)) && (suUnstrip($row[$fld]) != '') && (file_exists(ADMIN_UPLOAD_PATH . suUnstrip($row[$fld])))) {
                                                        $path = base64_encode(UPLOAD_URL . suUnstrip($row[$fld]));
                                                        $td .= '<td><a id="photo_' . $i . '" href="javascript:;" class="imgThumb" style="background:url(' . UPLOAD_URL . suUnstrip($row[$fld]) . ')" data-toggle="modal" data-target="#myModal" onclick="window.overlayFrame.location.href = \'' . ADMIN_URL . 'view-image.php?t=' . time() . '&path=' . $path . '\';"></a></td>';
                                                        //Any actions desired at this point should be coded in this file
                                                        if (file_exists('includes/custom/manage-h.php')) {
                                                            include('includes/custom/manage-h.php');
                                                        }
                                                    } else {
                                                        //If the column data is an array, show data as bullet points
                                                        if (is_array(json_decode($row[$fld]))) {
                                                            $row[$fld] = json_decode($row[$fld]);
                                                            //Loop through the array to make bullet points
                                                            $o = '';
                                                            foreach ($row[$fld] as $value) {
                                                                $o .= " <i class='fa fa-check'></i> " . suUnstrip($value);
                                                            }
                                                            $row[$fld] = $o;
                                                            //Any actions desired at this point should be coded in this file
                                                            if (file_exists('includes/custom/manage-i.php')) {
                                                                include('includes/custom/manage-i.php');
                                                            }
                                                        } else {
                                                            //If the column data is not empty, print the data as it is
                                                            if ($row[$fld] != 'null' && $row[$fld] != '') {
                                                                $row[$fld] = suUnstrip($row[$fld]);
                                                                //Get onX events


                                                                $onchange = $miniStructure[$fld]['onchange'];
                                                                $onclick = $miniStructure[$fld]['onclick'];
                                                                $onkeyup = $miniStructure[$fld]['onkeyup'];
                                                                $onkeypress = $miniStructure[$fld]['onkeypress'];
                                                                $onblur = $miniStructure[$fld]['onblur'];
                                                                //==
                                                                $hiddenField = '<input autocomplete="off" maxlength="' . $miniStructure[$fld]['length'] . '" required="required" class="form-control" type="hidden" name="' . $fld . '" id="' . INLINE_EDIT_HIDDEN_FIELD_PREFIX . $fld . '_' . $row['id'] . '" value="' . $row[$fld] . '" onblur="doInlineEdit(\'hide\',\'' . ADMIN_URL . '\',\'' . INLINE_EDIT_HIDDEN_FIELD_PREFIX . $fld . '_' . $row['id'] . '\',\'' . INLINE_EDIT_HIDDEN_SPAN_PREFIX . $fld . '_' . $row['id'] . '\',\'' . suUnTablify($table) . '\',\'' . $fld . '\',\'' . $row['id'] . '\');' . $onblur . '" onkeypress="return doEnter(event,this);' . $onkeypress . '" onchange="' . $onchange . '" onclick="' . $onclick . '" onkeyup="' . $onkeyup . '" >';
                                                                //Any actions desired at this point should be coded in this file
                                                                if (file_exists('includes/custom/manage-j.php')) {
                                                                    include('includes/custom/manage-j.php');
                                                                }
                                                            } else {
                                                                //If the column data is empty, do not print anything

                                                                $row[$fld] = '';
                                                                //Any actions desired at this point should be coded in this file
                                                                if (file_exists('includes/custom/manage-k.php')) {
                                                                    include('includes/custom/manage-k.php');
                                                                }
                                                            }
                                                        }
                                                        //If the column data is not empty, print the data as it is
                                                        if ($hiddenField == '') {

                                                            $td .= '<td>' . $row[$fld] . '</td>';
                                                            //Any actions desired at this point should be coded in this file
                                                            if (file_exists('includes/custom/manage-l.php')) {
                                                                include('includes/custom/manage-l.php');
                                                            }
                                                        } else {
                                                            if (in_array($fld, $source) && (!in_array($fld, $compositeUnique))) {
                                                                if ($editAccess == TRUE && suUnTablify($table) != 'groups') {
                                                                    $td .= '<td><spanx title="' . DOUBLECLICK_TO_EDIT . '" class="dashed" id="' . INLINE_EDIT_HIDDEN_SPAN_PREFIX . $fld . '_' . $row['id'] . '" ondblclick="doInlineEdit(\'show\',\'' . ADMIN_URL . '\',\'' . INLINE_EDIT_HIDDEN_FIELD_PREFIX . $fld . '_' . $row['id'] . '\',\'' . INLINE_EDIT_HIDDEN_SPAN_PREFIX . $fld . '_' . $row['id'] . '\',\'' . $fld . '\',\'' . $row['id'] . '\')">' . $row[$fld] . '</spanx>' . $hiddenField . '</td>';
                                                                    //Any actions desired at this point should be coded in this file
                                                                    if (file_exists('includes/custom/manage-m.php')) {
                                                                        include('includes/custom/manage-m.php');
                                                                    }
                                                                } else {
                                                                    $td .= '<td>' . $row[$fld] . '</td>';
                                                                    //Any actions desired at this point should be coded in this file
                                                                    if (file_exists('includes/custom/manage-n.php')) {
                                                                        include('includes/custom/manage-n.php');
                                                                    }
                                                                }
                                                            } else {
                                                                if ($miniStructure[$fld]['type'] == 'email') {
                                                                    $row[$fld] = "<a href='mailto:" . $row[$fld] . "'>" . $row[$fld] . "</a>";
                                                                    //Any actions desired at this point should be coded in this file
                                                                    if (file_exists('includes/custom/manage-o.php')) {
                                                                        include('includes/custom/manage-o.php');
                                                                    }
                                                                } elseif ($miniStructure[$fld]['type'] == 'integer') {//If integer then apply number format
                                                                    $row[$fld] = number_format($row[$fld]);
                                                                    $align = " class='number-right'";
                                                                    $align = " class='number-right'";
                                                                    //Any actions desired at this point should be coded in this file
                                                                    if (file_exists('includes/custom/manage-p.php')) {
                                                                        include('includes/custom/manage-p.php');
                                                                    }
                                                                } elseif ($miniStructure[$fld]['type'] == 'decimal') {//If decimal then apply number format
                                                                    $row[$fld] = number_format($row[$fld], 2);
                                                                    //Any actions desired at this point should be coded in this file
                                                                    if (file_exists('includes/custom/manage-q.php')) {
                                                                        include('includes/custom/manage-q.php');
                                                                    }
                                                                } elseif ($miniStructure[$fld]['type'] == 'currency') {//If currency then apply number format
                                                                    $row[$fld] = '<sup>' . $getSettings['site_currency'] . '</sup> ' . number_format($row[$fld], 2);
                                                                    $align = " class='number-right'";
                                                                    //Any actions desired at this point should be coded in this file
                                                                    if (file_exists('includes/custom/manage-r.php')) {
                                                                        include('includes/custom/manage-r.php');
                                                                    }
                                                                } else {
                                                                    
                                                                }
                                                                $td .= '<td ' . $align . '>' . $j . $row[$fld] . '</td>';
                                                                //Any actions desired at this point should be coded in this file
                                                                if (file_exists('includes/custom/manage-s.php')) {
                                                                    include('includes/custom/manage-s.php');
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                echo $td;
                                                $redirect = '';
                                                //Build the query string to redirect page to
                                                if ($_SERVER['QUERY_STRING'] != '') {
                                                    $redirect = '?' . $_SERVER['QUERY_STRING'];
                                                    //Any actions desired at this point should be coded in this file
                                                    if (file_exists('includes/custom/manage-t.php')) {
                                                        include('includes/custom/manage-t.php');
                                                    }
                                                }
                                                ?>

                                                <td>

                                                    <!-- Edit -->
                                                    <?php if ($editAccess == TRUE) { ?>
                                                        <a title="<?php echo EDIT; ?>" id="edit_icon_<?php echo $row['id']; ?>" href="<?php echo ADMIN_URL; ?>update<?php echo PHP_EXTENSION; ?>/<?php echo $table; ?>/<?php echo $row['id']; ?>/<?php echo $redirect; ?>"><i class="fa fa-edit"></i></a>
                                                    <?php } ?>
                                                    <!-- Preview -->
                                                    <?php if ($previewAccess == TRUE) { ?>
                                                        <a title="<?php echo PREVIEW; ?>" id="preview_icon_<?php echo $row['id']; ?>" href="<?php echo ADMIN_URL; ?>preview<?php echo PHP_EXTENSION; ?>/<?php echo $table; ?>/<?php echo $row['id']; ?>/<?php echo $redirect; ?>"><i class="fa fa-eye"></i></a>
                                                    <?php } ?>
                                                    <!-- Duplicate -->
                                                    <?php if ($duplicateAccess == TRUE) { ?>
                                                        <a title="<?php echo DUPLICATE; ?>" id="duplicate_icon_<?php echo $row['id']; ?>" href="<?php echo ADMIN_URL; ?>update<?php echo PHP_EXTENSION; ?>/<?php echo $table; ?>/<?php echo $row['id']; ?>/duplicate/<?php echo $redirect; ?>"><i class="fa fa-copy"></i></a>
                                                    <?php } ?>
                                                    <?php ?>
                                                    <!-- Delete -->
                                                    <?php if ($deleteAccess == TRUE) { ?>
                                                        <?php if ($getSettings['multi_delete'] != 1) { ?>
                                                            <a title="<?php echo DELETE; ?>" id="del_icon_<?php echo $row['id']; ?>" onclick="return delById('<?php echo $row['id']; ?>', '<?php echo CONFIRM_DELETE_RESTORE; ?>')" href="<?php echo ADMIN_URL; ?>remote<?php echo PHP_EXTENSION; ?>/delete/<?php echo $row['id']; ?>/<?php echo $table; ?>/" target="remote"><i class="fa fa-trash"></i></a>


                                                            <a title="<?php echo RESTORE; ?>" id="restore_icon_<?php echo $row['id']; ?>" href="<?php echo ADMIN_URL; ?>remote<?php echo PHP_EXTENSION; ?>/restore/<?php echo $row['id']; ?>/<?php echo $table; ?>/" target="remote" style="display:none"><i class="fa fa-undo"></i></a>
                                                        <?php } ?>
                                                    <?php } ?>
                                                    <?php
                                                    //Any actions desired at this point should be coded in this file
                                                    if (file_exists('includes/custom/manage-u.php')) {
                                                        include('includes/custom/manage-u.php');
                                                    }
                                                    ?>
                                                </td>
                                                <!-- Multi-delete -->
                                                <?php if ($getSettings['multi_delete'] == 1 && $deleteAccess == TRUE) {//If multi delete allowed       ?>
                                                    <td>
                                                        <div class="pretty p-switch size-110" id="pretty_check_<?php echo $row['id']; ?>">


                                                            <?php
                                                            $delUrl = ADMIN_URL . 'remote' . PHP_EXTENSION . '/delete/' . $row['id'] . '/' . $table . '/';
                                                            $restoreUrl = ADMIN_URL . 'remote' . PHP_EXTENSION . '/restore/' . $row['id'] . '/' . $table . '/';
                                                            $arg = array('type' => 'checkbox', 'title' => DELETE, 'name' => 'delchk', 'id' => 'delchk_' . $row['id'], 'value' => $row['id'], 'onclick' => "delByIdCheckbox('" . $row['id'] . "','" . $delUrl . "','" . $restoreUrl . "')");
                                                            echo suInput('input', $arg);
                                                            ?>
                                                            <div class="state p-warning">
                                                                <label></label>
                                                            </div>
                                                        </div>
                                                        <?php
                                                        //Any actions desired at this point should be coded in this file
                                                        if (file_exists('includes/custom/manage-v.php')) {
                                                            include('includes/custom/manage-v.php');
                                                        }
                                                        ?>
                                                    </td>
                                                <?php } ?>
                                                <!-- Save for later -->
                                                <?php if ($saveForLater == 'Yes') { ?>
                                                    <td>
                                                        <?php
                                                        if ($row['save_for_later_use'] == 'Yes') {
                                                            echo '<span id="save_later_' . $row['id'] . '"><i class="fa fa-save color-gray"></i></span>';
                                                        } else {
                                                            echo '<span id="save_later_' . $row['id'] . '"><i class="fa fa-check  color-green"></i></span>';
                                                        }
                                                        ?>
                                                        <?php
                                                        //Any actions desired at this point should be coded in this file
                                                        if (file_exists('includes/custom/manage-w.php')) {
                                                            include('includes/custom/manage-w.php');
                                                        }
                                                        ?>
                                                    </td>
                                                <?php } ?>
                                            </tr>
                                            <?php
                                        }


                                        //Any actions desired at this point should be coded in this file
                                        if (file_exists('includes/custom/manage-x.php')) {
                                            include('includes/custom/manage-x.php');
                                        }
                                        ?>
                                    </tbody>
                                </table>


                            </div>
                        <?php } ?>
                        <!-- Pagination -->
                        <div id="pagination-area">
                            <?php
                            $sqlP = "SELECT COUNT(id) AS totalRecs $sqlFrom $where";
                            suPaginate($sqlP);
                            ?>
                        </div>
                        <!-- Download CSV and PDF files -->
                        <div id="download-area">
                            <p class="pull-right">
                                <?php if ($downloadAccessCSV == TRUE && $numRows > 0) { ?>
                                    <a title="<?php echo DOWNLOAD_CSV; ?>" target="remote" href="<?php echo ADMIN_URL; ?>manage<?php echo PHP_EXTENSION; ?>/<?php echo $table; ?>/stream-csv/?s=<?php echo suCrypt($sql); ?>" class="btn btn-theme"><i class="fa fa-file-excel-o"></i></a>

                                <?php } ?>

                                <?php if ($downloadAccessPDF == TRUE && $numRows > 0) { ?>
                                    <a title="<?php echo DOWNLOAD_PDF; ?>" target="remote" href="<?php echo ADMIN_URL; ?>manage<?php echo PHP_EXTENSION; ?>/<?php echo $table; ?>/stream-pdf/?s=<?php echo suCrypt($sql); ?>" class="btn btn-theme"><i class="fa fa-file-pdf-o"></i></a>
                                <?php } ?>
                            </p>
                        </div>
                        <div class="clearfix"></div>
                        <div id="post-table-placeholder"></div>
                    </div>
                    <?php
                    //Any actions desired at this point should be coded in this file
                    if (file_exists('includes/custom/manage-y.php')) {
                        include('includes/custom/manage-y.php');
                    }
                    ?>
                </main>
                <?php include('includes/sidebar.php'); ?>
            </div>
            <?php include('includes/footer.php'); ?>
        </div>
        <?php include('includes/footer-js.php'); ?>
    </body>
</html>
<?php suIframe(); ?>