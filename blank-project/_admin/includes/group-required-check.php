<?php

//Check if at least one group is entered
if (sizeof($_POST['add_access']) == 0 && sizeof($_POST['preview_access']) == 0 && sizeof($_POST['view_access']) == 0 && sizeof($_POST['update_access']) == 0 && sizeof($_POST['delete_access']) == 0 && sizeof($_POST['duplicate_access']) == 0 && sizeof($_POST['download_csv_access']) == 0 && sizeof($_POST['download_pdf_access']) == 0) {
    $error = sprintf(VALIDATE_EMPTY_CHECKBOX, 'Group');
    suPrintJs('
            parent.suToggleButton(0);
            parent.$("#message-area").hide();
            parent.$("#error-area").show();
            parent.$("#error-area").html("<ul><li>' . $error . '</li></ul>");
            parent.$("html, body").animate({ scrollTop: parent.$("html").offset().top }, "slow");
        ');
}
