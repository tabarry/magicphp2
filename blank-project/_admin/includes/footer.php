<?php if ($_GET['overlay'] != 1) { ?>
    <footer>
        <a href="<?php echo $getSettings['site_footer_link']; ?>"><?php echo $getSettings['site_footer']; ?></a>
    </footer>
<?php } ?>
<!-- Add anything in HTML DOM at this point -->
<div id="last-dom"></div>
<?php
$soundSettings = $_SESSION[SESSION_PREFIX . 'user_sound'];
if ($soundSettings == '1') {
    if ($_GET['sound'] == 'welcome') {
        suPlaySound(BASE_URL . 'sulata/sounds/welcome-man.mp3');
    } else {
        suPlaySound(BASE_URL . 'sulata/sounds/page-load.mp3');
    }
}
