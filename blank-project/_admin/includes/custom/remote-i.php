<?php

//Fields to be added in profile
if ($tableSegment == USERS_TABLE_NAME && $_POST['_____profile'] == 'profile') {
    $_POST['status'] = 'Active';
    $_POST['user_group'] = $_SESSION[SESSION_PREFIX . 'user_group'];
}
//User cannot update anyone else's profile
if (($tableSegment == USERS_TABLE_NAME && $_POST['_____profile'] == 'profile') && (suDecrypt($_POST['id']) != $_SESSION[SESSION_PREFIX . 'user_id'])) {
    suExit(INVALID_ACCESS);
}

//Check if at least one group is entered
if ($tableSegment == 'groups') {
    if (file_exists('includes/group-required-check.php')) {
        include('includes/group-required-check.php');
    }
}
