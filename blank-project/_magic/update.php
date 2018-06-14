<?php
include('../sulata/includes/config.php');
include('../sulata/includes/language.php');
include('../sulata/includes/functions.php');
include('../sulata/includes/get-settings.php');

//Check magic login.
//If user is not logged in, send to login page.
checkMagicLogin();

$mode = 'update';
//Assign the value of segment 1 to $id.
$id = suSegment(1);
if (!is_numeric($id)) {
    //If $id is not a number, exit and display relevant message.
    suExit(INVALID_RECORD);
}
//Build SQL to fetch form structure and settings.
$sql = "SELECT id,title,label_add,label_update,slug,redirect_after_add,show_form_on_manage,show_sorting_module,comments,structure,display,save_for_later,extrasql_on_add,extrasql_on_update,extrasql_on_single_update,extrasql_on_delete,extrasql_on_restore,extrasql_on_view FROM " . STRUCTURE_TABLE_NAME . " WHERE live='Yes' AND id='" . $id . "' LIMIT 0,1 ";
$result = suQuery($sql);

$result['result'] = suUnstrip($result['result']);
$row = $result['result'][0];
//Get number of rows fetched and store in the $numRows variable.
$numRows = $result['num_rows'];
//If number of rows is equal to 0, exit and display relevant message.
if ($numRows == 0) {
    suExit(INVALID_RECORD);
}
//Segment 2 can either be 'update' or 'duplicate'.
//If segment 2 is 'duplicate'.
if (suSegment(2) == 'duplicate') {
    $do = 'add'; //Set action variable of this page to 'add'.
    $h1 = 'Duplicate Form'; //Set form heading
} else {
    $do = 'update'; //Set action variable of this page to 'update'.
    $h1 = 'Update Form'; //Set form heading
}
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
                //Rename elements
                doChangeEleName();


            });
        </script> 
        <!-- Sortable JS -->
        <script>
            //Function to sort the table rows using JQUI sortable plugin.
            $(function () {
                $("#sortable").sortable();
                $("#sortable").disableSelection();
            });
        </script>


    </head>
    <body>

        <div class="container">
            <div class="row">
                <main>
                    <div class="col-sm-12 content-area">
                        <!-- Add new -->
                        <a href="<?php echo MAGIC_URL; ?>index<?php echo PHP_EXTENSION; ?>/" class="btn btn-circle"><i class="fa fa-table"></i></a>
                        <?php
                        include('includes/header.php');
                        ?>

                        <!-- Source Row to clone -->
                        <div class="hide">
                            <li class="ui-state-default" id="sourceLi">
                                <?php include('includes/magic.php'); ?>
                            </li>
                        </div>
                        <!-- // -->
                        <div id="error-area">
                            <ul></ul>
                        </div>    
                        <div id="message-area">
                            <p></p>
                        </div>
                        <form action="<?php echo MAGIC_URL; ?>remote<?php echo PHP_EXTENSION; ?>/<?php echo $do; ?>/" accept-charset="utf-8" name="suForm" id="suForm" method="post" target="remote" >

                            <div class="row">
                                <!-- Title/Slug -->
                                <div class="form-group">
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                        <?php
                                        //Call the doSlugify() function to make a slug as the table name is typed.

                                        $js = "$('#slug').val(doSlugify(this.value, '_'))";
                                        //Make arguments to be passed on to suInput() to make a control.
                                        $arg = array('type' => 'text', 'name' => 'title', 'id' => 'title', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Title', 'required' => 'required', 'value' => suUnstrip($row['title']), 'onkeyup' => $js);
                                        if (in_array($row['slug'], $reservedTables)) {
                                            $argRo = array('readonly' => 'readonly');
                                            $arg = array_merge($arg, $argRo);
                                        }
                                        echo suInput('input', $arg);
                                        ?>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                        <?php
                                        $arg = array('type' => 'text', 'name' => 'slug', 'id' => 'slug', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Slug', 'required' => 'required', 'readonly' => 'readonly', 'value' => suUnstrip($row['slug']),);
                                        echo suInput('input', $arg);

                                        //Old slug. This is required if the user is changing the main formset name. If so is the case, old name will be retained in this field and deleted on submission.
                                        $arg = array('type' => 'hidden', 'name' => 'old_slug', 'id' => 'old_slug', 'value' => suUnstrip($row['slug']),);
                                        echo suInput('input', $arg);
                                        ?>
                                    </div>
                                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                        <?php
                                        //build the redirect_to_manage_after_add  field.
                                        //This field tells whether the page needs to redirect to manage or stay there after add.
                                        $options = $redirectAfterAddArray;
                                        $js = "class='form-control'";
                                        echo suDropdown('redirect_after_add', $options, suUnstrip($row['redirect_after_add']), $js);
                                        ?>
                                    </div>
                                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                        <?php
                                        //This field tells whether to show add on manage page.
                                        $options = $showFormOnManageArray;
                                        $js = "class='form-control'";
                                        echo suDropdown('show_form_on_manage', $options, suUnstrip($row['show_form_on_manage']), $js);
                                        ?>
                                    </div>
                                    <!-- Sorting Module Requirement -->
                                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                        <?php
                                        //This field tells whether sorting module is required
                                        $options = $showSortingModuleArray;
                                        $js = "class='form-control'";
                                        echo suDropdown('show_sorting_module', $options, suUnstrip($row['show_sorting_module']), $js);
                                        ?>
                                    </div>

                                    <!-- Labels -->
                                    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                        <?php
                                        //build the label_add field.
                                        //This field tells whether the control needs to have a label or placeholder on record add page.
                                        $options = $labelAddArray;
                                        $js = "class='form-control'";
                                        echo suDropdown('label_add', $options, suUnstrip($row['label_add']), $js);
                                        ?>
                                    </div>
                                    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                        <?php
                                        //build the label_update field.
                                        //This field tells whether the control needs to have a label or placeholder on record add page.
                                        $options = $labelUpdateArray;
                                        $js = "class='form-control'";
                                        echo suDropdown('label_update', $options, suUnstrip($row['label_update']), $js);
                                        ?>
                                    </div>
                                    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                        <?php
                                        //build the display field.

                                        $options = $displayFormArray;
                                        $js = "class='form-control'";
                                        echo suDropdown('display', $options, suUnstrip($row['display']), $js);
                                        ?>
                                    </div>
                                    <!-- Enable/Disable 'Save for Later' option -->
                                    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                        <?php
                                        //build the display field.
                                        //This field tells save for later option is required on the form
                                        $options = $saveForLaterArray;
                                        $js = "class='form-control'";
                                        echo suDropdown('enable_save_for_later', $options, suUnstrip($row['save_for_later']), $js);
                                        ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <!-- ExtraSQL -->
                                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                        <?php
                                        $arg = array('type' => 'text', 'name' => 'extrasql_on_add', 'id' => 'extrasql_on_add', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Extra SQL on Add', 'title' => 'Extra SQL on Add', 'value' => suUnstrip($row['extrasql_on_add']));
                                        echo suInput('input', $arg);
                                        ?>
                                    </div>
                                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                        <?php
                                        $arg = array('type' => 'text', 'name' => 'extrasql_on_update', 'id' => 'extrasql_on_update', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Extra SQL on Update', 'title' => 'Extra SQL on Update', 'value' => suUnstrip($row['extrasql_on_update']));
                                        echo suInput('input', $arg);
                                        ?>
                                    </div>
                                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                        <?php
                                        $arg = array('type' => 'text', 'name' => 'extrasql_on_single_update', 'id' => 'extrasql_on_single_update', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Extra SQL on Single Update', 'title' => 'Extra SQL on Single Update', 'value' => suUnstrip($row['extrasql_on_single_update']));
                                        echo suInput('input', $arg);
                                        ?>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <!-- ExtraSQL -->
                                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                        <?php
                                        $arg = array('type' => 'text', 'name' => 'extrasql_on_delete', 'id' => 'extrasql_on_delete', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Extra SQL on Delete', 'title' => 'Extra SQL on Delete', 'value' => suUnstrip($row['extrasql_on_delete']));
                                        echo suInput('input', $arg);
                                        ?>
                                    </div>

                                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                        <?php
                                        $arg = array('type' => 'text', 'name' => 'extrasql_on_restore', 'id' => 'extrasql_on_restore', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Extra SQL on Restore', 'title' => 'Extra SQL on Restore', 'value' => suUnstrip($row['extrasql_on_restore']));
                                        echo suInput('input', $arg);
                                        ?>
                                    </div>
                                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                        <?php
                                        $arg = array('type' => 'text', 'name' => 'extrasql_on_view', 'id' => 'extrasql_on_view', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Extra SQL on View', 'title' => 'Extra SQL on View', 'value' => suUnstrip($row['extrasql_on_view']));
                                        echo suInput('input', $arg);
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <!-- COMMENTS -->
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                            <?php
                                            $arg = array('type' => 'text', 'name' => 'comments', 'id' => 'comments', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Comments', 'title' => 'Comments', 'value' => suUnstrip($row['comments']));
                                            echo suInput('input', $arg);
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>&nbsp;</div>

                            <ul id="sortable">
                                <?php
//Get table structure and assign it to $structure variable
                                $structure = $row['structure'];
                                //echo $structure= html_entity_decode($structure);
                                //$structure = json_decode($structure, 1);
//Loop through the structre array to build row and populate it with data
                                for ($i = 0; $i <= sizeof($structure) - 1; $i++) {
                                    ////print_array($structure);
                                    include('includes/magic.php');
                                }
                                ?>
                                <div id="destLi"></div>
                            </ul>

                            <div class="clearfix"></div>
                            <div>&nbsp;</div>
                            <ul>
                                <li>Extra SQL in ExtraSQL, ExtraSQL on View, ExtraSQL on Update, ExtraSQL on Single Update, ExtraSQL on Delete and ExtraSQL on Restore may be added like:  AND lcase(TRIM(BOTH '"' FROM json_extract(data,'$.status'))) = 'active'.</li>
                                <li>Extra SQL in ExtraSQL, ExtraSQL on View, ExtraSQL on Update, ExtraSQL on Single Update, ExtraSQL on Delete and ExtraSQL on Restore may also be added like:  AND id='$id'.</li>
                                <li>Extra SQL in ExtraSQL on Add may be added like:  AND lcase(TRIM(BOTH '"' FROM json_extract(data,'$.status'))) = 'active'.</li>
                                <li>If field type is selected as 'Date', you can provide default value by specifying a number. 0 is today, -1 is yesterday, +1 is tomorrow and so on.</li>
                                <li>If field type 'Year' is selected, provide 'start year' and 'end year', separated by a comma in the 'Length/Value' textbox in a format, number of years minus current year and number of years + current years. E.g. '-100,+10'. To pass a default value, provide '0' to specify current year or -x or +x where x is the number of years from current year.</li>
                            </ul>
                            <button type="button" onclick="doCloneRow('sourceLi', 'destLi');" id="doClone" class="btn btn-sm btn-theme"><i class="fa fa-plus-circle"></i> Add Row</button>

                            <div class="clearfix"></div>
                            <p>
                                <?php
                                $arg = array('type' => 'submit', 'name' => 'Submit', 'id' => 'Submit', 'class' => 'btn btn-theme pull-right btn-circle-submit');
                                echo suInput('button', $arg, "<i class='fa fa-magic'></i>", TRUE);


//Id field
                                $arg = array('type' => 'hidden', 'name' => 'id', 'id' => 'id', 'value' => $id);
                                echo suInput('input', $arg);
                                ?>
                            </p>
                            <p>&nbsp;</p>
                        </form>
                    </div>
                </main>
            </div>
            <?php include('includes/footer.php'); ?>
        </div>
        <?php include('includes/footer-js.php'); ?>
    </body>
</html>
<?php suIframe(); ?>