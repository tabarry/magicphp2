<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!--// CSS -->
<!-- Chosen -->
<link href="<?php echo BASE_URL; ?>sulata/external-scripts/chosen/bootstrap-chosen.css" rel="stylesheet" type="text/css"/>
<!-- Bootstrap -->
<link href="<?php echo BASE_URL; ?>sulata/external-scripts/bootstrap-3.3.7/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<!-- Font Awesome -->
<link href="<?php echo BASE_URL; ?>sulata/external-scripts/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<!-- JQUERY UI -->
<link href="<?php echo BASE_URL; ?>sulata/external-scripts/jquery-ui-1.12.1.custom/jquery-ui.css" rel="stylesheet" type="text/css"/>
<!-- Pretty Checkbox -->
<link href="<?php echo BASE_URL; ?>sulata/external-scripts/pretty-checkbox/pretty-checkbox.min.css" rel="stylesheet" type="text/css"/>
<!-- CK Editor -->
<script src="<?php echo BASE_URL; ?>sulata/external-scripts/ckeditor/ckeditor.js"></script>
<!-- Parsley -->
<link href="<?php echo BASE_URL; ?>sulata/external-scripts/parsley/parsley.css" rel="stylesheet" type="text/css"/>
<!-- Google Fonts -->

<script>
    if (navigator.onLine) {
        //document.writeln("<link href=\"https://fonts.googleapis.com/css?family=Raleway\" rel=\"stylesheet\">");
    }
</script>


<!-- Admin -->
<link href="<?php echo BASE_URL; ?>sulata/css/admin/style.css" rel="stylesheet" type="text/css"/>
<!-- Favicon -->
<link rel="icon" href="<?php echo BASE_URL; ?>sulata/css/favicon.png">
<?php
if ($_COOKIE['ck_theme'] != '') {
    $_SESSION[SESSION_PREFIX . 'user_theme'] = $_COOKIE['ck_theme'];
}
if ($_SESSION[SESSION_PREFIX . 'user_theme'] == '') {
    $_SESSION[SESSION_PREFIX . 'user_theme'] = 'default';
}
?>
<link id="themeCss" href="<?php echo BASE_URL; ?>sulata/css/admin/themes/<?php echo $_SESSION[SESSION_PREFIX . 'user_theme']; ?>/style.css" rel="stylesheet" type="text/css"/>
<!-- Sortable -->
<link href="<?php echo BASE_URL; ?>sulata/css/admin/sortable.css" rel="stylesheet" type="text/css"/>

<!-- CSS //-->
<!-- // JS -->
<!-- JQuery -->
<script src="<?php echo BASE_URL; ?>sulata/external-scripts/jquery-3.2.1/jquery-3.2.1.min.js" type="text/javascript"></script>
<!-- Sortable -->
<script src="<?php echo BASE_URL; ?>sulata/external-scripts/jquery-ui-1.12.1.custom/jquery-ui.js" type="text/javascript"></script>
<!-- Parsley -->
<script src="<?php echo BASE_URL; ?>sulata/external-scripts/parsley/parsley.min.js" type="text/javascript"></script>
<!-- Chosen -->
<script src="<?php echo BASE_URL; ?>sulata/external-scripts/chosen/chosen.jquery.js" type="text/javascript"></script>
<!-- Site Header -->
<script src="<?php echo BASE_URL; ?>sulata/js/admin/header.js" type="text/javascript"></script>
<!-- JS // -->
