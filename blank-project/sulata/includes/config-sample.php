<?php

/*
 * MAGIC PHP
 * Version: #VERSION#
 * Release Date: #RELEASE_DATE#
 */
//Start session
session_start();
//Error reporting
//error_reporting("E_ALL & ~E_NOTICE & ~E_DEPRECATED");
error_reporting("E_ALL & ~E_NOTICE");
//ini_set('display_errors', 1);

/* MISC SETTINGS */
define('LOCAL_URL', 'http://localhost/#PROJECT_NAME#/');
define('WEB_URL', 'http://localhost/#PROJECT_NAME#/');
define('ADMIN_FOLDER', '_admin'); //This is the name of admin folder
define('MAGIC_FOLDER', '_magic'); //This is the name of magic folder
define('SESSION_PREFIX', 'a#SESSION_PREFIX#');
define('UID_LENGTH', 18); //13 from uid, 1 from _, 4 from prefix
define('COOKIE_EXPIRY_DAYS', '30');

/* URLs AND DB SETTINGS */
//If Local
if (!strstr($_SERVER['HTTP_HOST'], ".")) {
    define('PHP_EXTENSION', '.php'); //This will add or remove '.php' in file links

    if (!isset($_GET['debug'])) { //Debug mode can be toggled from querystring as ?debug=0 or ?debug=1
        define('DEBUG', TRUE); //Default debug setting TRUE
    } else {
        define('DEBUG', $_GET['debug']);
    }
    define('BASE_URL', LOCAL_URL);
    define('ADMIN_URL', BASE_URL . ADMIN_FOLDER . '/');
    define('MAGIC_URL', BASE_URL . MAGIC_FOLDER . '/');
    define('UPLOAD_FOLDER', 'files');
    define('UPLOAD_URL', BASE_URL . UPLOAD_FOLDER . '/');
    define('ADMIN_SUBMIT_URL', ADMIN_URL);
    define('PING_URL', BASE_URL . 'sulata/static/ping.html');
    define('NOSCRIPT_URL', BASE_URL . 'sulata/static/no-script.html');
    define('ACCESS_DENIED_URL', BASE_URL . 'sulata/static/access-denied.html');
    define('ADMIN_UPLOAD_PATH', '../files/');
    define('PUBLIC_UPLOAD_PATH', 'files/');
    define('LOCAL', TRUE);
    //API Settings
    define('API_URL', BASE_URL . 'phpMyRest/');
    define('API_KEY', 'x#API_KEY#');
    define('API_DEBUG', FALSE);
    //MySQL DB Settings
    define('DB_HOST', '#DB_HOST#');
    define('DB_NAME', '#DB_NAME#');
    define('DB_USER', '#DB_USER#');
    define('DB_PASSWORD', '#DB_PASSWORD#');
    define('DB_PORT', '#DB_PORT#');
    define('DB_JSON_FIELD', '#DB_JSON_FIELD#');
} else { //If online
    define('PHP_EXTENSION', '.php'); //This will add or remove '.php' in file links

    if (!isset($_GET['debug'])) { //Debug mode can be toggled from querystring as ?debug=0 or ?debug=1
        define('DEBUG', FALSE);
    } else {
        define('DEBUG', $_GET['debug']);
    }
    define('BASE_URL', WEB_URL);
    define('ADMIN_URL', BASE_URL . ADMIN_FOLDER . '/');
    define('MAGIC_URL', BASE_URL . MAGIC_FOLDER . '/');
    define('UPLOAD_FOLDER', 'files');
    define('UPLOAD_URL', BASE_URL . UPLOAD_FOLDER . '/');
    define('ADMIN_SUBMIT_URL', ADMIN_URL);
    define('PING_URL', BASE_URL . 'sulata/static/ping.html');
    define('NOSCRIPT_URL', BASE_URL . 'sulata/static/no-script.html');
    define('ACCESS_DENIED_URL', BASE_URL . 'sulata/static/access-denied.html');
    define('ADMIN_UPLOAD_PATH', '../files/');
    define('PUBLIC_UPLOAD_PATH', 'files/');
    define('LOCAL', FALSE);
    //API Settings
    define('API_URL', BASE_URL . 'phpMyRest/');
    define('API_KEY', 'x#API_KEY#');
    define('API_DEBUG', FALSE);
    //MySQL Settings
    define('DB_HOST', '#DB_HOST#');
    define('DB_NAME', '#DB_NAME#');
    define('DB_USER', '#DB_USER#');
    define('DB_PASSWORD', '#DB_PASSWORD#');
    define('DB_PORT', '#DB_PORT#');
    define('DB_JSON_FIELD', '#DB_JSON_FIELD#');
}
/* DISPLAY ERRORS */
if (DEBUG == TRUE) {
    ini_set('display_errors', 1);
} else {
    ini_set('display_errors', 0);
}
/* DEFAULT ACCESS SETTINGS */
$addAccess = FALSE;
$viewAccess = FALSE;
$previewAccess = FALSE;
$editAccess = FALSE;
$deleteAccess = FALSE;
$duplicateAccess = FALSE;
$downloadAccessCSV = FALSE;
$downloadAccessPDF = FALSE;



/* Builder Configuration File */
include('magic-config.php');
