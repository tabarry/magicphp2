<?php

include('../sulata/includes/config.php');
include('../sulata/includes/language.php');
include('../sulata/includes/functions.php');
include('../sulata/includes/get-settings.php');

//The remote page cannot be opened directly in browser
if ($_GET['do'] != 'autocomplete') {
    if (DEBUG == FALSE) {
        suFrameBuster();
    }
}


//Check admin login.
//If user is not logged in, send to login page.
checkAdminLogin();
$sessionUserId = $_SESSION[SESSION_PREFIX . 'user_id'];

$do = suSegment(1);
$table = suSegment(2);
$tableSegment = suSegment(2);

//Any actions desired at this point should be coded in this file
if (file_exists('includes/custom/remote-a.php')) {
    include('includes/custom/remote-a.php');
}
if ($_GET['do'] != 'autocomplete') {
    if (!isset($do) || !isset($table)) {
        suExit(INVALID_RECORD);
    }
}

//Add record
if ($do == 'add') {

//    print_array($_POST);
//    exit;
//Check referrer
    suCheckRef();

//Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-b.php')) {
        include('includes/custom/remote-b.php');
    }

//Stop unauthorised add access
    $addAccess = suCheckAccess(suUnTablify($table), 'addables');
    if (!in_array(ADMIN_GROUP_NAME, $_SESSION[SESSION_PREFIX . 'user_group'])) {
//Check IP restriction
        suCheckIpAccess();
//Stop unauthorised access
        if ($addAccess == FALSE) {
            suExit(INVALID_ACCESS);
        }
    }
    //Handle `Save for Later` button
    $saveForLater = FALSE;
    if (isset($_POST['save_for_later_use']) && $_POST['save_for_later_use'] == 'Yes') {
        $saveForLater = TRUE;
    }

//Check unique
    if ($saveForLater == FALSE) {
        suCheckUnique(suUnTablify($table));
    }

//Check composite unique
    if ($saveForLater == FALSE) {
        suCheckCompositeUnique(suUnTablify($table));
    }

//Declare validate array
    if (!isset($vError)) {
        $vError = array();
    }
//Get form fields built
    $sql = "SELECT title, redirect_after_add, structure, extrasql_on_add FROM " . STRUCTURE_TABLE_NAME . " WHERE live='Yes' AND slug='" . suUnTablify($table) . "' LIMIT 0,1";
//Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-c.php')) {
        include('includes/custom/remote-c.php');
    }
    $result = suQuery($sql);
    $numRows = $result['num_rows'];
    if ($numRows == 0) {
        suExit(INVALID_RECORD);
    }
    $result['result'] = suUnstrip($result['result']);
    $row = $result['result'][0];
    $title = $row['title'];
    $redirectAfterAdd = $row['redirect_after_add'];
//Prepare extra sql
    $extrasqlOnAdd = html_entity_decode($row['extrasql_on_add']);
//Eval the string if $ in string
    if (stristr($extrasqlOnAdd, '$')) {
        eval("\$extrasqlOnAdd = \"$extrasqlOnAdd\";");
    }
    $extraData = json_decode("{" . $extrasqlOnAdd . "}", 1);
//==

    $structure = $row['structure'];
    $data = suProcessForm($structure);

//Handle Sort Order
    $data['sortOrder'] = $_POST['sortOrder'];

    //Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-d.php')) {
        include('includes/custom/remote-d.php');
    }
    //Merge data and extra data
    if ($extraData != '') {
        $data = array_merge($data, $extraData);
    }

    //Handle `Save for Later` button
    if (isset($_POST['save_for_later_use']) && $_POST['save_for_later_use'] == 'Yes') {
        $data['save_for_later_use'] = 'Yes';
    } else {
        $data['save_for_later_use'] = 'No';
    }

    $filesToUpload = $data['filesToUpload'];


    //Remove unwanted elements from array();
    $data = suRemoveFromArray($data, $fieldsToRemove);
    //Json encode data
    $data = json_encode($data);

    $sql = "INSERT INTO " . suUnTablify($table) . " SET data='" . $data . "', live='Yes'";
    //Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-e.php')) {
        include('includes/custom/remote-e.php');
    }
    $result = suQuery($sql);

    if ($result['errno'] > 0) {//If error exisits
        $error = MYSQL_ERROR;
        suPrintJs('
            parent.suToggleButton(0);
            parent.$("#save_for_later_use").val("No");
            parent.$("#message-area").hide();
            parent.$("#error-area").show();
            parent.$("#error-area").html("<ul><li>' . $error . '</li></ul>");
            parent.$("html, body").animate({ scrollTop: parent.$("html").offset().top }, "slow");
        ');
    } else {
        $maxId = $result['insert_id'];
        //exit(sizeof($filesToUpload));
        //Upload pictures
        for ($i = 0; $i <= sizeof($filesToUpload); $i++) {
            //echo $filesToUpload[$i]['type'];
            if ($filesToUpload[$i]['type'] == 'picture') {
                //Upload multi files
                if (isset($_FILES[$filesToUpload[$i]['name']]['tmp_name'][$i])) {
                    //@unlink(ADMIN_UPLOAD_PATH . $filesToUpload[$i]['value']);
                    suResize($filesToUpload[$i]['width'], $filesToUpload[$i]['height'], $_FILES[$filesToUpload[$i]['name']]['tmp_name'][$i], ADMIN_UPLOAD_PATH . $filesToUpload[$i]['value']);
                }
                //Upload single files
                if (isset($_FILES[$filesToUpload[$i]['name']]['tmp_name'][$i])) {
                    //@unlink(ADMIN_UPLOAD_PATH . $filesToUpload[$i]['value']);
                    suResize($filesToUpload[$i]['width'], $filesToUpload[$i]['height'], $_FILES[$filesToUpload[$i]['name']]['tmp_name'], ADMIN_UPLOAD_PATH . $filesToUpload[$i]['value']);
                }
            } else {

                //Upload multi files
                if (isset($_FILES[$filesToUpload[$i]['name']]['tmp_name'][$i])) {
                    //@unlink(ADMIN_UPLOAD_PATH . $filesToUpload[$i]['value']);
                    copy($_FILES[$filesToUpload[$i]['name']]['tmp_name'][$i], ADMIN_UPLOAD_PATH . $filesToUpload[$i]['value']);
                }
                //Upload single files
                if (isset($_FILES[$filesToUpload[$i]['name']]['tmp_name'][$i])) {
                    //@unlink(ADMIN_UPLOAD_PATH . $filesToUpload[$i]['value']);
                    copy($_FILES[$filesToUpload[$i]['name']]['tmp_name'], ADMIN_UPLOAD_PATH . $filesToUpload[$i]['value']);
                }
            }
        }
//Insert usage log
        suMakeUsageLog('add', suUnTablify($table), $maxId);

//Reload
//If this is from add module on manage page.
        if ($_GET['reload'] == 'yes') {
            $js = "top.location.href='" . ADMIN_URL . "manage" . PHP_EXTENSION . "/" . $tableSegment . "/'";
            suPrintJs($js);
            exit;
        }
//If redirect is set
        if ($_POST['redirect'] != '') {
            $doJs = 'parent.window.location.href="' . $_POST['redirect'] . '";';
        } else { //If redirect is not set, reset form
            if ($redirectAfterAdd == 'No') {
                $doJs = 'parent.suForm.reset();';
            } else {
                $doJs = "parent.window.location.href='" . ADMIN_URL . "manage" . PHP_EXTENSION . "/" . $tableSegment . "/';";
            }
//Any actions desired at this point should be coded in this file
            if (file_exists('includes/custom/remote-f.php')) {
                include('includes/custom/remote-f.php');
            }
            if ($_POST['reloadField'] != '') {
                $doJs = 'parent.suForm.reset();';
                $f = $_POST['sourceField'];
                $doJs .= 'parent.parent.' . $_POST['reloadField'] . '.options.add(new Option("' . $_POST[$f] . '", "' . $_POST[$f] . '"), parent.parent.' . $_POST['reloadField'] . '.options[2]);';
                $doJs .= 'parent.parent.sortSelect("' . $_POST['reloadField'] . '", "' . $_POST[$f] . '", "+")';
            }
        }

//Any actions desired at this point should be coded in this file
        if (file_exists('includes/custom/remote-g.php')) {
            include('includes/custom/remote-g.php');
        }
        suPrintJs('
            parent.suToggleButton(0);
            parent.$("#save_for_later_use").val("No");
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
    $mode = 'update';
//Check referrer
    suCheckRef();
//Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-h.php')) {
        include('includes/custom/remote-h.php');
    }

//Stop unauthorised update access
    $editAccess = suCheckAccess(suUnTablify($table), 'updateables');
    if (!in_array(ADMIN_GROUP_NAME, $_SESSION[SESSION_PREFIX . 'user_group'])) {
//Check IP restriction
        suCheckIpAccess();
//Stop unauthorised access
        if ($_POST['_____profile'] != 'profile') {
            if ($editAccess == FALSE) {
                suExit(INVALID_ACCESS);
            }
        }
    }

//Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-i.php')) {
        include('includes/custom/remote-i.php');
    }
    //Handle `Save for Later` button
    $saveForLater = FALSE;
    if (isset($_POST['save_for_later_use']) && $_POST['save_for_later_use'] == 'Yes') {
        $saveForLater = TRUE;
    }
//Check unique
    suCheckUnique(suUnTablify($table), suDecrypt($_POST['id']));
//Check composite unique
    suCheckCompositeUnique(suUnTablify($table), suDecrypt($_POST['id']));

//Declare validate array
    if (!isset($vError)) {
        $vError = array();
    }
//Get form fields built
    $sql = "SELECT title, structure,extrasql_on_update FROM " . STRUCTURE_TABLE_NAME . " WHERE live='Yes' AND slug='" . suUnTablify($table) . "' LIMIT 0,1";
//Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-j.php')) {
        include('includes/custom/remote-j.php');
    }
    $result = suQuery($sql);
    $numRows = $result['num_rows'];
    if ($numRows == 0) {
        suExit(INVALID_RECORD);
    }
    $result['result'] = suUnstrip($result['result']);
    $row = $result['result'][0];
    $title = $row['title'];

//Prepare extra sql
    $extrasqlOnUpdate = html_entity_decode($row['extrasql_on_update']);
//Eval the string if $ in string
    if (stristr($extrasqlOnUpdate, '$')) {
        eval("\$extrasqlOnUpdate = \"$extrasqlOnUpdate\";");
    }
//==
    $structure = $row['structure'];
    //Process data
    $data = suProcessForm($structure);

//Handle Sort Order
    $data['sortOrder'] = $_POST['sortOrder'];

    //Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-k.php')) {
        include('includes/custom/remote-k.php');
    }

    //Merge data and extra data
    if ($extraData != '') {
        $data = array_merge($data, $extraData);
    }
    //Handle `Save for Later` button
    if (isset($_POST['save_for_later_use']) && $_POST['save_for_later_use'] == 'Yes') {
        $data['save_for_later_use'] = 'Yes';
    } else {
        $data['save_for_later_use'] = 'No';
    }
    $filesToUpload = $data['filesToUpload'];

    //Remove unwanted elements from array();
    $data = suRemoveFromArray($data, $fieldsToRemove);

    //Json encode data
    $data = json_encode($data);

//Prepare update statment
    $sql = "UPDATE " . suUnTablify($table) . " SET data='" . $data . "' WHERE id='" . suDecrypt($_POST['id']) . "' {$extrasqlOnUpdate}";
//Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-l.php')) {
        include('includes/custom/remote-l.php');
    }
    $result = suQuery($sql);

    if ($result['errno'] > 0) {
        $error = MYSQL_ERROR;
        suPrintJs('
            parent.suToggleButton(0);
            parent.$("#save_for_later_use").val("No");
            parent.$("#message-area").hide();
            parent.$("#error-area").show();
            parent.$("#error-area").html("<ul><li>' . $error . '</li></ul>");
            parent.$("html, body").animate({ scrollTop: parent.$("html").offset().top }, "slow");
        ');
    } else {
        $maxId = suDecrypt($_POST['id']);
        //Upload files
        for ($i = 0; $i <= sizeof($filesToUpload); $i++) {
            //echo $filesToUpload[$i]['type'];
            if ($filesToUpload[$i]['type'] == 'picture') {

                //Upload single picture files
                if (isset($_FILES[$filesToUpload[$i]['name']]['tmp_name'][$i])) {
                    echo $previousFile = $_POST[RESERVED_PREVIOUS_PREFEX . $filesToUpload[$i]['name']];
                    @unlink(ADMIN_UPLOAD_PATH . $previousFile);
                    //@unlink(ADMIN_UPLOAD_PATH . $filesToUpload[$i]['value']);
                    suResize($filesToUpload[$i]['width'], $filesToUpload[$i]['height'], $_FILES[$filesToUpload[$i]['name']]['tmp_name'], ADMIN_UPLOAD_PATH . $filesToUpload[$i]['value']);
                }
            } else {

                //Upload single files
                if (isset($_FILES[$filesToUpload[$i]['name']]['tmp_name'][$i])) {
                    echo $previousFile = $_POST[RESERVED_PREVIOUS_PREFEX . $filesToUpload[$i]['name']];
                    @unlink(ADMIN_UPLOAD_PATH . $previousFile);
                    //@unlink(ADMIN_UPLOAD_PATH . $filesToUpload[$i]['value']);
                    copy($_FILES[$filesToUpload[$i]['name']]['tmp_name'], ADMIN_UPLOAD_PATH . $filesToUpload[$i]['value']);
                }
            }
        }


//Insert usage log
        suMakeUsageLog('update', suUnTablify($table), $maxId);


//Any actions desired at this point should be coded in this file
        if (file_exists('includes/custom/remote-l.php')) {
            include('includes/custom/remote-l.php');
        }
        $doJs = 'parent.window.location.href="' . $_POST['redirect'] . '";';
//Any actions desired at this point should be coded in this file
        if (file_exists('includes/custom/remote-m.php')) {
            include('includes/custom/remote-m.php');
        }
        suPrintJs('
            parent.suToggleButton(0);
            parent.$("#save_for_later_use").val("No");
            parent.$("#error-area").hide();
            parent.$("#message-area").show();
            parent.$("#message-area").html("' . SUCCESS_MESSAGE . '");
            parent.$("html, body").animate({ scrollTop: parent.$("html").offset().top }, "slow");
            ' . $doJs . '
        ');
    }

    exit;
}
//Update single record
if ($do == 'update-single') {

//Check referrer
//suCheckRef();
//Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-n.php')) {
        include('includes/custom/remote-n.php');
    }

    $table = suSegment(2);
    $tableSegment = suSegment(2);

    $id = suSegment(3);
    $field = suSegment(4);

//Stop unauthorised update access
    $editAccess = suCheckAccess(suUnTablify($table), 'updateables');
    if (!in_array(ADMIN_GROUP_NAME, $_SESSION[SESSION_PREFIX . 'user_group'])) {
//Check IP restriction
        suCheckIpAccess();
//Stop unauthorised access
        if ($editAccess == FALSE) {
            suExit(INVALID_ACCESS);
        }
    }

//Get form fields built
    $sql = "SELECT extrasql_on_single_update FROM " . STRUCTURE_TABLE_NAME . " WHERE live='Yes' AND slug='" . suUnTablify($table) . "' LIMIT 0,1";
//Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-o.php')) {
        include('includes/custom/remote-o.php');
    }
    $result = suQuery($sql);
    $numRows = $result['num_rows'];
    if ($numRows == 0) {
        suExit(INVALID_RECORD);
    }
    $result['result'] = suUnstrip($result['result']);
    $row = $result['result'][0];
//Prepare extra SQL
    //$extrasqlOnSingleUpdate = html_entity_decode(suUnstrip($row['extrasql_on_single_update']));
//Eval the string if $ in string
    if (stristr($extrasqlOnSingleUpdate, '$')) {
        eval("\$extrasqlOnSingleUpdate = \"$extrasqlOnSingleUpdate\";");
    }
//==

    $value = $_GET['v'];

    if ($value == '') {
        suPrintJs("parent.$('#_____span_" . $field . "_" . $id . "').html(parent._____v);parent.$('#_____hidden_" . $field . "_" . $id . "').val(parent._____v);");
        exit();
    }
    $_REQUEST[$field] = $_GET['v']; //Required to be used in unique check functions
//Check unique
    $uError = suCheckUnique(suUnTablify($table), $id, TRUE);
    if ($uError != '') {//If error, revert the changes
        suPrintJs("alert('" . $uError . "');parent.$('#_____span_" . $field . "_" . $id . "').html(parent._____v);parent.$('#_____hidden_" . $field . "_" . $id . "').val(parent._____v);window.location.href='" . PING_URL . "'");
        exit();
    }
//Check composite unique
    $uError = suCheckCompositeUnique(suUnTablify($table), $id, TRUE);
    if ($uError != '') {//If error, revert the changes
        suPrintJs("alert('" . $uError . "');parent.$('#_____span_" . $field . "_" . $id . "').html(parent._____v);parent.$('#_____hidden_" . $field . "_" . $id . "').val(parent._____v);window.location.href='" . PING_URL . "'");
        exit();
    }



    $sql = "UPDATE " . suUnTablify($table) . " SET data= JSON_REPLACE(data,'$." . $field . "','" . urlencode($value) . "') WHERE id='$id' {$extrasqlOnSingleUpdate}";
//Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-p.php')) {
        include('includes/custom/remote-p.php');
    }
    suQuery($sql);
//Insert usage log
    suMakeUsageLog('update-single', suUnTablify($table), $id);

//Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-q.php')) {
        include('includes/custom/remote-q.php');
    }

    exit;
}
//Delete record
if ($do == "delete") {
//Check referrer
    suCheckRef();

    $id = suSegment(2);
    $table = suSegment(3);
    $tableSegment = suSegment(3);


//Stop unauthorised delete access
    $deleteAccess = suCheckAccess(suUnTablify($table), 'deleteables');
    if (!in_array(ADMIN_GROUP_NAME, $_SESSION[SESSION_PREFIX . 'user_group'])) {
//Check IP restriction
        suCheckIpAccess();
//Stop unauthorised access
        if ($deleteAccess == FALSE) {
            suExit(INVALID_ACCESS);
        }
    }
//Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-r.php')) {
        include('includes/custom/remote-r.php');
    }

//Get form fields built
    $sql = "SELECT extrasql_on_delete FROM " . STRUCTURE_TABLE_NAME . " WHERE live='Yes' AND slug='" . suUnTablify($table) . "' LIMIT 0,1";
//Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-s.php')) {
        include('includes/custom/remote-s.php');
    }
    $result = suQuery($sql);
    $numRows = $result['num_rows'];
    if ($numRows == 0) {
        suExit(INVALID_RECORD);
    }
    $result['result'] = suUnstrip($result['result']);
    $row = $result['result'][0];
//Prepare extra SQL
    $extrasqlOnDelete = html_entity_decode($row['extrasql_on_delete']);
//Eval the string if $ in string
    if (stristr($extrasqlOnDelete, '$')) {
        eval("\$extrasqlOnDelete = \"$extrasqlOnDelete\";");
    }
//==
//
//Update the table
    $sql = "UPDATE " . suUnTablify($table) . " SET live='No' WHERE id = '" . $id . "' {$extrasqlOnDelete} ";
//Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-t.php')) {
        include('includes/custom/remote-t.php');
    }
    $result = suQuery($sql);
//Insert usage log
    suMakeUsageLog('delete', suUnTablify($table), $id);

//Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-u.php')) {
        include('includes/custom/remote-u.php');
    }
}



//Restore record
if ($do == "restore") {
//Check referrer
    suCheckRef();


    $id = suSegment(2);
    $table = suSegment(3);
    $tableSegment = suSegment(3);

//Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-v.php')) {
        include('includes/custom/remote-v.php');
    }

//Get form fields built
    $sql = "SELECT extrasql_on_restore FROM " . STRUCTURE_TABLE_NAME . " WHERE live='Yes' AND slug='" . suUnTablify($table) . "' LIMIT 0,1";
//Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-w.php')) {
        include('includes/custom/remote-w.php');
    }
    $result = suQuery($sql);
    $numRows = $result['num_rows'];
    if ($numRows == 0) {
        suExit(INVALID_RECORD);
    }
    $result['result'] = suUnstrip($result['result']);
    $row = $result['result'][0];
//Prepare extra SQL
    $extrasqlOnRestore = html_entity_decode($row['extrasql_on_restore']);
//Eval the string if $ in string
    if (stristr($extrasqlOnRestore, '$')) {
        eval("\$extrasqlOnRestore = \"$extrasqlOnRestore\";");
    }
//==
//Fetch record
    $sql = "SELECT data FROM " . suUnTablify($table) . " WHERE id='" . $id . "' AND live='No' LIMIT 0,1";
//Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-x.php')) {
        include('includes/custom/remote-x.php');
    }
    $result = suQuery($sql);
    $numRows = $result['num_rows'];
    if ($numRows == 0) {
        suExit(INVALID_RECORD);
    } else {
        $row = $result['result'][0]['data'];
    }
    $row = json_decode($row, 1);
    foreach ($row as $key => $value) {
        $_REQUEST[$key] = $value;
    }

//Check unique
    $response = suCheckUnique(suUnTablify($table), 0, TRUE, TRUE);
    if ($response != '') {
        suPrintJs('
            parent.suToggleButton(0);
            parent.$("#save_for_later_use").val("No");
            parent.$("#message-area").hide();
            parent.$("#error-area").show();
            parent.$("#error-area").html("<ul><li>' . $response . '</li></ul>");
            parent.$("html, body").animate({ scrollTop: parent.$("html").offset().top }, "slow");
        ');
        exit;
    }
//Check composite unique
    $response = suCheckUnique(suUnTablify($table), 0, TRUE, TRUE);
    if ($response != '') {
        suPrintJs('
            parent.suToggleButton(0);
            parent.$("#save_for_later_use").val("No");
            parent.$("#message-area").hide();
            parent.$("#error-area").show();
            parent.$("#error-area").html("<ul><li>' . $response . '</li></ul>");
            parent.$("html, body").animate({ scrollTop: parent.$("html").offset().top }, "slow");
        ');
        exit;
    }


//
//
//Update the table
    $sql = "UPDATE " . suUnTablify($table) . " SET live='Yes' WHERE id = '" . $id . "' {$extrasqlOnRestore}";
//Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-y.php')) {
        include('includes/custom/remote-y.php');
    }
    $result = suQuery($sql);
//Insert usage log
    suMakeUsageLog('restore', suUnTablify($table), $id);
//Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-z.php')) {
        include('includes/custom/remote-z.php');
    }

    suPrintJs('
                parent.restoreById("' . $id . '");
                parent.$("#error-area").hide();
                //parent.$("#message-area").show();
                //parent.$("#message-area").html("' . RECORD_RESTORED . '");
                //parent.$("html, body").animate({ scrollTop: parent.$("html").offset().top }, "slow");
            ');
}
//if autocomplete
if ($_GET['do'] == 'autocomplete') {

//If not ajax request, exit
    if (!$_SERVER['HTTP_X_REQUESTED_WITH']) {
        if (DEBUG == FALSE) {
            suExit(INVALID_ACCESS);
        }
    }

    $arr['Source'] = $_GET['source'];

//Get data from table
    $tableField = explode('.', $arr['Source']);
    $table = $tableField[0];
    $field = suStrip($tableField[1]);
    $field = suSlugifyStr($field, '_');
    $extraSql = html_entity_decode($arr['ExtraSQL']);
    $extraSQL = suDecrypt($_GET['extra']);
    $extraSQL = str_replace('&quot;', '"', $extraSQL);


    $sql = "SELECT " . suJsonExtract('data', $field, FALSE) . " AS f1, " . suJsonExtract('data', $field, FALSE) . " AS f2 FROM  " . suUnTablify($table) . " WHERE lcase(" . suJsonExtract('data', $field, FALSE) . ") LIKE lcase('%" . suUnstrip($_REQUEST['term']) . "%') AND live='Yes'  " . $extraSQL . " GROUP BY " . suJsonExtract('data', $field, FALSE) . "ORDER BY " . suJsonExtract('data', $field, FALSE);

//To overwrite above query, use the file below
    if (file_exists('includes/overwrite-autocomplete-query.php')) {
        include('includes/overwrite-autocomplete-query.php');
    }

//Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-za.php')) {
        include('includes/custom/remote-za.php');
    }

    $result = suQuery($sql);

    $data = array();
    if ($result && $result['num_rows']) {
        foreach ($result['result'] as $row) {
            $data[] = array(
                'label' => suUnstrip(urldecode($row['f1'])),
                'value' => suUnstrip(urldecode($row['f2']))
            );
        }
    }

    echo json_encode($data);
    flush();
}
//Preview
if ($do == "preview-print") {
    echo "<html>"
    . "<head>"
    . "<title>"
    . $getSettings['site_name']
    . "</title>"
    . "<style>"
    . "body{font-family:arial;font-size:13px;}"
    . "td{font-family:arial;font-size:13px;padding:4px;}"
    . ".imgThumb{width:70px;height: 70px;display: block;background-repeat: no-repeat;background-position: center !important;background-size: cover !important;border: 5px solid #CCC;border-radius: 5px;margin-bottom: 10px;}"
    . "</style>"
    . "</head>"
    . "<body>"
    . "<div id='printable-div'></div>"
    . "</body>"
    . "</html>";
    $str = "document.getElementById('printable-div').innerHTML=parent.document.getElementById('printable-div').innerHTML;document.getElementById('printable-table').style.width='600px';window.print();window.location.href='" . PING_URL . "'";
//Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/remote-zb.php')) {
        include('includes/custom/remote-zb.php');
    }
    suPrintJS($str);
}

//Sort record
if ($do == 'sort') {
//Check referrer (CSRF)
    suCheckRef();
    for ($i = 0; $i < sizeof($_POST['sortOrder']); $i++) {
        $j = $i + 10;
        $sql = "UPDATE " . suUnTablify($table) . " SET data= JSON_REPLACE(data,'$." . 'sortOrder' . "','" . $j . "') WHERE id='" . $_POST['sortOrder'][$i] . "'";
        if (file_exists('includes/custom/remote-zc.php')) {
            include('includes/custom/remote-zc.php');
        }
        suQuery($sql);
    }

    /* POST SORT PLACE */
    suPrintJs("parent.window.location.href='" . ADMIN_URL . 'manage' . PHP_EXTENSION . "/" . $table . "/'");

    exit;
}
//Add more section
if ($do == 'add-more') {

    $source = suSegment(2);
    $mode = suSegment(3);

    $table = suSegment(4);
    $tableSegment = suSegment(4);

    $rid = suSegment(5);

    //Get save for later option
    $sql = "SELECT save_for_later FROM " . STRUCTURE_TABLE_NAME . " WHERE slug='" . $source . "' AND live='Yes'";
    $result = suQuery($sql);
    $result = suUnstrip($result['result']);
    $save_for_later = $result[0]['save_for_later'];

    if ($table == '' && $rid == '') {
        suAddMore($source);
    } else {


        $sql = "SELECT id," . suJsonExtract('data', $source) . " FROM " . $table . " WHERE live='Yes' AND id='" . $rid . "'";
        $result = suQuery($sql);
        $result['result'] = suUnstrip($result['result']);

        $values = $result['result'][0][$source];

        //Get first field
        foreach ($values as $key => $value) {
            $firstKey = $key;
            break;
        }
        $firstKey2 = explode(FIELD_SEPARATOR, $firstKey);
        $firstKey2 = $firstKey2[0];
        $firstKey2 = $firstKey2 . FIELD_SEPARATOR;

        //Get max keys
        $x = 0;
        foreach ($values as $key => $value) {
            if (stristr($key, $firstKey2)) {
                $x++;
            }
        }
        //Make array to fill
        for ($y = 0; $y < $x; $y++) {
            //make values to pass
            suAddMore($source, $values, ($y + 1));
        }
    }
}