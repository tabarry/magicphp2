<?php

//Set session to empty so that new settings can be fetched
if ($table == SETTINGS_TABLE_NAME) {
    $_SESSION[SESSION_PREFIX . 'getSettings'] = '';
}

