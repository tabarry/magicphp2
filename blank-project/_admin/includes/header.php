<?php include('modal.php'); ?>
<header>
    <?php if ($_GET['overlay'] != 1) { ?>
        <div class="band-a">&nbsp;</div>
        <?php if ($_SESSION[SESSION_PREFIX . 'user_id'] != '') { ?>
            <div class="band-b"><h1><a href="<?php echo ADMIN_URL; ?>"> <?php echo $getSettings['site_name']; ?></a>  
                    <?php if ($getSettings['show_profile_picture'] == 1 && $_SESSION[SESSION_PREFIX . 'user_photo'] != '' && file_exists(ADMIN_UPLOAD_PATH . urldecode($_SESSION[SESSION_PREFIX . 'user_photo']))) { ?>
                        <span class="imgProfileThumb" style="background-image: url(<?php echo BASE_URL . 'files/' . urldecode($_SESSION[SESSION_PREFIX . 'user_photo']); ?>);"><?php echo $_SESSION[SESSION_PREFIX . 'user_name']; ?></span>
                    <?php } else { ?>
                        <i class="fa fa-user"></i> 
                        <?php echo $_SESSION[SESSION_PREFIX . 'user_name']; ?>
                    <?php } ?>
                </h1></div>
        <?php } else { ?>
            <div class="band-b"><h1><a href="<?php echo ADMIN_URL; ?>"> <?php echo $getSettings['site_name']; ?></a></div>
        <?php } ?>
    <?php } ?>
    <h1>
        <span class="h1-band" id="page-header">
            <?php echo $h1; ?>
        </span> 
    </h1>
</header>
