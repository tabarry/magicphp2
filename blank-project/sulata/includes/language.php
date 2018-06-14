<?php

/*
 * Define english messages here
 */
define('VALID_DATE', '`%s` must be a valid date like ' . DATE_FORMAT . '.');
define('ERROR_MSG', 'Sorry, this site is temporarily unavailable.');
define('INVALID_ACCESS', 'Invalid access, you are not allowed to access this page.');
define('INVALID_RECORD', 'The record you are trying to access is either invalid or has been deleted.');
define('REQUIRED_FIELD', '`%s` is a required field.');
define('PASSWORD_MATCH_ERROR', 'Your passwords do not match.');
define('INCORRECT_AUTOCOMPLETE_VALUE', 'Please select an auto suggested value for `%s`.');
define('VALID_EMAIL', '`%s` must be a valid email address.');
define('VALID_STRING', '`%s` must be a valid string with letter and spaces only.');
define('VALID_URL', '`%s` must be a valid URL.');
define('VALID_IP', '`%s` must be a valid IP address.');
define('VALID_CC', '`%s` must be a valid credit card number.');
define('VALID_NUMBER', '`%s` must be a valid number.');
define('VALID_INTEGER', '`%s` must be a valid number without decimals.');
define('VALID_PHONE', '`%s` must be a valid phone number, numbers only.');
define('VALID_YEAR', '`%s` must be a valid year, four digits.');

define('VALID_FILE_FORMAT', 'Only `%s` allowed in `%s`.');
define('VALID_FILE_FORMATS', 'Only `%s` allowed.');
define('MYSQL_ERROR', 'Sorry, there has been an error processing the form. Please try again.');
define('GENERIC_ERROR', 'Sorry, there has been an error, you may try again.');
define('CURL_ERROR', 'CURL is not enabled on this server, please contact the administrator.');

define('DUPLICATION_ERROR', 'A record with the same `%s` already exists.');
define('PROFILE_UPDATE', 'Profile updated.');
define('DUPLICATION_ERROR_ON_RESTORE', 'A record with the same `%s` has been created after your deletion, hence this record cannot be restored.');

define('AUTO_PASSWORD_MESSAGE', 'For your convenience, user\'s password has been automatically generated below.');
define('SUCCESS_MESSAGE', 'Record added successfully.');
define('VIEW_FILE', 'View uploaded file..');
define('VALIDATE_EMPTY_CHECKBOX', 'Please select at least one `%s` checkbox.');
define('INVALID_LOGIN', 'Invalid login, please try again.');
define('NO_LOST_PASSWORD_DATA', 'Sorry, the provided email address does not exist in our records.');
define('LOST_PASSWORD_DATA_SENT', 'Your login details have been emailed to you.');
define('LOST_PASSWORD_SUBJECT', 'Your `%s` login information');
define('USER_WELCOME_EMAIL', 'Welcome to `%s`, your login information is here');
define('OLD_PASSWORD_MESSAGE', 'Old password will not be displayed and the password boxes below will show empty.');
define('MIN_ADMIN_ERROR', 'At least one `Admin` level user should remain in the system.');

define('SELF_DELETE_ERROR', 'Sorry, you cannot delete your own record.');
define('CONFIRM_DELETE', 'Are you sure?');
define('CONFIRM_DELETE_RESTORE', 'This record will be deleted but can be restored, till this page is reloaded.');
define('RECORD_RESTORED', 'Record restored successfully.');
define('QUICK_PICK_ERROR', 'The target element can only be a textbox or textarea.');
define('IP_RESTRICTED', 'Access over your IP is not allowed.');
define('DB_VERSION_ERROR', 'Incompatible database. You need at least MySQL %s or MariaDB %s.');
define('MIN_MYSQL_VERSION', '5.7');
define('MIN_MARIADB_VERSION', '10');


define('EDIT_RECORD', 'Edit record.');
define('DELETE_RECORD', 'Delete record.');
define('RECORD_NOT_FOUND', 'There are no records available to display.');
define('SEARCH_NOT_FOUND', "<a href='javascript:history.back(1)' class='color-Crimson'><i class='fa fa-arrow-left'></i></a> <a href='javascript:history.back(1)' class='color-Crimson'>Sorry, no record found.</a>");
define('INACTIVE_MESSAGE', 'Sorry, your access have been disabled. Please contact administrator.');
define('NOTES_UDPATE_MESSAGE', 'Notes updated successfully.');
define('MULTIPLE_LOGIN_ERROR_MESSAGE', 'This user has logged in from another location, hence this session has been logged out.');
define('ALLOWED_ATTACHMENTS_MESSAGE', "The following files were not uploaded due to unallowed file formats.\\n\\n `%s` \\nOnly `%s` formats are allowed.\\n");
define('MAX_UPLOAD_SIZE_MESSAGE', 'Total `%s` collective upload size.');
define('TYPE_FOR_SUGGESTIONS', 'Type for suggestions..');

/** Short Tags * */
define('EDIT', 'Edit');
define('PREVIEW', 'Preview');
define('SINGLE_PRINT', 'Print');
define('DUPLICATE', 'Duplicate');
define('DELETE', 'Delete');
define('DELETE_ALL', 'Delete All');
define('RESTORE', 'Restore');
define('QUICK_PICKS', 'Quick Picks:');
define('CONFIRM_PASSWORD', 'Confirm Password:');
define('CLICK_TO_SELECT', 'Click to select');
define('CLEAR_FIELD', 'Clear field');
define('PREVIEW_PASSWORD', 'Show/Hide Password');
define('MANAGE', 'Manage');
define('ADD', 'Add');
define('SORT', 'Sort');
define('SUBMIT', 'Submit');
define('SAVE_FOR_LATER', 'Save for Later');


define('SERIAL', '&nbsp;');
define('CONFIRM', 'Confirm');
define('FILE_PATH', 'File Path');
define('NOTES_LABEL', 'Write anything here like things to do, things to remember, etc.');
define('DOWNLOAD_CSV', 'Download CSV');
define('DOWNLOAD_PDF', 'Download PDF');

//** Site Specific **/
define('THUMBNAIL_WIDTH', 'Thumbnail Width');
define('THUMBNAIL_HEIGHT', 'Thumbnail Height');
define('IMAGE_WIDTH', 'Image Width');
define('IMAGE_HEIGHT', 'Image Height');
define('NOTIFICATIONS_UPDATE', 'Notifications groups updated.');
define('NOTIFICATION_GROUPS_REQUIRED', 'Please select all notification groups.');
