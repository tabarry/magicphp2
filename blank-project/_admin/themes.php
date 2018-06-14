<?php
include('../sulata/includes/config.php');
include('../sulata/includes/language.php');
include('../sulata/includes/functions.php');
include('../sulata/includes/get-settings.php');
$showManageIcon = FALSE;

//Check admin login.
//If user is not logged in, send to login page.
checkAdminLogin();
$sessionUserId = $_SESSION[SESSION_PREFIX . 'user_id'];


//Check IP restriction
if (!in_array(ADMIN_GROUP_NAME, $_SESSION[SESSION_PREFIX . 'user_group'])) {
    suCheckIpAccess();
}
//Navigation settings
if (suSegment(1) == 'navigation') {
    $navigationSettings = strtolower($_GET['n']);
    //Switch to right
    if ($navigationSettings == 'left') {
        $sql = "UPDATE " . USERS_TABLE_NAME . " SET data= JSON_REPLACE(data,'$.navigation_settings','Right') WHERE id='" . $_SESSION[SESSION_PREFIX . 'user_id'] . "'";
        $result = suQuery($sql);
        if ($result['affected_rows'] == 1) {
            $_SESSION[SESSION_PREFIX . 'user_navigation'] = 'Right';
            $js = "parent.$('#flip_navigation').hide();parent.window.location.href='" . ADMIN_URL . "themes" . PHP_EXTENSION . "/themes/'";
            suPrintJS($js);
        }
    } else { //Switch to left
        $sql = "UPDATE " . USERS_TABLE_NAME . " SET data= JSON_REPLACE(data,'$.navigation_settings','Left') WHERE id='" . $_SESSION[SESSION_PREFIX . 'user_id'] . "'";
        $result = suQuery($sql);
        if ($result['affected_rows'] == 1) {
            $_SESSION[SESSION_PREFIX . 'user_navigation'] = 'Left';
            $js = "parent.$('#flip_navigation').hide();parent.window.location.href='" . ADMIN_URL . "themes" . PHP_EXTENSION . "/themes/'";
            suPrintJS($js);
        }
    }
    exit();
}
//Sound settings
if (suSegment(1) == 'sound') {
    $soundSettings = $_SESSION[SESSION_PREFIX . 'user_sound'];
    //Switch off
    if ($soundSettings == '1') {
        $sql = "UPDATE " . USERS_TABLE_NAME . " SET data= JSON_REPLACE(data,'$.sound_settings','0') WHERE id='" . $_SESSION[SESSION_PREFIX . 'user_id'] . "'";
        $result = suQuery($sql);
        if ($result['affected_rows'] == 1) {
            $_SESSION[SESSION_PREFIX . 'user_sound'] = '0';
            $js = "parent.$('#sound-icon').removeClass();";
            $js .= "parent.$('#sound-icon').addClass('fa fa-volume-off color-lightGrey');";
            suPrintJS($js);
        }
    } else { //Switch on
        $sql = "UPDATE " . USERS_TABLE_NAME . " SET data= JSON_REPLACE(data,'$.sound_settings','1') WHERE id='" . $_SESSION[SESSION_PREFIX . 'user_id'] . "'";
        $result = suQuery($sql);
        if ($result['affected_rows'] == 1) {
            $_SESSION[SESSION_PREFIX . 'user_sound'] = '1';
            suPlaySound(BASE_URL . 'sulata/sounds/page-load.mp3');
            $js = "parent.$('#sound-icon').removeClass();";
            $js .= "parent.$('#sound-icon').addClass('fa fa-volume-up');";
            suPrintJS($js);
        }
    }
    exit();
}
if ($_GET['theme'] != '') {
    $newTheme = $_GET['theme'];
    $sql = "UPDATE " . USERS_TABLE_NAME . " SET data= JSON_REPLACE(data,'$.theme','" . urlencode($newTheme) . "') WHERE id='" . $_SESSION[SESSION_PREFIX . 'user_id'] . "'";

    suQuery($sql);
    //Set theme in session
    $_SESSION[SESSION_PREFIX . 'user_theme'] = $newTheme;
    //Set theme in cookie
    setcookie('ck_theme', $_SESSION[SESSION_PREFIX . 'user_theme'], time() + (COOKIE_EXPIRY_DAYS * 86400), '/');

    suPrintJs('
            parent.document.getElementById("themeCss").setAttribute("href", "' . BASE_URL . 'sulata/css/admin/themes/' . $newTheme . '/style.css");
        ');
    exit();
}
$title = 'Themes';
$h1 = $title;
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
        <link href="<?php echo BASE_URL; ?>/sulata/css/admin/themes.css" rel="stylesheet" type="text/css"/>


    </head>
    <body>

        <p id="loading-area"></p>
        <div class="container-fluid" id="container-area">
            <div class="row">
                <main>
                    <div class="col-sm-10 content-area" id="working-area">
                        <!-- Add new -->
                        <?php if ($showManageIcon == TRUE) { ?>
                            <a href="<?php echo ADMIN_URL; ?>manage<?php echo PHP_EXTENSION; ?>/<?php echo $table; ?>/" class="btn btn-circle"><i class="fa fa-table"></i></a>
                        <?php } ?>

                        <?php include('includes/header.php'); ?>

                        <div id="error-area">
                            <ul></ul>
                        </div>
                        <div id="message-area">
                            <p></p>
                        </div>
                        <!-- Flip Navigation -->
                        <p>
                        <div class="pretty p-switch size-110" id="pretty_navigation">
                            <?php
                            if (strtolower($_SESSION[SESSION_PREFIX . 'user_navigation']) == 'left') {
                                $navigationChecked = '';
                                $navigationValue = 'Right';
                            } else {
                                $navigationChecked = 'checked="checked"';
                                $navigationValue = 'Left"';
                            }
                            ?>
                            <input title="Flip Navigation" name="flip_navigation" id="flip_navigation" value="<?php echo $navigationValue; ?>" type="checkbox" <?php echo $navigationChecked; ?> onclick="if (this.value == 'Left') {
                                        this.value = 'Right';
                                    } else {
                                        this.value = 'Left';
                                    }
                                    ;
                                    remote.location.href = '<?php echo ADMIN_URL; ?>themes.php/navigation/?n=' + this.value">   

                            <div class="state p-warning">
                                <label>Navigation Placement</label>
                            </div>
                        </div>
                        </p>
                        <div class="row">
                            <?php
                            $dir = '../sulata/css/admin/themes/';
                            $files = scandir($dir);
                            for ($i = 0; $i <= sizeof($files) - 1; $i++) {

                                if ($files[$i][0] != '.' && $files[$i] != MAGIC_THEME) {
                                    ?>
                                    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                        <table width="100%" class="theme-main-table" id="theme-table" onclick="doChangeTheme('<?php echo $files[$i]; ?>');">

                                            <tr>
                                                <td width="70%">
                                                    <table align="center" width="80%">
                                                        <tr>
                                                            <td>&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="theme-inner-table">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td>&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="theme-inner-table">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td>&nbsp;</td>
                                                        </tr>

                                                    </table>
                                                </td>
                                                <td width="20%" class="<?php echo $files[$i]; ?>">
                                                    &nbsp;
                                                </td>
                                                <td width="10%" class="<?php echo $files[$i]; ?>-sidebar">
                                                    &nbsp;
                                                </td>
                                            </tr>


                                        </table>
                                    </div>

                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>

                </main>
                <?php include('includes/sidebar.php'); ?>
            </div>
            <?php include('includes/footer.php'); ?>
        </div>
        <?php include('includes/footer-js.php'); ?>
        <?php suIframe(); ?>
    </body>
</html>