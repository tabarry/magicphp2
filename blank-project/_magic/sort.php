<?php
include('../sulata/includes/config.php');
include('../sulata/includes/language.php');
include('../sulata/includes/functions.php');
include('../sulata/includes/get-settings.php');

//Check magic login.
//If user is not logged in, send to login page.
checkMagicLogin();
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

            //Sort
            $(function () {
                $("#sortable").sortable();
                $("#sortable").disableSelection();
            });
        </script>

    </style>
</head>
<body>

    <div class="container">
        <div class="row">
            <main>
                <div class="col-sm-12 content-area">
                    <!-- Add new -->
                    <a href="<?php echo MAGIC_URL; ?>add<?php echo PHP_EXTENSION; ?>/" class="btn btn-circle"><i class="fa fa-plus"></i></a>
                    <?php
                    $h1 = 'Sort Pages';
//Include header
                    include('includes/header.php');
                    ?>
                    <p>&nbsp;</p>
                    <form name="suForm" method="post" id="suForm" target="remote" action="<?php echo MAGIC_URL; ?>remote<?php echo PHP_EXTENSION; ?>/sort/">
                    <ul id="sortable">
                        <?php
                        $sql = "SELECT id,title FROM " . STRUCTURE_TABLE_NAME . " WHERE live='Yes' AND display='Yes' ORDER BY sort_order,title";
                        $result = suQuery($sql);
                        $numRows = $result['num_rows'];
                        if ($numRows > 0) {
                            $row = $result['result'];
                            foreach ($row as $value) {
                                ?>
                        <li class="ui-state-default"><i class="fa fa-th color-lightSlateGray"></i> <?php echo suUnstrip($value['title']);?><input type="hidden" name="sort_order[]" value="<?php echo $value['id'];?>"/></li>
                                <?php
                                
                            }
                        }
                        ?>
                        </ul>
                        <p class="pull-right">
                        <button type="submit" id="Submit" name="Submit" class="btn btn-theme"><i class="fa fa-check"></i></button>
                        </p>
                    </form>
                        <p>&nbsp;</p>

                    </div>
                </main>
            </div>
            <?php include('includes/footer.php'); ?>
        </div>
        <?php include('includes/footer-js.php'); ?>
    </body>
    </html>
    <?php suIframe(); ?>