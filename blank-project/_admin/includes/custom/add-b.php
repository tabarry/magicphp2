<?php

//Password note
if ($tableSegment == 'users') {
    if ($getSettings['autogenerate_user_password'] == '1') {
        echo "<p><i class='fa fa-info-circle color-dodgerBlue'></i> " . AUTO_PASSWORD_MESSAGE . "</p>";
    }
}
