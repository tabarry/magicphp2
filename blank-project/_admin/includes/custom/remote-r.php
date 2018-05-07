<?php

//Stop user from self deleting
if ($table == USERS_TABLE_NAME) {
    if ($table == USERS_TABLE_NAME && $_SESSION[SESSION_PREFIX . 'user_id'] == $id) {
        $error = SELF_DELETE_ERROR;
        suPrintJs('
            parent.suToggleButton(0);
            parent.$("#message-area").hide();
            parent.$("#error-area").show();
            parent.$("#error-area").html("<ul><li>' . $error . '</li></ul>");
            parent.$("html, body").animate({ scrollTop: parent.$("html").offset().top }, "slow");
        ');
        exit();
    }

}
//Stop from deleting 'Admin' user group
if ($table == 'groups') {
    $sql = "SELECT " . suJsonExtract('data', 'group_title') . " FROM groups WHERE id='" . $id . "' AND live='Yes' LIMIT 0,1";
    $result = suQuery($sql);
    $result = $result['result'][0];
    $groupTitle = suUnstrip($result['group_title']);
    //If Admin group, exit;
    if (suUnstrip($groupTitle) == ADMIN_GROUP_NAME) {
        suExit(INVALID_ACCESS);
    }
}