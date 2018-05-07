<?php

//Check database version compatibility//
$sql = "SELECT VERSION() AS version";
$result = suQuery($sql);
$row = $result['result'][0];
$versionInfo = strtolower($row['version']);
$version = floatval($versionInfo);
if (stristr($versionInfo, 'mariadb')) {
    $mariaDb = TRUE;
    if ($version < MIN_MARIADB_VERSION) {
        exit(sprintf(DB_VERSION_ERROR, MIN_MYSQL_VERSION, MIN_MARIADB_VERSION));
    }
} else {
    $mariaDb = FALSE;
    if ($version < MIN_MYSQL_VERSION) {
        exit(sprintf(DB_VERSION_ERROR, MIN_MYSQL_VERSION, MIN_MARIADB_VERSION));
    }
}
//==
