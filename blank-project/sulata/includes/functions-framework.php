<?php

/*
 * SULATA FRAMEWORK
 * This file contains the default functions of Sulata Framework
 * For framework version, please refer to the config.php file.
 */


/* CSRF token */
if (!function_exists('suCsrfToken')) {

    function suCsrfToken() {
        $csrfToken = SESSION_PREFIX . session_id();
        if (function_exists('sha1')) {
            $csrfToken = sha1($csrfToken);
        } elseif (function_exists('md5')) {
            $csrfToken = md5($csrfToken);
        }
        return $csrfToken;
    }

}
/* Include File */
if (!function_exists('suInclude')) {

    function suInclude($fileWithPath) {
        if (file_exists($fileWithPath)) {
            include($fileWithPath);
        }
    }

}

/* check referrer */
if (!function_exists('suCheckRef')) {

    function suCheckRef() {
//Build host names
        $thisHost = strtolower($_SERVER['HTTP_HOST']);
        $referrerHost = parse_url($_SERVER['HTTP_REFERER']);
        $referrerHost = strtolower($referrerHost['host']);
//Check host names
        if ($thisHost != $referrerHost) {
            suExit(INVALID_ACCESS);
        }
    }

}
/* fuction to stop openening page outside frame */
if (!function_exists('suFrameBuster')) {

    function suFrameBuster($url = ACCESS_DENIED_URL) {
        suPrintJs("
            if (parent.frames.length 
<1) { 
                parent.window.location.href = '$url';
            }
        ");
    }

}
/* Function to get url segment */
if (!function_exists('suSegment')) {

    function suSegment($segment = '') {
        $path = $_SERVER['PATH_INFO'];
        if (!strstr($path, '/')) {
            $path = $_SERVER['ORIG_PATH_INFO'];
        }
        if ($segment == '') {
            return $path;
        } else {
            $path = explode('/', $path);
            return $path[$segment];
        }
    }

}
/* Check if this is a mobile device */
if (!function_exists('suIsMobile')) {

// Create the function, so you can use it
    function suIsMobile() {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up \.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }

}
/* Build Iframe */

if (!function_exists('suIframe')) {

    function suIframe($debug = DEBUG, $name = 'remote') {
        if ($debug == TRUE) {
            $display = 'block';
        } else {
            $display = 'none';
        }

        echo "
<div style='clear:both'></div>
<div style='height:35px;line-height:35px;width:95%;font-family:Arial;color:#000;background-color:#FFCD9B;display:{$display};'>&nbsp;This is debug window. Set define('DEBUG', FALSE) in config.php file to hide it.</div>
<iframe frameborder='0' name='{$name}' id='{$name}' width='95%' height='300' style='display:{$display};border:1px solid #FFCD9B;'/>
Sorry, your browser does not support frames.
</iframe>
";
    }

}
/* Resize image */
if (!function_exists('suResize')) {

    function suResize($forcedwidth, $forcedheight, $sourcefile, $destfile, $canvasfolder = ADMIN_UPLOAD_PATH) {
        set_time_limit(0);

//Check required if file has been uploaded

        $fw = $forcedwidth;
        $fh = $forcedheight;
//Get image size
        @$is = getimagesize($sourcefile);
//Get image extension
        $extension = $is["mime"];
        if (($extension != "image/jpeg") && ($extension != "image/png") && ($extension != "image/gif")) {
            $msg = "Source file must be an image in JPG, PNG or GIF.";
            return $msg;
            exit;
        }
//If width is wild card
        if ($fw == "*") {
            $w_ratio = $is[0] / $is[1];
            $fw = $is[1] * $w_ratio;
        }
//If height is wild card
        if ($fh == "*") {
            $h_ratio = $is[1] / $is[0];
            $fh = $is[1] / $h_ratio;
        }

        if ($is[0] >= $is[1]) {
            $orientation = 0;
        } else {
            $orientation = 1;
        }
        if ($is[0] > $fw || $is[1] > $fh) {
            if (( $is[0] - $fw ) >= ( $is[1] - $fh )) {
                $iw = $fw;
                $ih = ( $fw / $is[0] ) * $is[1];
            } else {
                $ih = $fh;
                $iw = ( $ih / $is[1] ) * $is[0];
            }
            $t = 1;
        } else {
            $iw = $is[0];
            $ih = $is[1];
            $t = 2;
        }
        if ($t == 1) {
            if ($extension == "image/png") {
                $img_src = imagecreatefrompng($sourcefile);
            } elseif ($extension == "image/jpeg") {
                $img_src = imagecreatefromjpeg($sourcefile);
            } elseif ($extension == "image/gif") {
                $img_src = imagecreatefromgif($sourcefile);
            }

//Create white canvas
//            $canvas_img = imagecreate($forcedwidth, $forcedheight);
//            $background = imagecolorallocate($canvas_img, 255, 255, 255);
//            @unlink($canvasfolder . '/canvas.png');
//            imagepng($canvas_img, $canvasfolder . '/canvas.png');


            $img_dst = imagecreatetruecolor($iw, $ih);
//Delete any exiting image
            @unlink($destfile);
            if ($extension == "image/png" or $extension == "image/gif") {
//Preserve tranparency
                imagecolortransparent($img_dst, imagecolorallocatealpha($img_dst, 0, 0, 0, 127));
                imagealphablending($img_dst, false);
                imagesavealpha($img_dst, true);
            }

//Create new image
            imagecopyresampled($img_dst, $img_src, 0, 0, 0, 0, $iw, $ih, $is[0], $is[1]);

            if ($extension == "image/png") {
                if (!imagepng($img_dst, $destfile, 9)) {
                    exit();
                }
            } elseif ($extension == "image/jpeg") {
                if (!imagejpeg($img_dst, $destfile, 90)) {
                    exit();
                }
            } elseif ($extension == "image/gif") {
                if (!imagegif($img_dst, $destfile, 90)) {
                    exit();
                }
            }
        } else if ($t == 2) {
            copy($sourcefile, $destfile);
        }
    }

}

/* Exit with message */
if (!function_exists('suExit')) {

    function suExit($str) {

        $str = "
<div style='color:#0000FF;font-family:Tahoma,Verdana,Arial;font-size:13px;'>{$str}</div>
";
        exit($str);
    }

}
/* Strip */
if (!function_exists('suStrip')) {

    function suStrip($str, $titleCase = FALSE) {
        if ($titleCase == TRUE) {
            $str = suTitleCase($str);
        }
        $str = str_replace('&', '~`~', $str);
        $str = urlencode(trim($str));
        return $str;
    }

}
/* Unstrip */
if (!function_exists('suUnstrip')) {

    function suUnstrip($str) {
        $str = htmlspecialchars(urldecode($str));

        if (LOCAL == TRUE) {
            $str = str_replace(WEB_URL, LOCAL_URL, $str);
        } else {
            $str = str_replace(LOCAL_URL, WEB_URL, $str);
        }
        $str = str_replace('~`~', '&', $str);

        return $str;
    }

}
/* Parse String */
if (!function_exists('suParse')) {

    function suParse($str, $start, $end) {
        $startString = explode($start, $str);
        $endString = explode($end, $startString[1]);
        return $endString[0];
    }

}

/* Print JS */
if (!function_exists('suPrintJS')) {

    function suPrintJS($js) {

        echo "
<script type=\"text/javascript\">
		{$js}
		</script>
";
    }

}


/* Create a tag */
if (!function_exists('suInput')) {

//Tag name, $attributes array,$data html, $has ending tag
    function suInput($tag, $attributes, $data = '', $has_ending = FALSE) {
        global $uniqueArray;
        if (is_array($attributes)) {
            $atts = '';
            foreach ($attributes as $key => $val) {

                if ($key != '') {

                    if (strtolower($key) == 'name') {
                        $fieldName = $val;
                    }
                    if ($has_ending == FALSE) {

                        if (strtolower($key) == 'maxlength') {
                            if (in_array($fieldName, $uniqueArray)) {
                                $val = $val - UID_LENGTH;
                            }
                            $atts .= ' ' . $key . '="' . $val . '"';
                        } else {
                            $atts .= ' ' . $key . '="' . $val . '"';
                        }
                    } else {
                        if ($key != 'type') {
                            if (strtolower($key) == 'maxlength') {
                                if (in_array($key, $uniqueArray)) {
                                    $val = $val - UID_LENGTH;
                                }
                                $atts .= ' ' . $key . '="' . $val . '"';
                            } else {
                                $atts .= ' ' . $key . '="' . $val . '"';
                            }
                        }
                    }
                }
            }
            $attributes = $atts;
        }

        if ($has_ending == TRUE) {
            $tag = "<{$tag}" . $attributes . ">" . $data . "</{$tag}>";
        } else {
            $tag = "<{$tag}" . $attributes . "/>";
        }
        return $tag;
    }

}
/* searchable dropdown */
if (!function_exists('suSearchableDropdown')) {

    function suSearchableDropdown($name, $sql, $selectedVal, $js) {

        if ($js == '') {
            $js = ' class="chosen-select form-control" ';
        } else {
            $js = $js;
        }
        $searchableDD = '';
        $opt = "<option value=\"^\">Select..</option>\n";
        $selected = '';
        $result = suQuery($sql);
        foreach ($result['result'] as $row) {
            if (suUnstrip($row['f1']) == $selectedVal) {
                $selected = " selected=\"selected\"";
            } else {
                $selected = "";
            }
            $opt .= "<option " . $selected . " value=\"" . suUnstrip($row['f1']) . "\">" . suUnstrip($row['f2']) . "</option>";
        }
        $searchableDD = '<select name="' . $name . '" id="' . $name . '" ' . $js . '>' . "\n" . $opt . '</select>' . "\n";

        return $searchableDD;
    }

}
/* form dropdown */
if (!function_exists('suDropdown')) {

    function suDropdown($name, $options, $selected = '', $extra = '') {
        foreach ($options as $key => $value) {
            $o[suUnstrip($key)] = suUnstrip($value);
        }
        $options = $o;
        $opt = '';
        foreach ($options as $key => $val) {
            if (trim($key) === trim($selected)) {
                $sel = " selected=\"selected\"";
            } else {
                $sel = "";
            }

            $opt .= "<option value=\"" . suUnstrip($key) . "\" $sel>" . suUnstrip($val) . "</option>\n";
        }
        return "<select name=\"" . $name . "\" id=\"" . $name . "\" $extra>" . $opt . "</select>";
        ;
    }

}
/* form radio */
if (!function_exists('suRadio')) {

    function suRadio($name, $options, $checked, $extra = '', $type) {
        foreach ($options as $key => $value) {
            $o[suUnstrip($key)] = suUnstrip($value);
        }
        $options = $o;
        if ($type == 'regular') {
            $type = 'p-default p-round p-thick';
        } else {
            $type = 'p-switch p-slim';
        }
        $rd = '';
        $arg = '';
        foreach ($extra as $key => $value) {
            $arg .= $key . "='" . $value . "' ";
        }
        for ($i = 0; $i <= sizeof($options) - 1; $i++) {
            $options[$i] = trim($options[$i]);
            if ($checked == $options[$i]) {
                $check = 'checked="checked"';
            } else {
                $check = '';
            }

            $rd .= '
                <div class="pretty ' . $type . ' size-110">
                    <input ' . $arg . ' ' . $check . ' type="radio" name="' . $name . '" id="' . $name . '" value="' . $options[$i] . '"/>
                    <div class="state p-warning">
                        <label>' . $options[$i] . '</label>
                    </div>
                </div>
                
                ';
        }

        return $rd;
    }

}
/* form checkbox */
if (!function_exists('suCheckbox')) {

    function suCheckbox($name, $options, $checked, $extra = '', $type) {

        foreach ($options as $key => $value) {
            $o[suUnstrip($key)] = suUnstrip($value);
        }
        $options = $o;
        if ($type == 'regular') {
            $type = 'p-default p-curve p-thick';
        } else {
            $type = 'p-switch';
        }

        $last = substr($name, -1);
        if ($last != ']') {
            $name = $name . '[]';
        }
        $rd = '';
        $arg = '';
        foreach ($extra as $key => $value) {
            if ($key != 'value') {//Add by Tahir as value was populating as array on update page
                $arg .= $key . "='" . $value . "' ";
            }
        }
        for ($i = 0; $i <= sizeof($options) - 1; $i++) {
            $x = $options[$i];
            if (in_array($options[$i], $checked)) {
                $check = 'checked="checked"';
            } else {
                $check = '';
            }
            $rd .= '
                <div class="pretty ' . $type . ' size-110">
                    <input ' . $arg . ' ' . $check . ' type="checkbox" name="' . $name . '" id="' . $name . '" value="' . $options[$i] . '"  />
                    <div class="state p-warning">
                        <label>' . $options[$i] . '</label>
                    </div>
                </div>
                ';
        }

        return $rd;
    }

}
/* Print Array */
if (!function_exists('print_array')) {

//Tag name, html, $attributes,$has ending tag
    function print_array($array) {
        echo '
<pre>';
        print_r($array);
        echo '</pre>
';
    }

}
/* Make dropdown array from db */
if (!function_exists('suFillDropdown')) {

    function suFillDropdown($sql) {
        $suFillDropdown = array('^' => 'Select..');
        $result = suQuery($sql);
        foreach ($result['result'] as $row) {
            $suFillDropdown[suUnstrip($row['f1'])] = suUnstrip($row['f2']);
        }
        return $suFillDropdown;
    }

}
/* Convert date format for database */
if (!function_exists('suDate2Db')) {

//mm-dd-yyyy or dd-mm-yyyy
    function suDate2Db($date, $sep = '-') {
        if ($date != '') {
            $nDate = explode($sep, $date);
            if (DATE_FORMAT == 'mm-dd-yy') {
                $nDate = $nDate['2'] . '-' . $nDate['0'] . '-' . $nDate['1'];
            } else {
                $nDate = $nDate['2'] . '-' . $nDate['1'] . '-' . $nDate['0'];
            }
            return $nDate;
        } else {
            return $date = '';
        }
    }

}
/* Convert date format from database */
if (!function_exists('suDateFromDb')) {

//mm-dd-yyyy or dd-mm-yyyy
    function suDateFromDb($date, $sep = '-') {
        if (($date == '') || ($date == '0000-00-00')) {
            return $date = '';
        } else {

            $nDate = explode($sep, $date);
            if (DATE_FORMAT == 'mm-dd-yy') {
                $nDate = $nDate['1'] . '-' . $nDate['2'] . '-' . $nDate['0'];
            } else {
                $nDate = $nDate['2'] . '-' . $nDate['1'] . '-' . $nDate['0'];
            }
            return $nDate;
        }
    }

}
/* Convert date format from database to English */
if (!function_exists('suDateFromDbToEnglish')) {

//mm-dd-yyyy or dd-mm-yyyy
    function suDateFromDbToEnglish($date, $sep = '-') {
        if (($date == '') || ($date == '0000-00-00')) {
            return $date = '';
        } else {
            $englishMonths = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');


            $nDate = explode($sep, $date);
            $nDate['1'] = $nDate['1'] - 1;
            $nDate = $nDate['2'] . '-' . $englishMonths[(int) $nDate['1']] . '-' . $nDate['0'];

            return $nDate;
        }
    }

}


/* Check admin login */
if (!function_exists('checkAdminLogin')) {

//Check if logged in
    function checkAdminLogin() {
        //If admin session not set
        if ($_SESSION[SESSION_PREFIX . 'admin_login'] == '') {
            $url = ADMIN_URL . 'login' . PHP_EXTENSION . '/?' . suCrypt($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);
            suPrintJs("parent.window.location.href='{$url}';");
            exit();
        } else {
            global $getSettings;
            //If multi login not allowed
            if ($getSettings['allow_multiple_location_login'] == '1') {
                $sql = "SELECT " . suJsonExtract('data', 'ip') . " FROM " . USERS_TABLE_NAME . " WHERE id='" . $_SESSION[SESSION_PREFIX . 'user_id'] . "' AND live='Yes' LIMIT 0,1";
                $result = suQuery($sql);
                if (suUnstrip($result['result'][0]['ip']) != $_SERVER['REMOTE_ADDR']) {
                    $url = ADMIN_URL . 'login' . PHP_EXTENSION . '/multilogin/';
                    suPrintJs("parent.window.location.href='{$url}';");
                    exit();
                }
            }
        }
    }

}
/* Check Module Permission for Groups */
if (!function_exists('suCheckAccess')) {

    function suCheckAccess($table, $checkWhat, $debug = FALSE) {//Eg suCheckAccess('people','updateables');
        $groupWhere = suJsonExtract('data', 'group_title', FALSE);
        $permission_groups = $_SESSION[SESSION_PREFIX . 'user_group'];

        //$permission_groups = array('People', 'Cities');
        $groups = '';
        for ($i = 0; $i < sizeof($permission_groups); $i++) {
            $groups .= " " . $groupWhere . "='" . suStrip($permission_groups[$i]) . "' OR ";
        }
        $groups = substr($groups, 0, -3);
        $add_access = suJsonExtract('data', 'add_access');
        $view_access = suJsonExtract('data', 'view_access');
        $preview_access = suJsonExtract('data', 'preview_access');
        $sort_access = suJsonExtract('data', 'sort_access');
        $update_access = suJsonExtract('data', 'update_access');
        $delete_access = suJsonExtract('data', 'delete_access');
        $duplicate_access = suJsonExtract('data', 'duplicate_access');
        $download_csv_access = suJsonExtract('data', 'download_csv_access');
        $download_pdf_access = suJsonExtract('data', 'download_pdf_access');

        $sql = " SELECT id,$add_access,$view_access,$preview_access,$sort_access,$update_access,$delete_access,$duplicate_access,$download_csv_access,$download_pdf_access FROM groups WHERE live='Yes' AND ( " . $groups . " )";
        $result = suQuery($sql);
        $row = $result['result'];

        $addables = array();
        $viewables = array();
        $previewables = array();
        $sortables = array();
        $updateables = array();
        $deleteables = array();
        $duplicateables = array();
        $csv_downloadables = array();
        $pdf_downloadables = array();
        for ($i = 0; $i < sizeof($row); $i++) {
            if ($row[$i]['add_access'] != '') {
                $a = json_decode($row[$i]['view_access'], 1);
                for ($j = 0; $j < sizeof($a); $j++) {
                    array_push($addables, $a[$j]);
                }
            }
            if ($row[$i]['view_access'] != '') {
                $v = json_decode($row[$i]['view_access'], 1);
                for ($j = 0; $j < sizeof($v); $j++) {
                    array_push($viewables, $v[$j]);
                }
            }
            if ($row[$i]['preview_access'] != '') {
                $p = json_decode($row[$i]['preview_access'], 1);
                for ($j = 0; $j < sizeof($p); $j++) {
                    array_push($previewables, $p[$j]);
                }
            }
            if ($row[$i]['sort_access'] != '') {
                $s = json_decode($row[$i]['sort_access'], 1);
                for ($j = 0; $j < sizeof($s); $j++) {
                    array_push($sortables, $s[$j]);
                }
            }
            if ($row[$i]['update_access'] != '') {
                $u = json_decode($row[$i]['update_access'], 1);
                for ($j = 0; $j < sizeof($u); $j++) {
                    array_push($updateables, $u[$j]);
                }
            }
            if ($row[$i]['delete_access'] != '') {
                $d = json_decode($row[$i]['delete_access'], 1);
                for ($j = 0; $j < sizeof($d); $j++) {
                    array_push($deleteables, $d[$j]);
                }
            }
            if ($row[$i]['duplicate_access'] != '') {
                $du = json_decode($row[$i]['duplicate_access'], 1);
                for ($j = 0; $j < sizeof($du); $j++) {
                    array_push($duplicateables, $du[$j]);
                }
            }
            if ($row[$i]['download_csv_access'] != '') {
                $dc = json_decode($row[$i]['download_csv_access'], 1);
                for ($j = 0; $j < sizeof($dc); $j++) {
                    array_push($csv_downloadables, $dc[$j]);
                }
            }
            if ($row[$i]['download_pdf_access'] != '') {
                $dp = json_decode($row[$i]['download_pdf_access'], 1);
                for ($j = 0; $j < sizeof($dp); $j++) {
                    array_push($pdf_downloadables, $dp[$j]);
                }
            }
        }
        $group_permissions = array(
            'addables' => $addables,
            'viewables' => $viewables,
            'previewables' => $previewables,
            'sortables' => $sortables,
            'updateables' => $updateables,
            'deleteables' => $deleteables,
            'duplicateables' => $duplicateables,
            'csv_downloadables' => $csv_downloadables,
            'pdf_downloadables' => $pdf_downloadables,
        );
        //Debug action
        if ($debug == TRUE) {
            print_array($group_permissions);
        }
        //Check if admin
        if (in_array(ADMIN_GROUP_NAME, $_SESSION[SESSION_PREFIX . 'user_group'])) {
            return TRUE;
        } else {

            //Check the required
            if (in_array($table, $group_permissions[$checkWhat])) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

}
/* Restrict access to IP */
if (!function_exists('suCheckIpAccess')) {

    function suCheckIpAccess() {
        global $getSettings;
        if ($getSettings['restrict_over_ip'] != '-') {
            if ($getSettings['restrict_over_ip'] != $_SERVER['REMOTE_ADDR']) {
                $url = ADMIN_URL . "message.php?msg=" . urlencode(IP_RESTRICTED);
                suPrintJs("parent.window.location.href='{$url}';");
                exit();
            }
        }
    }

}
/* Check group permissions */
if (!function_exists('suCheckGroupPermissions')) {

    function suCheckGroupPermissions($table) {
        $sql = "SELECT " . suJsonExtract('data', 'add_access') . "," . suJsonExtract('data', 'view_access') . "," . suJsonExtract('data', 'preview_access') . "," . suJsonExtract('data', 'sort_access') . "," . suJsonExtract('data', 'update_access') . "," . suJsonExtract('data', 'delete_access') . "," . suJsonExtract('data', 'duplicate_access') . "," . suJsonExtract('data', 'download_csv_access') . "," . suJsonExtract('data', 'download_pdf_access') . " FROM groups WHERE " . suJsonExtract('data', 'group_title', FALSE) . "='" . $_SESSION[SESSION_PREFIX . 'user_group'] . "' AND " . suJsonExtract('data', 'status', FALSE) . "='Active' AND live='Yes' LIMIT 0,1";
        $result = suQuery($sql);
        //Build add access
        $addAccess = $result['result'][0]['add_access'];
        $addAccess = json_decode($addAccess);
        $addables = $addAccess;
        if (in_array($table, $addAccess)) {
            $addAccess = TRUE;
        } else {
            $addAccess = FALSE;
        }
        //Build view access
        $viewAccess = $result['result'][0]['view_access'];
        $viewAccess = json_decode($viewAccess);
        $viewables = $viewAccess;
        if (in_array($table, $viewAccess)) {
            $viewAccess = TRUE;
        } else {
            $viewAccess = FALSE;
        }
        //Build preview access
        $previewAccess = $result['result'][0]['preview_access'];
        $previewAccess = json_decode($previewAccess);
        $previewables = $previewAccess;
        if (in_array($table, $previewAccess)) {
            $previewAccess = TRUE;
        } else {
            $previewAccess = FALSE;
        }
        //Build sort access
        $sortAccess = $result['result'][0]['sort_access'];
        $sortAccess = json_decode($sortAccess);
        $sortables = $sortAccess;
        if (in_array($table, $sortAccess)) {
            $sortAccess = TRUE;
        } else {
            $sortAccess = FALSE;
        }
        //Build update access
        $updateAccess = $result['result'][0]['update_access'];
        $updateAccess = json_decode($updateAccess);
        $updateables = $updateAccess;
        if (in_array($table, $updateAccess)) {
            $updateAccess = TRUE;
        } else {
            $updateAccess = FALSE;
        }
        //Build delete access
        $deleteAccess = $result['result'][0]['delete_access'];
        $deleteAccess = json_decode($deleteAccess);
        $deleteables = $deleteAccess;
        if (in_array($table, $deleteAccess)) {
            $deleteAccess = TRUE;
        } else {
            $deleteAccess = FALSE;
        }
        //Build duplicate access
        $duplicateAccess = $result['result'][0]['duplicate_access'];
        $duplicateAccess = json_decode($duplicateAccess);
        if (in_array($table, $duplicateAccess)) {
            $duplicateAccess = TRUE;
        } else {
            $duplicateAccess = FALSE;
        }
        //Build download CSV access
        $downloadAccessCSV = $result['result'][0]['download_csv_access'];
        $downloadAccessCSV = json_decode($downloadAccessCSV);
        if (in_array($table, $downloadAccessCSV)) {
            $downloadAccessCSV = TRUE;
        } else {
            $downloadAccessCSV = FALSE;
        }
        //Build download PDF access
        $downloadAccessPDF = $result['result'][0]['download_pdf_access'];
        $downloadAccessPDF = json_decode($downloadAccessPDF);
        if (in_array($table, $downloadAccessPDF)) {
            $downloadAccessPDF = TRUE;
        } else {
            $downloadAccessPDF = FALSE;
        }

        $groupAccess = array('add_access' => $addAccess, 'view_access' => $viewAccess, 'preview_access' => $previewAccess, 'sort_access' => $sortAccess, 'update_access' => $updateAccess, 'delete_access' => $deleteAccess, 'duplicate_access' => $duplicateAccess, 'download_csv_access' => $downloadAccessCSV, 'download_pdf_access' => $downloadAccessPDF, 'viewables' => $viewables, 'previewables' => $previewables, 'sortables' => $sortables, 'addables' => $addables, 'updateables' => $updateables, 'deleteables' => $deleteables,);
        return $groupAccess;
    }

}



/* Slugify file name */
if (!function_exists('suSlugify')) {

//File name and uniqid
    function suSlugify($string, $uid) {
        $suFileName = '';
        $string = explode('.', $string);
        $ext = '.' . end($string);
        for ($i = 0; $i <= sizeof($string) - 2; $i++) {
            $suFileName .= $string[$i];
        }
        $suFileName = preg_replace('/[^A-Za-z0-9-]+/', '-', $suFileName);
        $suFileName = $suFileName . '-' . $uid . $ext;

        return $suFileName;
    }

}
/* Slugify string */
if (!function_exists('suSlugifyStr')) {

//File name
    function suSlugifyStr($str, $replaceChar = '-') {
        $str = preg_replace('/[^A-Za-z0-9-]+/', $replaceChar, $str);
        return strtolower($str);
    }

}
/* Unslugify string */
if (!function_exists('suUnslugifyStr')) {

//File name
    function suUnslugifyStr($str, $replaceChar = '-') {
        $str = str_replace($replaceChar, '_', $str);
        return $str;
    }

}

/* Get file extension */
if (!function_exists('suGetExtension')) {

    function suGetExtension($name) {
        return end(explode(".", strtolower($name)));
    }

}
/* print $vError validation errors */
if (!function_exists('suValdationErrors')) {

    function suValdationErrors($vError) {
        for ($i = 0; $i <= sizeof($vError) - 1; $i++) {
            $li .= '
<li>' . $vError[$i] . '</li>
';
        }

        echo "
<div id='error-area'>
  <ul>
    " . $li . "
  </ul>
</div>
";

        if (sizeof($vError) > 0) {
            suPrintJs('
            parent.suToggleButton(0);
            parent.$("#message-area").hide();
            parent.$("#error-area").show();
            parent.$("#error-area").html(document.getElementById(\'error-area\').innerHTML);
            parent.$("html, body").animate({ scrollTop: parent.$("html").offset().top }, "slow");
        ');
            exit();
        }
    }

}

/* Make CKEditor out of textarea */
if (!function_exists('suCKEditor')) {

//File name and uniqid
    function suCKEditor($editorId) {
        suPrintJS("
 CKEDITOR.replace( '" . $editorId . "' , {
                                        toolbar: [
                                            { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
                                            { name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Scayt' ] },
                                            { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
                                            { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] },
                                            { name: 'tools', items: [ 'Maximize' ] },
                                            { name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source' ] },
                                            { name: 'others', items: [ '-' ] },
                                            '/',
                                            [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ],
                                            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Strike', '-', 'RemoveFormat' ] },
                                            { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
                                            { name: 'styles', items: [ 'Styles', 'Format' ] },
                                            { name: 'about', items: [ 'About' ] }
                                        ]
                                    });
");
        echo "<div>&nbsp;</div>";
    }

}
/* Crypt */
if (!function_exists('suCrypt')) {

    function suCrypt($str) {
        return base64_encode(base64_encode($str));
    }

}
/* Decrypt */
if (!function_exists('suDecrypt')) {

    function suDecrypt($str) {
        return base64_decode(base64_decode($str));
    }

}
/* Redirect */
if (!function_exists('suRedirect')) {

    function suRedirect($url) {
        suPrintJs("parent.window.location.href='{$url}';");
        exit;
    }

}
/* Initial ChartJs library */

if (!function_exists('suInitChartJs')) {

    function suInitChartJs($pathToChartJs = FALSE, $jquery = TRUE) {
//Include Jquery if not already included in page
        if ($jquery == TRUE) {
            echo '<script src="includes/jquery.js" type="text/javascript"></script>';
        }
        //Include chart.js file
        if ($pathToChartJs == FALSE) {
            echo '<script src="includes/chart.js" type="text/javascript"></script>';
        } else {
            echo '<script src="' . $pathToChartJs . 'chart.js" type="text/javascript"></script>';
        }
    }

}

/* Render Chart for ChartJs library */
if (!function_exists('suRenderChartJs')) {

    function suRenderChartJs($id, $type, $labelsArray, $dataArray, $title = '', $clickUrl = FALSE, $borderWidth = 1) {

        //Example: suRenderChart('myChart1', 'bar', $labelsArray, $dataArray, $title, 'http://www.sulata.com/?');
//Build labels
        $jsLabels = '';
        foreach ($labelsArray as $value) {
            $jsLabels .= '"' . $value . '",';
        }
        $jsLabels = substr($jsLabels, 0, -1);

//Build data
        $jsData = '';
        foreach ($dataArray as $value) {
            $jsData .= '"' . $value . '",';
        }
        $jsData = substr($jsData, 0, -1);

        //Build canvas size
        if ($sizeArray != FALSE) {
            $width = $sizeArray[0];
            $height = $sizeArray[1];
        } else {
            $width = '90%';
            $height = '90%';
        }
        //Build background colors
        $backgroundColors = array("rgba(75, 192, 192, 0.2)", "rgba(54, 162, 235, 0.2)", "rgba(153, 102, 255, 0.2)", "rgba(255, 206, 86, 0.2)", "rgba(255, 159, 64, 0.2)", "rgba(150, 75, 0, 0.2)", "rgba(255, 99, 132, 0.2)",);
        $backgroundColors2 = array("rgba(75, 192, 192, 0.4)", "rgba(54, 162, 235, 0.4)", "rgba(153, 102, 255, 0.4)", "rgba(255, 206, 86, 0.4)", "rgba(255, 159, 64, 0.4)", "rgba(150, 75, 0, 0.4)", "rgba(255, 99, 132, 0.4)",);
        $backgroundColors = array_merge($backgroundColors, $backgroundColors2);


        $jsBackgroundColors = '';
        foreach ($backgroundColors as $value) {
            $jsBackgroundColors .= '"' . $value . '",';
        }
        $jsBackgroundColors = substr($jsBackgroundColors, 0, -1);


        //Build border colors
        $borderColors = array("rgba(75, 192, 192, 1)", "rgba(54, 162, 235, 1)", "rgba(153, 102, 255, 1)", "rgba(255, 206, 86, 1)", "rgba(255, 159, 64, 1)", "rgba(150, 75, 0, 1)", "rgba(255, 99, 132, 1)",);

        $jsBorderColors = '';
        foreach ($borderColors as $value) {
            $jsBorderColors .= '"' . $value . '",';
        }
        $jsBorderColors = substr($jsBorderColors, 0, -1);
        //==
        $jsBorderColors = "[" . $jsBorderColors . "]";
        $jsBackgroundColors = "[" . $jsBackgroundColors . "]";
        //==
        if ($type == 'line') {
            $jsBackgroundColors = "'rgb(255, 99, 132,0.2)'";
            $jsBorderColors = "'rgb(255, 99, 132,1)'";
        }

        //Build onclick event
        if ($clickUrl != FALSE || $clickUrl != '') {

            $onClick = 'canvas.onclick = function (evt) {
                            var activePoints = ' . $id . '.getElementsAtEvent(evt);
                            if (activePoints[0]) {
                                var chartData = activePoints[0]["_chart"].config.data;
                                var idx = activePoints[0]["_index"];

                                var label = chartData.labels[idx];
                                var value = chartData.datasets[0].data[idx];

                                var clickUrl = "' . $clickUrl . 'label=" + escape(label) + "&value=" + value;
                                top.window.location.href=clickUrl;
                            }
                        };';
        } else {
            $onClick = '';
        }
        echo '<div class="chart-container-' . $id . '" style="position: relative; width:' . $width . '; height:' . $height . '"><canvas id="' . $id . '"></canvas></div>
        <script>
            var ctx = document.getElementById("' . $id . '");
            var data = {
                labels: [' . $jsLabels . '],
                datasets: [{
                        label: "' . $title . '",
                        data: [' . $jsData . '],
                        backgroundColor: ' . $jsBackgroundColors . ',
                        borderColor: ' . $jsBorderColors . ',
                        borderWidth: ' . $borderWidth . '
                    }]
            };
            var options = {
                //responsive: true,
                //maintainAspectRatio: false,
                legend: {display: false},';
        if ($clickUrl != FALSE || $clickUrl != '') {
            echo 'hover: {
                        onHover: function (e) {
                            $("#' . $id . '").css("cursor", e[0] ? "pointer" : "default");

                            /* without jquery it can be like this:
                             var el = document.getElementById("canvas1");
                             el.style.cursor = e[0] ? "pointer" : "default";
                             */
                        }
                            },';
        }
        echo 'scales: {
                    yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }],
                        
                }
            };
            $(document).ready(
                    function () {
                        var canvas = document.getElementById("' . $id . '");
                        var ' . $id . ' = new Chart(ctx, {
                            type: "' . $type . '",
                            data: data,
                            options: options,
                        });

                        ' . $onClick . '
                        
                    });

        </script>
        ';
    }

}
/* Mail */
if (!function_exists('suMail')) {

    function suMail($to, $subject, $message, $fromName, $fromEmail, $html = FALSE, $replyTo = FALSE, $attachment = FALSE) {
        if (DEBUG == TRUE) {
            echo "<table>"
            . "<tr><td>Subject: " . $subject . "</td></tr>"
            . "<tr><td>" . $message . "</td></tr>"
            . "</table>";
        } else {
            if ($html == FALSE) {
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= "Content-Type:text/plain;charset=utf-8\r\n";
                $headers .= "From: $fromName <$fromEmail>" . "\r\n";
                $headers .= "From: $fromName <$fromEmail>" . "\r\n";
                if ($replyTo != FALSE) {
                    $headers = "Reply-to: $replyTo";
                } else {
                    $headers = "Reply-to: $email";
                }
                if (is_array($to)) {
                    $sendTo = '';
                    for ($i = 0; $i <= sizeof($to); $i++) {
                        if ($to[$i] != '') {
                            $sendTo .= $to[$i] . ',';
                        }
                    }
                    $to = substr($sendTo, -1);
                }
                mail($to, $subject, $message, $headers);
            } else {
                $mail = new PHPMailer(); // defaults to using php "mail()"
                $body = $message;
                if (function_exists('eregi_replace')) {
                    $body = eregi_replace("[\]", '', $body);
                }
                if ($replyTo != FALSE) {
                    $mail->AddReplyTo($replyTo, $fromName);
                } else {
                    $mail->AddReplyTo($fromEmail, $fromName);
                }
                $mail->AddReplyTo($fromEmail, $fromName);
                $mail->SetFrom($fromEmail, $fromName);
                $mail->CharSet = 'UTF-8';
                if (is_array($to)) {
                    for ($i = 0; $i <= sizeof($to); $i++) {
                        if ($to[$i] != '') {
                            $mail->AddAttachment($attachment[$i]);
                            $mail->AddAddress($to[$i], $to[$i]);
                        }
                    }
                } else {
                    $mail->AddAddress($to, $to);
                }


                $mail->Subject = $subject;
                $mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
                $mail->MsgHTML($body);
                if ($attachment == TRUE) {
                    if (is_array($attachment)) {
                        for ($i = 0; $i <= sizeof($attachment); $i++) {
                            if ($attachment[$i] != '') {
                                $mail->AddAttachment($attachment[$i]);      // attachment
                            }
                        }
                    } else {
                        $mail->AddAttachment($attachment);      // attachment
                    }
                }
                if (!$mail->Send()) {
                    echo "Mailer Error: " . $mail->ErrorInfo;
                } else {
//echo "Message sent!";
                }
            }
        }
    }

}
/* Download as CSV */
if (!function_exists('suSqlToCSV')) {

    function suSqlToCSV($sql, $fields, $outputFileName) {
        $outputFileName = $outputFileName . '.csv';
        $headerArray = array();
        for ($i = 0; $i <= sizeof($fields) - 1; $i++) {
            array_push($headerArray, suUnstrip($fields[$i]));
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $outputFileName);
        $output = fopen('php://output', 'w');
        fputcsv($output, $headerArray);
        $result = suQuery($sql);

        foreach ($result['result'] as $row) {
            $data = array();
            foreach ($row as $key => $value) {
                if ($key != 'id') {
                    if (is_array(json_decode($value))) {
                        array_push($data, html_entity_decode(suUnstrip($value)));
                    } else {
                        array_push($data, suUnstrip($value));
                    }
                }
            }
            fputcsv($output, $data);
        }
    }

}
/* Download as PDF */
if (!function_exists('suSqlToPDF')) {

    function suSqlToPDF($sql, $fields, $outputFileName, $dateArray) {
        global $getSettings;
        //Make field names
        for ($i = 0; $i <= sizeof($fields) - 1; $i++) {
            $fld = suSlugifyStr($fields[$i], '_');
        }
        //Make td size (first td is 5%)
        $tdSize = round(95 / sizeof($fields));
        $title = ucwords(str_replace('_', ' ', $outputFileName)); //Title of the sheet
        $outputFileName = $outputFileName . '.pdf'; //Name of the file to download
        $resultPdf = suQuery($sql);
        $resultPdf = $resultPdf['result'];
        $tbl = '';
        $tbl .= '<table style="width:100%;" cellspacing="0">';
        $thStyle = 'style="font-family: arial; font-size:13px;font-weight:bold;border-bottom:1px solid #000;line-height:20px;width:5%;"';
        $thStyle2 = 'style="font-family: arial; font-size:13px;font-weight:bold;border-bottom:1px solid #000;line-height:20px;width:' . $tdSize . '%"';
        $tdStyle = 'style="font-family: arial; font-size:12px;font-weight:normal;border-bottom:1px solid #000;line-height:20px;width:5%;"';
        $tdStyle2 = 'style="font-family: arial; font-size:12px;font-weight:normal;border-bottom:1px solid #000;line-height:20px;width:' . $tdSize . '%"';
        $tbl .= '<tr><td ' . $thStyle . '>Sr. </td>';
        for ($i = 0; $i <= sizeof($fields) - 1; $i++) {
            $tbl .= '<td ' . $thStyle2 . '>' . suUnstrip($fields[$i]) . '</td>';
        }
        $tbl .= '</tr>';
        $cnt = 0;
        foreach ($resultPdf as $rowPdf) {
            $cnt = $cnt + 1;
            //Build td to display data
            $tbl .= '<tr><td ' . $tdStyle . '>' . $cnt . '. </td>';
            for ($i = 0; $i <= sizeof($fields) - 1; $i++) {
                $fld = suSlugifyStr($fields[$i], '_');
                if (in_array($fld, $dateArray)) {
                    $tbl .= '<td ' . $tdStyle2 . '>' . suUnstrip($rowPdf[$fld . '2']) . '</td>';
                } else {
                    $tbl .= '<td ' . $tdStyle2 . '>' . suUnstrip($rowPdf[$fld]) . '</td>';
                }
            }
            $tbl .= '</tr>';
        }
        $tbl .= '</table>';
        $html = $tbl;
        // Include the main TCPDF library (search for installation path).
        require_once('../sulata/tcpdf/tcpdf.php');

// create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($getSettings['site_name']);
        $pdf->SetTitle($title);
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

// set default header data
        $pdf->SetHeaderData('', '', $title, $getSettings['site_name'], array(0, 0, 0), array(0, 0, 0));
        $pdf->setFooterData(array(0, 0, 0), array(0, 0, 0));

// set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

// ---------------------------------------------------------
// set default font subsetting mode
        $pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
        $pdf->SetFont('helvetica', '', 11, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
        $pdf->AddPage();

// set text shadow effect
        $pdf->setTextShadow(array('enabled' => false, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));


// Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
        $pdf->Output($outputFileName, 'D');
//============================================================+
// END OF FILE
//====================
        exit;
    }

}
/* Download as PDF */
if (!function_exists('suSqlToPDF2')) {

    function suSqlToPDF2($sql, $fields, $outputFileName, $dateArray) {
        global $getSettings;
        //Make field names
        for ($i = 0; $i <= sizeof($fields) - 1; $i++) {
            $fld = suSlugifyStr($fields[$i], '_');
        }
        $title = ucwords(str_replace('_', ' ', $outputFileName)); //Title of the sheet
        $outputFileName = $outputFileName . '.pdf'; //Name of the file to download
        $resultPdf = suQuery($sql);
        $resultPdf = $resultPdf['result'];
        $tbl = '';
        $cnt = 0;
        foreach ($resultPdf as $rowPdf) {
            $cnt = $cnt + 1;
            //Build td to display data
            $tbl .= '';
            for ($i = 0; $i <= sizeof($fields) - 1; $i++) {
                $fld = suSlugifyStr($fields[$i], '_');
                if (in_array($fld, $dateArray)) {
                    $tbl .= suUnstrip($fields[$i]) . ': ' . suUnstrip($rowPdf[$fld . '2']) . '<br/>';
                } else {
                    $tbl .= suUnstrip($fields[$i]) . ': ' . suUnstrip($rowPdf[$fld]) . '<br/>';
                }
            }
            $tbl .= '<div style="text-align:right;font-size:9px;border-bottom:1px solid #333;color:#333">' . $cnt . '</div><div>&nbsp;</div>';
        }
        $html = $tbl;
        // Include the main TCPDF library (search for installation path).
        require_once('../sulata/tcpdf/tcpdf.php');

// create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($getSettings['site_name']);
        $pdf->SetTitle($title);
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

// set default header data
        $pdf->SetHeaderData('', '', $title, $getSettings['site_name'], array(0, 0, 0), array(0, 0, 0));
        $pdf->setFooterData(array(0, 0, 0), array(0, 0, 0));

// set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

// ---------------------------------------------------------
// set default font subsetting mode
        $pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
        $pdf->SetFont('helvetica', '', 11, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
        $pdf->AddPage();

// set text shadow effect
        $pdf->setTextShadow(array('enabled' => false, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));


// Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
        $pdf->Output($outputFileName, 'D');
//============================================================+
// END OF FILE
//====================
        exit;
    }

}
/* Substr */
if (!function_exists('suSubstr')) {

    function suSubstr($string, $length = 30) {
        if (strlen($string) > $length) {
            $string = substr($string, 0, $length) . '..';
            return $string;
        } else {
            return $string;
        }
    }

}
/* Make title case */
if (!function_exists('suTitleCase')) {

    function suTitleCase($str) {
        $out = '';
        $exceptions = array('on', 'in', 'at', 'for', 'ago', 'to', 'till', 'until', 'by', 'into', 'onto', 'from', 'of', 'is', 'are', 'were', 'will', 'the', 'it', 'and', 'a');
        $musts = array('i');
        $punctuations = array('.', ',', ':', ';', '!');
        $str = trim($str);
        $str = explode(' ', $str);
        for ($i = 0; $i <= sizeof($str) - 1; $i++) {
            //If it is not empty space
            if ($str[$i] != '') {
                //Get last character
                $end = substr($str[$i], -1);
                //Check if last character is in punctuation array
                if (in_array($end, $punctuations)) {
                    $str[$i] = substr($str[$i], 0, -1);
                    $addEnd = TRUE;
                } else {
                    $addEnd = FALSE;
                }
                //If in exceptions, so not convert to title case
                if (!in_array($str[$i], $exceptions)) {
                    $out .= ucwords($str[$i]);
                } else {
                    if (in_array($str[$i], $musts)) {
                        strtoupper($str[$i]);
                    }
                    $out .= $str[$i];
                }

                //Add the end character and space at the end
                if ($addEnd == TRUE) {
                    $out .= $end . ' ';
                } else {
                    $out .= ' ';
                }
            }
        }
        //Capitalise the first letter of output
        $startStr = strtoupper($out[0]); //Get first letter and captialise it
        $endStr = substr($out, 1); //Get remaining string
        $out = $startStr . $endStr; //Join strings
        //Return output
        return $out;
    }

}

/* make upload path */
if (!function_exists('suMakeUploadPath')) {

    function suMakeUploadPath($basePath) {
        $d = date('d');
        $m = date('m');
        $y = date('Y');
        if (!file_exists($basePath . $y)) {
            mkdir($basePath . $y, 0777);
        }
        if (!file_exists($basePath . $y . '/' . $m)) {
            mkdir($basePath . $y . '/' . $m, 0777);
        }
        if (!file_exists($basePath . $y . '/' . $m . '/' . $d)) {
            mkdir($basePath . $y . '/' . $m . '/' . $d, 0777);
        }
        return $uploadPath = $y . '/' . $m . '/' . $d . '/';
    }

}
/* unmake upload path */
if (!function_exists('suUnMakeUploadPath')) {

    function suUnMakeUploadPath($fileName) {
        $ext = suGetExtension($fileName);
        $fileName = explode('/', $fileName);
        $fileName = end($fileName);
        $fileName = explode('.', $fileName);
        $fileName = substr($fileName[0], 0, -14) . '.' . $ext;
        return $fileName;
    }

}


//Generate password
if (!function_exists('suGeneratePassword')) {

    function suGeneratePassword() {
        $colors = array('white', 'yellow', 'pink', 'red', 'orange', 'blue', 'green', 'purple', 'brown', 'black');
        $rand = rand(0, sizeof($colors) - 1);
        $rand = $colors[$rand];
        $time = time();
        $time = substr($time, -4);
        $password = $rand . $time;
        return $password;
    }

}
//Upload multiple files
if (!function_exists('suUploadMultiple')) {

    function suUploadMultiple($fileArray) {

        if (!defined('ALLOWED_ATTACHMENTS_MESSAGE')) {
            define('ALLOWED_ATTACHMENTS_MESSAGE', "The following files were not uploaded due to unallowed file formats.\\n\\n %s \\nOnly %s formats are allowed.\\n");
        }
        global $getSettings;
        $error = '';
        $data = array();
        $response = array();
        $allowed_attachment_formats = $getSettings['allowed_attachment_formats'];
        $allowed_attachment_formats2 = explode(',', $allowed_attachment_formats);
        $uploadPath = suMakeUploadPath(ADMIN_UPLOAD_PATH);
        for ($i = 0; $i <= count($_FILES[$fileArray]['name']); $i++) {
            if (isset($_FILES[$fileArray]['name'][$i]) && ($_FILES[$fileArray]['name'][$i] != '')) {
                $fileName = $_FILES[$fileArray]['name'][$i];
                $src = $_FILES[$fileArray]['tmp_name'][$i];
                if (isset($fileName) && ($fileName != '')) {
                    if (in_array(suGetExtension($fileName), $allowed_attachment_formats2)) {
                        $uid = uniqid();
                        $slugifiedName = suSlugify($fileName, $uid);
                        copy($src, ADMIN_UPLOAD_PATH . $uploadPath . $slugifiedName);
                        $uploadPath . $slugifiedName;
                        array_push($data, $uploadPath . $slugifiedName);
                    } else {
                        $error .= "* " . $fileName . "\\n";
                    }
                }
            }
        }
        if ($error != '') {
            $msg = sprintf(ALLOWED_ATTACHMENTS_MESSAGE, $error, $allowed_attachment_formats);
            suPrintJs("alert('" . $msg . "');");
            $error = 'Yes';
        } else {
            $error = 'No';
        }
        $response = array('error' => $error, 'data' => $data);
        return $response;
    }

}

//FTP transfer
if (!function_exists('suFtpDownload')) {

    function suFtpDownload($hostName, $loginId, $password, $sourceFile, $destinationFile, $debug = FALSE) {

//Set time limit to zero
        set_time_limit(0);

// set up basic connection
        $conn_id = ftp_connect($hostName);


// login with username and password
        $login_result = ftp_login($conn_id, $loginId, $password);

        if ($debug == TRUE) {
// get contents of the current directory
            $contents = ftp_nlist($conn_id, ".");

// output $contents
            echo "<pre>";
            print_r($contents);
            echo "</pre>";
        }



// try to download $sourceFile and save to $destinationFile
        if (ftp_get($conn_id, $destinationFile, $sourceFile, FTP_BINARY)) {
            return TRUE;
        } else {
            return FALSE;
        }


// close the connection
        ftp_close($conn_id);
    }

}

if (!function_exists('suFtpUpload')) {

    function suFtpUpload($hostName, $loginId, $password, $sourceFile, $destinationFile, $debug = FALSE) {
        $ftp_server = $hostName;
        $ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
        $login = ftp_login($ftp_conn, $loginId, $password);
        if ($debug == TRUE) {
// get contents of the current directory
            $contents = ftp_nlist($ftp_conn, ".");

// output $contents
            echo "<pre>";
            print_r($contents);
            echo "</pre>";
        }
        $file = $sourceFile;

// upload file
        if (ftp_put($ftp_conn, $destinationFile, $file, FTP_BINARY)) {
            return TRUE;
        } else {
            return FALSE;
        }

// close connection
        ftp_close($ftp_conn);
    }

}
//Function to check if $src string has an image extension
if (!function_exists('suIsImage')) {

    function suIsImage($src) {
        global $getSettings;
        $allowed_picture_formats = $getSettings['allowed_picture_formats'];
        $allowed_picture_formats = explode(',', $allowed_picture_formats);
        $ext = suGetExtension($src);
        for ($i = 0; $i <= sizeof($allowed_picture_formats) - 1; $i++) {
            if (!in_array($ext, $allowed_picture_formats)) {
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }

}

if (!function_exists('suGetMaxUploadSize')) {

    function suGetMaxUploadSize($detail = FALSE) {
        $max = ini_get('upload_max_filesize') . 'B';
        if ($detail != FALSE) {
            $max = sprintf(MAX_UPLOAD_SIZE_MESSAGE, $max);
        }
        return $max;
    }

}
//Play sound
if (!function_exists('suPlaySound')) {

    function suPlaySound($soundFile) {
        echo "<audio controls autoplay class='hide'><source src='" . $soundFile . "' type='audio/mpeg'>Your browser does not support the audio element.</audio> ";
    }

}
//Validate field types
if (!function_exists('suValidateFieldType')) {

    function suValidateFieldType($fieldValue, $fieldType, $fieldRequired, $fieldName) {
        global $getSettings, $vError, $table;
        $allowed_picture_formats = $getSettings['allowed_picture_formats'];
        $allowed_file_formats = $getSettings['allowed_file_formats'];

//Validate required
        if ($fieldValue == '') {
            if ($fieldRequired == 'yes') {
                $vError[] = sprintf(REQUIRED_FIELD, urldecode($fieldName));
            }
        } else {
//Validate email

            if ($fieldType == 'email') {
                if (!filter_var($fieldValue, FILTER_VALIDATE_EMAIL)) {
                    $vError[] = sprintf(VALID_EMAIL, urldecode($fieldName));
                }
            }


//Validate password
            if ($fieldType == 'password') {
                $password2 = $_POST[suSlugifyStr($fieldName, '_') . CONFIRM_PASSWORD_POSTFIX];
                if ($fieldValue == '' || $password2 == '') {
                    $vError[] = sprintf(REQUIRED_FIELD, urldecode($fieldName));
                }
                if ($fieldValue != $password2) {
                    $vError[] = PASSWORD_MATCH_ERROR;
                }
            }

            //Validate if autocomplete values are as in db
            if ($fieldType == 'autocomplete') {
                //Get table structure
                $sql = "SELECT title, structure, extrasql_on_add FROM " . STRUCTURE_TABLE_NAME . " WHERE live='Yes' AND slug='" . $table . "' LIMIT 0,1";
                $result = suQuery($sql);
                $numRows = $result['num_rows'];
                if ($numRows == 0) {
                    suExit(INVALID_RECORD);
                }
                $row = $result['result'][0];
                $structure = $row['structure'];
                $structure = json_decode($structure, 1);
                for ($i = 0; $i <= sizeof($structure) - 1; $i++) {
                    if ($structure[$i]['Name'] == $fieldName) {
                        $src = $structure[$i]['Source'];
                        //Get data from table
                        $tableField = explode('.', $src);
                        $tableName = $tableField[0];
                        $field = $tableField[1];
                        $field = suSlugifyStr($field, '_');

                        $sql2 = "SELECT " . suJsonExtract('data', $field) . " FROM  " . $tableName . " WHERE lcase(" . suJsonExtract('data', $field, FALSE) . ") = '" . strtolower(suStrip($fieldValue)) . "' AND live='Yes' LIMIT 0,1";
                        $result2 = suQuery($sql2);
                        $numRows2 = $result2['num_rows'];
                        if ($numRows2 == 0) {
                            $vError[] = sprintf(INCORRECT_AUTOCOMPLETE_VALUE, urldecode($fieldName));
                        }
                    }
                }
            }

//Validate integer
            if ($fieldType == 'integer') {
                if ($fieldValue != 0) {
                    if (!filter_var($fieldValue, FILTER_VALIDATE_INT)) {
                        $vError[] = sprintf(VALID_INTEGER, urldecode($fieldName));
                    }
                }
            }
//Validate decimal/float
            if ($fieldType == 'decimal') {
                if ($fieldValue != 0) {
                    if (!filter_var($fieldValue, FILTER_VALIDATE_FLOAT)) {
                        $vError[] = sprintf(VALID_NUMBER, urldecode($fieldName));
                    }
                }
            }
//Validate currency
            if ($fieldType == 'currency') {
                if ($fieldValue != 0) {
                    if (!filter_var($fieldValue, FILTER_VALIDATE_FLOAT)) {
                        $vError[] = sprintf(VALID_NUMBER, urldecode($fieldName));
                    }
                }
            }

//Validate URL
            if ($fieldType == 'url') {
                if (!filter_var($fieldValue, FILTER_VALIDATE_URL)) {
                    $vError[] = sprintf(VALID_NUMBER, urldecode($fieldName));
                }
            }

//Validate date
            if ($fieldType == 'date') {
                $fieldValue = explode('-', $fieldValue);
                if (DATE_FORMAT == 'mm-dd-yy') {
                    $m = $fieldValue[0];
                    $d = $fieldValue[1];
                    $y = $fieldValue[2];
                } elseif (DATE_FORMAT == 'mm-dd-yyyy') {
                    $m = $fieldValue[0];
                    $d = $fieldValue[1];
                    $y = $fieldValue[2];
                } elseif (DATE_FORMAT == 'dd-mm-yy') {
                    $m = $fieldValue[1];
                    $d = $fieldValue[0];
                    $y = $fieldValue[2];
                } elseif (DATE_FORMAT == 'dd-mm-yyyy') {
                    $m = $fieldValue[1];
                    $d = $fieldValue[0];
                    $y = $fieldValue[2];
                }
                if (checkdate($m, $d, $y) == FALSE) {
                    $vError[] = sprintf(VALID_DATE, urldecode($fieldName));
                }
            }


//Validate attachment


            if ($fieldType == 'attachment_field') {
//Get extension
                $extension = explode('.', strtolower($fieldValue));

                $extension = end($extension);
                $allowed = '';
                for ($i = 0; $i <= sizeof($allowed_file_formats); $i++) {
                    $allowed .= $allowed_file_formats[$i] . '/';
                }
                $allowed = substr($allowed, 0, -1);
                if ($fieldValue == '') {
                    $vError[] = sprintf(REQUIRED_FIELD, urldecode($fieldName));
                }
                if (!in_array($extension, $allowed_file_formats)) {
                    $vError[] = sprintf(VALID_FILE_FORMAT, $allowed, urldecode($fieldName));
                }
            }
            //Validate picture
            if ($fieldType == 'picture_field') {
                //Get extension
                $extension = explode('.', strtolower($fieldValue));

                $extension = end($extension);
                $allowed = '';
                for ($i = 0; $i <= sizeof($allowed_picture_formats); $i++) {
                    $allowed .= $allowed_picture_formats[$i] . '/';
                }
                $allowed = substr($allowed, 0, -1);
                if ($fieldValue == '') {
                    $vError[] = sprintf(REQUIRED_FIELD, urldecode($fieldName));
                }
                if (!in_array($extension, $allowed_picture_formats)) {
                    $vError[] = sprintf(VALID_FILE_FORMAT, $allowed, urldecode($fieldName));
                }
            }
        }
    }

    return $vError;
}

//functions written below the above function will not work, so write any functions above the above one