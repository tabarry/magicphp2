<?php
include('../sulata/includes/config.php');
include('../sulata/includes/language.php');
include('../sulata/includes/functions.php');
include('../sulata/includes/get-settings.php');

//Check magic login.
//If user is not logged in, send to login page.
checkMagicLogin();

$mode = 'add';
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
                //Clone row at page load
                doCloneRow('sourceLi', 'destLi');
            });
        </script> 
        <!-- Sortable JS -->
        <script>
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
                        $h1 = 'Build a Form';
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
                        <form name="suForm" method="post" id="suForm" target="remote" action="<?php echo MAGIC_URL; ?>remote<?php echo PHP_EXTENSION; ?>/add/">

                            <div class="row">
                                <!-- Name/Title -->
                                <div class="form-group">
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                        <?php
                                        //Call the doSlugify() function to make a slug as the table name is typed.
                                        $js = "$('#slug').val(doSlugify(this.value, '_'))";
                                        //Make arguments to be passed on to suInput() to make a control.
                                        $arg = array('type' => 'text', 'name' => 'title', 'id' => 'title', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Title', 'required' => 'required', 'onkeyup' => $js, 'title' => 'Title');
                                        echo suInput('input', $arg);
                                        ?>
                                    </div>
                                    <!-- Slug -->
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                        <?php
                                        //Make arguments to be passed on to suInput() to make a control.
                                        $arg = array('type' => 'text', 'name' => 'slug', 'id' => 'slug', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Slug', 'readonly' => 'readonly', 'title' => 'Slug');
                                        echo suInput('input', $arg);
                                        ?>
                                    </div>
                                    <!-- Redirect -->
                                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                        <?php
                                        //build the redirect_to_manage_after_add  field.
                                        //This field tells whether the page needs to redirect to manage or stay there after add.
                                        $options = $redirectAfterAddArray;
                                        $js = "class='form-control'";
                                        echo suDropdown('redirect_after_add', $options, 'No', $js);
                                        ?>
                                    </div>
                                    <!-- Do not show on Manage -->
                                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                        <?php
                                        //This field tells whether to show add on manage page.
                                        $options = $showFormOnManageArray;
                                        $js = "class='form-control'";
                                        echo suDropdown('show_form_on_manage', $options, 'No', $js);
                                        ?>
                                    </div>
                                    <!-- Sorting Module Requirement -->
                                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                        <?php
                                        //This field tells whether sorting module is required
                                        $options = $showSortingModuleArray;
                                        $js = "class='form-control'";
                                        echo suDropdown('show_sorting_module', $options, 'No', $js);
                                        ?>
                                    </div>


                                    <!-- Labels -->
                                    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                        <?php
                                        //build the label_add field.
                                        //This field tells whether the control needs to have a label or placeholder on record add page.
                                        $options = $labelAddArray;
                                        $js = "class='form-control'";
                                        echo suDropdown('label_add', $options, 'No', $js);
                                        ?>
                                    </div>
                                    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                        <?php
                                        //build the label_update field.
                                        //This field tells whether the control needs to have a label or placeholder on record add page.
                                        $options = $labelUpdateArray;
                                        $js = "class='form-control'";
                                        echo suDropdown('label_update', $options, 'Yes', $js);
                                        ?>
                                    </div>
                                    <!-- Show/Hide table at admin end -->
                                    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                        <?php
                                        //build the display field.
                                        //This field tells whether the form link needs to be display in under sidebar.
                                        $options = $displayFormArray;
                                        $js = "class='form-control'";
                                        echo suDropdown('display', $options, 'Yes', $js);
                                        ?>
                                    </div>
                                    <!-- Enable/Disable 'Save for Later' option -->
                                    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                        <?php
                                        //build the display field.
                                        //This field tells save for later option is required on the form
                                        $options = $saveForLaterArray;
                                        $js = "class='form-control'";
                                        echo suDropdown('enable_save_for_later', $options, 'No', $js);
                                        ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <!-- ExtraSQL -->
                                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                        <?php
                                        $arg = array('type' => 'text', 'name' => 'extrasql_on_add', 'id' => 'extrasql_on_add', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Extra SQL on Add', 'title' => 'Extra SQL on Add');
                                        echo suInput('input', $arg);
                                        ?>
                                    </div>
                                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                        <?php
                                        $arg = array('type' => 'text', 'name' => 'extrasql_on_update', 'id' => 'extrasql_on_update', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Extra SQL on Update', 'title' => 'Extra SQL on Update');
                                        echo suInput('input', $arg);
                                        ?>
                                    </div>
                                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                        <?php
                                        $arg = array('type' => 'text', 'name' => 'extrasql_on_single_update', 'id' => 'extrasql_on_single_update', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Extra SQL on Single Update', 'title' => 'Extra SQL on Single Update');
                                        echo suInput('input', $arg);
                                        ?>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <!-- ExtraSQL -->
                                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                        <?php
                                        $arg = array('type' => 'text', 'name' => 'extrasql_on_delete', 'id' => 'extrasql_on_delete', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Extra SQL on Delete', 'title' => 'Extra SQL on Delete');
                                        echo suInput('input', $arg);
                                        ?>
                                    </div>

                                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                        <?php
                                        $arg = array('type' => 'text', 'name' => 'extrasql_on_restore', 'id' => 'extrasql_on_restore', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Extra SQL on Restore', 'title' => 'Extra SQL on Restore');
                                        echo suInput('input', $arg);
                                        ?>
                                    </div>
                                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                        <?php
                                        $arg = array('type' => 'text', 'name' => 'extrasql_on_view', 'id' => 'extrasql_on_view', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Extra SQL on View', 'title' => 'Extra SQL on View');
                                        echo suInput('input', $arg);
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <!-- COMMENTS -->
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <?php
                                        $arg = array('type' => 'text', 'name' => 'comments', 'id' => 'comments', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Comments', 'title' => 'Comments');
                                        echo suInput('input', $arg);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div>&nbsp;</div>
                            <ul id="sortable">
                                <div id="destLi"></div>
                            </ul>
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