<?php

if ($tableSegment == 'groups') {

    //Make data variable
    $data['add_access'] = $_POST['add_access'];
    $data['view_access'] = $_POST['view_access'];
    $data['preview_access'] = $_POST['preview_access'];
    $data['delete_access'] = $_POST['delete_access'];
    $data['update_access'] = $_POST['update_access'];
    $data['duplicate_access'] = $_POST['duplicate_access'];
    $data['download_csv_access'] = $_POST['download_csv_access'];
    $data['download_pdf_access'] = $_POST['download_pdf_access'];
    $data['sort_access'] = $_POST['sort_access'];
}
