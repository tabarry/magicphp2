<?php
include('includes/config.php');
include('includes/functions.php');

//DB User
if (isset($_COOKIE['ck_db_user']) && $_COOKIE['ck_db_user'] != '') {
    $ck_db_user = $_COOKIE['ck_db_user'];
} else {
    $ck_db_user = '';
}
//DB password
if (isset($_COOKIE['ck_db_password']) && $_COOKIE['ck_db_password'] != '') {
    $ck_db_password = $_COOKIE['ck_db_password'];
} else {
    $ck_db_password = '';
}
//DB host
if (isset($_COOKIE['ck_db_host']) && $_COOKIE['ck_db_host'] != '') {
    $ck_db_host = $_COOKIE['ck_db_host'];
} else {
    $ck_db_host = 'localhost';
}
//DB port
if (isset($_COOKIE['ck_db_port']) && $_COOKIE['ck_db_port'] != '') {
    $ck_db_port = $_COOKIE['ck_db_port'];
} else {
    $ck_db_port = '3306';
}

//Form fields
$onkeyup = "this.value=doSlugify(this.value, '_');document.getElementById('db_name').value=doSlugify(this.value, '_');document.getElementById('project-name').innerHTML=this.value;";
$fieldsArray = array(
    'project_name' => array('placeholder' => 'Project Name', 'title' => 'Project Name', 'required' => 'yes', 'type' => 'text', 'value' => '', 'onkeyup' => $onkeyup, 'autocomplete' => 'off'),
    'db_name' => array('placeholder' => 'Database Name', 'title' => 'Database Name', 'required' => 'yes', 'type' => 'text', 'value' => '', 'autocomplete' => 'off'),
    'db_user' => array('placeholder' => 'Database User', 'title' => 'Database User', 'required' => 'yes', 'type' => 'text', 'value' => $ck_db_user, 'autocomplete' => 'off'),
    'db_password' => array('placeholder' => 'Database Password', 'title' => 'Database Password', 'type' => 'password', 'value' => $ck_db_password, 'autocomplete' => 'off'),
    'db_host' => array('placeholder' => 'Database Host', 'title' => 'Database Host', 'required' => 'yes', 'type' => 'text', 'value' => $ck_db_host, 'autocomplete' => 'off'),
    'db_port' => array('placeholder' => 'Database Port', 'title' => 'Database Port', 'required' => 'yes', 'type' => 'text', 'value' => $ck_db_port, 'autocomplete' => 'off'),
);

//If form submitted
if (isset($_GET['do']) && $_GET['do'] == 'magic') {

    //Make variables

    $project_name = stripslashes($_POST['project_name']);
    $db_name = stripslashes($_POST['db_name']);
    $test_db_name = 'mysql';
    $db_user = stripslashes($_POST['db_user']);
    $db_password = stripslashes($_POST['db_password']);
    $db_host = stripslashes($_POST['db_host']);
    $db_port = stripslashes($_POST['db_port']);
    $folder_path = '../' . $project_name;
    $config_sample_path = '../' . $project_name . '/sulata/includes/config-sample.php';
    $config_path = '../' . $project_name . '/sulata/includes/config.php';
    $magic_config_sample_path = '../' . $project_name . '/sulata/includes/magic-config-sample.php';
    $magic_config_path = '../' . $project_name . '/sulata/includes/magic-config.php';

    $project_magic_location = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $project_name . '/_magic/login.php?';
    $project_admin_location = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $project_name . '/_admin/login.php?';
    $db_dump_path = 'blank-project/db/magic.sql';

    //Set cookies for next use
    setcookie('ck_db_name', $db_name, $cookieExpiry, '/');
    setcookie('ck_db_user', $db_user, $cookieExpiry, '/');
    setcookie('ck_db_password', $db_password, $cookieExpiry, '/');
    setcookie('ck_db_host', $db_host, $cookieExpiry, '/');
    setcookie('ck_db_port', $db_port, $cookieExpiry, '/');

    //Check if folder exists
    $error = array();
    if (file_exists($folder_path)) {
        array_push($error, sprintf(FOLDER_ALREADY_EXISTS, $project_name));
    }
    //Connect to database
    $link = mysqli_connect($db_host, $db_user, $db_password, $test_db_name, $db_port);
    if (mysqli_connect_errno() > 0) {
        array_push($error, mysqli_connect_error($link));
    }
    //Check db version
    $sql = "SELECT VERSION() AS version";
    $result = mysqli_query($link, $sql);
    $row = mysqli_fetch_array($result);
    $versionInfo = strtolower($row['version']);
    $version = floatval($versionInfo);
    if (stristr($versionInfo, 'mariadb')) {
        $mariaDb = TRUE;
        if ($version < $minMariaDbVersion) {
            array_push($error, sprintf(DB_VERSION_ERROR, $minMysqlVersion, $minMariaDbVersion));
        }
    } else {
        $mariaDb = FALSE;
        if ($version < $minMysqlVersion) {
            array_push($error, sprintf(DB_VERSION_ERROR, $minMysqlVersion, $minMariaDbVersion));
        }
    }
    //Check if db exists
    $sql = "SHOW DATABASES LIKE '" . $db_name . "'";
    $result = mysqli_query($link, $sql);
    if (mysqli_errno($link) > 0) {
        array_push($error, mysqli_error($link));
    }
    $numRows = mysqli_num_rows($result);
    mysqli_free_result($result);
    if ($numRows > 0) {
        array_push($error, sprintf(DATABASE_ALREADY_EXISTS, $db_name));
    }
    if (sizeof($error) == 0) {
        //Create folder
        suCopyFolder('blank-project', $folder_path);
        //Write Magic config
        $magic_config = file_get_contents($magic_config_sample_path);
        $magic_config = str_replace('#VERSION#', FRAMEWORK_VERSION, $magic_config);
        suWriteFile($magic_config_path, $magic_config);
        //Write config
        $config = file_get_contents($config_sample_path);
        $config = str_replace('#PROJECT_NAME#', $project_name, $config);
        $config = str_replace('#SESSION_PREFIX#', uniqid(), $config);
        $config = str_replace('#VERSION#', FRAMEWORK_VERSION, $config);
        $config = str_replace('#RELEASE_DATE#', RELEASE_DATE, $config);
        $config = str_replace('#API_KEY#', uniqid().uniqid(), $config);
        $config = str_replace('#DB_HOST#', $db_host, $config);
        $config = str_replace('#DB_NAME#', $db_name, $config);
        $config = str_replace('#DB_USER#', $db_user, $config);
        $config = str_replace('#DB_PASSWORD#', $db_password, $config);
        $config = str_replace('#DB_PORT#', $db_port, $config);
        if ($mariaDb == TRUE) {
            $config = str_replace('#DB_JSON_FIELD#', 'text', $config);
        } else {
            $config = str_replace('#DB_JSON_FIELD#', 'json', $config);
        }

        suWriteFile($config_path, $config);
        //Create db
        $sql = "CREATE DATABASE " . $db_name;
        mysqli_query($link, $sql);
        //Popuate db
        $sql = "USE " . $project_name;
        mysqli_query($link, $sql);
        $db_dump = file_get_contents($db_dump_path);
        $site_name = str_replace('_', ' ', $_POST['project_name']);
        $db_dump = str_replace("#SITE_NAME#", urlencode(ucwords($site_name)), $db_dump);
        $db_dump = str_replace("#SUPER_USER#", urlencode(SUPER_USER), $db_dump);
        $db_dump = str_replace("#SUPER_USER_LOGIN#", urlencode(SUPER_USER_LOGIN), $db_dump);
        $db_dump = str_replace("#SUPER_USER_PASSWORD#", suCrypt(SUPER_USER_PASSWORD), $db_dump);
        $db_dump = str_replace("#MAGIC_LOGIN#", urlencode(MAGIC_LOGIN), $db_dump);
        $db_dump = str_replace("#ADMIN_USER#", urlencode(ADMIN_USER), $db_dump);
        $db_dump = str_replace("#ADMIN_LOGIN#", urlencode(ADMIN_LOGIN), $db_dump);
        $db_dump = str_replace("#ADMIN_PASSWORD#", suCrypt(ADMIN_PASSWORD), $db_dump);
        $db_dump = str_replace("#MAGIC_LOGIN#", urlencode(MAGIC_LOGIN), $db_dump);
        $db_dump = str_replace("#MAGIC_PASSWORD#", urlencode(MAGIC_PASSWORD), $db_dump);

        if ($mariaDb == TRUE) {
            $db_dump = str_replace("#jsonField#", 'json', $db_dump);
        } else {
            $db_dump = str_replace("#jsonField#", 'text', $db_dump);
        }

        $dbExp = explode(';', $db_dump);
        for ($i = 0; $i <= sizeof($dbExp) - 1; $i++) {
            if ($dbExp[$i] != '') {
                $sql = $dbExp[$i];
                mysqli_query($link, $sql);
            }
        }
        //Finish project
        //$js = "alert('" . sprintf(SUCCESS_MESSAGE, $project_name) . "');window.location.href='index.php?do=nothing;';top.window.location.href='" . $project_magic_location . "'";
        $js = "parent.document.getElementById('suForm').style.display='none';";
        $js .= "parent.document.getElementById('error-area').innerHTML='';";
        $js .= "parent.document.getElementById('success-area').style.display='block';";
        $js .= "parent.document.getElementById('magic-url').href='" . $project_magic_location . "';";
        $js .= "parent.document.getElementById('admin-url').href='" . $project_admin_location . "';";
        $js .= "parent.suForm.reset();";

        suPrintJS($js);
    } else {
        echo suPrintError($error);
    }

    exit;
}
if (isset($_GET['do']) && $_GET['do'] == 'nothing') {
    exit();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $title; ?></title>
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
        <script src="js/magic.js" type="text/javascript"></script>
    </head>
    <body>
    <center>
        <h1><a href="./"><?php echo FRAMEWORK_NAME; ?></a> <small><?php echo FRAMEWORK_VERSION; ?></small></h1>
        <ul id="error-area"></ul>
        <form name="suForm" id="suForm" method="post" target="remote" action="?do=magic">
            <?php suBuildChat($fieldsArray); ?>
            <input name="submit" type="submit" value="Do the Magic"/>
        </form>
        <h3 id="success-area">
            <?php echo SUCCESS_MESSAGE; ?>
            <p><a href="./" alt="Refresh"><img src="images/refresh.png" border="0"/></a></p>
        </h3>
        <footer>
            <?php echo FRAMEWORK_NAME; ?> by <a href="http://www.sulata.com.pk" target="_blank">Sulata iSoft</a>.
        </footer>
    </center>
</body>
<iframe name="remote" id="remote" class="<?php echo $frame; ?>" frameborder="0"></iframe>
</html>
