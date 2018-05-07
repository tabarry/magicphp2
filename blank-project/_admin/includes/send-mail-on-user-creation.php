<?php

    if ($_POST['send_mail_to_user'] == 'Yes') {
        $email = file_get_contents('../sulata/mails/new-user.html');
        $email = str_replace('#NAME#', $_POST['name'], $email);
        $email = str_replace('#SITE_NAME#', $getSettings['site_name'], $email);
        $email = str_replace('#EMAIL#', $_POST['email'], $email);
        $email = str_replace('#USER#', $_SESSION[SESSION_PREFIX . 'user_name'], $email);
        $email = str_replace('#PASSWORD#', $_POST['password'], $email);
        $email = str_replace('#URL#', BASE_URL, $email);
        $subject = sprintf(USER_WELCOME_EMAIL, $getSettings['site_name']);
        //Send mails

        suMail($_POST['email'], $subject, $email, $getSettings['site_name'], $getSettings['site_email'], TRUE);
        //Update send_mail_to_user value to No so that it comes selected as No on update page
        $sql = "UPDATE $table SET data= JSON_REPLACE(data,'$.send_mail_to_user','No') WHERE id='$maxId'";
        suQuery($sql);
}
