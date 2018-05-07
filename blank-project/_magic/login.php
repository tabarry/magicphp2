<?php
include('../sulata/includes/config.php');
include('../sulata/includes/language.php');
include('../sulata/includes/functions.php');
include('../sulata/includes/get-settings.php');

//Destroy this session
$_SESSION[SESSION_PREFIX . 'magic_login'] = '';

$do = suSegment(1);

//Login
if ($do == 'login') {
    if ($getSettings['magic_login'] == $_POST['email'] && $getSettings['magic_password'] == $_POST['password']) {
        $_SESSION[SESSION_PREFIX . 'magic_login'] = '1';
        suPrintJS("parent.suRedirect('" . MAGIC_URL . "');");
        exit;
    } else {
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
//Logout
if ($do == 'logout') {
    $_SESSION[SESSION_PREFIX . 'magic_login'] = '';
    suPrintJS("parent.suRedirect('" . MAGIC_URL . "login" . PHP_EXTENSION . "/');");
    exit;
}
//Retrieve
if ($do == 'retrieve-password') {
    if ($getSettings['magic_login'] != '' && $getSettings['magic_password'] != '' && $getSettings['magic_login'] == $_POST['email']) {
        $email = file_get_contents('../sulata/mails/lost-password.html');
        $email = str_replace('#NAME#', 'Administrator', $email);
        $email = str_replace('#SITE_NAME#', $getSettings['site_name'], $email);
        $email = str_replace('#EMAIL#', $getSettings['magic_login'], $email);
        $email = str_replace('#URL#', MAGIC_URL, $email);
        $email = str_replace('#PASSWORD#', $getSettings['magic_password'], $email);
        $subject = sprintf(LOST_PASSWORD_SUBJECT, $getSettings['site_name']);
        //Send mails
        suMail(suUnstrip($getSettings['magic_login']), $subject, $email, $getSettings['site_name'], $getSettings['site_email'], TRUE);
//Redirect
        suPrintJS("alert('" . LOST_PASSWORD_DATA_SENT . "');parent.suRedirect('" . MAGIC_URL . "login" . PHP_EXTENSION . "/');");
    } else {
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
        <title><?php echo MAGIC_TITLE . ' ' . MAGIC_VERSION; ?></title>
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
        </script>

    </head>
    <body>

        <div class="container">
            <div class="row">
                <main>
                    <div class="col-sm-12 content-area">

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
                                <?php if ($do == 'retrieve') { ?>
                                    <form class="form-horizontal" name="suForm" id="suForm" method="post" action="<?php echo MAGIC_URL; ?>login<?php echo PHP_EXTENSION; ?>/retrieve-password/" target="remote">

                                        <div class="form-group">
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                <p class="color-gray">Please provide your email address in the textbox below.</p>
                                                <?php
                                                $arg = array('type' => 'email', 'name' => 'email', 'id' => 'email', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Email', 'required' => 'required');
                                                echo suInput('input', $arg);
                                                ?>
                                                <div>&nbsp</div>
                                            </div>

                                        </div>
                                        <div class="form-group">
                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

                                                <a href="<?php echo MAGIC_URL; ?>login<?php echo PHP_EXTENSION; ?>/">&laquo; Back to Login</a>
                                                <div>&nbsp</div>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

                                                <?php
                                                $arg = array('type' => 'submit', 'name' => 'Submit', 'id' => 'Submit', 'class' => 'btn btn-theme pull-right');
                                                echo suInput('button', $arg, "<i class='fa fa-check'></i>", TRUE);
                                                ?>
                                                <div>&nbsp</div>
                                            </div>
                                        </div>


                                    </form>
                                <?php } else { ?>
                                    <form class="form-horizontal" name="suForm" id="suForm" method="post" action="<?php echo MAGIC_URL; ?>login<?php echo PHP_EXTENSION; ?>/login/" target="remote">

                                        <div class="form-group">
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                <?php
                                                $arg = array('type' => 'email', 'name' => 'email', 'id' => 'email', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Email', 'required' => 'required');
                                                echo suInput('input', $arg);
                                                ?>
                                                <div>&nbsp</div>
                                            </div>
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                <?php
                                                $arg = array('type' => 'password', 'name' => 'password', 'id' => 'password', 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Password', 'required' => 'required');
                                                echo suInput('input', $arg);
                                                ?>
                                                <div>&nbsp</div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                <a href="<?php echo MAGIC_URL; ?>login<?php echo PHP_EXTENSION; ?>/retrieve/">Lost Password?</a>
                                                <div>&nbsp</div>
                                            </div>
                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

                                                <?php
                                                $arg = array('type' => 'submit', 'name' => 'Submit', 'id' => 'Submit', 'class' => 'btn btn-theme pull-right');
                                                echo suInput('button', $arg, "<i class='fa fa-key'></i>", TRUE);
                                                ?>
                                                <div>&nbsp</div>
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
    </body>
</html>
<?php suIframe(); ?>