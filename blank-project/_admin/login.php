<?php
include('../sulata/includes/config.php');
include('../sulata/includes/language.php');
include('../sulata/includes/functions.php');
include('../sulata/includes/get-settings.php');


$showManageIcon = FALSE;

$do = suSegment(1);
$h1 = 'Login';

//Set redirect URL
if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != '') {
    $goto = $_SERVER['QUERY_STRING'];
} else {
    $goto = '';
}

//Logout
if ($do == 'logout') {
    //Insert usage log
    suMakeUsageLog('logout');

    //Destroy this session
    $_SESSION[SESSION_PREFIX . 'admin_login'] = '';
    //Unset login sessions
    $_SESSION[SESSION_PREFIX . 'user_id'] = '';
    $_SESSION[SESSION_PREFIX . 'user_name'] = '';
    $_SESSION[SESSION_PREFIX . 'user_email'] = '';
    $_SESSION[SESSION_PREFIX . 'user_photo'] = '';
    $_SESSION[SESSION_PREFIX . 'user_theme'] = '';
    $_SESSION[SESSION_PREFIX . 'user_group'] = '';
    $_SESSION[SESSION_PREFIX . 'getSettings'] = '';
    //Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/login-c.php')) {
        include('includes/custom/login-c.php');
    }
    session_unset();
}


//Login
if ($do == 'login') {

    $sql = "SELECT id, " . suJsonExtract('data', 'name') . "," . suJsonExtract('data', 'email') . "," . suJsonExtract('data', 'photo') . "," . suJsonExtract('data', 'theme') . "," . suJsonExtract('data', 'sound_settings') . "," . suJsonExtract('data', 'navigation_settings') . "," . suJsonExtract('data', 'user_group') . " FROM " . USERS_TABLE_NAME . " WHERE " . suJsonExtract('data', 'email', FALSE) . "='" . suPost('email') . "' AND " . suJsonExtract('data', 'password', FALSE) . "='" . suCrypt($_POST['password']) . "' AND " . suJsonExtract('data', 'status', FALSE) . "='" . suStrip('Active') . "' AND Live='Yes' LIMIT 0,1";

    //Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/login-a.php')) {
        include('includes/custom/login-a.php');
    }

    $result = suQuery($sql);
    $result['result'] = suUnstrip($result['result']);
    $numRows = $result['num_rows'];


    if ($numRows == 1) {
        //Insert usage log
        suMakeUsageLog('login-success');

        //set sessions
        $_SESSION[SESSION_PREFIX . 'admin_login'] = '1';
        $_SESSION[SESSION_PREFIX . 'user_id'] = $result['result'][0]['id'];
        $_SESSION[SESSION_PREFIX . 'user_name'] = $result['result'][0]['name'];
        $_SESSION[SESSION_PREFIX . 'user_email'] = $result['result'][0]['email'];
        $_SESSION[SESSION_PREFIX . 'user_photo'] = $result['result'][0]['photo'];
        $_SESSION[SESSION_PREFIX . 'user_theme'] = $result['result'][0]['theme'];
        $_SESSION[SESSION_PREFIX . 'user_sound'] = $result['result'][0]['sound_settings'];
        $_SESSION[SESSION_PREFIX . 'user_navigation'] = $result['result'][0]['navigation_settings'];
        $_SESSION[SESSION_PREFIX . 'user_group'] = $result['result'][0]['user_group'];

        //Any actions desired at this point should be coded in this file
        if (file_exists('includes/custom/login-b.php')) {
            include('includes/custom/login-b.php');
        }
        //Update IP in user table
        $sql = "UPDATE " . USERS_TABLE_NAME . " SET data= JSON_REPLACE(data,'$.ip','" . suStrip($_SERVER['REMOTE_ADDR']) . "') WHERE id='" . $_SESSION[SESSION_PREFIX . 'user_id'] . "'";
        suQuery($sql);

        //Set theme in cookie
        setcookie('ck_theme', $_SESSION[SESSION_PREFIX . 'user_theme'], time() + (COOKIE_EXPIRY_DAYS * 86400), '/');


//echo suDecrypt($_POST['redirect']);
        //If referer is not set, go to admin home
        if ($_POST['redirect'] == '') {
            $goto = ADMIN_URL . '?sound=welcome';
        } else {
            $goto = suDecrypt($_POST['redirect']);
            //If refer set, go to referer
            if (!strstr($goto, '?')) {
                $goto = $goto . '?sound=welcome';
            } else {
                $goto = $goto . '&sound=welcome';
            }
        }


        suPrintJS("parent.suRedirect('" . $goto . "');");
        exit;
    } else {

        //Insert usage log
        suMakeUsageLog('login-failure');

        $error = INVALID_LOGIN;

        suPrintJs('
            parent.suToggleButton(0);
            parent.$("#message-area").hide();
            parent.$("#error-area").show();
            parent.$("#error-area").html("<ul><li>' . $error . '</li></ul>");
            parent.$("html, body").animate({ scrollTop: parent.$("html").offset().top }, "slow");
        ');
        exit;
    }
    exit;
}

//Retrieve
if ($do == 'retrieve-password') {
    $sql = "SELECT id," . suJsonExtract('data', 'email') . "," . suJsonExtract('data', 'password') . " FROM " . USERS_TABLE_NAME . " WHERE " . suJsonExtract('data', 'email', FALSE) . "='" . suPost('email') . "' AND Live='Yes' ";
    //Any actions desired at this point should be coded in this file
    if (file_exists('includes/custom/login-b.php')) {
        include('includes/custom/login-b.php');
    }
    $result = suQuery($sql);
    if ($result['num_rows'] == 1) {

        //Insert usage log
        suMakeUsageLog('retrieve-password-success');
        $result['result'] = suUnstrip($result['result']);
        $row = $result['result'][0];
        $email = file_get_contents('../sulata/mails/lost-password.html');
        $email = str_replace('#NAME#', 'Administrator', $email);
        $email = str_replace('#SITE_NAME#', $getSettings['site_name'], $email);
        $email = str_replace('#EMAIL#', $row['email'], $email);
        $email = str_replace('#URL#', ADMIN_URL, $email);
        $email = str_replace('#PASSWORD#', suDecrypt($row['password']), $email);
        $subject = sprintf(LOST_PASSWORD_SUBJECT, $getSettings['site_name']);
        //Send mails
        suMail($row['email'], $subject, $email, $getSettings['site_name'], $getSettings['site_email'], TRUE);

//Redirect
        suPrintJS("alert('" . LOST_PASSWORD_DATA_SENT . "');parent.suRedirect('" . ADMIN_URL . "login" . PHP_EXTENSION . "/');");
    } else {

        //Insert usage log
        suMakeUsageLog('retrieve-password-failure');

        $vError = array();
        $vError[] = NO_LOST_PASSWORD_DATA;
        suValdationErrors($vError);
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $getSettings['site_name'] . ' - ' . $h1; ?></title>
        <?php include('includes/head.php'); ?>
        <script type="text/javascript">

            $(document).ready(function () {
                //Keep session alive
                $(function () {
                    window.setInterval("suStayAlive('<?php echo PING_URL; ?>')", 300000);
                });
                //Disable submit button
                suToggleButton(1);

            });
            //Set variable TRUE to save on CTRL + S
            var saveOnCtrlS = false;
        </script> 

    </head>
    <body>

        <p id="loading-area"></p>
        <div class="container-fluid" id="container-area">
            <div class="row">
                <main>
                    <div class="col-sm-12 content-area" id="working-area">

                        <?php
                        if ($do == 'retrieve') {
                            $h1 = 'Retrieve Password';
                        } else {
                            $h1 = 'Login';
                        }
                        include('includes/header.php');
                        ?>
                        <div id="error-area">
                            <ul></ul>
                        </div>    
                        <div id="message-area">
                            <p></p>
                        </div>
                        <div class="row">
                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                &nbsp;
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                <p>&nbsp;</p>
                                <?php if ($do == 'retrieve') { ?>
                                    <form class="form-horizontal" name="suForm" id="suForm" method="post" action="<?php echo ADMIN_URL; ?>login<?php echo PHP_EXTENSION; ?>/retrieve-password/" target="remote">

                                        <div class="form-group">
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                <p class="color-gray">Please provide your email address in the textbox below.</p>
                                                <?php
                                                $arg = array('type' => 'email', 'name' => 'email', 'id' => 'email', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Email', 'required' => 'required');
                                                echo suInput('input', $arg);
                                                ?>
                                                <div>&nbsp;</div>
                                            </div>

                                        </div>
                                        <div class="form-group">
                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

                                                <a href="<?php echo ADMIN_URL; ?>login<?php echo PHP_EXTENSION; ?>/">&laquo; Back to Login</a>
                                                <div>&nbsp;</div>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

                                                <?php
                                                $arg = array('type' => 'submit', 'name' => 'Submit', 'id' => 'Submit', 'class' => 'btn btn-theme pull-right');
                                                echo suInput('button', $arg, "<i class='fa fa-check'></i>", TRUE);
                                                ?>
                                                <div>&nbsp;</div>
                                            </div>
                                        </div>

                                    </form>
                                <?php } else { ?>
                                    <form class="form-horizontal" name="suForm" id="suForm" method="post" action="<?php echo ADMIN_URL; ?>login<?php echo PHP_EXTENSION; ?>/login/" target="remote">
                                        <?php if (suSegment(1) == 'multilogin') { ?>
                                            <p class="color-Crimson"><?php echo MULTIPLE_LOGIN_ERROR_MESSAGE; ?></p>
                                        <?php } ?>
                                        <div class="form-group">
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                <?php
                                                $arg = array('type' => 'email', 'name' => 'email', 'id' => 'email', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Email', 'required' => 'required');
                                                echo suInput('input', $arg);
                                                ?>
                                                <div>&nbsp;</div>
                                            </div>
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                <?php
                                                $arg = array('type' => 'password', 'name' => 'password', 'id' => 'password', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Password', 'required' => 'required');
                                                echo suInput('input', $arg);
                                                ?>
                                                <div>&nbsp;</div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                <a href="<?php echo ADMIN_URL; ?>login<?php echo PHP_EXTENSION; ?>/retrieve/">Lost Password?</a>
                                                <div>&nbsp;</div>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

                                                <?php
                                                //Redirect URL
                                                $arg = array('type' => 'hidden', 'name' => 'redirect', 'id' => 'redirect', 'value' => $goto);
                                                echo suInput('input', $arg);

                                                $arg = array('type' => 'submit', 'name' => 'Submit', 'id' => 'Submit', 'class' => 'btn btn-theme pull-right');
                                                echo suInput('button', $arg, "<i class='fa fa-key'></i>", TRUE);
                                                ?>
                                                <div>&nbsp;</div>
                                            </div>
                                        </div>


                                    </form>
                                <?php } ?>
                            </div>
                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                &nbsp;
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            <?php include('includes/footer.php'); ?>
        </div>
        <?php include('includes/footer-js.php'); ?>
        <?php suIframe(); ?>
    </body>
</html>