<?php

//Check if at least one group is entered
if ($table == 'groups') {   
    if (file_exists('includes/group-required-check.php')) {
        include('includes/group-required-check.php');
    }
}
