<?php

//Set session to empty so that new settings can be fetched
if ($table == SETTINGS_TABLE_NAME) {
    $_SESSION[SESSION_PREFIX . 'getSettings'] = '';
}

//Send mail to user on creation
if ($table == USERS_TABLE_NAME) {
    if (file_exists('includes/send-mail-on-user-creation.php')) {
        include('includes/send-mail-on-user-creation.php');
    }
    //Reset own user sessions
    if ($_SESSION[SESSION_PREFIX . 'user_id'] == suDecrypt($_POST['id'])) {
        $_SESSION[SESSION_PREFIX . 'user_name'] = $_POST['name'];
        $_SESSION[SESSION_PREFIX . 'user_email'] = $_POST['email'];
        $_SESSION[SESSION_PREFIX . 'user_group'] = $_POST['user_group'];
    }
}
