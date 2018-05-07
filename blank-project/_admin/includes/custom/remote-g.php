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

}
