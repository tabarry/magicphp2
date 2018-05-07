<?php include('modal.php'); ?>
<header>
    <?php if ($_GET['overlay'] != 1) { ?>
        <div class="band-a">&nbsp;</div>
        <div class="band-b"><h1><a href="<?php echo ADMIN_URL; ?>">~ <?php echo $getSettings['site_name']; ?></a> ~ <small><?php echo $getSettings['site_tagline']; ?> ~ <small><?php echo $_SESSION[SESSION_PREFIX . 'user_name']; ?></small></small></h1></div>
    <?php } ?>
    <h1><span class="h1-band" id="page-header"><?php echo $h1; ?></span></h1>
</header>
