<?php

//Build chat
function suBuildChat($fieldsArray) {
    foreach ($fieldsArray as $key => $value) {
        $arg = '';
        foreach ($value as $key1 => $value1) {
            $arg .= ' ' . $key1 . '="' . $value1 . '" ';
        }
        echo $input = '<input name="' . $key . '" id="' . $key . '" ' . $arg . '/>';
    }
}

//Print array
function print_array($array) {
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

//Copy
function suCopyFolder($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ( $file = readdir($dir))) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if (is_dir($src . '/' . $file)) {
                if ($file != 'db') {//Do not copy DB folder
                    suCopyFolder($src . '/' . $file, $dst . '/' . $file);
                }
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

//Write file
function suWriteFile($file, $content, $arg = 'w') {
    $file = fopen($file, $arg);
    fwrite($file, $content);
    fclose($file);
}

//Print errors
function suPrintError($array) {
    $e = '';
    for ($i = 0; $i <= sizeof($array) - 1; $i++) {
        $e .= "<li>{$array[$i]}</li>";
    }
    $js = "parent.document.getElementById('error-area').innerHTML='" . $e . "'";
    suPrintJS($js);
}

//Print JS
function suPrintJS($str) {

    echo "
<script type=\"text/javascript\">
		{$str}
		</script>
";
}

/* Crypt */
if (!function_exists('suCrypt')) {

    function suCrypt($str) {
        return base64_encode(base64_encode($str));
    }

}