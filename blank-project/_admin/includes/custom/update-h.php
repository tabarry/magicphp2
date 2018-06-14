<?php

//Hide fields not required to be updated in profile
if ($tableSegment == USERS_TABLE_NAME && suSegment(3) == 'profile') {
    suPrintJs("
        if($('#data_div_user_group')){
            $('#data_div_user_group').hide();
        }
        if($('#data_div_status')){
            $('#data_div_status').hide();
        }
        if($('#data_div_send_mail_to_user')){
            $('#data_div_send_mail_to_user').hide();
        }
              
    ");
}
if ($tableSegment == USERS_TABLE_NAME) {
    //Hide fields for Super User
    if ($rid == ADMIN_1 || $rid == ADMIN_2) {
        suPrintJs("
        if($('#data_div_user_group')){
            $('#data_div_user_group').hide();
        }
        if($('#data_div_status')){
            $('#data_div_status').hide();
        }
        if($('#data_div_send_mail_to_user')){
            $('#data_div_send_mail_to_user').hide();
        }
              
    ");
    }
    //Hide fields for Self
    if ($rid == $_SESSION[SESSION_PREFIX . 'user_id']) {
        suPrintJs("
        if($('#data_div_user_group')){
            $('#data_div_user_group').hide();
        }
        if($('#data_div_status')){
            $('#data_div_status').hide();
        }
        if($('#data_div_send_mail_to_user')){
            $('#data_div_send_mail_to_user').hide();
        }
              
    ");
    }
}

//Build the group permissions
if ($tableSegment == 'groups') {
    if (file_exists('includes/permissions-matrix.php')) {
        include('includes/permissions-matrix.php');
    }
}

