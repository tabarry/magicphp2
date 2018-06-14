<?php

//Set session to empty so that new settings can be fetched
if ($tableSegment == SETTINGS_TABLE_NAME) {
    $_SESSION[SESSION_PREFIX . 'getSettings'] = '';
}

//Send mail to user on creation
if ($tableSegment == USERS_TABLE_NAME) {
    if (file_exists('includes/send-mail-on-user-creation.php')) {
        include('includes/send-mail-on-user-creation.php');
    }

    //Reset user session
    if ($_SESSION[SESSION_PREFIX . 'user_id'] == suDecrypt($_POST['id'])) {
        $sql = "SELECT id, " . suJsonExtract('data', 'name') . "," . suJsonExtract('data', 'email') . "," . suJsonExtract('data', 'photo') . "," . suJsonExtract('data', 'theme') . "," . suJsonExtract('data', 'sound_settings') . "," . suJsonExtract('data', 'navigation_settings') . "," . suJsonExtract('data', 'user_group') . " FROM " . USERS_TABLE_NAME . " WHERE id='" . $_SESSION[SESSION_PREFIX . 'user_id'] . "' LIMIT 0,1";

        $result = suQuery($sql);
        $result['result'] = suUnstrip($result['result']);
        $numRows = $result['num_rows'];

        if ($numRows == 1) {

            //set sessions
            $_SESSION[SESSION_PREFIX . 'admin_login'] = '1';
            $_SESSION[SESSION_PREFIX . 'user_id'] = $result['result'][0]['id'];
            $_SESSION[SESSION_PREFIX . 'user_name'] = $result['result'][0]['name'];
            $_SESSION[SESSION_PREFIX . 'user_email'] = $result['result'][0]['email'];
            $_SESSION[SESSION_PREFIX . 'user_photo'] = $result['result'][0]['photo'];
            $_SESSION[SESSION_PREFIX . 'user_theme'] = $result['result'][0]['theme'];
            $_SESSION[SESSION_PREFIX . 'user_sound'] = $result['result'][0]['sound_settings'];
            $_SESSION[SESSION_PREFIX . 'user_navigation'] = $result['result'][0]['navigation_settings'];
            $_SESSION[SESSION_PREFIX . 'user_group'] = $_SESSION[SESSION_PREFIX . 'user_group'];
        }
    }
}
