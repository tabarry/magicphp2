<header>
    <div class="band-a">&nbsp;</div>
    <div class="band-b"><h1> <i class="fa fa-magic"></i> ~ <a href="<?php echo MAGIC_URL; ?>"><?php echo MAGIC_TITLE; ?></a> ~ <small><?php echo MAGIC_VERSION; ?></small></h1></div>
    <?php if ($_SESSION[SESSION_PREFIX . 'magic_login'] != '') { ?>
        <div><a href="<?php echo MAGIC_URL; ?>"><i class="fa fa-home"></i> Home</a> . <a href="<?php echo MAGIC_URL; ?>sort.php"><i class="fa fa-sort-alpha-asc"></i> Sort</a> . <a href="<?php echo ADMIN_URL; ?>"><i class="fa fa-user"></i> Admin</a> . <a href="<?php echo MAGIC_URL; ?>login<?php echo PHP_EXTENSION; ?>/logout/" target="remote"><i class="fa fa-power-off"></i> Log Out</a></div>
    <?php } ?>
        <h1 class="heading"><?php echo $h1; ?></h1>
</header>