<?php

//Generate auto password on add user
if ($mode == 'add') {

    if ($getSettings['autogenerate_user_password'] == '1') {
        $password = suGeneratePassword();
        suPrintJS("if(\$('#password')){\$('#password').val('" . $password . "')};if(\$('#password')){\$('#password" . CONFIRM_PASSWORD_POSTFIX . "').val('" . $password . "')};");
    }
}