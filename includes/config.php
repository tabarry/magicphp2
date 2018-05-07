<?php

ini_set('display_errors', 1);
//VERSION INFO
define('FRAMEWORK_NAME', 'Magic PHP');
define('FRAMEWORK_VERSION', '2.0');
define('RELEASE_DATE', 'May 3, 2018');
//Debug
define('DUBUG', FALSE);

//Variables
$title = FRAMEWORK_NAME . ' ' . FRAMEWORK_VERSION;
$minMysqlVersion = '5.7';
$minMariaDbVersion = '10';
$cookieExpiry = time() + (30 * 86400);
if (DUBUG == TRUE) {
    $frame = 'frame-show';
} else {
    $frame = 'frame-hide';
}

//Logins
define('MAGIC_LOGIN', 'magic@sulata.com.pk');
define('MAGIC_PASSWORD', 'magic');
define('SUPER_USER', 'Superman');
define('SUPER_USER_LOGIN', 'superman@sulata.com.pk');
define('SUPER_USER_PASSWORD', 'krypton');
define('ADMIN_USER', 'Admin');
define('ADMIN_LOGIN', 'admin@sulata.com.pk');
define('ADMIN_PASSWORD', 'pepper#123');

//Messages
define('FOLDER_ALREADY_EXISTS', 'A folder with the name `%s` already exists.');
define('DATABASE_ALREADY_EXISTS', 'A database with the name `%s` already exists.');
define('DB_VERSION_ERROR', 'Minimum version requirement for MySQL is `%s` or MariaDB is `%s`.');
define('SUCCESS_MESSAGE', 'Project `<span id="project-name"></span>` created successfully.<br>Go to project\'s <a id="magic-url" href="">`_magic`</a> folder or <a id="admin-url" href="">`_admin`</a> folder.<br>_magic Login:`'.MAGIC_LOGIN.'` Password: `'.MAGIC_PASSWORD.'`.<br>_admin Login: `'.ADMIN_LOGIN.'` Password: `'.ADMIN_PASSWORD.'`.');
