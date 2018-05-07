<?php

include('../sulata/includes/config.php');
/*
 * phpMyRest is a PHP + MySQL RESTful API, developed by Sulata iSoft - www.sulata.com.pk
 * It has been kept as simple as possible to use and supports SQL input.
 * The only thing you need to change in the script is database configurations and API Key below.
 * The variables used are $_POST['sql'], $_POST['api_key'], $_POST['debug']
 * Creating date: January 19, 2016
 */

#############################################
#############################################
/* DATABASE CONFIGURATIONS */
define('DB_NAME', DB_NAME); //Database name
define('DB_USER', DB_USER); //Database user
define('DB_PASSWORD', DB_PASSWORD); //Database password
define('DB_HOST', DB_HOST); //Database host, leave unchanged if in doubt
define('DB_PORT', DB_PORT); //Database port, leave unchanged if in doubt
/* API KEY */
define('API_KEY', API_KEY); //API Key, must be at least 32 characters
#############################################
#############################################

/* * * DO NOT EDIT BELOW THIS LINE * * */

/* SET/INCREASE SERVER TIMEOUT TIME */
set_time_limit(0);

/* ERROR REPORTING */
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

if (isset($_POST['debug'])) {
    $debug = strtolower($_POST['debug']);
    if (($debug == 'true') || (($debug == '1'))) {
        $debug = TRUE;
    } else {
        $debug = FALSE;
    }
} else {
    $debug = FALSE;
}


/* VARIABLES */
//API KEY
if (isset($_POST['api_key'])) {
    $apiKey = $_POST['api_key'];
} else {
    $apiKey = '';
}

//SQL query
if (isset($_POST['sql'])) {
    $sql = $_POST['sql'];
} else {
    $sql = '';
}
//$sql = " SELECT id,title,slug FROM _structure WHERE live='Yes' AND (title LIKE '%aa%' OR slug LIKE '%aa%') ORDER BY title LIMIT 0,10 ";
//Build action: select, insert, update or delete
$do = trim($sql);
$do = explode(' ', $do);
$do = strtolower($do[0]);

$response = array(); //Error, result, record count, message
$response['connect_error'] = 0;
$response['errno'] = 0;

/* ERROR MESSAGES */
define('INVALID_API_KEY', 'Invalid API Key.');


if (!isset($apiKey) || ($apiKey != API_KEY)) {
    exit(INVALID_API_KEY);
} else {

    /* CONNECTION STRING */
    $cn = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

    /* CONNECTION ERRORS */
    if ($debug == TRUE) {
        $response['connect_error'] = mysqli_connect_error();
        $response['connect_errno'] = mysqli_connect_errno();
    } else {
        $response['connect_errno'] = mysqli_connect_errno();
    }
    @mysqli_query($cn, "SET NAMES utf8");
    @mysqli_query($cn, "SET SESSION sql_mode = ''");

    /* SELECT CODE */
    if ($do == 'select') {
        $result = @mysqli_query($cn, $sql);
        if ($debug == TRUE) {
            $response['error'] = @mysqli_error($cn);
            $response['errno'] = @mysqli_errno($cn);
        } else {
            $response['errno'] = @mysqli_errno($cn);
        }
        $response['num_rows'] = @mysqli_num_rows($result);

        //Return result
        while ($row = @mysqli_fetch_assoc($result)) {
            $response['result'][] = $row;
        }

        @mysqli_free_result($result);

        /* INSERT CODE */
    } elseif ($do == 'insert') {
        @mysqli_query($cn, $sql);
        if ($debug == TRUE) {
            $response['error'] = @mysqli_error($cn);
            $response['errno'] = @mysqli_errno($cn);
        } else {
            $response['errno'] = @mysqli_errno($cn);
        }
        //Get duplicate errors
        if (@mysqli_errno($cn) == 1062) {
            $response['errno'] = @mysqli_errno($cn);
        }
        //Get insert ID
        $response['insert_id'] = @mysqli_insert_id($cn);

        /* UPDATE CODE */
    } elseif ($do == 'update') {
        @mysqli_query($cn, $sql);
        if ($debug == TRUE) {
            $response['error'] = @mysqli_error($cn);
            $response['errno'] = @mysqli_errno($cn);
        } else {
            $response['errno'] = @mysqli_errno($cn);
        }
        //Get duplicate errors
        if (@mysqli_errno($cn) == 1062) {
            $response['errno'] = @mysqli_errno($cn);
        }
        //Get affected rows
        $response['affected_rows'] = @mysqli_affected_rows($cn);

        /* DELETE CODE */
    } elseif ($do == 'delete') {
        @mysqli_query($cn, $sql);
        if ($debug == TRUE) {
            $response['error'] = @mysqli_error($cn);
            $response['errno'] = @mysqli_errno($cn);
        } else {
            $response['errno'] = @mysqli_errno($cn);
        }
        $response['affected_rows'] = @mysqli_affected_rows($cn);
        /* CREATE CODE */
    } elseif ($do == 'create') {
        @mysqli_query($cn, $sql);
        if ($debug == TRUE) {
            $response['error'] = @mysqli_error($cn);
            $response['errno'] = @mysqli_errno($cn);
        } else {
            $response['errno'] = @mysqli_errno($cn);
        }
        $response['affected_rows'] = @mysqli_affected_rows($cn);
        /* ALTER CODE */
    } elseif ($do == 'alter') {
        @mysqli_query($cn, $sql);
        if ($debug == TRUE) {
            $response['error'] = @mysqli_error($cn);
            $response['errno'] = @mysqli_errno($cn);
        } else {
            $response['errno'] = @mysqli_errno($cn);
        }
        $response['affected_rows'] = @mysqli_affected_rows($cn);
        /* RENAME CODE */
    } elseif ($do == 'rename') {
        @mysqli_query($cn, $sql);
        if ($debug == TRUE) {
            $response['error'] = @mysqli_error($cn);
            $response['errno'] = @mysqli_errno($cn);
        } else {
            $response['errno'] = @mysqli_errno($cn);
        }
        $response['affected_rows'] = @mysqli_affected_rows($cn);
    } else {
        if ($debug == TRUE) {
            $response['error'] = 'The sql can only be of select, add, update or delete type';
            $response['errno'] = '1000000';
        } else {
            $response['errno'] = '1000000';
        }
    }

    /* OUTPUT JSON */
    header('Content-Type: application/json');
    echo json_encode($response);
}
