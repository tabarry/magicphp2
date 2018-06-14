<?php

/*
 * MAGIC PHP
 * Version: 2.0
 * Release Date: May 3, 2018
 */
//Start session
session_start();
//Error reporting
//error_reporting("E_ALL & ~E_NOTICE & ~E_DEPRECATED");
error_reporting("E_ALL & ~E_NOTICE");
//ini_set('display_errors', 1);

/* MISC SETTINGS */
define('LOCAL_URL', 'http://localhost/karafit/');
define('WEB_URL', 'http://localhost/karafit/');
define('ADMIN_FOLDER', '_admin'); //This is the name of admin folder
define('MAGIC_FOLDER', '_magic'); //This is the name of magic folder
define('SESSION_PREFIX', 'a5af073784b815_');
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
    define('API_KEY', 'x5af073784b8275af073784b82c');
    define('API_DEBUG', FALSE);
    //MySQL DB Settings
    define('DB_HOST', '127.0.0.1');
    define('DB_NAME', 'karafit');
    define('DB_USER', 'root');
    define('DB_PASSWORD', 'root');
    define('DB_PORT', '3306');
    define('DB_JSON_FIELD', 'text');
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
    define('API_KEY', 'x5af073784b8275af073784b82c');
    define('API_DEBUG', FALSE);
    //MySQL Settings
    define('DB_HOST', '127.0.0.1');
    define('DB_NAME', 'karafit');
    define('DB_USER', 'root');
    define('DB_PASSWORD', 'root');
    define('DB_PORT', '3306');
    define('DB_JSON_FIELD', 'text');
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

/* INSTANTIATE DOCUMENT READY SESSION */
$documentReady = '';
$documentReadyUid = 'document_ready_js_' . uniqid();
$GLOBALS[$documentReadyUid] = '';

/* Builder Configuration File */
include('magic-config.php');
