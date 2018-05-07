<?php

//Only self profile can be updated

if ($table == USERS_TABLE_NAME) {
    if (suSegment(3) == 'profile') {
        if ($rid != $_SESSION[SESSION_PREFIX . 'user_id']) {
            suExit(INVALID_ACCESS);
        }
    }
}

