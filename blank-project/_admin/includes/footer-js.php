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
<?php if (suSegment(3) == 'profile') { ?>
            $('#lk_profile').addClass('sidebar-a-selected');
<?php } else { ?>
            $('#lk_<?php echo suSegment(1); ?>').addClass('sidebar-a-selected');
<?php } ?>

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
        suSave('Submit', saveOnCtrlS);
        //PHP generated $documentReady;
<?php echo $GLOBALS[$documentReadyUid]; ?>

        //Datepicker
        //doChangeToDateBox('<?php echo $date_format; ?>');
        //Only integers allowed
        doOnlyIntegers();
        //Only integers decimal values
        doOnlyDecimals();
        //Document ready built from PHP
<?php echo $documentReady; ?>

        //Datepicker for addmore dateboxes
        $('body').on('focus', ".dateBox2", function () {//Dynamic date picker starts

            oldId = this.id;//Get id
            $(this).removeAttr('id');
            $(this).datepicker(
                    {
                        dateFormat: '<?php echo $date_format; ?>',
                        changeMonth: true,
                        changeYear: true,
                        yearRange: 'c-100:c+10',
                        onSelect: function () {
                            $(this).attr('id', oldId);
                        },
                        onBlur: function () {
                            $(this).attr('id', oldId);
                        }
                    }

            );

        });//Dynamic date picker ends



    });


    //Menu placement
<?php if ($_GET['overlay'] == 1) { ?>
        var menuPlacement = 'right';
<?php } else { ?>
        var menuPlacement = '<?php echo $getSettings['menu_placement']; ?>';
<?php } ?>
    //To fix form opening in overlay
    if (menuPlacement.toLowerCase() == 'left') {
        $('#working-area').addClass('pull-right');
        $('#navigation-area').addClass('pull-left');
    }
</script>