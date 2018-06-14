<?php

/* * ********************** */
/* DO NOT EDIT THIS PAGE */
/* * ********************** */
//Check database version compatibility
include('check-db-compatibility.php');

//Get settings
if ($_SESSION[SESSION_PREFIX . 'getSettings'] == '' || sizeof($_SESSION[SESSION_PREFIX . 'getSettings'] < 1)) {

    $sql = "SELECT id, TRIM(BOTH '\"' FROM json_extract(data,'$.setting_title') ) AS setting_title , TRIM(BOTH '\"' FROM json_extract(data,'$.setting_key') ) AS setting_key , TRIM(BOTH '\"' FROM json_extract(data,'$.setting_value') ) AS setting_value FROM _settings WHERE live='Yes' ";
    $result = suQuery($sql);

    if ($result['connect_errno'] == 0 && $result['errno'] == 0) {
        foreach ($result['result'] as $row) {
            $_SESSION[SESSION_PREFIX . 'getSettings'][suUnstrip($row['setting_key'])] = suUnstrip($row['setting_value']);
        }
    }
}

$getSettings = array();
$getSettings = $_SESSION[SESSION_PREFIX . 'getSettings'];

//Explode the values to make array
$getSettings['allowed_file_formats'] = explode(',', $getSettings['allowed_file_formats']);
$getSettings['allowed_picture_formats'] = explode(',', $getSettings['allowed_picture_formats']);

//Define date format
define('DATE_FORMAT', $getSettings['date_format']);
if (DATE_FORMAT == 'mm-dd-yy') {
    $today = date("m-d-Y");
} else {
    $today = date("d-m-Y");
}

//Use button image or text
if ($getSettings['show_submit_button_text'] == '1') {
    $submitButton = SUBMIT;
    $saveButton = SAVE_FOR_LATER;
} else {
    $submitButton = "<i class='fa fa-check'></i>";
    $saveButton = "<i class='fa fa-save'></i>";
}
//==