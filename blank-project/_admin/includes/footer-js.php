<!--// JS -->
<!-- Bootstrap -->
<script src="<?php echo BASE_URL; ?>sulata/external-scripts/bootstrap-3.3.7/js/bootstrap.min.js" type="text/javascript"></script>


<!-- This Site -->
<script src="<?php echo BASE_URL; ?>sulata/js/common/sulata.js" type="text/javascript"></script>
<script src="<?php echo BASE_URL; ?>sulata/js/common/this-site.js" type="text/javascript"></script>
<script src="<?php echo BASE_URL; ?>sulata/js/admin/magic.js" type="text/javascript"></script>
<script>
    $(document).ready(function () {

        //Equalise columns
        doEqualiseColumns();
        //Preloader
        doPreloader('loading-area', 'container-area');

        //Set active link
        $('#lk_<?php echo suSegment(1); ?>').addClass('sidebar-a-selected');

        //Add value to save hidden field to handle save for later button
        if ($("#save_for_later")) {
            $("#save_for_later").click(function () {
                $('#save_for_later_use').val('Yes');
            });
        }
        if ($("#Submit")) {
            $("#Submit").click(function () {
                $('#save_for_later_use').val('No');
            });
        }
        //Menu placement
        var menuPlacement = '<?php echo strtolower($_SESSION[SESSION_PREFIX . 'user_navigation']); ?>';
        if (menuPlacement.toLowerCase() == 'left') {
            $('#working-area').addClass('pull-right');
            $('#navigation-area').addClass('pull-left');
        }
        //Save on CTRL + S
        suSave('Submit',saveOnCtrlS);
        //Play sound
        
    });
</script>

