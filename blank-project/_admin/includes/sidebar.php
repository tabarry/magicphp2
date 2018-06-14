<?php if ($_GET['overlay'] != 1) { ?>
    <div class="col-sm-2 sidebar-area" style="padding:0px;" id="navigation-area">
        <nav>
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td width="10%">
                        &nbsp;
                    </td>
                    <td width="75%" id="sidebar-nav">
                        <div>&nbsp;</div>
                        <?php if ($_SESSION[SESSION_PREFIX . 'admin_login'] != '') { ?>
                            <ul>
                                <li>
                                    <a id="lk_home" href="<?php echo ADMIN_URL; ?>index<?php echo PHP_EXTENSION; ?>/home/"><i class="fa fa-home"></i>&nbsp;&nbsp;Home</a>

                                </li>
                                <li><a id="lk_profile" href="<?php echo ADMIN_URL; ?>update<?php echo PHP_EXTENSION; ?>/users/<?php echo $_SESSION[SESSION_PREFIX . 'user_id']; ?>/profile/"><i class="fa fa-user"></i>&nbsp;&nbsp;Profile</a></li>
                                <li><a id="lk_themes" href="<?php echo ADMIN_URL; ?>themes<?php echo PHP_EXTENSION; ?>/themes/"><i class="fa fa-picture-o"></i>&nbsp;&nbsp;Themes</a></li>
                                <?php
                                $soundSettings = $_SESSION[SESSION_PREFIX . 'user_sound'];

                                if ($soundSettings == '1') {
                                    $soundIcon = 'fa fa-volume-up';
                                } else {
                                    $soundIcon = 'fa fa-volume-off  color-lightGrey';
                                }
                                ?>
                                <li><a id="lk_sound" target="remote" href="<?php echo ADMIN_URL; ?>themes<?php echo PHP_EXTENSION; ?>/sound/"><i class="fa <?php echo $soundIcon; ?>" id="sound-icon"></i>&nbsp;&nbsp;Sounds</a></li>
                                <?php if ($_SESSION[SESSION_PREFIX . 'user_id'] == ADMIN_1) { ?>
                                    <li><a id="lk__settings" href="<?php echo ADMIN_URL; ?>manage<?php echo PHP_EXTENSION; ?>/_settings/"><i class="fa fa-cogs"></i>&nbsp;&nbsp;Settings</a></li>
                                <?php } ?>
                                <li class="hr"></li>
                                <?php
                                suBuildFormLinks();
                                ?>
                                <li class="hr"></li>
                                <li><a href="<?php echo ADMIN_URL; ?>login<?php echo PHP_EXTENSION; ?>/logout/"><i class="fa fa-power-off"></i>&nbsp;&nbsp;Log Out</a>        
                            </ul>
                        <?php } ?>
                        <div>&nbsp;</div>
                    </td>
                    <td width="5%">
                        &nbsp;
                    </td>
                    <td width="10%" id="sidebar-bg">
                        &nbsp;
                    </td>
                </tr>
            </table>

        </nav>
    </div>
<?php } ?>
