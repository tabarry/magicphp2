<?php

//Build the group permissions
if ($tableSegment == 'groups') {
    if (file_exists('includes/permissions-matrix.php')) {
        include('includes/permissions-matrix.php');
    }
}
//Generate auto password on add user
if ($tableSegment == 'users') {
    if (file_exists('includes/generate-user-password.php')) {
        include('includes/generate-user-password.php');
    }
}
