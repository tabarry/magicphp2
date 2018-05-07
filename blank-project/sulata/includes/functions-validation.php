<?php

/*
 * Form validation functions
 */

/* Required */
if (!function_exists('isRequired')) {

    //string to validate,text to print 
    function isRequired($str, $text) {
        global $vError;
        if (($str == '') || ($str == '^')) {
            return $vError[] = sprintf(REQUIRED_FIELD, $text);
        }
    }

}
/* Email */
if (!function_exists('isEmail')) {

    function isEmail($str, $text) {
        global $vError;
        $v = preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i', $str);
        if ($v == FALSE) {
            return $vError[] = sprintf(VALID_EMAIL, $text);
        }
    }

}

/* default php number functions */
//is_numeric, is_double, is_float, is_int, is_integer

/* number and decimal */
if (!function_exists('isInt')) {

    function isInt($str, $text) {
        global $vError;
        //For dropdown
        if ($str == '^') {
            $v = isRequired($str, $text);
            if ($v == FALSE) {
                return $vError[] = sprintf(REQUIRED_FIELD, $text);
            }
        } else {
            $v = is_numeric($str);
            if ($v == FALSE) {
                return $vError[] = sprintf(VALID_NUMBER, $text);
            } else {
                if (strstr($str, '.')) {
                    return $vError[] = sprintf(VALID_NUMBER, $text);
                }
            }
        }
    }

}
/* double */
if (!function_exists('isDouble')) {

    function isDouble($str, $text) {
        global $vError;
        //For dropdown
        if ($str == '^') {
            $v = isRequired($str, $text);
            if ($v == FALSE) {
                return $vError[] = sprintf(REQUIRED_FIELD, $text);
            }
        } else {
            $v = is_numeric($str);
            if ($v == FALSE) {
                return $vError[] = sprintf(VALID_NUMBER, $text);
            }
        }
    }

}
/* float */
if (!function_exists('isFloat')) {

    function isFloat($str, $text) {
        global $vError;
        //For dropdown
        if ($str == '^') {
            $v = isRequired($str, $text);
            if ($v == FALSE) {
                return $vError[] = sprintf(REQUIRED_FIELD, $text);
            }
        } else {
            $v = is_numeric($str);
            if ($v == FALSE) {
                return $vError[] = sprintf(VALID_NUMBER, $text);
            }
        }
    }

}
/* enum */
if (!function_exists('isEnum')) {

    function isEnum($str, $text) {
        global $vError;
        if (($str == '') || ($str == '^')) {
            return $vError[] = sprintf(REQUIRED_FIELD, $text);
        }
    }

}


/* letters and space only */
if (!function_exists('isString')) {

    function isString($str, $text) {
        global $vError;
        echo 'here..';
        $v = preg_match('/^[A-Za-z\s ]+$/', $str);
        if ($v == FALSE) {
            return $vError[] = sprintf(VALID_STRING, $text);
        }
    }

}
/* URL */
if (!function_exists('isURL')) {

    function isURL($str, $text) {
        global $vError;
        $v = preg_match('/^(http(s?):\/\/|ftp:\/\/{1})((\w+\.){1,})\w{2,}$/i', $str);
        if ($v == FALSE) {
            return $vError[] = sprintf(VALID_URL, $text);
        }
    }

}
/* IP */
if (!function_exists('isIP')) {

    function isIP($str, $text) {
        global $vError;
        $v = preg_match('/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/', $str);
        if ($v == FALSE) {
            return $vError[] = sprintf(VALID_URL, $text);
        }
    }

}
/* credit card */
if (!function_exists('isCC')) {

    function isCC($str, $text) {
        global $vError;
        $v = preg_match('/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6011[0-9]{12}|3(?:0[0-5]|[68][0-9])[0-9]{11}|3[47][0-9]{13})$/', $str);
        if ($v == FALSE) {
            return $vError[] = sprintf(VALID_CC, $text);
        }
    }

}
/* date */
if (!function_exists('isDate')) {

    function isDate($str, $text) {
        global $vError;
        if (DATE_FORMAT == 'mm-dd-yy') {
            $v = preg_match('/^((0?[1-9]|1[012])[-](0?[1-9]|[12][0-9]|3[01])[-][0-9]?[0-9]?[0-9]{2})*$/', $str);
            if ($v == FALSE) {
                return $vError[] = sprintf(VALID_DATE, $text);
            }
        } else {
            $v = preg_match('/^(0?[1-9]|[12][0-9]|3[01])[-]((0?[1-9]|1[012])[-][0-9]?[0-9]?[0-9]{2})*$/', $str);
            if ($v == FALSE) {
                return $vError[] = sprintf(VALID_DATE, $text);
            }
        }
    }

}

/* Validate password */
if (!function_exists('isPassword')) {

    //$str = field_name

    function isPassword($str, $text) {
        global $vError;
        if ($_POST[$str] != $_POST[$str . '2']) {
            return $vError[] = PASSWORD_MATCH_ERROR;
        }
    }

}
/* Validate Image */
if (!function_exists('isImage')) {

    //$fieldName = field_name and not posted value
    //Image array name should be allowed_image_formats

    function isImage($fieldName, $text) {
        global $vError, $getSettings;
        $allowed_image_formats = $getSettings['allowed_image_formats'];
        $allowed_image_formats = explode(',', $allowed_image_formats);
        $ext = suGetExtension($_FILES[$fieldName]['name']);
        for ($i = 0; $i <= sizeof($allowed_image_formats) - 1; $i++) {
            if (!in_array($ext, $allowed_image_formats)) {
                return $vError[] = sprintf(VALID_FILE_FORMAT, $getSettings['allowed_image_formats'], $text);
            }
        }
    }

}
/* Validate File */
if (!function_exists('isFile')) {

    //$fieldName = field_name and not posted value
    //File array name should be allowed_file_formats

    function isFile($fieldName, $text) {
        global $vError, $getSettings;
        $allowed_file_formats = $getSettings['allowed_file_formats'];
        $allowed_file_formats = explode(',', $allowed_file_formats);
        $ext = suGetExtension($_FILES[$fieldName]['name']);
        for ($i = 0; $i <= sizeof($allowed_file_formats) - 1; $i++) {
            if (!in_array($ext, $allowed_file_formats)) {
                return $vError[] = sprintf(VALID_FILE_FORMAT, $getSettings['allowed_file_formats'], $text);
            }
        }
    }

}
/* Validate Attachment */
if (!function_exists('isAttachment')) {

    //$fieldName = field_name and not posted value
    //File array name should be allowed_attachment_formats

    function isAttachment($fieldName, $text) {
        global $vError, $getSettings;
        $allowed_attachment_formats = $getSettings['allowed_attachment_formats'];
        $allowed_attachment_formats = explode(',', $allowed_attachment_formats);
        $ext = suGetExtension($_FILES[$fieldName]['name']);
        for ($i = 0; $i <= sizeof($allowed_attachment_formats) - 1; $i++) {
            if (!in_array($ext, $allowed_attachment_formats)) {
                return $vError[] = sprintf(VALID_FILE_FORMAT, $getSettings['allowed_attachment_formats'], $text);
            }
        }
    }

}