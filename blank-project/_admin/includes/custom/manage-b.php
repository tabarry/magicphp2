<?php

if ($tableSegment == USERS_TABLE_NAME) {
    //Other users should not be able to see Super User
    if ($_SESSION[SESSION_PREFIX . 'user_id'] != ADMIN_1) {
        $userRestrictionSql = " AND id !='" . ADMIN_1 . "' ";
        $sqlSelect = "SELECT id,$f $saveForLaterSql "; //$f is the fields built above for sql
        $sqlFrom = " FROM " . suUnTablify($tableSegment) . " WHERE live='Yes' $userRestrictionSql $extrasqlOnView ";
    }
}
