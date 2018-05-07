<?php

/*
 * SULATA FRAMEWORK
 * This file contains the functions related to this framework.
 * User 'function-this-site.php' for site specific functions
 * Do not remove the inclusion of functions-framework.php file
 */
/* Include the database file */
include('sulata/includes/functions-mysql.php');
/* include validation functions */
include('sulata/includes/functions-validation.php');
/* include core framework functions */
include('sulata/includes/functions-framework.php');
/* include this site only functions */
include('sulata/includes/functions-this-site.php');
/* include PHP Mailer */
require_once('sulata/phpmailer/class.phpmailer.php');
/* Do not delete above this line*/

