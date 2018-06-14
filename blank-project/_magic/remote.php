<?php

include('../sulata/includes/config.php');
include('../sulata/includes/language.php');
include('../sulata/includes/functions.php');
include('../sulata/includes/get-settings.php');

//Check magic login.
//If user is not logged in, send to login page.
checkMagicLogin();

//Assign the value of segment 1 to $do.
//$do decides regarding the section of the page to execute, like, add, update, delete and restore.
$do = suSegment(1);

//Add record section
if ($do == 'add') {

    //Check referrer (CSRF)
    suCheckRef();

    //Check reserved table names so that tables with same names cannot be created.
    if (in_array($_POST['slug'], $reservedTables)) {
        suPrintJs('
            parent.suToggleButton(0);
            parent.$("#message-area").hide();
            parent.$("#error-area").show();
            parent.$("#error-area").html("<ul><li>' . sprintf(RESERVED_TABLE_MESSAGE, $_POST['slug']) . '</li></ul>");
            parent.$("html, body").animate({ scrollTop: parent.$("html").offset().top }, "slow");
        ');
        exit;
    }

    //Instantiate the 'data' variable as array
    $data = array();
    //Loop through all posted fields for table structure creation
    foreach ($_POST as $key => $value) {
        if (strstr($key, 'name_') && !strstr($key, 'hint_name_')) {
            $v = explode('name_', $key);
            $v = $v[1];
            //Make array against each row.
            //This will make each field's properties.
            $d = array(
                'Name' => suStrip($_POST['name_' . $v]),
                'Type' => suStrip($_POST['type_' . $v]),
                'Length' => suStrip($_POST['length_' . $v]),
                'ImageWidth' => suStrip($_POST['imagewidth_' . $v]),
                'ImageHeight' => suStrip($_POST['imageheight_' . $v]),
                'Width' => suStrip($_POST['width_' . $v]),
                'Show' => suStrip($_POST['show_' . $v]),
                'CssClass' => suStrip($_POST['cssclass_' . $v]),
                'OrderBy' => suStrip($_POST['orderby_' . $v]),
                'SearchBy' => suStrip($_POST['searchby_' . $v]),
                'ExtraSQL' => suStrip($_POST['extrasql_' . $v]),
                'Source' => suStrip($_POST['source_' . $v]),
                'Default' => ($_POST['default_' . $v]),
                'Required' => suStrip($_POST['required_' . $v]),
                'RequiredSaveForLater' => suStrip($_POST['requiredsaveforlater_' . $v]),
                'Unique' => suStrip($_POST['unique_' . $v]),
                'CompositeUnique' => suStrip($_POST['compositeunique_' . $v]),
                'OnChange' => suStrip($_POST['onchange_' . $v]),
                'OnClick' => suStrip($_POST['onclick_' . $v]),
                'OnKeyUp' => suStrip($_POST['onkeyup_' . $v]),
                'OnKeyPress' => suStrip($_POST['onkeypress_' . $v]),
                'OnBlur' => suStrip($_POST['onblur_' . $v]),
                'ReadOnlyAdd' => suStrip($_POST['readonlyadd_' . $v]),
                'ReadOnlyUpdate' => suStrip($_POST['readonlyupdate_' . $v]),
                'HideOnUpdate' => suStrip($_POST['hideonupdate_' . $v]),
                'HideOnAdd' => suStrip($_POST['hideonadd_' . $v]),
                'Slug' => suSlugifyStr($_POST['name_' . $v], '_'));
            //Check if the posted field is not empty
            if ($_POST['name_' . $v] != '') {
                //Push field properties to the $data array
                array_push($data, $d);
            }
        }
    }

    $data = json_encode($data);

    $sql = "INSERT INTO " . STRUCTURE_TABLE_NAME . " SET title='" . suStrip($_POST['title']) . "',slug='" . suStrip($_POST['slug']) . "',redirect_after_add ='" . suStrip($_POST['redirect_after_add']) . "',show_form_on_manage ='" . suStrip($_POST['show_form_on_manage']) . "',show_sorting_module ='" . suStrip($_POST['show_sorting_module']) . "',label_add='" . $_POST['label_add'] . "',label_update='" . $_POST['label_update'] . "',comments='" . suStrip($_POST['comments']) . "',structure='" . $data . "',display='" . $_POST['display'] . "',save_for_later='" . $_POST['enable_save_for_later'] . "', live='Yes', extrasql_on_add ='" . suStrip($_POST['extrasql_on_add']) . "', extrasql_on_update ='" . suStrip($_POST['extrasql_on_update']) . "', extrasql_on_single_update ='" . suStrip($_POST['extrasql_on_single_update']) . "', extrasql_on_delete ='" . suStrip($_POST['extrasql_on_delete']) . "', extrasql_on_restore ='" . suStrip($_POST['extrasql_on_restore']) . "', extrasql_on_view ='" . suStrip($_POST['extrasql_on_view']) . "'";
    $result = suQuery($sql);
    //If there is an SQL error, exectue the following block.
    if ($result['errno'] > 0) {
        if ($result['errno'] == 1062) {//If the error is a duplication error.
            $error = sprintf(DUPLICATION_ERROR, 'Title or Slug'); //Print duplication error message.
        } else {
            $error = MYSQL_ERROR; //Print generic error.
        }
        suPrintJs('
            parent.suToggleButton(0);
            parent.$("#message-area").hide();
            parent.$("#error-area").show();
            parent.$("#error-area").html("<ul><li>' . $error . '</li></ul>");
            parent.$("html, body").animate({ scrollTop: parent.$("html").offset().top }, "slow");
        ');
    } else {
        //Store the insert id of this record in $maxId variable
        $maxId = $result['insert_id'];
        //Build SQL to create table to hold data for this formset
        $sql = "CREATE TABLE IF NOT EXISTS `" . $_POST['slug'] . "` (`id` int(11) NOT NULL,`data` " . DB_JSON_FIELD . " NOT NULL,`live` enum('Yes','No') NOT NULL DEFAULT 'Yes') ENGINE=MyISAM DEFAULT CHARSET=utf8;";
        $result = suQuery($sql);
        //If there is an SQL error, exectue the following block.
        if ($result['errno'] == 0) {
            //Alter the table to add primary key.
            $sql = "ALTER TABLE `" . $_POST['slug'] . "`ADD PRIMARY KEY (`id`);";
            suQuery($sql);
            //Alter the table to add auto increment key.
            $sql = "ALTER TABLE `" . $_POST['slug'] . "`MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
            suQuery($sql);
        } else {
            $error = MYSQL_ERROR; //Print generic error.
            suPrintJs('
            parent.suToggleButton(0);
            parent.$("#message-area").hide();
            parent.$("#error-area").show();
            parent.$("#error-area").html("<ul><li>' . $error . '</li></ul>");
            parent.$("html, body").animate({ scrollTop: parent.$("html").offset().top }, "slow");
        ');
            exit;
        }

        //Build variable to redirect to index.php page.
        //$doJs = 'parent.window.location.href="' . MAGIC_URL . 'index' . PHP_EXTENSION . '/?q=' . suStrip($_POST['title']) . '"';
        //$doJs = 'parent.window.location.href="' . MAGIC_URL . 'index' . PHP_EXTENSION . '/' . '"';
        $doJs = 'parent.window.location.href="' . MAGIC_URL . 'sort' . PHP_EXTENSION . '/' . '"';

        suPrintJs('
            parent.suToggleButton(0);
            parent.$("#error-area").hide();
            parent.$("#message-area").show();
            parent.$("#message-area").html("' . SUCCESS_MESSAGE . '");
            parent.$("html, body").animate({ scrollTop: parent.$("html").offset().top }, "slow");
            ' . $doJs . '
        ');
    }
    exit;
}
//Update record
if ($do == 'update') {
    //Check referrer (CSRF)
    suCheckRef();

    //Take backup of existing table
    $uid = 'bu_' . date("YmdHis") . '_';
    $sql = "INSERT INTO " . STRUCTURE_TABLE_NAME . " (title,slug,show_form_on_manage,show_sorting_module,redirect_after_add,label_add,label_update,extrasql_on_add,extrasql_on_update,extrasql_on_single_update,extrasql_on_delete,extrasql_on_restore,extrasql_on_view,comments,structure,display,sort_order,save_for_later,live) SELECT CONCAT('" . $uid . "',title),CONCAT('" . $uid . "',slug),show_form_on_manage,show_sorting_module,redirect_after_add,label_add,label_update,extrasql_on_add,extrasql_on_update,extrasql_on_single_update,extrasql_on_delete,extrasql_on_restore,extrasql_on_view,comments,structure,display,sort_order,save_for_later,'No' FROM " . STRUCTURE_TABLE_NAME . " WHERE id='" . $_POST['id'] . "'";
    suQuery($sql);
    //Instantiate the 'data' variable as array
    $data = array();
    //Loop through all posted fields for table structure creation
    foreach ($_POST as $key => $value) {
        if (strstr($key, 'name_')) {
            $v = explode('name_', $key);
            $v = $v[1];
            $d = array(
                'Name' => suStrip($_POST['name_' . $v]),
                'Type' => suStrip($_POST['type_' . $v]),
                'Length' => suStrip($_POST['length_' . $v]),
                'ImageWidth' => suStrip($_POST['imagewidth_' . $v]),
                'ImageHeight' => suStrip($_POST['imageheight_' . $v]),
                'Width' => suStrip($_POST['width_' . $v]),
                'Show' => suStrip($_POST['show_' . $v]),
                'CssClass' => suStrip($_POST['cssclass_' . $v]),
                'OrderBy' => suStrip($_POST['orderby_' . $v]),
                'SearchBy' => suStrip($_POST['searchby_' . $v]),
                'ExtraSQL' => suStrip($_POST['extrasql_' . $v]),
                'Source' => suStrip($_POST['source_' . $v]),
                'Default' => ($_POST['default_' . $v]),
                'Required' => suStrip($_POST['required_' . $v]),
                'RequiredSaveForLater' => suStrip($_POST['requiredsaveforlater_' . $v]),
                'Unique' => suStrip($_POST['unique_' . $v]),
                'CompositeUnique' => suStrip($_POST['compositeunique_' . $v]),
                'OnChange' => suStrip($_POST['onchange_' . $v]),
                'OnClick' => suStrip($_POST['onclick_' . $v]),
                'OnKeyUp' => suStrip($_POST['onkeyup_' . $v]),
                'OnKeyPress' => suStrip($_POST['onkeypress_' . $v]),
                'OnBlur' => suStrip($_POST['onblur_' . $v]),
                'ReadOnlyAdd' => suStrip($_POST['readonlyadd_' . $v]),
                'ReadOnlyUpdate' => suStrip($_POST['readonlyupdate_' . $v]),
                'HideOnUpdate' => suStrip($_POST['hideonupdate_' . $v]),
                'HideOnAdd' => suStrip($_POST['hideonadd_' . $v]),
                'Slug' => suSlugifyStr($_POST['name_' . $v], '_'));
            //Check if not empty
            if ($_POST['name_' . $v] != '') {
                array_push($data, $d);
            }
        }
    }
    $data = json_encode($data);

    $sql = "UPDATE " . STRUCTURE_TABLE_NAME . " SET title='" . suStrip($_POST['title']) . "',label_add='" . $_POST['label_add'] . "',label_update='" . $_POST['label_update'] . "',slug='" . suStrip($_POST['slug']) . "',redirect_after_add ='" . suStrip($_POST['redirect_after_add']) . "',show_form_on_manage ='" . suStrip($_POST['show_form_on_manage']) . "',show_sorting_module ='" . suStrip($_POST['show_sorting_module']) . "',display='" . $_POST['display'] . "',save_for_later='" . $_POST['enable_save_for_later'] . "',comments='" . suStrip($_POST['comments']) . "',structure='" . $data . "', extrasql_on_add ='" . suStrip($_POST['extrasql_on_add']) . "', extrasql_on_update ='" . suStrip($_POST['extrasql_on_update']) . "', extrasql_on_single_update ='" . suStrip($_POST['extrasql_on_single_update']) . "', extrasql_on_delete ='" . suStrip($_POST['extrasql_on_delete']) . "', extrasql_on_restore ='" . suStrip($_POST['extrasql_on_restore']) . "', extrasql_on_view ='" . suStrip($_POST['extrasql_on_view']) . "' WHERE id='" . $_POST['id'] . "'";
    $result = suQuery($sql);



    //If there is an SQL error, exectue the following block.
    if ($result['errno'] > 0) {
        if ($result['errno'] == 1062) {
            $error = sprintf(DUPLICATION_ERROR, 'Title or Slug');
        } else {
            $error = MYSQL_ERROR;
        }
        suPrintJs('
            parent.suToggleButton(0);
            parent.$("#message-area").hide();
            parent.$("#error-area").show();
            parent.$("#error-area").html("<ul><li>' . $error . '</li></ul>");
            parent.$("html, body").animate({ scrollTop: parent.$("html").offset().top }, "slow");
        ');
    } else {
        //Store the insert id of this record in $maxId variable
        $maxId = $_POST['id'];

        //Rename the table if required
        if ($_POST['slug'] != $_POST['old_slug']) {
            $sql = "RENAME TABLE " . $_POST['old_slug'] . " TO " . $_POST['slug'];
            $result = suQuery($sql);
        }
        /* POST UPDATE PLACE */
        suPrintJs("parent.window.location.href='" . MAGIC_URL . "index" . PHP_EXTENSION . "/'");
    }
    exit;
}
//Delete record (soft delete)
if ($do == "delete") {
    //Check referrer (CSRF)
    suCheckRef();

    $id = suSegment(2);
    $uid = suSegment(3);
    $table = suSegment(4);
    $oldTable = suSegment(5);

    //Due to soft delete logic, deletion is made by renaming the table and changing the status from live='Yes' to live='No'.
    //Build SQL to update the table, concatinate a uid with the title to retain its uniqueness and change status from live='Yes' to live='No' 
    $sql = "UPDATE " . STRUCTURE_TABLE_NAME . " SET title=CONCAT('" . $uid . "',title), slug=CONCAT('" . $uid . "',slug), live='No' WHERE id = '" . $id . "'";
    $result = suQuery($sql);
    if ($result['errno'] == 0) {
        //Also Rename the data table by adding a uid as prefix, maintaining the soft delete logic
        $sql = "RENAME TABLE " . $oldTable . " TO " . $uid . $oldTable;
        suQuery($sql);
    } else {
        $error = MYSQL_ERROR;
        suPrintJs('
            parent.suToggleButton(0);
            parent.$("#message-area").hide();
            parent.$("#error-area").show();
            parent.$("#error-area").html("<ul><li>' . $error . '</li></ul>");
            parent.$("html, body").animate({ scrollTop: parent.$("html").offset().top }, "slow");
        ');
    }
}

//Sort record
if ($do == 'sort') {
    //Check referrer (CSRF)
    suCheckRef();
    for ($i = 0; $i < sizeof($_POST['sort_order']); $i++) {
        $j = $i + 10;
        $sql = "UPDATE " . STRUCTURE_TABLE_NAME . " SET sort_order='" . $j . "' WHERE id='" . $_POST['sort_order'][$i] . "'";
        suQuery($sql);
    }

    /* POST SORT PLACE */
    suPrintJs("parent.window.location.href='" . MAGIC_URL . "index" . PHP_EXTENSION . "/'");

    exit;
}

//Restore record
//Restore option can only be used if the page has not been refreshed after deletion
if ($do == "restore") {
    //Check referrer (CSRF)
    suCheckRef();

    $id = suSegment(2);
    $uid = suSegment(3);
    $table = suSegment(4);

//Build SQL to update the table, removed the prefixed uid with the title and change status from live='No' to live='Yes' 
    $sql = "UPDATE " . STRUCTURE_TABLE_NAME . " SET title=SUBSTR(title," . (UID_LENGTH + 1) . "),slug=SUBSTR(slug," . (UID_LENGTH + 1) . "), live='Yes' WHERE id = '" . $id . "'";
    $result = suQuery($sql);
    if ($result['errno'] > 0) {
        if ($result['errno'] == 1062) {
            $error = sprintf(DUPLICATION_ERROR_ON_UPDATE, 'Title');
        } else {
            $error = MYSQL_ERROR;
        }

        suPrintJs('
                parent.$("#message-area").hide();
                parent.$("#error-area").show();
                parent.$("#error-area").html("<ul><li>' . $error . '</li></ul>");
                parent.$("html, body").animate({ scrollTop: parent.$("html").offset().top }, "slow");
            ');
    } else {
        //Rename the original data table by removing the prefixed uid
        $sql = "RENAME TABLE " . $uid . $table . " TO " . $table;
        suQuery($sql);
        suPrintJs('
                parent.restoreById("' . $id . '");
                parent.$("#error-area").hide();
                parent.$("#message-area").show();
                parent.$("#message-area").html("' . RECORD_RESTORED . '");
                parent.$("html, body").animate({ scrollTop: parent.$("html").offset().top }, "slow");
            ');
    }
}