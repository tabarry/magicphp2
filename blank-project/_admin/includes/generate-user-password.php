<?php
//Generate auto password on add user
if ($mode == 'add') {
    $password = suGeneratePassword();
    suPrintJS("if(\$('#password')){\$('#password').val('" . $password . "')};if(\$('#password')){\$('#password" . CONFIRM_PASSWORD_POSTFIX . "').val('" . $password . "')};");
}