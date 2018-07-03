<?php

/* Magic functions */
/* Check magic login */
if (!function_exists('checkMagicLogin')) {

//Check if logged in
    function checkMagicLogin() {
        if ($_SESSION[SESSION_PREFIX . 'magic_login'] == '') {
            $url = MAGIC_URL . 'login' . PHP_EXTENSION . '/';
            suPrintJs("parent.window.location.href='{$url}';");
            exit();
        }
    }

}
/* Build admin form links */
if (!function_exists('suBuildFormLinks')) {

//Pass the viewable pages array
    function suBuildFormLinks() {
        $sql = "SELECT title,slug FROM " . STRUCTURE_TABLE_NAME . " WHERE live='Yes' AND display='Yes' ORDER BY sort_order,title";
        $result = suQuery($sql);
        $numRows = $result['num_rows'];
        if ($numRows > 0) {
            $row = $result['result'];
            foreach ($row as $value) {
                //If the user has view permissions, then show in link
                if (in_array(ADMIN_GROUP_NAME, $_SESSION[SESSION_PREFIX . 'user_group'])) {
                    echo "<li><a id=\"lk_" . suUnstrip($value['slug']) . "\" href=\"" . ADMIN_URL . "manage" . PHP_EXTENSION . "/" . suTablify(suUnstrip($value['slug'])) . "/\"><i class=\"fa fa-angle-double-right\"></i>&nbsp;&nbsp;" . suUnstrip($value['title']) . "</a></li>";
                } else {
                    $menuAccess = suCheckAccess($value['slug'], 'viewables');
                    if ($menuAccess == TRUE) {
                        echo "<li><a id=\"lk_" . suUnstrip($value['slug']) . "\" href=\"" . ADMIN_URL . "manage" . PHP_EXTENSION . "/" . suTablify(suUnstrip($value['slug'])) . "/\"><i class=\"fa fa-angle-double-right\"></i>&nbsp;&nbsp;" . suUnstrip($value['title']) . "</a></li>";
                    }
                }
            }
        }
    }

}

//Add fields from remote
if (!function_exists('suProcessForm')) {

    function suProcessForm($structure) {

        global $vError, $saveForLater, $mode;
        if ($mode != 'update') {
            $mode = 'add';
        }
        $data = array(); //Array to store data
        for ($i = 0; $i < sizeof($structure); $i++) {
            //If this is not add more section
            if ($structure[$i]['Type'] != 'add_more_section') {//If not add-more section
                //Stop field injection
                //Check data type
                //Validate if data type is attachment or picture
                if ($structure[$i]['Type'] == 'attachment_field' || $structure[$i]['Type'] == 'picture_field') {//If type is attachment or picture
                    if ($saveForLater == FALSE) {
                        if ($mode == 'add') {
                            suValidateFieldType($_FILES[$structure[$i]['Slug']]['name'], $structure[$i]['Type'], $structure[$i]['Required'], $structure[$i]['Name']);
                        }
                    }
                    //Make upload array
                    if ($_FILES[$structure[$i]['Slug']]['name'] != '') {//If the field is not empty
                        $uid = uniqid();
                        $fileName = $_FILES[$structure[$i]['Slug']]['name'];
                        $fileName = suSlugify($fileName, $uid);
                        $uploadPath = suMakeUploadPath(ADMIN_UPLOAD_PATH);
                        $uploadPath = $uploadPath . $fileName;

                        if ($structure[$i]['Type'] == 'picture_field') {//If picture
                            $width = $structure[$i]['ImageWidth'];
                            $height = $structure[$i]['ImageHeight'];
                            $data['filesToUpload'][] = array('name' => $structure[$i]['Slug'], 'type' => 'picture', 'value' => $uploadPath, 'width' => $width, 'height' => $height);
                        } else {//If other file
                            $data['filesToUpload'][] = array('name' => $structure[$i]['Slug'], 'type' => 'file', 'value' => $uploadPath);
                        }
                    }//If the field is not empty

                    if ($mode == 'update') {//If update mode
                        //If not uploading new file
                        if ($_FILES[$structure[$i]['Slug']]['name'] == '') {
                            $uploadPath = $_POST[RESERVED_PREVIOUS_PREFEX . $structure[$i]['Slug']];
                            $data[$structure[$i]['Slug']] = $uploadPath;
                        } else {
                            $data[$structure[$i]['Slug']] = $uploadPath;
                        }
                    } else {//If add mode
                        if ($_FILES[$structure[$i]['Slug']]['name'] != '') {
                            $data[$structure[$i]['Slug']] = $uploadPath;
                        }
                    }
                } else {//If type is not attachment or picture
                    //Validate if data type is not attachment or picture or hide on add field
                    if ($saveForLater == FALSE) {

                        suValidateFieldType($_POST[$structure[$i]['Slug']], $structure[$i]['Type'], $structure[$i]['Required'], $structure[$i]['Name']);
                    }
                }//If type is attachment or picture
                //Make insertable values
                if ($structure[$i]['Type'] == 'date') {//If date, change to DB date
                    $data[$structure[$i]['Slug']] = suDate2Db($_POST[$structure[$i]['Slug']]);
                } elseif ($structure[$i]['Type'] == 'password') {//If password, encrypt it
                    $data[$structure[$i]['Slug']] = suCrypt($_POST[$structure[$i]['Slug']]);
                } else {
                    if (is_array($_POST[$structure[$i]['Slug']])) {
                        $ll = array();
                        for ($l = 0; $l <= sizeof($_POST[$structure[$i]['Slug']]) - 1; $l++) {
                            array_push($ll, suStrip($_POST[$structure[$i]['Slug']][$l]));
                        }

                        $data[$structure[$i]['Slug']] = $ll;
                    } else {
                        if ($structure[$i]['Type'] == 'attachment_field' || $structure[$i]['Type'] == 'picture_field') {//If picture field or attachment
                        } else {//If other type of data
                            $data[$structure[$i]['Slug']] = suStrip($_POST[$structure[$i]['Slug']]);
                        }//If picture field or attachment
                    }
                }
            } else {//If add-more section
                $nextArray = $structure[$i]['Source'];
                //Get number of posted fields
                $size = $_POST['_____size_' . $nextArray];
                //Get structure
                $sql = "SELECT structure FROM " . STRUCTURE_TABLE_NAME . " WHERE live='Yes' AND slug='" . $nextArray . "' LIMIT 0,1";


                $result = suQuery($sql);
                $result['result'][0] = suUnstrip($result['result'][0]);
                $row = $result['result'][0];
                $structure2 = $row['structure'];
                $fields = array();
                $data2 = '';
                //Get the number of fields
                for ($s = 0; $s < sizeof($structure2); $s++) {//Loop through form elements
                    for ($k = 0; $k < $size; $k++) {//Loop through submitted elements
                        $l = $k + 1;
                        //Check data type
                        //Validate if data type is attachment or picture
                        if ($structure2[$s]['Type'] == 'attachment_field' || $structure2[$s]['Type'] == 'picture_field') {//If attachment or picture
                            if ($saveForLater == FALSE) {
                                if ($mode == 'add') {//If add
                                    suValidateFieldType($_FILES[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l]['name'], $structure2[$s]['Type'], $structure2[$s]['Required'], $structure2[$s]['Name']);
                                } else {//If update
                                    if ($_FILES[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l]['name'] != '') {
                                        suValidateFieldType($_FILES[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l]['name'], $structure2[$s]['Type'], $structure2[$s]['Required'], $structure2[$s]['Name']);
                                    }
                                }
                            }
                            //Make upload array
                            if ($mode == 'add') {//If add
                                $uid = uniqid();
                                $fileName = $_FILES[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l]['name'];
                                $fileName = suSlugify($fileName, $uid);
                                $uploadPath = suMakeUploadPath(ADMIN_UPLOAD_PATH);
                                $uploadPath = $uploadPath . $fileName;

                                if ($structure2[$s]['Type'] == 'picture_field') {//If picture
                                    $width = $structure2[$s]['ImageWidth'];
                                    $height = $structure2[$s]['ImageHeight'];
                                    $data['filesToUpload'][] = array('name' => $structure2[$s]['Slug'] . FIELD_SEPARATOR . $l, 'type' => 'picture', 'value' => $uploadPath, 'width' => $width, 'height' => $height);
                                } else {
                                    $data['filesToUpload'][] = array('name' => $structure2[$s]['Slug'] . FIELD_SEPARATOR . $l, 'type' => 'file', 'value' => $uploadPath);
                                }
                            } else {//If update
                                if ($_FILES[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l]['name'] != '') {
                                    $uid = uniqid();
                                    $fileName = $_FILES[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l]['name'];
                                    $fileName = suSlugify($fileName, $uid);
                                    $uploadPath = suMakeUploadPath(ADMIN_UPLOAD_PATH);
                                    $uploadPath = $uploadPath . $fileName;

                                    if ($structure2[$s]['Type'] == 'picture_field') {//If picture
                                        $width = $structure2[$s]['ImageWidth'];
                                        $height = $structure2[$s]['ImageHeight'];
                                        $data['filesToUpload'][] = array('name' => $structure2[$s]['Slug'] . FIELD_SEPARATOR . $l, 'type' => 'picture', 'value' => $uploadPath, 'width' => $width, 'height' => $height);
                                    } else {
                                        $data['filesToUpload'][] = array('name' => $structure2[$s]['Slug'] . FIELD_SEPARATOR . $l, 'type' => 'file', 'value' => $uploadPath);
                                    }
                                }
                            }
                        } else {//If not attachment or picture
                            //Validate if data type is not attachment or picture or hide on add field
                            if ($saveForLater == FALSE) {
                                //Regular entry 
                                if (stristr($structure2[$s]['Type'], 'checkbox')) {
                                    //if ($structure2[$s]['Type'] == 'checkbox' || $structure2[$s]['Type'] == 'checkbox_from_db') {
                                    // echo "";
                                    suValidateFieldType($_POST[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l], $structure2[$s]['Type'], $structure2[$s]['Required'], $structure2[$s]['Name']);
                                } else {
                                    //Regular textbox entry
                                    suValidateFieldType($_POST[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l], $structure2[$s]['Type'], $structure2[$s]['Required'], $structure2[$s]['Name']);
                                }
                            }
                        }//If attachment or picture
                        //Build data variable

                        if ($structure2[$s]['Type'] == 'date') {//If date, change to DB date
                            $data2[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l] = suDate2Db($_POST[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l]);
                        } elseif ($structure2[$s]['Type'] == 'password') {//If password, encrypt it
                            $data2[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l] = suCrypt($_POST[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l]);
                        } else {
                            if (is_array($_POST[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l])) {
                                for ($t = 0; $t < sizeof($_POST[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l]); $t++) {
                                    $data2[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l][$t] = suStrip($_POST[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l][$t]);
                                }
                            } else {
                                if ($structure2[$s]['Type'] == 'attachment_field' || $structure2[$s]['Type'] == 'picture_field') {//If attachment or picture
                                    if ($mode == 'update') {//If update mode
                                        //If not uploading new file
                                        if ($_FILES[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l]['name'] == '') {
                                            $uploadPath = RESERVED_PREVIOUS_PREFEX . $structure2[$s]['Slug'] . FIELD_SEPARATOR . $l;
                                            $data2[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l] = $_POST[$uploadPath];
                                        } else {
                                            $data2[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l] = $uploadPath;
                                        }
                                    } else {//If add mode
                                        if ($_FILES[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l]['name'] == '') {
                                            $uploadPath = RESERVED_PREVIOUS_PREFEX . $structure2[$s]['Slug'] . FIELD_SEPARATOR . $l;
                                            $data2[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l] = $_POST[$uploadPath];
                                        } else {
                                            $data2[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l] = $uploadPath;
                                        }
                                    }
                                } else {//If not attachment or picture
                                    $data2[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l] = suStrip($_POST[$structure2[$s]['Slug'] . FIELD_SEPARATOR . $l]);
                                }//If attachment or picture
                            }
                        }
                    }
                }


                $data[$nextArray] = $data2;
            }//If not or is add-more section
        }
        //validate errors
        if ($saveForLater == FALSE) {
            if (sizeof($vError) > 0) {
                suValdationErrors($vError);
                exit;
            } else {
                return $data;
            }
        } else {
            return $data;
        }
    }

}
//Build form fields
if (!function_exists('suBuildField')) {

    function suBuildField($arr, $mode, $labelRequirement = 'No') {//mode is add or update
        global $getSettings, $today, $duplicate, $addAccess, $save_for_later, $mode, $documentReadyUid, $tableSegment, $table, $rid, $pageMode;

        $mode2 = $mode;
        if ($duplicate == TRUE) {
            $mode = 'add';
            $mode2 = 'update';
        }

        $clearIcon = '';
        //Unstrip
        $arr = suUnstrip($arr);
        $arr['Slug'] = $arr['Slug'];
        $arr['_____value'] = $arr['_____value'];
        $arr['CssClass'] = $arr['CssClass'];
        $arr['OnClick'] = $arr['OnClick'];
        $arr['OnKeyUp'] = $arr['OnKeyUp'];
        $arr['OnKeyPress'] = $arr['OnKeyPress'];
        $arr['OnBlur'] = $arr['OnBlur'];
        $arr['OnChange'] = $arr['OnChange'];
        $arr['Required'] = $arr['Required'];
        $arr['RequiredSaveForLater'] = $arr['RequiredSaveForLater'];
        $arr['Default'] = $arr['Default'];
        $arr['ReadOnlyAdd'] = $arr['ReadOnlyAdd'];
        $arr['ReadOnlyUpdate'] = $arr['ReadOnlyUpdate'];
        $arr['HideOnUpdate'] = $arr['HideOnUpdate'];
        $arr['Name'] = $arr['Name'];
        $arr['Length'] = $arr['Length'];
        switch ($arr['Type']) {
            //Textbox
            case "textbox":
                $arg = array('type' => 'text', 'name' => $arr['Slug'], 'id' => $arr['Slug'], 'autocomplete' => 'off');
                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['Length'] != '') {
                    $arg = array_merge($arg, array('data-parsley-maxlength' => $arr['Length']));
                    $arg = array_merge($arg, array('maxlength' => $arr['Length']));
                }
                if ($arr['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr['CssClass']));
                }
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }
                if ($arr['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr['OnKeyUp']));
                }
                if ($arr['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr['OnKeyPress']));
                }
                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }
                //Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($pageMode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($pageMode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }


                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a title='" . CLEAR_FIELD . "' id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }
                    //Remove the clear icon if readonly
                    if ($pageMode == 'add' && $arr['ReadOnlyAdd'] == 'yes') {
                        $clearIcon = "";
                    }
                    if ($pageMode == 'update' && $arr['ReadOnlyUpdate'] == 'yes') {
                        $clearIcon = "";
                    }
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . suUnstrip($arr['Name'])));
                }
                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));

                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));
                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suInput('input', $arg);
                echo '</span>';
                break;



            //Phone
            case "phone":
                $arg = array('type' => 'text', 'name' => $arr['Slug'], 'id' => $arr['Slug'], 'autocomplete' => 'off');
                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);

                if ($arr['Length'] != '') {
                    $arg = array_merge($arg, array('data-parsley-maxlength' => $arr['Length']));
                    $arg = array_merge($arg, array('maxlength' => $arr['Length']));
                    //Specify max length for input type number
                    $max = '';
                    for ($n = 0; $n < $arr['Length']; $n++) {
                        $max .= '9';
                    }
                    $arg = array_merge($arg, array('min' => 0));
                    $arg = array_merge($arg, array('max' => $max));
                }
                if ($arr['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr['CssClass'] . ' integer'));
                } else {
                    $arg = array_merge($arg, array('class' => 'integer'));
                }
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }
                if ($arr['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr['OnKeyUp']));
                }
                if ($arr['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr['OnKeyPress']));
                }
                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }
                //Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($pageMode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($pageMode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }

                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a title='" . CLEAR_FIELD . "' id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }
//Remove the clear icon if readonly
                    if ($pageMode == 'add' && $arr['ReadOnlyAdd'] == 'yes') {
                        $clearIcon = "";
                    }
                    if ($pageMode == 'update' && $arr['ReadOnlyUpdate'] == 'yes') {
                        $clearIcon = "";
                    }
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name']));
                }
                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));
                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suInput('input', $arg);
                echo '</span>';
                break;


            //Hidden
            case "hidden":
                $arg = array('type' => 'hidden', 'name' => $arr['Slug'], 'id' => $arr['Slug']);
                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build title
                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suInput('input', $arg);
                echo '</span>';

                break;

            //JSON
            case "json":
                $arg = array('type' => 'hidden', 'name' => $arr['Slug'], 'id' => $arr['Slug']);
                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build title
                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suInput('input', $arg);
                echo '</span>';

                break;

            //IP Address
            case "ip_address":
                $arg = array('type' => 'hidden', 'name' => $arr['Slug'], 'id' => $arr['Slug']);
                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                } else {
                    $arg = array_merge($arg, array('value' => $_SERVER['REMOTE_ADDR']));
                }
                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build title
                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suInput('input', $arg);
                echo '</span>';
                break;

            //Decimal
            case "decimal":
                $arg = array('type' => 'number', 'step' => 'any', 'name' => $arr['Slug'], 'id' => $arr['Slug'], 'autocomplete' => 'off');
                //Parsley
                $arg = array_merge($arg, array('data-parsley-type' => 'number', 'data-parsley-trigger' => 'keyup'));

                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['Length'] != '') {
                    $arg = array_merge($arg, array('data-parsley-maxlength' => $arr['Length']));
                    $arg = array_merge($arg, array('maxlength' => $arr['Length']));
                    //Specify max length for input type number
                    $max = '';
                    $min = '-';
                    for ($n = 0; $n < $arr['Length']; $n++) {
                        $max .= '9';
                        $min .= '9';
                    }
                    $arg = array_merge($arg, array('min' => $min));
                    $arg = array_merge($arg, array('max' => $max));
                }
                if ($arr['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr['CssClass'] . ' decimal'));
                } else {
                    $arg = array_merge($arg, array('class' => 'decimal'));
                }
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }
                if ($arr['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr['OnKeyUp']));
                }
                if ($arr['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr['OnKeyPress']));
                }
                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }
                //Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($pageMode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($pageMode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a title='" . CLEAR_FIELD . "' id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }
                    //Remove the clear icon if readonly
                    if ($pageMode == 'add' && $arr['ReadOnlyAdd'] == 'yes') {
                        $clearIcon = "";
                    }
                    if ($pageMode == 'update' && $arr['ReadOnlyUpdate'] == 'yes') {
                        $clearIcon = "";
                    }
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name']));
                }
                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));
                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suInput('input', $arg);
                echo '</span>';
                break;

            //Integer
            case "integer":
                $arg = array('type' => 'number', 'name' => $arr['Slug'], 'id' => $arr['Slug'], 'autocomplete' => 'off');
                //Parsley
                $arg = array_merge($arg, array('data-parsley-type' => 'integer', 'data-parsley-trigger' => 'keyup'));

                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['Length'] != '') {
                    $arg = array_merge($arg, array('data-parsley-maxlength' => $arr['Length']));
                    $arg = array_merge($arg, array('maxlength' => $arr['Length']));
                    //Specify max length for input type number
                    $max = '';
                    $min = '-';
                    for ($n = 0; $n < $arr['Length']; $n++) {
                        $max .= '9';
                        $min .= '9';
                    }
                    $arg = array_merge($arg, array('min' => $min));
                    $arg = array_merge($arg, array('max' => $max));
                }
                if ($arr['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr['CssClass'] . ' integer'));
                } else {
                    $arg = array_merge($arg, array('class' => 'integer'));
                }
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }
                if ($arr['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr['OnKeyUp']));
                }
                if ($arr['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr['OnKeyPress']));
                }
                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }
                //Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($pageMode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($pageMode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a title='" . CLEAR_FIELD . "' id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }
                    //Remove the clear icon if readonly
                    if ($pageMode == 'add' && $arr['ReadOnlyAdd'] == 'yes') {
                        $clearIcon = "";
                    }
                    if ($pageMode == 'update' && $arr['ReadOnlyUpdate'] == 'yes') {
                        $clearIcon = "";
                    }
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name']));
                }
                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));
                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suInput('input', $arg);
                echo '</span>';
                break;

            //Email
            case "email":
                $arg = array('type' => 'email', 'name' => $arr['Slug'], 'id' => $arr['Slug'], 'autocomplete' => 'off');
                //Parsley
                $arg = array_merge($arg, array('data-parsley-type' => 'email', 'data-parsley-trigger' => 'keyup'));

                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['Length'] != '') {
                    $arg = array_merge($arg, array('data-parsley-maxlength' => $arr['Length']));
                    $arg = array_merge($arg, array('maxlength' => $arr['Length']));
                }
                if ($arr['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr['CssClass']));
                }
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }
                if ($arr['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr['OnKeyUp']));
                }
                if ($arr['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr['OnKeyPress']));
                }
                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }
                //Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($pageMode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }


                if ($pageMode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($pageMode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a title='" . CLEAR_FIELD . "' id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }
                    //Remove the clear icon if readonly
                    if ($pageMode == 'add' && $arr['ReadOnlyAdd'] == 'yes') {
                        $clearIcon = "";
                    }
                    if ($pageMode == 'update' && $arr['ReadOnlyUpdate'] == 'yes') {
                        $clearIcon = "";
                    }
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name']));
                }
                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));

                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suInput('input', $arg);
                echo '</span>';
                break;

            //Year
            case "year":
                $arg = array();
                $moreArg = '';

                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                } else {
                    if ($pageMode == 'add') {
                        $arr['Default'] = date('Y') + ($arr['Default']);
                    }
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr['CssClass']));
                }
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }
                if ($arr['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr['OnKeyUp']));
                }
                if ($arr['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr['OnKeyPress']));
                }
                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }
                //Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($pageMode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($pageMode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));

                foreach ($arg as $argKey => $argValue) {
                    $moreArg .= $argKey . "='" . $argValue . "' ";
                }

                //Build label if required
                if ($labelRequirement == 'Yes') {
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ":</label>";
                    $o[''] = 'Select..';
                } else {
                    $o[''] = $requiredStar . $arr['Name'] . '..';
                }
                //Get start and end year
                $period = explode(',', suUnstrip($arr['Length']));
                $startYear = trim($period[0]);
                $startYear = date('Y') + ($startYear);
                $endYear = trim($period[1]);
                $endYear = date('Y') + ($endYear);
                for ($i = $startYear; $i <= $endYear; $i++) {
                    $o[$i] = $i;
                }
                $options = $o;
                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suDropdown($arr['Slug'], $options, $arr['Default'], $moreArg);
                echo '</span>';

                break;
            //Dropdown
            case "dropdown":
                $arg = array();
                $moreArg = '';

                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr['CssClass']));
                }
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }
                if ($arr['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr['OnKeyUp']));
                }
                if ($arr['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr['OnKeyPress']));
                }
                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }
                //Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($pageMode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($pageMode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));

                foreach ($arg as $argKey => $argValue) {
                    $moreArg .= $argKey . "='" . $argValue . "' ";
                }

                //Build label if required
                if ($labelRequirement == 'Yes') {
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ":</label>";
                    $o[''] = 'Select..';
                } else {
                    $o[''] = $requiredStar . $arr['Name'] . '..';
                }
                $optionsItems = explode(',', suUnstrip($arr['Length']));
                for ($i = 0; $i <= sizeof($optionsItems) - 1; $i++) {

                    $optionsItems[$i] = trim($optionsItems[$i]);
                    $o[$optionsItems[$i]] = $optionsItems[$i];
                }
                $options = $o;
                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suDropdown($arr['Slug'], $options, $arr['Default'], $moreArg);
                echo '</span>';

                break;

            //Dropdown from DB
            case "dropdown_from_db":

                $arg = array();
                $moreArg = '';
                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr['CssClass']));
                }
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }
                if ($arr['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr['OnKeyUp']));
                }
                if ($arr['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr['OnKeyPress']));
                }
                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }
                if ($pageMode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($pageMode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));

                foreach ($arg as $argKey => $argValue) {
                    $moreArg .= $argKey . "='" . $argValue . "' ";
                }

                //Build label if required
                if ($labelRequirement == 'Yes') {
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ":</label>";
                    $optionsSelect = array('' => 'Select..');
                } else {
                    $optionsSelect = array('' => $requiredStar . $arr['Name'] . '..');
                }


                //Get data from table
                $tableField = explode('.', $arr['Source']);
                $table = $tableField[0];
                $field = $tableField[1];
                $field = suSlugifyStr($field, '_');

                $extraSql = html_entity_decode(html_entity_decode($arr['ExtraSQL']));


                $sql = "SELECT " . suJsonExtract('data', $field) . " FROM  " . $table . " WHERE live='Yes'  " . $extraSql . " ORDER BY " . $field;

                $result = suQuery($sql);
                $o = $result['result'];

                $x = array();

                foreach ($o as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        $x[$value[$key2]] = $value2;
                    }
                }
                $options = array_merge($optionsSelect, $x);

                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suDropdown($arr['Slug'], $options, $arr['Default'], $moreArg);
                echo '</span>';
                if ($addAccess == TRUE) {
                    suPrintJS($arr['Slug'] . '.options.add(new Option("+", ""), ' . $arr['Slug'] . '.options[1]);' . '$("#' . $arr['Slug'] . '").change(function () {doOverlay(this, "' . ADMIN_URL . 'add.php/' . $table . '/?overlay=1&sourceField=' . $field . '&reloadField=' . $arr['Slug'] . '");});');
                }
                break;



            //Searchable Dropdown
            case "searchable_dropdown":
                $arg = array();
                $moreArg = '';
                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr['CssClass']));
                }
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }
                if ($arr['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr['OnKeyUp']));
                }
                if ($arr['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr['OnKeyPress']));
                }
                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }
                if ($pageMode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($pageMode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));

                foreach ($arg as $argKey => $argValue) {
                    $moreArg .= $argKey . "='" . $argValue . "' ";
                }

                //Build label if required
                if ($labelRequirement == 'Yes') {
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ":</label>";
                    $optionsSelect = array('' => 'Select..');
                } else {
                    $optionsSelect = array('' => $requiredStar . $arr['Name'] . '..');
                }

                $optionsItems = explode(',', suUnstrip($arr['Length']));
                for ($i = 0; $i <= sizeof($optionsItems) - 1; $i++) {
                    $optionsItems[$i] = trim($optionsItems[$i]);
                    $o[$optionsItems[$i]] = $optionsItems[$i];
                }
                $options = array_merge($optionsSelect, $o);
                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suDropdown($arr['Slug'], $options, $arr['Default'], $moreArg);
                echo '</span>';
                echo "
                    <script id=\"searchable_dd_" . $arr['Slug'] . "\">
                    $(function() {
                        $('#" . $arr['Slug'] . "').chosen();
                    });
                </script>
                ";
                break;


            //Searchable Dropdown from database
            case "searchable_dropdown_from_db":
                $arg = array();
                $moreArg = '';
                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr['CssClass']));
                }
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }
                if ($arr['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr['OnKeyUp']));
                }
                if ($arr['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr['OnKeyPress']));
                }
                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }
                //Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }

//Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));

                foreach ($arg as $argKey => $argValue) {
                    $moreArg .= $argKey . "='" . $argValue . "' ";
                }

                //Build label if required
                if ($labelRequirement == 'Yes') {
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ":</label>";
                    $optionsSelect = array('' => 'Select..');
                } else {
                    $optionsSelect = array('' => $requiredStar . $arr['Name'] . '..');
                }

                //Get data from table
                $tableField = explode('.', $arr['Source']);
                $table = $tableField[0];
                $field = $tableField[1];
                $field = suSlugifyStr($field, '_');
                $extraSql = html_entity_decode(html_entity_decode($arr['ExtraSQL']));


                $sql = "SELECT " . suJsonExtract('data', $field) . " FROM  " . $table . " WHERE live='Yes'  " . $extraSql . " ORDER BY " . $field;

                $result = suQuery($sql);
                $o = $result['result'];

                $x = array();

                foreach ($o as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        $x[$value[$key2]] = $value2;
                    }
                }
                $options = array_merge($optionsSelect, $x);
                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suDropdown($arr['Slug'], $options, $arr['Default'], $moreArg);
                echo '</span>';
                echo "
                    <script id=\"searchable_dd_db_" . $arr['Slug'] . "\">
                        $(function() {
                            $('#" . $arr['Slug'] . "').chosen();
                        });
                    </script>
                    ";
                break;

            //Password
            case "password":
                $arg = array('type' => 'password', 'name' => $arr['Slug'], 'id' => $arr['Slug'], 'autocomplete' => 'off');
                $arg2 = array('type' => 'password', 'name' => $arr['Slug'] . CONFIRM_PASSWORD_POSTFIX, 'id' => $arr['Slug'] . CONFIRM_PASSWORD_POSTFIX, 'autocomplete' => 'off');
                if ($arr['Length'] != '') {
                    $arg = array_merge($arg, array('data-parsley-maxlength' => $arr['Length']));
                    $arg = array_merge($arg, array('maxlength' => $arr['Length']));
                    $arg2 = array_merge($arg2, array('data-parsley-maxlength' => $arr['Length']));
                    $arg2 = array_merge($arg2, array('maxlength' => $arr['Length']));
                }
                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr['CssClass']));
                    $arg2 = array_merge($arg2, array('class' => $arr['CssClass']));
                }
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }
                if ($arr['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr['OnKeyUp']));
                }
                if ($arr['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr['OnKeyPress']));
                }
                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }
                //Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => suDecrypt($arr['Default'])));
                    $arg2 = array_merge($arg2, array('value' => suDecrypt($arr['Default'])));
                }
                if ($pageMode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($pageMode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                $arg2 = array_merge($arg2, array('tabindex' => $arr['TabIndex']));
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));
                $arg2 = array_merge($arg2, array('title' => 'Confirm ' . $arr['Name']));

                //Build label if required
                if ($labelRequirement == 'Yes') {
                    if ($getSettings['toggle_password'] == '1') {
                        $togglePassword = "<a title='" . PREVIEW_PASSWORD . "' href='javascript:;' onclick=\"doTogglePassword('" . $arr['Slug'] . "','" . CONFIRM_PASSWORD_POSTFIX . "')\"><i id=\"password-eye\" class='fa fa-eye'></i></a>";
                    } else {
                        $togglePassword = '';
                    }

                    echo '<div class="row">';
                    echo '<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">';
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": " . $togglePassword . "</label>";
                    echo '<span id="data_span_' . $arr['Slug'] . '">';
                    echo suInput('input', $arg);
                    echo '</span>';
                    echo '</div>';

                    echo '<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">';
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . 'Confirm ' . $arr['Name'] . ": " . "</label>";
                    echo suInput('input', $arg2);
                    echo '</div>';
                    echo '</div>';
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name']));
                    $arg2 = array_merge($arg2, array('placeholder' => $requiredStar . 'Confirm ' . $arr['Name']));
                    echo '<div class="row">';
                    echo '<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">';
                    echo '<span id="data_span_' . $arr['Slug'] . '">';
                    echo suInput('input', $arg);
                    echo '</span>';
                    echo '</div>';
                    echo '<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">';
                    echo suInput('input', $arg2);
                    echo '</div>';
                    echo '</div>';
                }
                break;
            //picture
            case "picture_field":
                $js = "$('#" . $arr['Slug'] . "_file').html(doStripFakepath($('#" . $arr['Slug'] . "').val())); ";
                //$js = "$('#" . $arr['Slug'] . "_file').html($('#" . $arr['Slug'] . "').val()); ";
                $arg = array('type' => 'file', 'name' => $arr['Slug'], 'id' => $arr['Slug'], 'class' => 'hide', 'onchange' => $js);


                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }

                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }
                //Required
                $requiredStar = '';
                if ($pageMode == 'add') {
                    if ($save_for_later == 'No') {
                        if ($arr['Required'] == 'yes') {
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                            $requiredStar = '*';
                        }
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $requiredStar = '*';
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                        }
                    }
                }


                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($pageMode == 'update') {
                    if (file_exists(ADMIN_UPLOAD_PATH . $arr['Default']) && ($arr['Default'] != '')) {

                        echo "<a target='_blank' href='" . UPLOAD_URL . $arr['Default'] . "' class='imgThumb' style='background:url(" . UPLOAD_URL . $arr['Default'] . ")'></a><p><a target='_blank' href='" . BASE_URL . 'files/' . $arr['Default'] . "'>" . suUnMakeUploadPath($arr['Default']) . "</a></p>";
                    }
                }

                //Get allowed picture formats
                for ($l = 0; $l <= sizeof($getSettings['allowed_picture_formats']) - 1; $l++) {
                    $allowed .= $getSettings['allowed_picture_formats'][$l] . '/';
                }
                $allowed = substr($allowed, 0, -1);
                $allowed = sprintf(VALID_FILE_FORMATS, urldecode($allowed));
                $allowed .= ' ' . suGetMaxUploadSize() . ' Max.';
                //Build label if required
                if ($labelRequirement == 'Yes') {

                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ":</label> <sup id='sup_" . $arr['Slug'] . "'>" . $allowed . "</sup>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name']));
                }
                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));
                echo suInput('input', $arg);
                if ($pageMode == 'update') {
                    $arg2 = array('type' => 'hidden', 'name' => RESERVED_PREVIOUS_PREFEX . $arr['Slug'], 'id' => RESERVED_PREVIOUS_PREFEX . $arr['Slug'], 'value' => $arr['_____value']);
                    echo suInput('input', $arg2);
                }


                $js = "$('#" . $arr['Slug'] . "').trigger('click');";
                $arg = array('name' => $arr['Slug'] . '_clip', 'id' => $arr['Slug'] . '_clip', 'class' => 'form-control', 'onclick' => $js, 'style' => 'text-align:left;cursor:pointer;');
                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));

                if ($labelRequirement == 'Yes') {
                    echo '<span id="data_span_' . $arr['Slug'] . '">';
                    echo suInput('div', $arg, '<i class="fa fa-paperclip"></i> ' . $requiredStar . $arr['Name'], TRUE);
                    echo '</span>';
                } else {
                    echo '<span id="data_span_' . $arr['Slug'] . '">';
                    echo suInput('div', $arg, '<i class="fa fa-paperclip"></i> ' . $requiredStar . $arr['Name'] . " <sup id='sup_" . $arr['Slug'] . "'>" . $allowed . "</sup>", TRUE);
                    echo '</span>';
                }
                echo '<div id="' . $arr['Slug'] . '_file" class="small color-gray"></div>';
                break;
            //Attachment
            case "attachment_field":

                $js = "$('#" . $arr['Slug'] . "_file').html(doStripFakepath($('#" . $arr['Slug'] . "').val())); ";
                $arg = array('type' => 'file', 'name' => $arr['Slug'], 'id' => $arr['Slug'], 'class' => 'hide', 'onchange' => $js);

                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }

                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }
//Required
                $requiredStar = '';
                if ($pageMode == 'add') {
                    if ($save_for_later == 'No') {
                        if ($arr['Required'] == 'yes') {
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                            $requiredStar = '*';
                        }
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $requiredStar = '*';
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                        }
                    }
                }

                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }

                if ($pageMode == 'update') {
                    if (file_exists(ADMIN_UPLOAD_PATH . $arr['Default']) && ($arr['Default'] != '')) {
                        $ext = suGetExtension($arr['Default']);
                        if ($ext == 'pdf') {
                            $faIcon = 'fa-file-pdf-o';
                        } elseif ($ext == 'doc') {
                            $faIcon = 'fa-file-word-o';
                        } elseif ($ext == 'docx') {
                            $faIcon = 'fa-file-word-o';
                        } elseif ($ext == 'xls') {
                            $faIcon = 'fa-file-excel-o';
                        } elseif ($ext == 'xlsx') {
                            $faIcon = 'fa-file-excel-o';
                        } elseif ($ext == 'ppt') {
                            $faIcon = 'fa-file-powerpoint-o';
                        } elseif ($ext == 'pptx') {
                            $faIcon = 'fa-file-powerpoint-o';
                        } elseif ($ext == 'gif') {
                            $faIcon = 'fa-image';
                        } elseif ($ext == 'jpg') {
                            $faIcon = 'fa-image';
                        } elseif ($ext == 'png') {
                            $faIcon = 'fa-image';
                        } elseif ($ext == 'jpeg') {
                            $faIcon = 'fa-image';
                        } else {
                            $faIcon = 'fa-file-o';
                        }
                        echo "<a target='_blank' href='" . UPLOAD_URL . $arr['Default'] . "' class='fa " . $faIcon . " attachmentThumb size-400'></a><p><a target='_blank' href='" . UPLOAD_URL . $arr['Default'] . "'>" . suUnMakeUploadPath($arr['Default']) . "</a></p>";
                    }
                }
//Get allowed picture formats
                for ($l = 0; $l <= sizeof($getSettings['allowed_file_formats']) - 1; $l++) {
                    $allowed .= $getSettings['allowed_file_formats'][$l] . '/';
                }
                $allowed = substr($allowed, 0, -1);
                $allowed = sprintf(VALID_FILE_FORMATS, urldecode($allowed));
                $allowed .= ' ' . suGetMaxUploadSize() . ' Max.';

                //Build label if required
                if ($labelRequirement == 'Yes') {
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ":</label> <sup id='sup_" . $arr['Slug'] . "'>" . $allowed . "</sup>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name']));
                }
                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));
                echo suInput('input', $arg);
                if ($pageMode == 'update') {
                    $arg2 = array('type' => 'hidden', 'name' => RESERVED_PREVIOUS_PREFEX . $arr['Slug'], 'id' => RESERVED_PREVIOUS_PREFEX . $arr['Slug'], 'value' => $arr['_____value']);
                    echo suInput('input', $arg2);
                }

                $js = "$('#" . $arr['Slug'] . "').trigger('click');";
                $arg = array('name' => $arr['Slug'] . '_clip', 'id' => $arr['Slug'] . '_clip', 'class' => 'form-control', 'onclick' => $js, 'style' => 'text-align:left;cursor:pointer;');
                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));
                if ($labelRequirement == 'Yes') {
                    echo '<span id="data_span_' . $arr['Slug'] . '">';
                    echo suInput('div', $arg, '<i class="fa fa-paperclip"></i> ' . $requiredStar . $arr['Name'], TRUE);
                    echo '</span>';
                } else {
                    echo '<span id="data_span_' . $arr['Slug'] . '">';
                    echo suInput('div', $arg, '<i class="fa fa-paperclip"></i> ' . $requiredStar . $arr['Name'] . " <sup id='sup_" . $arr['Slug'] . "'>" . $allowed . "</sup>", TRUE);
                    echo '</span>';
                }
                echo '<div id="' . $arr['Slug'] . '_file" class="small color-gray"></div>';
                break;

            //Autocomplete
            case "autocomplete":
                $arg = array('type' => 'text', 'name' => $arr['Slug'], 'id' => $arr['Slug'], 'autocomplete' => 'off');
                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['Length'] != '') {
                    $arg = array_merge($arg, array('data-parsley-maxlength' => $arr['Length']));
                    $arg = array_merge($arg, array('maxlength' => $arr['Length']));
                }
                if ($arr['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr['CssClass']));
                }
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }
                if ($arr['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr['OnKeyUp']));
                }
                if ($arr['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr['OnKeyPress']));
                }
                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }
                //Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }

                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a title='" . CLEAR_FIELD . "' id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }
                    //Remove the clear icon if readonly
                    if ($pageMode == 'add' && $arr['ReadOnlyAdd'] == 'yes') {
                        $clearIcon = "";
                    }
                    if ($pageMode == 'update' && $arr['ReadOnlyUpdate'] == 'yes') {
                        $clearIcon = "";
                    }
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label>";
                    $arg = array_merge($arg, array('placeholder' => TYPE_FOR_SUGGESTIONS));
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name'] . '. ' . TYPE_FOR_SUGGESTIONS));
                }
                //Handle Extra SQL
                if ($arr['ExtraSQL'] != '') {
                    $extraSql = sucrypt(html_entity_decode($arr['ExtraSQL']));
                    $extraSql = "&extra=" . $extraSql;
                } else {
                    $extraSql = '';
                }
                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));
                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suInput('input', $arg);
                echo '</span>';
                echo "
                    <script id=\"autocomplete_dd_" . $arr['Slug'] . "\">
                        //Autocomplete code
                        jQuery(document).ready(function() {
                            $('#" . $arr['Slug'] . "').autocomplete(
                                    {source: '" . ADMIN_URL . "remote.php?do=autocomplete" . $extraSql . "&source=" . urlencode($arr['Source']) . "', minLength: 2}
                            );
                        });
                    </script>
                    ";
                break;

            //Checkbox
            case "checkbox":
                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                //$arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }

                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
//Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        //$arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        //$arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
//Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build label
                echo "<label>" . $requiredStar . $arr['Name'] . ":</label>";

                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));


                $options = explode(',', suUnstrip($arr['Length']));
                //sort($options);


                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suCheckbox($arr['Slug'], $options, $arr['Default'], $arg, 'regular');
                echo '</span>';
                break;


            //Checkbox Switch
            case "checkbox_switch":
                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                //$arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }

                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
//Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        //$arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        //$arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
//Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build label
                echo "<label>" . $requiredStar . $arr['Name'] . ":</label>";

                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));


                $options = explode(',', suUnstrip($arr['Length']));
                //sort($options);


                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suCheckbox($arr['Slug'], $options, $arr['Default'], $arg, 'switch');
                echo '</span>';
                break;
            //Radio
            case "radio_button":
                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }

                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
//Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        //$arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        //$arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        //$arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        //$arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr['Default'] != '') {
                    //$arg = array_merge($arg, array('value' => $arr['Default']));
                }
//Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build label
                echo "<label>" . $requiredStar . $arr['Name'] . ":</label>";

                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));


                $options = explode(',', suUnstrip($arr['Length']));
                //sort($options);

                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suRadio($arr['Slug'], $options, $arr['Default'], $arg, 'regular');
                echo '</span>';
                break;



            //Radio SLider
            case "radio_button_slider":
                $arg = array();
                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }

                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
//Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
//                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
//                            $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
//                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
//                            $arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr['Default'] != '') {
                    //$arg = array_merge($arg, array('value' => $arr['Default']));
                }
//Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build label
                echo "<label>" . $requiredStar . $arr['Name'] . ":</label>";

                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));


                $options = explode(',', $arr['Length']);
                //sort($options);

                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suRadio($arr['Slug'], $options, $arr['Default'], $arg, 'slider');
                echo '</span>';
                break;



            //Radio from DB Slider
            case "radio_button_from_db_slider":
                $arg = array();
                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }

                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
//Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        //$arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        //$arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
//Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build label
                echo "<label>" . $requiredStar . $arr['Name'] . ":</label>";

                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));


                $tableField = explode('.', $arr['Source']);
                $table = $tableField[0];
                $field = $tableField[1];
                $field = suSlugifyStr($field, '_');
                $extraSql = html_entity_decode(html_entity_decode($arr['ExtraSQL']));


                $sql = "SELECT " . suJsonExtract('data', $field) . " FROM  " . $table . " WHERE live='Yes'  " . $extraSql . " ORDER BY " . $field;

                $result = suQuery($sql);
                $o = $result['result'];

                $options = array();
                $z = '0';
                foreach ($o as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        $options[$z] = $value2;
                        $z = $z + 1;
                    }
                }
                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suRadio($arr['Slug'], $options, $arr['Default'], $arg, 'slider');
                echo '</span>';
                break;
            //Radio from DB 
            case "radio_button_from_db":
                $arg = array();
                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }

                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
//Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        //$arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        //$arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
//Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build label
                echo "<label>" . $requiredStar . $arr['Name'] . ":</label>";

                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));


                $tableField = explode('.', $arr['Source']);
                $table = $tableField[0];
                $field = $tableField[1];
                $field = suSlugifyStr($field, '_');
                $extraSql = html_entity_decode(html_entity_decode($arr['ExtraSQL']));


                $sql = "SELECT " . suJsonExtract('data', $field) . " FROM  " . $table . " WHERE live='Yes'  " . $extraSql . " ORDER BY " . $field;

                $result = suQuery($sql);
                $o = $result['result'];

                $options = array();
                $z = '0';
                foreach ($o as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        $options[$z] = $value2;
                        $z = $z + 1;
                    }
                }
                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suRadio($arr['Slug'], $options, $arr['Default'], $arg, 'regular');
                echo '</span>';
                break;
            //Radio Button to Dropdown from DB
            case "radio_to_dropdown_from_db":

                $arg = array();


                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }

                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
//Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        //$arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        //$arg = array_merge($arg, array('required' => 'required'));
                    }
                }

//Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));

                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));


                $tableField = explode('.', $arr['Source']);
                $table = $tableField[0];
                $field = $tableField[1];
                $field = suSlugifyStr($field, '_');
                $extraSql = html_entity_decode(html_entity_decode($arr['ExtraSQL']));

                $sql = "SELECT " . suJsonExtract('data', $field) . " FROM  " . $table . " WHERE live='Yes'  " . $extraSql . " ORDER BY " . $field;

                $result = suQuery($sql);
                $o = $result['result'];

                $options = array();
                $z = '0';

                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ":</label>";
                    $options[''] = 'Select..';
                } else {
                    $options[''] = $requiredStar . $arr['Name'] . '..';
                }

                foreach ($o as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        $options[$z] = $value2;
                        $z = $z + 1;
                    }
                }

                //If div width is 6/12 columns
                if ($arr['Width'] <= 6) {
                    //If options are <=2 then make radio buttons
                    if (sizeof($options) <= 2) {
                        //Build label
                        echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ":</label>";
                        echo '<span id="data_span_' . $arr['Slug'] . '">';
                        echo suRadio($arr['Slug'], $options, $arr['Default'], $arg, 'regular');
                        echo '</span>';
                    } else {
                        //Build label
                        if ($labelRequirement == 'Yes') {
                            echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ":</label>";
                        }

                        if ($arr['CssClass'] != '') {
                            $arg = array_merge($arg, array('class' => $arr['CssClass']));
                        }
                        foreach ($arg as $argKey => $argValue) {
                            $moreArg .= $argKey . "='" . $argValue . "' ";
                        }
                        //$o[''] = 'Select..';

                        echo '<span id="data_span_' . $arr['Slug'] . '">';
                        echo suDropdown($arr['Slug'], $options, $arr['Default'], $moreArg);
                        echo '</span>';
                        if ($addAccess == TRUE) {
                            suPrintJS($arr['Slug'] . '.options.add(new Option("+", ""), ' . $arr['Slug'] . '.options[1]);' . '$("#' . $arr['Slug'] . '").change(function () {doOverlay(this, "' . ADMIN_URL . 'add.php/' . $table . '/?overlay=1&sourceField=' . $field . '&reloadField=' . $arr['Slug'] . '");});');
                        }
                    }
                } else {//If div width is greater than 6
                    //If options to populate are <=6 then make radio buttons
                    if (sizeof($options) <= 6) {
                        echo "<label>" . $requiredStar . $arr['Name'] . ":</label>";
                        echo '<span id="data_span_' . $arr['Slug'] . '">';
                        echo suRadio($arr['Slug'], $options, $arr['Default'], $arg, 'regular');
                        echo '</span>';
                    } else {
                        //Build label
                        if ($labelRequirement == 'Yes') {
                            echo "<label>" . $requiredStar . $arr['Name'] . ":</label>";
                        }
                        if ($arr['CssClass'] != '') {
                            $arg = array_merge($arg, array('class' => $arr['CssClass']));
                        }
                        foreach ($arg as $argKey => $argValue) {
                            $moreArg .= $argKey . "='" . $argValue . "' ";
                        }
                        echo '<span id="data_span_' . $arr['Slug'] . '">';
                        echo suDropdown($arr['Slug'], $options, $arr['Default'], $moreArg);
                        echo '</span>';
                        if ($addAccess == TRUE) {
                            suPrintJS($arr['Slug'] . '.options.add(new Option("+", ""), ' . $arr['Slug'] . '.options[1]);' . '$("#' . $arr['Slug'] . '").change(function () {doOverlay(this, "' . ADMIN_URL . 'add.php/' . $table . '/?overlay=1&sourceField=' . $field . '&reloadField=' . $arr['Slug'] . '");});');
                        }
                    }
                }


                break;


            //Checkbox from DB
            case "checkbox_from_db":
                $arg = array();
                if ($arr['_____value'] != '') {

                    $arr['Default'] = $arr['_____value'];
                }
                //$arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }

                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
//Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        //$arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        //$arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
//Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build label
                echo "<label>" . $requiredStar . $arr['Name'] . ":</label>";

                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));


                $tableField = explode('.', $arr['Source']);
                $table = $tableField[0];
                $field = $tableField[1];
                $field = suSlugifyStr($field, '_');
                $extraSql = html_entity_decode(html_entity_decode($arr['ExtraSQL']));

                $sql = "SELECT " . suJsonExtract('data', $field) . " FROM  " . $table . " WHERE live='Yes'  " . $extraSql . " ORDER BY " . $field;

                $result = suQuery($sql);
                $o = $result['result'];

                $options = array();
                $z = '0';
                foreach ($o as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        $options[$z] = $value2;
                        $z = $z + 1;
                    }
                }
                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suCheckbox($arr['Slug'], $options, $arr['Default'], $arg, 'switch');
                echo '</span>';
                break;


            //Checkbox from DB Switch
            case "checkbox_from_db_switch":
                $arg = array();

                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                //$arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }

                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
//Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        //$arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        //$arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
//Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build label
                echo "<label>" . $requiredStar . $arr['Name'] . ":</label>";

                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));

                $tableField = explode('.', $arr['Source']);
                $table = $tableField[0];
                $field = $tableField[1];
                $field = suSlugifyStr($field, '_');
                $extraSql = html_entity_decode(html_entity_decode($arr['ExtraSQL']));

                $sql = "SELECT " . suJsonExtract('data', $field) . " FROM  " . $table . " WHERE live='Yes'  " . $extraSql . " ORDER BY " . $field;

                $result = suQuery($sql);
                $o = $result['result'];

                $options = array();
                $z = '0';
                foreach ($o as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        $options[$z] = $value2;
                        $z = $z + 1;
                    }
                }
                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suCheckbox($arr['Slug'], $options, $arr['Default'], $arg, 'switch');
                echo '</span>';
                break;

            //Currency
            case "currency":
                $arg = array('type' => 'text', 'name' => $arr['Slug'], 'id' => $arr['Slug'], 'autocomplete' => 'off');

                //Parsley
                $arg = array_merge($arg, array('data-parsley-type' => 'number', 'data-parsley-trigger' => 'keyup'));

                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['Length'] != '') {
                    $arg = array_merge($arg, array('data-parsley-maxlength' => $arr['Length']));
                    $arg = array_merge($arg, array('maxlength' => $arr['Length']));
                }
                if ($arr['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr['CssClass'] . ' decimal'));
                } else {
                    $arg = array_merge($arg, array('class' => 'decimal'));
                }
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }
                if ($arr['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr['OnKeyUp']));
                }
                if ($arr['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr['OnKeyPress']));
                }
                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }
//Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($pageMode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($pageMode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a title='" . CLEAR_FIELD . "' id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }
                    //Remove the clear icon if readonly
                    if ($pageMode == 'add' && $arr['ReadOnlyAdd'] == 'yes') {
                        $clearIcon = "";
                    }
                    if ($pageMode == 'update' && $arr['ReadOnlyUpdate'] == 'yes') {
                        $clearIcon = "";
                    }
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label> <sup id='sup_" . $arr['Slug'] . "'>" . $getSettings['site_currency'] . "</sup>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name'] . " (" . $getSettings['site_currency'] . ")"));
                }
                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));
                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suInput('input', $arg);
                echo '</span>';
                break;
            //Percentage
            case "percentage":
                $arg = array('type' => 'text', 'name' => $arr['Slug'], 'id' => $arr['Slug'], 'autocomplete' => 'off');

                //Parsley
                $arg = array_merge($arg, array('data-parsley-type' => 'number', 'data-parsley-trigger' => 'keyup'));

                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['Length'] != '') {
                    $arg = array_merge($arg, array('data-parsley-maxlength' => $arr['Length']));
                    $arg = array_merge($arg, array('maxlength' => $arr['Length']));
                    $arg = array_merge($arg, array('maxlength' => $arr['Length']));
                    //Specify max length for input type number
                    $max = '';
                    $min = '-';
                    for ($n = 0; $n < $arr['Length']; $n++) {
                        $max .= '9';
                        $min .= '9';
                    }
                    $arg = array_merge($arg, array('min' => $min));
                    $arg = array_merge($arg, array('max' => $max));
                }
                if ($arr['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr['CssClass'] . ' decimal'));
                } else {
                    $arg = array_merge($arg, array('class' => 'decimal'));
                }
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }
                if ($arr['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr['OnKeyUp']));
                }
                if ($arr['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr['OnKeyPress']));
                }
                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }
                //Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($pageMode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($pageMode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a title='" . CLEAR_FIELD . "' id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }
                    //Remove the clear icon if readonly
                    if ($pageMode == 'add' && $arr['ReadOnlyAdd'] == 'yes') {
                        $clearIcon = "";
                    }
                    if ($pageMode == 'update' && $arr['ReadOnlyUpdate'] == 'yes') {
                        $clearIcon = "";
                    }
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label> <sup id='sup_" . $arr['Slug'] . "'>" . $getSettings['site_currency'] . "</sup>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name'] . " (%)"));
                }
                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));
                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suInput('input', $arg);
                echo '</span>';
                break;

            //Date
            case "date":
                $arg = array('type' => 'text', 'name' => $arr['Slug'], 'id' => $arr['Slug'], 'autocomplete' => 'off');
                //Parsley
                //$arg = array_merge($arg, array('data-parsley-type' => 'date', 'data-parsley-trigger' => 'keyup','data-date-format'=>$getSettings['date_format']));

                if ($arr['_____value'] != '') {
                    $arr['Default'] = suDateFromDb($arr['_____value']);
                }

                if ($arr['Length'] != '') {
                    $arg = array_merge($arg, array('data-parsley-maxlength' => $arr['Length']));
                    $arg = array_merge($arg, array('maxlength' => $arr['Length']));
                }
                if ($arr['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr['CssClass'] . ' dateBox'));
                }
                $dataPicker = ''; //"doDatePicker('" . $date_format . "',this);";

                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $dataPicker . $arr[$i]['OnClick']));
                } else {
                    $arg = array_merge($arg, array('onclick' => $dataPicker));
                }
                if ($arr['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr['OnKeyUp']));
                }
                if ($arr['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr['OnKeyPress']));
                }
                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }
                //Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr['Default'] != '') {
                    if ($pageMode == 'add') {
                        $sqlDate = "SELECT DATE_ADD('" . date('Y-m-d') . "', INTERVAL " . $arr['Default'] . " DAY) AS dt";
                        $resultDate = suQuery($sqlDate);
                        $arr['Default'] = $resultDate['result'][0]['dt'];
                        $arr['Default'] = suDateFromDb($arr['Default']);
                        $arg = array_merge($arg, array('value' => $arr['Default']));
                    }
                } else {
                    $arr['Default'] = '';
                }
                if ($pageMode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($pageMode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a title='" . CLEAR_FIELD . "' id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }
                    //Remove the clear icon if readonly
                    if ($pageMode == 'add' && $arr['ReadOnlyAdd'] == 'yes') {
                        $clearIcon = "";
                    }
                    if ($pageMode == 'update' && $arr['ReadOnlyUpdate'] == 'yes') {
                        $clearIcon = "";
                    }
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name']));
                }
                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
//Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));
                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suInput('input', $arg);
                echo '</span>';
                echo "
                <script>
                    $(function() {
                        $( '#" . $arr['Slug'] . "' ).datepicker({
                            changeMonth: true,
                            changeYear: true
                        });
                        $( '#" . $arr['Slug'] . "' ).datepicker( 'option', 'yearRange', 'c-100:c+10' );
                        $( '#" . $arr['Slug'] . "' ).datepicker( 'option', 'dateFormat', '" . $getSettings['date_format'] . "' );
                        $('#" . $arr['Slug'] . "').datepicker('setDate', '" . $arr['Default'] . "' );                
                    });
                </script>
                ";
                break;
            //HTML Area
            case "html_area":
                $arg = array('name' => $arr['Slug'], 'id' => $arr['Slug']);

                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr['CssClass']));
                }
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }
                if ($arr['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr['OnKeyUp']));
                }
                if ($arr['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr['OnKeyPress']));
                }
                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }
                //Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
//                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
//                            $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
//                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
//                            $arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr['Default'] != '') {
                    //$arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($pageMode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($pageMode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Use only label
                if ($arg['type'] == 'hidden') {
                    echo suInput('input', $arg);
                } else {
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ":</label>";
                    echo '<span id="data_span_' . $arr['Slug'] . '">';
                    echo suInput('textarea', $arg, $arr['Default'], TRUE);
                    echo '</span>';
                    suCKEditor($arr['Slug']);
                }

                break;


            //textarea
            case "textarea":
                $arg = array('name' => $arr['Slug'], 'id' => $arr['Slug']);
                if ($arr['Length'] != '') {
                    $arg = array_merge($arg, array('data-parsley-maxlength' => $arr['Length']));
                    $arg = array_merge($arg, array('data-maxlength' => $arr['Length']));
                }
                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr['CssClass']));
                }
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }
                if ($arr['OnKeyUp'] != '') {
                    if ($arr['Length'] != '') {
                        $f = "doWordCount('" . $arr['Slug'] . "', '" . $arr['Slug'] . "_charcount');";
                        $f .= $arr['OnKeyUp'];
                        $arg = array_merge($arg, array('onkeyup' => $f));
                        $f = '';
                    } else {
                        $arg = array_merge($arg, array('onkeyup' => "doWordCount('" . $arr['Slug'] . "', '" . $arr['Slug'] . "_charcount');"));
                    }
                } else {
                    $arg = array_merge($arg, array('onkeyup' => "doWordCount('" . $arr['Slug'] . "', '" . $arr['Slug'] . "_charcount');"));
                }
                if ($arr['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr['OnKeyPress']));
                }
                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }
                //Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr['Default'] != '') {
                    //$arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($pageMode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($pageMode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a title='" . CLEAR_FIELD . "' id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }
                    //Remove the clear icon if readonly
                    if ($pageMode == 'add' && $arr['ReadOnlyAdd'] == 'yes') {
                        $clearIcon = "";
                    }
                    if ($pageMode == 'update' && $arr['ReadOnlyUpdate'] == 'yes') {
                        $clearIcon = "";
                    }
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name']));
                }

                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
//Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));
                if ($arg['type'] == 'hidden') {
                    echo suInput('input', $arg);
                } else {
                    echo '<span id="data_span_' . $arr['Slug'] . '">';
                    echo suInput('textarea', $arg, $arr['Default'], TRUE);
                    echo '</span>';
                    if ($pageMode == 'update') {
                        $charCount = strlen($arr['Default']);
                    } else {
                        $charCount = 0;
                    }
                    if ($arr['Length'] > 0) {
                        echo "<div class='color-gray pull-right' id='" . $arr['Slug'] . "_charcount'>" . $charCount . "/" . $arr['Length'] . "</div>";
                        if ($pageMode == 'update') {
                            //suPrints("doWordCount('".$_POST[$arr['Slug']]."', '100')");
                        }
                    }
                }

                break;

            //Quick Picks
            case "quick_pick":
                $arg = array('name' => $arr['Slug'], 'id' => $arr['Slug']);
                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['Length'] != '') {
                    $arg = array_merge($arg, array('data-parsley-maxlength' => $arr['Length']));
                    $arg = array_merge($arg, array('data-maxlength' => $arr['Length']));
                }

                if ($arr['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr['CssClass']));
                }
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }
                if ($arr['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr['OnKeyUp']));
                }
                if ($arr['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr['OnKeyPress']));
                }
                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }
                //Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($pageMode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($pageMode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a title='" . CLEAR_FIELD . "' id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }
                    //Remove the clear icon if readonly
                    if ($pageMode == 'add' && $arr['ReadOnlyAdd'] == 'yes') {
                        $clearIcon = "";
                    }
                    if ($pageMode == 'update' && $arr['ReadOnlyUpdate'] == 'yes') {
                        $clearIcon = "";
                    }
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name']));
                }
                //Build quick picks
                $options = explode(',', suUnstrip($arr['Length']));
                //sort($options);
                //doQuickPick(sourceVal, targetEle);
                $quickPicks = '';
                for ($q = 0; $q <= sizeof($options) - 1; $q++) {
                    $options[$q] = trim($options[$q]);
                    $qArg = "'" . $options[$q] . "','" . $arr['Slug'] . "'";
                    $quickPicks .= '<a href="javascript:;" onclick="doQuickPick(' . $qArg . ')">' . $options[$q] . '</a>. ';
                }
                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));

                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));
                if ($arg['type'] == 'hidden') {
                    echo suInput('input', $arg);
                } else {
                    echo "<div><i>Quick Picks:</i> " . $quickPicks . "</div>";
                    echo '<span id="data_span_' . $arr['Slug'] . '">';
                    echo suInput('textarea', $arg, $arr['Default'], TRUE);
                    echo '</span>';
                }
                break;

            //Quick Pick from DB
            case "quick_pick_from_db":
                $arg = array('name' => $arr['Slug'], 'id' => $arr['Slug']);
                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['Length'] != '') {
                    $arg = array_merge($arg, array('data-parsley-maxlength' => $arr['Length']));
                    $arg = array_merge($arg, array('data-maxlength' => $arr['Length']));
                }

                if ($arr['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr['CssClass']));
                }
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }
                if ($arr['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr['OnKeyUp']));
                }
                if ($arr['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr['OnKeyPress']));
                }
                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }
                //Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($pageMode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($pageMode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a title='" . CLEAR_FIELD . "' id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }
                    //Remove the clear icon if readonly
                    if ($pageMode == 'add' && $arr['ReadOnlyAdd'] == 'yes') {
                        $clearIcon = "";
                    }
                    if ($pageMode == 'update' && $arr['ReadOnlyUpdate'] == 'yes') {
                        $clearIcon = "";
                    }
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name']));
                }
                //Build quick picks
                $tableField = explode('.', $arr['Source']);
                $table = $tableField[0];
                $field = $tableField[1];
                $field = suSlugifyStr($field, '_');
                $extraSql = html_entity_decode(html_entity_decode($arr['ExtraSQL']));

                $sql = "SELECT " . suJsonExtract('data', $field) . " FROM  " . $table . " WHERE live='Yes'  " . $extraSql . " ORDER BY " . $field;

                $result = suQuery($sql);
                $o = $result['result'];

                $options = array();
                $z = '0';
                foreach ($o as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        $options[$z] = $value2;
                        $z = $z + 1;
                    }
                }

                if (sizeof($options) > 0) {
                    $quickPicks = '';
                    for ($q = 0; $q <= sizeof($options) - 1; $q++) {
                        $options[$q] = trim($options[$q]);
                        $qArg = "'" . $options[$q] . "','" . $arr['Slug'] . "'";
                        $quickPicks .= '<a href="javascript:;" onclick="doQuickPick(' . $qArg . ')">' . $options[$q] . '</a>. ';
                    }
                    echo "<div><i>Quick Picks:</i> " . $quickPicks . "</div>";
                }
                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));
                if ($arg['type'] == 'hidden') {
                    echo suInput('input', $arg);
                } else {
                    echo '<span id="data_span_' . $arr['Slug'] . '">';
                    echo suInput('textarea', $arg, $arr['Default'], TRUE);
                    echo '</span>';
                }
                break;

            //Separator
            case "separator":
                $arg = array('type' => 'legend', 'name' => $arr['Slug'], 'id' => $arr['Slug']);

                if ($arr['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr['CssClass']));
                }

                if ($pageMode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        
                    }
                    //Build legend
                    $arg = array_merge($arg, array('title' => $arr['Name']));
                    echo '<span id="data_span_' . $arr['Slug'] . '">';
                    echo suInput('legend', $arg, $arr['Length'], TRUE);
                    echo '</span>';
                } else {
                    //Build legend
                    $arg = array_merge($arg, array('title' => $arr['Name']));
                    echo '<span id="data_span_' . $arr['Slug'] . '">';
                    echo suInput('legend', $arg, $arr['Length'], TRUE);
                    echo '</span>';
                }
                if ($pageMode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                break;

            //Line break
            case "line_break":
                $arg = array('type' => 'div');

                if ($arr['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr['CssClass']));
                } else {
                    $arg = array_merge($arg, array('class' => 'zero-height clearfix'));
                }

                if ($pageMode == 'update') {
                    echo suInput('div', $arg, '', TRUE);
                } else {
                    echo suInput('div', $arg, '', TRUE);
                }
                if ($pageMode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                break;


            //URL
            case "url":
                $arg = array('type' => 'text', 'name' => $arr['Slug'], 'id' => $arr['Slug'], 'autocomplete' => 'off');
                //Parsley
                $arg = array_merge($arg, array('data-parsley-type' => 'url', 'data-parsley-trigger' => 'keyup'));
                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr['Length'] != '') {
                    $arg = array_merge($arg, array('data-parsley-maxlength' => $arr['Length']));
                    $arg = array_merge($arg, array('maxlength' => $arr['Length']));
                }
                if ($arr['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr['CssClass']));
                }
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }
                if ($arr['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr['OnKeyUp']));
                }
                if ($arr['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr['OnKeyPress']));
                }
                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }
                //Required
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr['Required'] == 'yes') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($pageMode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($pageMode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($pageMode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a title='" . CLEAR_FIELD . "' id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }
                    //Remove the clear icon if readonly
                    if ($pageMode == 'add' && $arr['ReadOnlyAdd'] == 'yes') {
                        $clearIcon = "";
                    }
                    if ($pageMode == 'update' && $arr['ReadOnlyUpdate'] == 'yes') {
                        $clearIcon = "";
                    }
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name']));
                }
                //Add tabindex
                $arg = array_merge($arg, array('tabindex' => $arr['TabIndex']));
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));
                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suInput('input', $arg);
                echo '</span>';
                break;

            //Add more section
            case "add_more_section":
//Add more counter
                $arg = array('type' => 'legend', 'name' => $arr['Slug'], 'id' => $arr['Slug']);
                //Build legend
                $arg = array_merge($arg, array('title' => $arr['Name']));
                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suInput('legend', $arg, $arr['Length'], TRUE);
                echo '</span>';
                //Make header
                $sqlAddMore = "SELECT id,structure FROM " . STRUCTURE_TABLE_NAME . " WHERE live='Yes' AND slug='" . $arr['Source'] . "' LIMIT 0,1";
                $resultAddMore = suQuery($sqlAddMore);
                $resultAddMore['result'] = suUnstrip($resultAddMore['result']);
                $arrAddMore = $resultAddMore['result'][0]['structure'];

                //Exclude hidden fields to distribute proper column width division
                $hiddenFields = array('hidden', 'json', 'ip_address');
                $hiddenSlugs = array();
                for ($ix = 0; $ix <= sizeof($arrAddMore) - 1; $ix++) {
                    if (in_array($arrAddMore[$ix]['Type'], $hiddenFields)) {
                        array_push($hiddenSlugs, $arrAddMore[$ix]['Slug']);
                    }
                }
                $searchableDd = array(); //Array to store searchable dropdowns
                $th = '';
                $numTd = (sizeof($arrAddMore) - sizeof($hiddenSlugs)); //Subtract hidden fields from td width distribution
                $tdWidth = (int) (95 / $numTd) . '%';
                for ($i = 0; $i <= $numTd; $i++) {
                    if (in_array($arrAddMore[$i]['Source'], $hiddenSlugs)) {
                        $hiddenClass = 'hide';
                    }
                    //Add currency and % signs
                    if ($arrAddMore[$i]['Type'] == 'currency') {
                        $symbol = $getSettings['site_currency'];
                    } elseif ($arrAddMore[$i]['Type'] == 'percentage') {
                        $symbol = '%';
                    } else {
                        $symbol = '';
                    }
                    //Mark the required *
                    if ($arrAddMore[$i]['Required'] == 'yes') {
                        $requiredSymbol = '*';
                    } else {
                        $requiredSymbol = '';
                    }

                    $th .= '<th width="' . $tdWidth . '" class="' . $hiddenClass . '"><sup>' . $requiredSymbol . '</sup>' . suUnstrip($arrAddMore[$i]['Name']) . ' <sup>' . $symbol . '</sup></th>';
                }
                echo $headerTable = '<table width="100%" class="table table-striped table-hover tablex">'
                . '<thead><tr>'
                . '' . $th . '<th width="5%">&nbsp;</th></tr></thead>'
                . '</table>';
                //==
                echo '<input type="hidden" value="0" name="_____size_' . $arr['Source'] . '" id="_____size_' . $arr['Source'] . '"/><p id="more-destination-' . $arr['Source'] . '"><button type="button" class="btn btn-theme" onclick="remote_' . $arr['Source'] . '.location.href=\'' . ADMIN_URL . 'remote' . PHP_EXTENSION . '/add-more/' . $arr['Source'] . '/add-single/\'"><i class="fa fa-plus"></i></button></p>';
                suIframe(DEBUG, 'remote_' . $arr['Source']);
                if ($mode2 == 'update') {
                    $documentReady .= 'remote_' . $arr['Source'] . '.location.href="' . ADMIN_URL . 'remote' . PHP_EXTENSION . '/add-more/' . $arr['Source'] . '/' . $mode2 . '/' . suUnTablify($table) . '/' . $rid . '/";';
                } else {
                    $documentReady .= 'remote_' . $arr['Source'] . '.location.href="' . ADMIN_URL . 'remote' . PHP_EXTENSION . '/add-more/' . $arr['Source'] . '/' . $mode2 . '/";';
                }
                $documentReady .= "\n";
                $GLOBALS[$documentReadyUid] .= $documentReady;
                echo '<div id="more-destination-' . $arr['Source'] . '"></div>';
                break;
        }
    }

}

//Build preview fields
if (!function_exists('suPreviewField')) {

    function suPreviewField($arr) {
        global $getSettings;
        if ($arr['_____value'] != '') {
            switch ($arr['Type']) {
                //Textbox
                case "textbox":

                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "</td>";
                    break;

                //Phone
                case "phone":
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "</td>";
                    break;

                //Hidden
                case "hidden":
                    //Do nothing
                    break;
                //IP Address
                case "ip_address":
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "</td>";
                    break;
                //Decimal
                case "decimal":
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "</td>";
                    break;
                //Integer
                case "integer":
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "</td>";
                    break;
                //Email
                case "email":
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'><a href='mailto:" . suUnstrip($arr['_____value']) . "'>" . suUnstrip($arr['_____value']) . "</a></td>";
                    break;

                //Dropdown
                case "dropdown":
                    if (is_array(suUnstrip($arr['_____value']))) {
                        $arr['_____value'] = suMakeCheckBoxesFromArray(suUnstrip($arr['_____value']));
                    }
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . $arr['_____value'] . "</td>";

                    break;

                //Dropdown from DB
                case "dropdown_from_db":
                    if (is_array(suUnstrip($arr['_____value']))) {
                        $arr['_____value'] = suMakeCheckBoxesFromArray(suUnstrip($arr['_____value']));
                    }
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . $arr['_____value'] . "</td>";
                    break;



                //Searchable Dropdown
                case "searchable_dropdown":
                    if (is_array(suUnstrip($arr['_____value']))) {
                        $arr['_____value'] = suMakeCheckBoxesFromArray(suUnstrip($arr['_____value']));
                    }
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . $arr['_____value'] . "</td>";
                    break;


                //Searchable Dropdown from database
                case "searchable_dropdown_from_db":
                    if (is_array(suUnstrip($arr['_____value']))) {
                        $arr['_____value'] = suMakeCheckBoxesFromArray(suUnstrip($arr['_____value']));
                    }
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name'] . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . $arr['_____value']) . "</td>";
                    break;

                //Password
                case "password":
                    //Do nothing
                    break;
                //picture
                case "picture_field":
                    if (file_exists(ADMIN_UPLOAD_PATH . $arr['_____value']) && ($arr['_____value'] != '')) {

                        $imagePath = UPLOAD_URL . $arr['_____value'];
                        $imageSize = getimagesize($imagePath);
                        $imageWidth = $imageSize[0];
                        $imageHeight = $imageSize[1];
                        $imageRatio = $imageWidth / $imageHeight;
                        $newImageWidth = $getSettings['preview_image_width'];
                        $newImageHeight = round($getSettings['preview_image_width'] / $imageRatio);
                        $picture = "<p><img style='border:3px solid #DDD;border-radius:5px;' width='" . $newImageWidth . "' height='" . $newImageHeight . "' src='" . $imagePath . "'/></p>";
                    }

                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . $picture . "</td>";
                    break;
                //Attachment
                case "attachment_field":

                    if (file_exists(ADMIN_UPLOAD_PATH . $arr['_____value']) && ($arr['_____value'] != '')) {
                        //$attachment =  "<a target='_blank' href='" . UPLOAD_URL . $arr['_____value'] . "' class='fa fa-file-pdf-o attachmentThumb size-400'></a><p><a target='_blank' href='" . UPLOAD_URL . $arr['_____value'] . "'>" . suUnMakeUploadPath($arr['_____value']) . "</a></p>";
                        $attachment = "<a target='_blank' href='" . UPLOAD_URL . $arr['_____value'] . "' class='fa fa-file-pdf-o attachmentThumb size-400'></a><p><a target='_blank' href='" . UPLOAD_URL . $arr['_____value'] . "'>" . suUnMakeUploadPath($arr['_____value']) . "</a></p>";
                    }
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "</td>";
                    break;

                //Autocomplete
                case "autocomplete":
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "</td>";
                    break;

                //Checkbox
                case "checkbox":
                    if (is_array(suUnstrip($arr['_____value']))) {
                        $arr['_____value'] = suMakeCheckBoxesFromArray(suUnstrip($arr['_____value']));
                    }
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . $arr['_____value'] . "</td>";

                    break;


                //Checkbox Switch
                case "checkbox_switch":
                    if (is_array(suUnstrip($arr['_____value']))) {
                        $arr['_____value'] = suMakeCheckBoxesFromArray(suUnstrip($arr['_____value']));
                    }
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . $arr['_____value'] . "</td>";
                    break;
                //Radio
                case "radio_button":
                    if (is_array(suUnstrip($arr['_____value']))) {
                        $arr['_____value'] = suMakeCheckBoxesFromArray(suUnstrip($arr['_____value']));
                    }
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . $arr['_____value'] . "</td>";
                    break;



                //Radio SLider
                case "radio_button_slider":
                    if (is_array(suUnstrip($arr['_____value']))) {
                        $arr['_____value'] = suMakeCheckBoxesFromArray(suUnstrip($arr['_____value']));
                    }
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . $arr['_____value'] . "</td>";
                    break;



                //Radio from DB Slider
                case "radio_button_from_db":
                    if (is_array(suUnstrip($arr['_____value']))) {
                        $arr['_____value'] = suMakeCheckBoxesFromArray(suUnstrip($arr['_____value']));
                    }
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . $arr['_____value'] . "</td>";
                    break;
                //Radio from DB Slider
                case "radio_button_from_db_slider":
                    if (is_array(suUnstrip($arr['_____value']))) {
                        $arr['_____value'] = suMakeCheckBoxesFromArray(suUnstrip($arr['_____value']));
                    }
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . $arr['_____value'] . "</td>";
                    break;
                //Radio Button to Dropdown from DB
                case "radio_to_dropdown_from_db":
                    if (is_array(suUnstrip($arr['_____value']))) {
                        $arr['_____value'] = suMakeCheckBoxesFromArray(suUnstrip($arr['_____value']));
                    }
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . $arr['_____value'] . "</td>";
                    break;


                //Checkbox from DB
                case "checkbox_from_db":
                    if (is_array(suUnstrip($arr['_____value']))) {
                        $arr['_____value'] = suMakeCheckBoxesFromArray(suUnstrip($arr['_____value']));
                    }
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . $arr['_____value'] . "</td>";

                    break;


                //Checkbox from DB Switch
                case "checkbox_from_db_switch":
                    if (is_array(suUnstrip($arr['_____value']))) {
                        $arr['_____value'] = suMakeCheckBoxesFromArray(suUnstrip($arr['_____value']));
                    }
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . $arr['_____value'] . "</td>";

                    break;

                //Currency
                case "currency":

                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . $getSettings['site_currency'] . ' ' . suUnstrip($arr['_____value']) . "</td>";
                    break;
                //Percentage
                case "percentage":

                    if ($getSettings['format_percentage'] == 1) {
                        $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . number_format(suUnstrip($arr['_____value']), 2) . "%</td>";
                    } else {
                        $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "%</td>";
                    }
                    break;

                //Date
                case "date":
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suDateFromDbToEnglish(suUnstrip($arr['_____value'])) . "</td>";
                    break;
                //HTML Area
                case "html_area":

                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "</td>";
                    break;


                //textarea
                case "textarea":

                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . nl2br(suUnstrip($arr['_____value'])) . "</td>";
                    break;

                //Quick Picks
                case "quick_pick":
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "</td>";

                    break;

                //Quick Pick from DB
                case "quick_pick_from_db":
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "</td>";
                    break;

                //Separator
                case "separator":
                    break;


                //URL
                case "url":

                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'><a href='" . suUnstrip($arr['_____value']) . "'>" . suUnstrip($arr['_____value']) . "</a></td>";
                    break;

                //Else
                default:
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "</td>";
                    break;
            }
            echo '<tr id="preview-tr-' . $arr['Slug'] . '">' . $tableData . '</tr>';
        }
    }

}




//Function to check unique
if (!function_exists('suCheckUnique')) {

    function suCheckUnique($table, $selfId = 0, $returnErrorOnly = FALSE, $restoreMessage = FALSE) {
        //Get form fields built
        $sql = "SELECT title, structure FROM " . STRUCTURE_TABLE_NAME . " WHERE live='Yes' AND slug='" . $table . "' LIMIT 0,1";
        $result = suQuery($sql);
        $numRows = $result['num_rows'];
        if ($numRows == 0) {
            suExit(INVALID_RECORD);
        }

        $row = $result['result'][0];
        $title = suUnstrip($row['title']);
        $structure = $row['structure'];
        $structure = json_decode($structure, 1);
        $uniques = array(); //Array to hold unique fields
        $dates = array();
        for ($i = 0; $i <= sizeof($structure) - 1; $i++) {
//Check data type as unique
            if ($structure[$i]['Unique'] == 'yes') {
                $uniques[$structure[$i]['Name']] = $structure[$i]['Slug'];
            }
        }

        //Check if date
        for ($i = 0; $i <= sizeof($structure) - 1; $i++) {
            if ($structure[$i]['Type'] == 'date') {
                array_push($dates, $structure[$i]['Slug']);
            }
        }
        $uError = '';
        if ($selfId != 0) {
            $selfSQL = " AND id !='" . $selfId . "'";
        } else {
            $selfSQL = '';
        }
        foreach ($uniques as $key => $value) {
            if (in_array($value, $dates)) {//If type is date
                $sqlU = "SELECT id FROM $table WHERE lcase(" . suJsonExtract('data', $value, FALSE) . ")='" . suDate2Db(strtolower(suStrip($_REQUEST[$value]))) . "' AND " . suJsonExtract('data', 'save_for_later_use', FALSE) . "='No' AND live='Yes' " . $selfSQL;
            } else {
                $sqlU = "SELECT id FROM $table WHERE lcase(" . suJsonExtract('data', $value, FALSE) . ")='" . strtolower(suStrip($_REQUEST[$value])) . "' AND " . suJsonExtract('data', 'save_for_later_use', FALSE) . "='No' AND live='Yes' " . $selfSQL;
            }
            $resultU = suQuery($sqlU);
            $numRowsU = $resultU['num_rows'];
            //Only return error
            if ($returnErrorOnly == TRUE) {
                if ($numRowsU > 0) {
                    if ($restoreMessage == TRUE) {
                        $uError = sprintf(DUPLICATION_ERROR_ON_RESTORE, urldecode($key));
                    } else {
                        $uError = sprintf(DUPLICATION_ERROR, urldecode($key));
                    }
                    return $uError;
                    break;
                }
            } else {// Return error with html output
                if ($numRowsU > 0) {
                    if ($restoreMessage == TRUE) {
                        $uError = sprintf(DUPLICATION_ERROR_ON_RESTORE, urldecode($key));
                    } else {
                        $uError = sprintf(DUPLICATION_ERROR, urldecode($key));
                    }
                    suPrintJs('
            parent.suToggleButton(0);
            parent.$("#message-area").hide();
            parent.$("#error-area").show();
            parent.$("#error-area").html("<ul><li>' . $uError . '</li></ul>");
            parent.$("html, body").animate({ scrollTop: parent.$("html").offset().top }, "slow");
        ');
                    exit;
                }
            }
        }
    }

}
//Function to check composite uniques
if (!function_exists('suCheckCompositeUnique')) {

    function suCheckCompositeUnique($table, $selfId = 0, $returnErrorOnly = FALSE, $restoreMessage = FALSE) {

        //Get form fields built
        $sql = "SELECT title, structure FROM " . STRUCTURE_TABLE_NAME . " WHERE live='Yes' AND slug='" . $table . "' LIMIT 0,1";

        $result = suQuery($sql);
        $numRows = $result['num_rows'];
        if ($numRows == 0) {
            suExit(INVALID_RECORD);
        }
        $row = $result['result'][0];
        $title = suUnstrip($row['title']);
        $structure = $row['structure'];
        $structure = json_decode($structure, 1);
        $compositeUniques = array(); //Array to hold unique fields
        $dates = array();

        for ($i = 0; $i <= sizeof($structure) - 1; $i++) {
            //Check data type as unique
            if ($structure[$i]['CompositeUnique'] == 'yes') {
                $compositeUniques[$structure[$i]['Name']] = $structure[$i]['Slug'];
            }
        }

        //Check if date
        for ($i = 0; $i <= sizeof($structure) - 1; $i++) {
            if ($structure[$i]['Type'] == 'date') {
                array_push($dates, $structure[$i]['Slug']);
            }
        }

        if ($selfId != 0) {
            $selfSQL = " AND id!='" . $selfId . "'";
        } else {
            $selfSQL = '';
        }
        $uSql = '';
        $uError = '';

        $cnt = 0;
        foreach ($compositeUniques as $key => $value) {
            if (in_array($value, $dates)) {
                $uSql .= " lcase(" . suJsonExtract('data', $value, FALSE) . ")='" . suDate2Db(strtolower(suStrip($_REQUEST[$value]))) . "' AND " . suJsonExtract('data', 'save_for_later_use', FALSE) . "='No' AND"; //Do not disturb spaces in the $
                $uError .= " " . $key . " -";
            } else {
                $uSql .= " lcase(" . suJsonExtract('data', $value, FALSE) . ")='" . strtolower(suStrip($_REQUEST[$value])) . "' AND " . suJsonExtract('data', 'save_for_later_use', FALSE) . "='No' AND"; //Do not disturb spaces in the $
                $uError .= " " . $key . " /";
            }
        }
        $uError = substr($uError, 0, -1);
        $uSql = substr($uSql, 0, -3);
        $sqlU = "SELECT id FROM $table WHERE $uSql  AND live='Yes' " . $selfSQL;
        $resultU = suQuery($sqlU);
        $numRowsU = $resultU['num_rows'];

        //Only return error
        if ($returnErrorOnly == TRUE) {
            if ($numRowsU > 0) {
                if ($restoreMessage == TRUE) {
                    $uError = sprintf(DUPLICATION_ERROR_ON_RESTORE, urldecode($uError));
                } else {
                    $uError = sprintf(DUPLICATION_ERROR, urldecode($uError));
                }
            } else {
                $uError = '';
            }
            return $uError;
        } else {// Return error with html output
            if ($numRowsU > 0) {
                if ($restoreMessage == TRUE) {
                    $uError = sprintf(DUPLICATION_ERROR_ON_RESTORE, urldecode($uError));
                } else {
                    $uError = sprintf(DUPLICATION_ERROR, urldecode($uError));
                } suPrintJs('
            parent.suToggleButton(0);
            parent.$("#message-area").hide();
            parent.$("#error-area").show();
            parent.$("#error-area").html("<ul><li>' . $uError . '</li></ul>");
            parent.$("html, body").animate({ scrollTop: parent.$("html").offset().top }, "slow");
        ');
                exit;
            }
        }
    }

}
//Function to build usage log data
if (!function_exists('suMakeUsageLog')) {

    function suMakeUsageLog($mode, $table = FALSE, $id = FALSE) {
        if ($mode == 'add' || $mode == 'update' || $mode == 'update-single' || $mode == 'delete' || $mode == 'restore') {//$id is maxId in this case
            $sql = "SELECT data FROM {$table} WHERE id='$id' LIMIT 0,1";
            $result = suQuery($sql);
            $data = $result['result'][0]['data'];
            $data = json_decode($data, 1);
            $data = array_merge(array('id' => $id), $data);
            $data = array_merge(array('user' => $_SESSION[SESSION_PREFIX . 'user_id']), $data);
            $data = json_encode($data);
            $sql = "INSERT INTO " . LOG_TABLE_NAME . " SET action_on='" . date('Y-m-d') . "',action_by='" . $_SESSION[SESSION_PREFIX . 'user_name'] . "',mode='" . $mode . "',module='" . $table . "', data = '" . $data . "'";
            suQuery($sql);
        } elseif ($mode == 'login-success') {
            $data = array('email' => urlencode($_POST['email']), 'ip' => $_SERVER['REMOTE_ADDR']);
            $data = json_encode($data);
            $sql = "INSERT INTO " . LOG_TABLE_NAME . " SET action_on='" . date('Y-m-d') . "',action_by='" . $_SESSION[SESSION_PREFIX . 'user_name'] . "',mode='" . $mode . "',module='" . USERS_TABLE_NAME . "', data = '" . $data . "'";
            suQuery($sql);
        } elseif ($mode == 'login-failure') {
            $data = array('email' => urlencode($_POST['email']), 'ip' => $_SERVER['REMOTE_ADDR']);
            $data = json_encode($data);
            $sql = "INSERT INTO " . LOG_TABLE_NAME . " SET action_on='" . date('Y-m-d') . "',action_by='" . $_SESSION[SESSION_PREFIX . 'user_name'] . "',mode='" . $mode . "',module='" . USERS_TABLE_NAME . "', data = '" . $data . "'";
            suQuery($sql);
        } elseif ($mode == 'logout') {
            $data = array('name' => urlencode($_SESSION[SESSION_PREFIX . 'user_name']), 'email' => urlencode($_SESSION[SESSION_PREFIX . 'user_email']), 'ip' => $_SERVER['REMOTE_ADDR']);
            $data = json_encode($data);
            $sql = "INSERT INTO " . LOG_TABLE_NAME . " SET action_on='" . date('Y-m-d') . "',action_by='" . $_SESSION[SESSION_PREFIX . 'user_name'] . "',mode='" . $mode . "',module='" . USERS_TABLE_NAME . "', data = '" . $data . "'";
            suQuery($sql);
        } elseif ($mode == 'retrieve-password-success') {
            $data = array('email' => urlencode($_POST['email']), 'ip' => $_SERVER['REMOTE_ADDR']);
            $data = json_encode($data);
            $sql = "INSERT INTO " . LOG_TABLE_NAME . " SET action_on='" . date('Y-m-d') . "',action_by='" . $_SESSION[SESSION_PREFIX . 'user_name'] . "',mode='" . $mode . "',module='" . USERS_TABLE_NAME . "', data = '" . $data . "'";
            suQuery($sql);
        } elseif ($mode == 'retrieve-password-failure') {
            $data = array('email' => urlencode($_POST['email']), 'ip' => $_SERVER['REMOTE_ADDR']);
            $data = json_encode($data);
            $sql = "INSERT INTO " . LOG_TABLE_NAME . " SET action_on='" . date('Y-m-d') . "',action_by='" . $_SESSION[SESSION_PREFIX . 'user_name'] . "',mode='" . $mode . "',module='" . USERS_TABLE_NAME . "', data = '" . $data . "'";
            suQuery($sql);
        }
    }

}
/* Slugify table name, use '-' instead of '_' */
if (!function_exists('suTablify')) {

    function suTablify($str) {
        $str = str_replace('_', '-', $str);
        return $str;
    }

}
/* Slugify table name, use '_' instead of '-' */
if (!function_exists('suUnTablify')) {

    function suUnTablify($str) {
        $str = str_replace('-', '_', $str);
        return $str;
    }

}

//function suBuildField($sourceForm,$arr, $mode, $labelRequirement = 'No') {//mode is add or update
function suAddMore($sourceForm, $values = '', $addMoreCounter = '') {//mode is add or update
    global $getSettings, $today, $duplicate, $addAccess, $save_for_later, $mode;
    if ($addMoreCounter == '') {
        $addMoreCounter = FIELD_SEPARATOR . '[]';
    } else {
        $addMoreCounter = FIELD_SEPARATOR . $addMoreCounter;
    }
    if ($values != '') {
        $values = suUnstrip($values);
    }
    //echo $addMoreCounter;
    $mode2 = $mode;
    if ($mode == 'add-single') {
        $mode = 'add';
        $mode2 = 'add-single';
    }
    $sql = "SELECT id,structure FROM " . STRUCTURE_TABLE_NAME . " WHERE live='Yes' AND slug='" . $sourceForm . "' LIMIT 0,1";
    $result = suQuery($sql);
    $result['result'] = suUnstrip($result['result']);
    $arr = $result['result'][0]['structure'];
    //$arr = json_decode($structure, 1);
    //Exclude hidden fields to distribute proper column width division
    $hiddenFields = array('hidden', 'json', 'ip_address');
    $hiddenSlugs = array();
    for ($i = 0; $i <= sizeof($arr) - 1; $i++) {
        if (in_array($arr[$i]['Type'], $hiddenFields)) {
            array_push($hiddenSlugs, $arr[$i]['Slug']);
        }
    }
    $searchableDd = array(); //Array to store searchable dropdowns
    $th = '';
    $numTd = (sizeof($arr) - sizeof($hiddenSlugs)); //Subtract hidden fields from td width distribution
    $tdWidth = (int) (95 / $numTd) . '%';
    for ($i = 0; $i <= $numTd; $i++) {
        if (in_array($arr[$i]['Slug'], $hiddenSlugs)) {
            $hiddenClass = 'hide';
        }
        //Add currency and % signs
        if ($arr[$i]['Type'] == 'currency') {
            $symbol = $getSettings['site_currency'];
        } elseif ($arr[$i]['Type'] == 'percentage') {
            $symbol = '%';
        } else {
            $symbol = '';
        }
        //Mark the required *
        if ($arr[$i]['Required'] == 'yes') {
            $requiredSymbol = '*';
        } else {
            $requiredSymbol = '';
        }

        $th .= '<th width="' . $tdWidth . '" class="' . $hiddenClass . '"><sup>' . $requiredSymbol . '</sup>' . $arr[$i]['Name'] . ' <sup>' . $symbol . '</sup></th>';

        switch ($arr[$i]['Type']) {

            //Textarea
            case "textarea":
                $arg = array('type' => 'text', 'id' => $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]', 'name' => $arr[$i]['Slug'] . $addMoreCounter, 'autocomplete' => 'off');
                if ($arr[$i]['Length'] != '') {
                    $arg = array_merge($arg, array('data-parsley-maxlength' => $arr[$i]['Length']));
                    $arg = array_merge($arg, array('data-maxlength' => $arr[$i]['Length']));
                }

                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box'));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['Length'] != '') {
                    $arg = array_merge($arg, array('maxlength' => $arr[$i]['Length']));
                }
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }

                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suInput('textarea', $arg, $arr[$i]['Default'], TRUE) . '</td>';
                break;

            //HTML Area
            case "html_area":
                $arg = array('type' => 'text', 'id' => $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]', 'name' => $arr[$i]['Slug'] . $addMoreCounter, 'autocomplete' => 'off');
                if ($arr[$i]['Length'] != '') {
                    //$arg = array_merge($arg, array('data-parsley-maxlength' => $arr[$i]['Length']));
                    //$arg = array_merge($arg, array('data-maxlength' => $arr[$i]['Length']));
                }

                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box'));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = html_entity_decode($values[$arr[$i]['Slug'] . $addMoreCounter]);
                }
                if ($arr[$i]['Length'] != '') {
                    $arg = array_merge($arg, array('maxlength' => $arr[$i]['Length']));
                }
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
//                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
//                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
//                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
//                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                $arg = array_merge($arg, array('onfocus' => "doChangeToHTMLArea(this);"));
                $arg = array_merge($arg, array('onmouseover' => "doChangeToHTMLArea(this);"));
                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suInput('textarea', $arg, $arr[$i]['Default'], TRUE) . '</td>';
                break;

            //Quick Pick
            case "quick_pick":
                $arg = array('type' => 'text', 'id' => $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]', 'name' => $arr[$i]['Slug'] . $addMoreCounter, 'autocomplete' => 'off');
                if ($arr[$i]['Length'] != '') {
                    $arg = array_merge($arg, array('data-parsley-maxlength' => $arr[$i]['Length']));
                    $arg = array_merge($arg, array('data-maxlength' => $arr[$i]['Length']));
                }

                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box'));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['Length'] != '') {
                    $arg = array_merge($arg, array('maxlength' => $arr[$i]['Length']));
                }
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }
                $requiredStar = '';
                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }

                $options = explode(',', suUnstrip($arr[$i]['Length']));
                //sort($options);
                //doQuickPick(sourceVal, targetEle);
                $quickPicks = '';
                for ($q = 0; $q <= sizeof($options) - 1; $q++) {
                    $options[$q] = trim($options[$q]);
                    $qArg = "'" . $options[$q] . "',this";
                    $quickPicks .= '<a href="javascript:;" onclick="doQuickPickClosest(' . $qArg . ')">' . $options[$q] . '</a>. ';
                }
                //Build title
                $arg = array_merge($arg, array('title' => $arr[$i]['Name']));

                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '"><div><i>Quick Picks:</i> ' . $quickPicks . '</div>' . suInput('textarea', $arg, $arr[$i]['Default'], TRUE) . '</td>';
                break;

            //Quick Pick from DB
            case "quick_pick_from_db":
                $arg = array('type' => 'text', 'id' => $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]', 'name' => $arr[$i]['Slug'] . $addMoreCounter, 'autocomplete' => 'off');
                if ($arr[$i]['Length'] != '') {
                    $arg = array_merge($arg, array('data-parsley-maxlength' => $arr[$i]['Length']));
                    $arg = array_merge($arg, array('data-maxlength' => $arr[$i]['Length']));
                }

                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box'));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['Length'] != '') {
                    $arg = array_merge($arg, array('maxlength' => $arr[$i]['Length']));
                }
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }


                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build quick picks
                $tableField = explode('.', $arr[$i]['Source']);
                $table = $tableField[0];
                $field = $tableField[1];
                $field = suSlugifyStr($field, '_');
                $extraSql = html_entity_decode(html_entity_decode($arr[$i]['ExtraSQL']));

                $sql = "SELECT " . suJsonExtract('data', $field) . " FROM  " . $table . " WHERE live='Yes'  " . $extraSql . " ORDER BY " . $field;

                $result = suQuery($sql);
                $o = $result['result'];

                $options = array();
                $z = '0';
                foreach ($o as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        $options[$z] = $value2;
                        $z = $z + 1;
                    }
                }

                if (sizeof($options) > 0) {
                    //doQuickPick(sourceVal, targetEle);
                    $quickPicks = '';
                    for ($q = 0; $q <= sizeof($options) - 1; $q++) {
                        $options[$q] = trim($options[$q]);
                        $qArg = "'" . $options[$q] . "',this";
                        $quickPicks .= '<a href="javascript:;" onclick="doQuickPickClosest(' . $qArg . ')">' . $options[$q] . '</a>. ';
                    }
                }
                //Build title
                $arg = array_merge($arg, array('title' => $arr[$i]['Name']));

                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '"><div><i>Quick Picks:</i> ' . $quickPicks . '</div>' . suInput('textarea', $arg, $arr[$i]['Default'], TRUE) . '</td>';
                break;


            //Textbox
            case "textbox":
                $arg = array('type' => 'text', 'id' => $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]', 'name' => $arr[$i]['Slug'] . $addMoreCounter, 'autocomplete' => 'off');
                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box'));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }

                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                $arr['Default'] = html_entity_decode($arr['Default']);
                if ($arr[$i]['Length'] != '') {
                    $arg = array_merge($arg, array('maxlength' => $arr[$i]['Length']));
                }
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }


                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //$arg = array_merge($arg, array('placeholder' => $requiredStar . suUnstrip($arr[$i]['Name'])));
                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suInput('input', $arg) . '</td>';
                break;

            //Picture
            case "picture_field":
                $arg = array('type' => 'file', 'id' => $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]', 'name' => $arr[$i]['Slug'] . $addMoreCounter, 'autocomplete' => 'off');
                if ($arr[$i]['CssClass'] != '') {
                    $cssClass = $arr[$i]['CssClass'] . ' grid-box left';
                    $arg = array_merge($arg, array('class' => 'hide'));
                } else {
                    $arg = array_merge($arg, array('class' => 'hide'));
                    $cssClass = 'form-control grid-box left';
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                    //$arr[$i]['_____value'] = suUnstrip($values[$arr[$i]['Slug']]);
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['Length'] != '') {
                    $arg = array_merge($arg, array('maxlength' => $arr[$i]['Length']));
                }
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }

                $requiredStar = '';
                if ($mode == 'add') {
                    if ($save_for_later == 'No') {
                        if ($arr[$i]['Required'] == 'yes') {
                            $arg = array_merge($arg, array('required' => 'required'));
                            $requiredStar = '*';
                        }
                    } else {
                        if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                            $requiredStar = '*';
                            $arg = array_merge($arg, array('required' => 'required'));
                        }
                    }
                }
                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                $buttonVal = '<i class="fa fa-paperclip"></i> ';
                $onclickJs = "$(this).parent().children('input[type=file]').trigger('click');";
                $onchangeJs = "$(this).parent().children('button').children('span').html(doStripFakepath($(this).val()))";
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange'] . ';' . $onchangeJs));
                } else {
                    $arg = array_merge($arg, array('onchange' => $onchangeJs));
                }

                $paperClip = '<button title="' . CLICK_TO_SELECT . '" type="button" class="' . $cssClass . '" id="data_button_' . $arr[$i]['Slug'] . $addMoreCounter . '" onclick="' . $onclickJs . '">' . $buttonVal . '<span class="hint" id="data_span_' . $arr[$i]['Slug'] . $addMoreCounter . '"></span></button>';
                $allowed = '';
                for ($l = 0; $l <= sizeof($getSettings['allowed_picture_formats']) - 1; $l++) {
                    $allowed .= $getSettings['allowed_picture_formats'][$l] . '/';
                }
                $allowed = substr($allowed, 0, -1);
                $allowed = sprintf(VALID_FILE_FORMATS, urldecode($allowed));
                $allowed .= ' ' . suGetMaxUploadSize() . ' Max.';
                $allowed = '<div class="hint">' . $allowed . '</div>';

                if ($mode == 'update') {
                    $arg2 = array('type' => 'hidden', 'id' => RESERVED_PREVIOUS_PREFEX . $arr[$i]['Slug'] . '[]', 'name' => RESERVED_PREVIOUS_PREFEX . $arr[$i]['Slug'] . $addMoreCounter, 'value' => $arr[$i]['Default']);

                    if (file_exists(ADMIN_UPLOAD_PATH . $arr[$i]['Default']) && ($arr[$i]['Default'] != '')) {

                        $img = "<a target='_blank' href='" . UPLOAD_URL . $arr[$i]['Default'] . "' class='imgThumb' style='background:url(" . UPLOAD_URL . $arr[$i]['Default'] . ")'></a><p><a target='_blank' href='" . BASE_URL . 'files/' . $arr[$i]['Default'] . "'>" . suUnMakeUploadPath($arr[$i]['Default']) . "</a></p>";
                    }
                } else {
                    $arg2 = array('type' => 'hidden', 'id' => RESERVED_PREVIOUS_PREFEX . $arr[$i]['Slug'] . '[]', 'name' => RESERVED_PREVIOUS_PREFEX . $arr[$i]['Slug'] . $addMoreCounter);
                }
                //$arg = array_merge($arg, array('placeholder' => $requiredStar . suUnstrip($arr[$i]['Name'])));
                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . $img . suInput('input', $arg) . $paperClip . $allowed . suInput('input', $arg2) . '</td>';
                break;
            //Attachment
            case "attachment_field":
                $arg = array('type' => 'file', 'id' => $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]', 'name' => $arr[$i]['Slug'] . $addMoreCounter, 'autocomplete' => 'off');
                if ($arr[$i]['CssClass'] != '') {
                    $cssClass = $arr[$i]['CssClass'] . ' grid-box left';
                    $arg = array_merge($arg, array('class' => 'hide'));
                } else {
                    $arg = array_merge($arg, array('class' => 'hide'));
                    $cssClass = 'form-control grid-box left';
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['Length'] != '') {
                    $arg = array_merge($arg, array('maxlength' => $arr[$i]['Length']));
                }
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }


                $requiredStar = '';
                if ($mode == 'add') {
                    if ($save_for_later == 'No') {
                        if ($arr[$i]['Required'] == 'yes') {
                            $arg = array_merge($arg, array('required' => 'required'));
                            $requiredStar = '*';
                        }
                    } else {
                        if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                            $requiredStar = '*';
                            $arg = array_merge($arg, array('required' => 'required'));
                        }
                    }
                }

                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                        unset($arg['required']);
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }

                $buttonVal = '<i class="fa fa-paperclip"></i> ';
                $onclickJs = "$(this).parent().children('input[type=file]').trigger('click');";
                $onchangeJs = "$(this).parent().children('button').children('span').html(doStripFakepath($(this).val()))";
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange'] . ';' . $onchangeJs));
                } else {
                    $arg = array_merge($arg, array('onchange' => $onchangeJs));
                }
                $paperClip = '<button title="' . CLICK_TO_SELECT . '" type="button" class="' . $cssClass . '" id="data_button_' . $arr[$i]['Slug'] . '[]' . '" name="data_button_' . $arr[$i]['Slug'] . $addMoreCounter . '" onclick="' . $onclickJs . '">' . $buttonVal . '<span class="hint" id="data_span_' . $arr[$i]['Slug'] . $addMoreCounter . '"></span></button>';
                $allowed = '';
                for ($l = 0; $l <= sizeof($getSettings['allowed_file_formats']) - 1; $l++) {
                    $allowed .= $getSettings['allowed_file_formats'][$l] . '/';
                }
                $allowed = substr($allowed, 0, -1);
                $allowed = sprintf(VALID_FILE_FORMATS, urldecode($allowed));
                $allowed .= ' ' . suGetMaxUploadSize() . ' Max.';

                $allowed = '<div class="hint">' . $allowed . '</div>';
                if ($mode == 'update') {
                    $arg2 = array('type' => 'hidden', 'id' => RESERVED_PREVIOUS_PREFEX . $arr[$i]['Slug'] . '[]', 'name' => RESERVED_PREVIOUS_PREFEX . $arr[$i]['Slug'] . $addMoreCounter, 'value' => $arr[$i]['Default']);

                    if (file_exists(ADMIN_UPLOAD_PATH . $arr[$i]['Default']) && ($arr[$i]['Default'] != '')) {
                        $ext = suGetExtension($arr[$i]['Default']);
                        if ($ext == 'pdf') {
                            $faIcon = 'fa-file-pdf-o';
                        } elseif ($ext == 'doc') {
                            $faIcon = 'fa-file-word-o';
                        } elseif ($ext == 'docx') {
                            $faIcon = 'fa-file-word-o';
                        } elseif ($ext == 'xls') {
                            $faIcon = 'fa-file-excel-o';
                        } elseif ($ext == 'xlsx') {
                            $faIcon = 'fa-file-excel-o';
                        } elseif ($ext == 'ppt') {
                            $faIcon = 'fa-file-powerpoint-o';
                        } elseif ($ext == 'pptx') {
                            $faIcon = 'fa-file-powerpoint-o';
                        } elseif ($ext == 'gif') {
                            $faIcon = 'fa-image';
                        } elseif ($ext == 'jpg') {
                            $faIcon = 'fa-image';
                        } elseif ($ext == 'png') {
                            $faIcon = 'fa-image';
                        } elseif ($ext == 'jpeg') {
                            $faIcon = 'fa-image';
                        } else {
                            $faIcon = 'fa-file-o';
                        }
                        $att = "<a target='_blank' href='" . UPLOAD_URL . $arr[$i]['Default'] . "' class='fa " . $faIcon . " attachmentThumb size-400'></a><p><a target='_blank' href='" . UPLOAD_URL . $arr[$i]['Default'] . "'>" . suUnMakeUploadPath($arr[$i]['Default']) . "</a></p>";
                    }
                } else {
                    $arg2 = array('type' => 'hidden', 'id' => RESERVED_PREVIOUS_PREFEX . $arr[$i]['Slug'] . '[]', 'name' => RESERVED_PREVIOUS_PREFEX . $arr[$i]['Slug'] . $addMoreCounter);
                }
                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . $att . suInput('input', $arg) . $paperClip . $allowed . suInput('input', $arg2) . '</td>';
                break;

            //URL
            case "url":
                $arg = array('type' => 'text', 'id' => $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]', 'name' => $arr[$i]['Slug'] . $addMoreCounter, 'autocomplete' => 'off');
                //Parsley
                $arg = array_merge($arg, array('data-parsley-type' => 'url', 'data-parsley-trigger' => 'keyup'));


                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box'));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['Length'] != '') {
                    $arg = array_merge($arg, array('maxlength' => $arr[$i]['Length']));
                }
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }


                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //$arg = array_merge($arg, array('placeholder' => $requiredStar . suUnstrip($arr[$i]['Name'])));
                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suInput('input', $arg) . '</td>';
                break;

            //Date
            case "date":
                $arg = array('type' => 'text', 'id' => $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]', 'name' => $arr[$i]['Slug'] . $addMoreCounter, 'autocomplete' => 'off');
                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box dateBox dateBox2'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box dateBox dateBox2'));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = suDateFromDb($arr[$i]['_____value']);
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = suDateFromDb($values[$arr[$i]['Slug'] . $addMoreCounter]);
                }


                if ($arr[$i]['Default'] != '') {
                    if ($mode == 'add') {
                        $sqlDate = "SELECT DATE_ADD('" . date('Y-m-d') . "', INTERVAL " . $arr['Default'] . " DAY) AS dt";
                        $resultDate = suQuery($sqlDate);
                        $arr[$i]['Default'] = $resultDate['result'][0]['dt'];
                        $arr[$i]['Default'] = suDateFromDb($arr[$i]['Default']);
                        $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                    }
                } else {
                    $arr[$i]['Default'] = '';
                }

                if ($arr[$i]['Length'] != '') {
                    $arg = array_merge($arg, array('maxlength' => $arr[$i]['Length']));
                }
                //Date picker requirements
                $date_format = $getSettings['date_format'];
                $default_value = $arr[$i]['Default'];

                $dataPicker = ''; //"doDatePicker('" . $date_format . "',this);";

                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $dataPicker . $arr[$i]['OnClick']));
                } else {
                    $arg = array_merge($arg, array('onclick' => $dataPicker));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }


                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //$arg = array_merge($arg, array('placeholder' => $requiredStar . suUnstrip($arr[$i]['Name'])));

                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suInput('input', $arg) . '</td>';
                break;

            //Autocomplete
            case "autocomplete":
                $arg = array('type' => 'text', 'id' => $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]', 'name' => $arr[$i]['Slug'] . $addMoreCounter, 'autocomplete' => 'off');
                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box'));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['Length'] != '') {
                    $arg = array_merge($arg, array('maxlength' => $arr[$i]['Length']));
                }
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }


                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Convert to autocomplete
                $src = ADMIN_URL . "remote.php?do=autocomplete" . $extraSql . "&source=" . urlencode($arr[$i]['Source']);
                $arg = array_merge($arg, array('onfocus' => "doChangeToAutocomplete(this,'" . $src . "');"));
                $arg = array_merge($arg, array('onmouseover' => "doChangeToAutocomplete(this,'" . $src . "');"));


                //$arg = array_merge($arg, array('placeholder' => $requiredStar . suUnstrip($arr[$i]['Name'])));
                $arg = array_merge($arg, array('placeholder' => TYPE_FOR_SUGGESTIONS));
                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suInput('input', $arg) . '</td>';
                break;

            //Password
            case "password":
                $arg = array('type' => 'password', 'id' => $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]', 'name' => $arr[$i]['Slug'] . $addMoreCounter, 'autocomplete' => 'off');

                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box'));
                }
                //Build title
                $arg = array_merge($arg, array('title' => $arr[$i]['Name']));

                //Placeholder
                //$arg = array_merge($arg, array('placeholder' => $arr[$i]['Name']));

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = suDecrypt($values[$arr[$i]['Slug'] . $addMoreCounter]);
                }
                if ($arr[$i]['Length'] != '') {
                    $arg = array_merge($arg, array('maxlength' => $arr[$i]['Length']));
                }
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }
//                if ($arr[$i]['Required'] == 'yes') {
//                    $arg = array_merge($arg, array('required' => 'required'));
//                    $requiredStar = '*';
//                }

                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //$arg = array_merge($arg, array('placeholder' => $requiredStar . suUnstrip($arr[$i]['Name'])));
                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suInput('input', $arg) . '</td>';
                break;

            //Dropdown
            case "year":
                $arg = array();
                $moreArg = '';

                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box'));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                } else {
                    if ($mode == 'add') {
                        $arr[$i]['Default'] = date('Y') + ($arr[$i]['Default']);
                    }
                }

                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }


                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }



                if ($arr[$i]['Default'] != '') {
                    //$arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build title
                $arg = array_merge($arg, array('title' => $arr[$i]['Name']));

                foreach ($arg as $argKey => $argValue) {
                    $moreArg .= $argKey . "='" . $argValue . "' ";
                }

                $o[''] = 'Select..';

                //Get start and end year
                //echo $arr[$i]['Length'];
                $period = explode(',', $arr[$i]['Length']);
                $startYear = trim($period[0]);
                $startYear = date('Y') + ($startYear);
                $endYear = trim($period[1]);
                $endYear = date('Y') + ($endYear);
                for ($ix = $startYear; $ix <= $endYear; $ix++) {
                    $o[$ix] = $ix;
                }
                $options = $o;
                //$arg = array_merge($arg, array('placeholder' => $requiredStar . suUnstrip($arr[$i]['Name'])));
                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suDropdown($arr[$i]['Slug'] . $addMoreCounter, $options, $arr[$i]['Default'], $moreArg, $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]') . '</td>';
                break;
            //Dropdown
            case "dropdown":
                $arg = array();
                $moreArg = '';

                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box'));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }


                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build title
                $arg = array_merge($arg, array('title' => $arr[$i]['Name']));

                foreach ($arg as $argKey => $argValue) {
                    $moreArg .= $argKey . "='" . $argValue . "' ";
                }

                $o[''] = 'Select..';
                $optionsItems = explode(',', $arr[$i]['Length']);
                for ($n = 0; $n <= sizeof($optionsItems) - 1; $n++) {

                    $optionsItems[$n] = trim($optionsItems[$n]);
                    $o[$optionsItems[$n]] = $optionsItems[$n];
                }
                $options = $o;

                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suDropdown($arr[$i]['Slug'] . $addMoreCounter, $options, $arr[$i]['Default'], $moreArg, $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]') . '</td>';
                break;

            //Checkbox
            case "checkbox":

                $arg = array();
                $moreArg = '';
                if ($mode == 'add') {
                    if ($arr[$i]['Default'] != '') {
                        $arr[$i]['Default'] = explode(',', $arr[$i]['Default']);
                    }
                } else {
                    if ($arr[$i]['Default'] != '') {
                        $arr[$i]['Default'] = $arr[$i]['_____value'];
                    }
                }

                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box'));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }

                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                //$arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }



                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build title
                $arg = array_merge($arg, array('title' => $arr[$i]['Name']));

                $options = explode(',', $arr[$i]['Length']);


                //$arg = array_merge($arg, array('placeholder' => $requiredStar . suUnstrip($arr[$i]['Name'])));
                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suCheckbox($arr[$i]['Slug'] . $addMoreCounter, $options, $arr[$i]['Default'], $arg, 'regular', $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]') . '</td>';
                break;


            //Checkbox from DB
            case "checkbox_from_db":
                $arg = array();
                $moreArg = '';
                if ($mode == 'add') {
                    if ($arr[$i]['Default'] != '') {
                        $arr[$i]['Default'] = explode(',', $arr[$i]['Default']);
                    }
                } else {
                    if ($arr[$i]['Default'] != '') {
                        $arr[$i]['Default'] = $arr[$i]['_____value'];
                    }
                }

                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box'));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                //$arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }


                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build title
                $arg = array_merge($arg, array('title' => $arr[$i]['Name']));

                $tableField = explode('.', $arr[$i]['Source']);
                $table = $tableField[0];
                $field = $tableField[1];
                $field = suSlugifyStr($field, '_');
                $extraSql = html_entity_decode(html_entity_decode($arr[$i]['ExtraSQL']));


                $sql = "SELECT " . suJsonExtract('data', $field) . " FROM  " . $table . " WHERE live='Yes'  " . $extraSql . " ORDER BY " . $field;

                $result = suQuery($sql);
                $o = $result['result'];

                $options = array();
                $z = '0';
                foreach ($o as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        $options[$z] = $value2;
                        $z = $z + 1;
                    }
                }

                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suCheckbox($arr[$i]['Slug'] . $addMoreCounter, $options, $arr[$i]['Default'], $arg, 'regular', $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]') . '</td>';
                break;

            //Checkbox from DB Switch
            case "checkbox_from_db_switch":
                $arg = array();
                $moreArg = '';
                if ($mode == 'add') {
                    if ($arr[$i]['Default'] != '') {
                        $arr[$i]['Default'] = explode(',', $arr[$i]['Default']);
                    }
                } else {
                    if ($arr[$i]['Default'] != '') {
                        $arr[$i]['Default'] = $arr[$i]['_____value'];
                    }
                }

                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box'));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                //$arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }

                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build title
                $arg = array_merge($arg, array('title' => $arr[$i]['Name']));

                $tableField = explode('.', $arr[$i]['Source']);
                $table = $tableField[0];
                $field = $tableField[1];
                $field = suSlugifyStr($field, '_');
                $extraSql = html_entity_decode(html_entity_decode($arr[$i]['ExtraSQL']));

                $sql = "SELECT " . suJsonExtract('data', $field) . " FROM  " . $table . " WHERE live='Yes'  " . $extraSql . " ORDER BY " . $field;

                $result = suQuery($sql);
                $o = $result['result'];

                $options = array();
                $z = '0';
                foreach ($o as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        $options[$z] = $value2;
                        $z = $z + 1;
                    }
                }

                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suCheckbox($arr[$i]['Slug'] . $addMoreCounter, $options, $arr[$i]['Default'], $arg, 'switch', $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]') . '</td>';
                break;

            //Radio Button
            case "radio_button":
                $arg = array();
                $moreArg = '';
                //echo "===".$arr[$i]['_____value'];

                if ($mode == 'add') {
                    if ($arr[$i]['Default'] != '') {
                        $arr[$i]['Default'] = explode(',', $arr[$i]['Default']);
                    }
                } else {
                    if ($arr[$i]['Default'] != '') {
                        $arr[$i]['Default'] = $arr[$i]['_____value'];
                    }
                }

                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box'));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }

                //echo "===".$arr[$i]['_____value'];
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }

                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }
                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }

                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build title
                $arg = array_merge($arg, array('title' => $arr[$i]['Name']));

                $options = explode(',', $arr[$i]['Length']);
                //$arg = array_merge($arg, array('placeholder' => $requiredStar . suUnstrip($arr[$i]['Name'])));
                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suRadio($arr[$i]['Slug'] . $addMoreCounter, $options, $arr[$i]['Default'], $arg, 'regular', $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]') . '</td>';
                break;

            //Radio Button Slider
            case "radio_button_slider":
                $arg = array();
                $moreArg = '';
                if ($mode == 'add') {
                    if ($arr[$i]['Default'] != '') {
                        $arr[$i]['Default'] = explode(',', $arr[$i]['Default']);
                    }
                } else {
                    if ($arr[$i]['Default'] != '') {
                        $arr[$i]['Default'] = $arr[$i]['_____value'];
                    }
                }

                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box'));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }


                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build title
                $arg = array_merge($arg, array('title' => $arr[$i]['Name']));

                $options = explode(',', $arr[$i]['Length']);

                //$arg = array_merge($arg, array('placeholder' => $requiredStar . suUnstrip($arr[$i]['Name'])));
                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suRadio($arr[$i]['Slug'] . $addMoreCounter, $options, $arr[$i]['Default'], $arg, 'slider', $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]') . '</td>';
                break;

            //Radio from DB
            case "radio_button_from_db":
                $arg = array();
                $moreArg = '';
                if ($mode == 'add') {
                    if ($arr[$i]['Default'] != '') {
                        $arr[$i]['Default'] = explode(',', $arr[$i]['Default']);
                    }
                } else {
                    if ($arr[$i]['Default'] != '') {
                        $arr[$i]['Default'] = $arr[$i]['_____value'];
                    }
                }

                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box'));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }

                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }

                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build title
                $arg = array_merge($arg, array('title' => $arr[$i]['Name']));

                $tableField = explode('.', $arr[$i]['Source']);
                $table = $tableField[0];
                $field = $tableField[1];
                $field = suSlugifyStr($field, '_');
                $extraSql = html_entity_decode(html_entity_decode($arr[$i]['ExtraSQL']));

                $sql = "SELECT " . suJsonExtract('data', $field) . " FROM  " . $table . " WHERE live='Yes'  " . $extraSql . " ORDER BY " . $field;

                $result = suQuery($sql);
                $o = $result['result'];

                $options = array();
                $z = '0';
                foreach ($o as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        $options[$z] = $value2;
                        $z = $z + 1;
                    }
                }

                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suRadio($arr[$i]['Slug'] . $addMoreCounter, $options, $arr[$i]['Default'], $arg, 'regular', $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]') . '</td>';
                break;


            //Radio Button DB Slider
            case "radio_button_from_db_slider":
                $arg = array();
                $moreArg = '';
                if ($mode == 'add') {
                    if ($arr[$i]['Default'] != '') {
                        $arr[$i]['Default'] = explode(',', $arr[$i]['Default']);
                    }
                } else {
                    if ($arr[$i]['Default'] != '') {
                        $arr[$i]['Default'] = $arr[$i]['_____value'];
                    }
                }

                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box'));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }


                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }
                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build title
                $arg = array_merge($arg, array('title' => $arr[$i]['Name']));

                $tableField = explode('.', $arr[$i]['Source']);
                $table = $tableField[0];
                $field = $tableField[1];
                $field = suSlugifyStr($field, '_');
                $extraSql = html_entity_decode(html_entity_decode($arr[$i]['ExtraSQL']));

                $sql = "SELECT " . suJsonExtract('data', $field) . " FROM  " . $table . " WHERE live='Yes'  " . $extraSql . " ORDER BY " . $field;

                $result = suQuery($sql);
                $o = $result['result'];

                $options = array();
                $z = '0';
                foreach ($o as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        $options[$z] = $value2;
                        $z = $z + 1;
                    }
                }
                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suRadio($arr[$i]['Slug'] . $addMoreCounter, $options, $arr[$i]['Default'], $arg, 'slider', $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]') . '</td>';
                break;


            //Radio Button to Dropdown from DB
            case "radio_to_dropdown_from_db":
                $arg = array();
                $moreArg = '';
                if ($mode == 'add') {
                    if ($arr[$i]['Default'] != '') {
                        $arr[$i]['Default'] = explode(',', $arr[$i]['Default']);
                    }
                } else {
                    if ($arr[$i]['Default'] != '') {
                        $arr[$i]['Default'] = $arr[$i]['_____value'];
                    }
                }

                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box'));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }

                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build title
                $arg = array_merge($arg, array('title' => $arr[$i]['Name']));

                $tableField = explode('.', $arr[$i]['Source']);
                $table = $tableField[0];
                $field = $tableField[1];
                $field = suSlugifyStr($field, '_');
                $extraSql = html_entity_decode(html_entity_decode($arr[$i]['ExtraSQL']));

                $sql = "SELECT " . suJsonExtract('data', $field) . " FROM  " . $table . " WHERE live='Yes'  " . $extraSql . " ORDER BY " . $field;

                $result = suQuery($sql);
                $o = $result['result'];
                if (sizeof($o) > 3) {
                    $options = array('' => 'Select..');
                }
                $z = '0';
                foreach ($o as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        $options[$z] = $value2;
                        $z = $z + 1;
                    }
                }

                foreach ($arg as $argKey => $argValue) {
                    $moreArg .= $argKey . "='" . $argValue . "' ";
                }

                if (sizeof($options) <= 3) {
                    $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suRadio($arr[$i]['Slug'] . $addMoreCounter, $options, $arr[$i]['Default'], $arg, 'regular', $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]') . '</td>';
                } else {
                    $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suDropdown($arr[$i]['Slug'] . $addMoreCounter, $options, $arr[$i]['Default'], $moreArg, $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]') . '</td>';
                }
                break;

            //Checkbox Switch
            case "checkbox_switch":
                $arg = array();
                $moreArg = '';
                if ($mode == 'add') {
                    if ($arr[$i]['Default'] != '') {
                        $arr[$i]['Default'] = explode(',', $arr[$i]['Default']);
                    }
                } else {
                    if ($arr[$i]['Default'] != '') {
                        $arr[$i]['Default'] = $arr[$i]['_____value'];
                    }
                }

                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box'));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }

                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                //$arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }


                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build title
                $arg = array_merge($arg, array('title' => $arr[$i]['Name']));

                $options = explode(',', $arr[$i]['Length']);


                //$arg = array_merge($arg, array('placeholder' => $requiredStar . suUnstrip($arr[$i]['Name'])));
                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suCheckbox($arr[$i]['Slug'] . $addMoreCounter, $options, $arr[$i]['Default'], $arg, 'switch', $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]') . '</td>';

                break;


            //Searchable dropdown
            case "searchable_dropdown":
                $arg = array();
                $moreArg = '';

                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box'));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }


                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Convert to searchable dropdown
                $arg = array_merge($arg, array('onfocus' => "doChangeToSearchable(this);"));
                $arg = array_merge($arg, array('onmouseover' => "doChangeToSearchable(this);"));

                //Build title
                $arg = array_merge($arg, array('title' => $arr[$i]['Name']));

                foreach ($arg as $argKey => $argValue) {
                    $moreArg .= $argKey . "='" . $argValue . "' ";
                }

                $o[''] = 'Select..';
                $optionsItems = explode(',', $arr[$i]['Length']);
                for ($n = 0; $n <= sizeof($optionsItems) - 1; $n++) {

                    $optionsItems[$n] = trim($optionsItems[$n]);
                    $o[$optionsItems[$n]] = $optionsItems[$n];
                }
                $options = $o;

                //$arg = array_merge($arg, array('placeholder' => $requiredStar . suUnstrip($arr[$i]['Name'])));
                array_push($searchableDd, $options);
                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suDropdown($arr[$i]['Slug'] . $addMoreCounter, $options, $arr[$i]['Default'], $moreArg, $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]') . '</td>';

                break;
            //Dropdown from DB
            case "dropdown_from_db":
                $arg = array();
                $moreArg = '';

                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box'));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }


                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build title
                $arg = array_merge($arg, array('title' => $arr[$i]['Name']));

                foreach ($arg as $argKey => $argValue) {
                    $moreArg .= $argKey . "='" . $argValue . "' ";
                }

                $optionsSelect = array('' => 'Select..');

                //Get data from table
                $tableField = explode('.', $arr[$i]['Source']);
                $table = $tableField[0];
                $field = $tableField[1];
                $field = suSlugifyStr($field, '_');
                $extraSql = html_entity_decode(html_entity_decode($arr[$i]['ExtraSQL']));


                $sql2 = "SELECT " . suJsonExtract('data', $field) . " FROM  " . $table . " WHERE live='Yes'  " . $extraSql . " ORDER BY " . $field;

                $result2 = suQuery($sql2);
                $o = $result2['result'];

                $x = array();

                foreach ($o as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        $x[$value[$key2]] = $value2;
                    }
                }
                $options = array_merge($optionsSelect, $x);

                //$arg = array_merge($arg, array('placeholder' => $requiredStar . suUnstrip($arr[$i]['Name'])));
                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suDropdown($arr[$i]['Slug'] . $addMoreCounter, $options, $arr[$i]['Default'], $moreArg, $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]') . '</td>';
                break;
            //Searchable Dropdown from DB
            case "searchable_dropdown_from_db":
                $arg = array();
                $moreArg = '';

                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box'));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }


                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Convert to searchable dropdown
                $arg = array_merge($arg, array('onfocus' => "doChangeToSearchable(this);"));
                $arg = array_merge($arg, array('onmouseover' => "doChangeToSearchable(this);"));

                //Build title
                $arg = array_merge($arg, array('title' => $arr[$i]['Name']));

                foreach ($arg as $argKey => $argValue) {
                    $moreArg .= $argKey . "='" . $argValue . "' ";
                }

                $optionsSelect = array('' => 'Select..');

                //Get data from table
                $tableField = explode('.', $arr[$i]['Source']);
                $table = $tableField[0];
                $field = $tableField[1];
                $field = suSlugifyStr($field, '_');
                $extraSql = html_entity_decode(html_entity_decode($arr[$i]['ExtraSQL']));


                $sql2 = "SELECT " . suJsonExtract('data', $field) . " FROM  " . $table . " WHERE live='Yes'  " . $extraSql . " ORDER BY " . $field;

                $result2 = suQuery($sql2);
                $o = $result2['result'];

                $x = array();

                foreach ($o as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        $x[$value[$key2]] = $value2;
                    }
                }
                $options = array_merge($optionsSelect, $x);

                //$arg = array_merge($arg, array('placeholder' => $requiredStar . suUnstrip($arr[$i]['Name'])));
                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suDropdown($arr[$i]['Slug'] . $addMoreCounter, $options, $arr[$i]['Default'], $moreArg, $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]') . '</td>';
                break;
            //Email
            case "email":
                $arg = array('type' => 'email', 'id' => $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]', 'name' => $arr[$i]['Slug'] . $addMoreCounter, 'autocomplete' => 'off');
                //Parsley
                $arg = array_merge($arg, array('data-parsley-type' => 'email', 'data-parsley-trigger' => 'keyup'));


                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box'));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }

                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);

                if ($arr[$i]['Length'] != '') {
                    $arg = array_merge($arg, array('maxlength' => $arr[$i]['Length']));
                }
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }


                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //$arg = array_merge($arg, array('placeholder' => $requiredStar . suUnstrip($arr[$i]['Name'])));
                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suInput('input', $arg) . '</td>';
                break;
            //Integer
            case "integer":


                $arg = array('type' => 'number', 'id' => $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]', 'name' => $arr[$i]['Slug'] . $addMoreCounter, 'autocomplete' => 'off');


                //Parsley
                $arg = array_merge($arg, array('data-parsley-type' => 'integer', 'data-parsley-trigger' => 'keyup'));
                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box integer'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box integer'));
                }
                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['Length'] != '') {
                    $arg = array_merge($arg, array('maxlength' => $arr[$i]['Length']));
                    //Specify max length for input type number
                    $max = '';
                    $min = '-';
                    for ($n = 0; $n < $arr[$i]['Length']; $n++) {
                        $max .= '9';
                        $min .= '9';
                    }
                    $arg = array_merge($arg, array('min' => $min));
                    $arg = array_merge($arg, array('max' => $max));
                }
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $okp = "doOnlyIntegers2(event);";
                    $arg = array_merge($arg, array('onkeypress' => $okp . $arr[$i]['OnKeyPress']));
                } else {
                    $okp = "doOnlyIntegers2(event);";
                    $arg = array_merge($arg, array('onkeypress' => $okp));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }


                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }

                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suInput('input', $arg) . '</td>';
                break;
            //Decimal
            case "decimal":
                $arg = array('type' => 'number', 'step' => 'any', 'id' => $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]', 'name' => $arr[$i]['Slug'] . $addMoreCounter, 'autocomplete' => 'off');

                //Parsley
                $arg = array_merge($arg, array('data-parsley-type' => 'number', 'data-parsley-trigger' => 'keyup'));
                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box decimal'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box decimal'));
                }
                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['Length'] != '') {
                    $arg = array_merge($arg, array('maxlength' => $arr[$i]['Length']));
                    //Specify max length for input type number
                    $max = '';
                    $min = '-';
                    for ($n = 0; $n < $arr[$i]['Length']; $n++) {
                        $max .= '9';
                        $min .= '9';
                    }
                    $arg = array_merge($arg, array('min' => $min));
                    $arg = array_merge($arg, array('max' => $max));
                }
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $okp = "doOnlyDecimals2(event);";
                    $arg = array_merge($arg, array('onkeypress' => $okp . $arr[$i]['OnKeyPress']));
                } else {
                    $okp = "doOnlyDecimals2(event);";
                    $arg = array_merge($arg, array('onkeypress' => $okp));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }


                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }

                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suInput('input', $arg) . '</td>';
                break;

            //Currency
            case "currency":
                $arg = array('type' => 'number', 'step' => 'any', 'id' => $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]', 'name' => $arr[$i]['Slug'] . $addMoreCounter, 'autocomplete' => 'off');

                //Parsley
                $arg = array_merge($arg, array('data-parsley-type' => 'number', 'data-parsley-trigger' => 'keyup'));
                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box decimal'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box decimal'));
                }
                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['Length'] != '') {
                    $arg = array_merge($arg, array('maxlength' => $arr[$i]['Length']));
                    //Specify max length for input type number
                    $max = '';
                    $min = '-';
                    for ($n = 0; $n < $arr[$i]['Length']; $n++) {
                        $max .= '9';
                        $min .= '9';
                    }
                    $arg = array_merge($arg, array('min' => $min));
                    $arg = array_merge($arg, array('max' => $max));
                }
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $okp = "doOnlyDecimals2(event);";
                    $arg = array_merge($arg, array('onkeypress' => $okp . $arr[$i]['OnKeyPress']));
                } else {
                    $okp = "doOnlyDecimals2(event);";
                    $arg = array_merge($arg, array('onkeypress' => $okp));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }



                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //$arg = array_merge($arg, array('placeholder' => $getSettings['site_currency']));

                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suInput('input', $arg) . '</td>';
                break;

            //Percentage
            case "percentage":
                $arg = array('type' => 'number', 'step' => 'any', 'id' => $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]', 'name' => $arr[$i]['Slug'] . $addMoreCounter, 'autocomplete' => 'off');

                //Parsley
                $arg = array_merge($arg, array('data-parsley-type' => 'number', 'data-parsley-trigger' => 'keyup'));
                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box decimal'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box decimal'));
                }
                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['Length'] != '') {
                    $arg = array_merge($arg, array('maxlength' => $arr[$i]['Length']));
                    //Specify max length for input type number
                    $max = '';
                    $min = '-';
                    for ($n = 0; $n < $arr[$i]['Length']; $n++) {
                        $max .= '9';
                        $min .= '9';
                    }
                    $arg = array_merge($arg, array('min' => $min));
                    $arg = array_merge($arg, array('max' => $max));
                }
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $okp = "doOnlyDecimals2(event);";
                    $arg = array_merge($arg, array('onkeypress' => $okp . $arr[$i]['OnKeyPress']));
                } else {
                    $okp = "doOnlyDecimals2(event);";
                    $arg = array_merge($arg, array('onkeypress' => $okp));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }


                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //$arg = array_merge($arg, array('placeholder' => '%'));

                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suInput('input', $arg) . '</td>';
                break;


            //Phone
            case "phone":
                $arg = array('type' => 'text', 'id' => $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]', 'name' => $arr[$i]['Slug'] . $addMoreCounter, 'autocomplete' => 'off');
                //Parsley
                $arg = array_merge($arg, array('data-parsley-type' => 'integer', 'data-parsley-trigger' => 'keyup'));
                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass'] . ' grid-box integer'));
                } else {
                    $arg = array_merge($arg, array('class' => 'form-control grid-box integer'));
                }
                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['Length'] != '') {
                    $arg = array_merge($arg, array('maxlength' => $arr[$i]['Length']));
                    //Specify max length for input type number
                    $max = '';
                    for ($n = 0; $n < $arr[$i]['Length']; $n++) {
                        $max .= '9';
                    }
                    $arg = array_merge($arg, array('min' => 0));
                    $arg = array_merge($arg, array('max' => $max));
                }
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $okp = "doOnlyIntegers2(event);";
                    $arg = array_merge($arg, array('onkeypress' => $okp . $arr[$i]['OnKeyPress']));
                } else {
                    $okp = "doOnlyIntegers2(event);";
                    $arg = array_merge($arg, array('onkeypress' => $okp));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }

                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //$arg = array_merge($arg, array('placeholder' => $requiredStar . suUnstrip($arr[$i]['Name'])));

                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suInput('input', $arg) . '</td>';
                break;

            //Hidden
            case "hidden":
                $arg = array('type' => 'hidden', 'id' => $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]', 'name' => $arr[$i]['Slug'] . $addMoreCounter);
                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suInput('input', $arg) . '</td>';

                break;
            //JSON
            case "json":
                $arg = array('type' => 'hidden', 'id' => $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]', 'name' => $arr[$i]['Slug'] . $addMoreCounter);
                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suInput('input', $arg) . '</td>';

                break;
            //IP Address
            case "ip_address":
                $arg = array('type' => 'hidden', 'id' => $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]', 'name' => $arr[$i]['Slug'] . $addMoreCounter);
                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                } else {
                    $arg = array_merge($arg, array('value' => $_SERVER['REMOTE_ADDR']));
                }
                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suInput('input', $arg) . '</td>';

                break;


            //Separator
            case "separator":
                $arg = array('type' => 'legend', 'id' => $sourceForm . FIELD_SEPARATOR . $arr[$i]['Slug'] . FIELD_SEPARATOR . '[]', 'name' => $arr[$i]['Slug'] . $addMoreCounter, 'autocomplete' => 'off');
                if ($arr[$i]['Length'] != '') {
                    $arg = array_merge($arg, array('data-parsley-maxlength' => $arr[$i]['Length']));
                    $arg = array_merge($arg, array('data-maxlength' => $arr[$i]['Length']));
                }

                if ($arr[$i]['CssClass'] != '') {
                    $arg = array_merge($arg, array('class' => $arr[$i]['CssClass']));
                }

                if ($arr[$i]['_____value'] != '') {
                    $arr[$i]['Default'] = $arr[$i]['_____value'];
                }
                if ($values[$arr[$i]['Slug'] . $addMoreCounter] != '') {
                    $arr[$i]['Default'] = $values[$arr[$i]['Slug'] . $addMoreCounter];
                }
                $arr[$i]['Default'] = html_entity_decode($arr[$i]['Default']);
                if ($arr[$i]['Length'] != '') {
                    $arg = array_merge($arg, array('maxlength' => $arr[$i]['Length']));
                }
                if ($arr[$i]['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr[$i]['OnClick']));
                }
                if ($arr[$i]['OnKeyUp'] != '') {
                    $arg = array_merge($arg, array('onkeyup' => $arr[$i]['OnKeyUp']));
                }
                if ($arr[$i]['OnKeyPress'] != '') {
                    $arg = array_merge($arg, array('onkeypress' => $arr[$i]['OnKeyPress']));
                }
                if ($arr[$i]['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr[$i]['OnBlur']));
                }
                if ($arr[$i]['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr[$i]['OnChange']));
                }


                $requiredStar = '';
                if ($save_for_later == 'No') {
                    if ($arr[$i]['Required'] == 'yes') {
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    }
                } else {
                    if ($arr[$i]['RequiredSaveForLater'] == 'yes') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('required' => 'required'));
                    }
                }


                if ($arr[$i]['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr[$i]['Default']));
                }
                if ($mode == 'add') {
                    if ($arr[$i]['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr[$i]['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr[$i]['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        unset($arg['required']);
                        unset($arg['data-parsley-required']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }

                $td .= '<td class="' . $hiddenClass . '" width="' . $tdWidth . '" id="grid-td-' . $arr[$i]['Slug'] . '">' . suInput('legend', $arg, $arr[$i]['Length'], TRUE) . '</td>';
                break;
        }//End of case
    }
    $trUid = 'tr_' . uniqid();
    $td = '<tr id="' . $trUid . '">' . $td . '<td width="5%"><a title="' . DELETE . '" href="javascript:;" onclick="doRemoveTr(\'' . $trUid . '\',\'' . CONFIRM_DELETE . '\',\'' . $sourceForm . '\')"><i class="fa fa-times color-Crimson"></i></a></td>'
            . '</tr>';

//Build Header Table

    $headerTable = '<table width="100%" class="table table-striped table-hover tablex">'
            . '<thead><tr>'
            . '' . $th . '<th width="5%">&nbsp;</th></tr></thead>'
            . '</table>';

    $headerTable = '';

//Build Data Table
    $dataTable = '<table width="100%" class="table table-striped table-hover tablex" style="margin-top:-22px">'
            . '<tbody>' . $td . '</tbody>'
            . '</table>';

    if ($mode2 == 'add') {
        echo '<div id="more-destination-' . $sourceForm . '">' . $headerTable . $dataTable . '</div>';
        suPrintJs("remoteText=document.getElementById('more-destination-" . $sourceForm . "').innerHTML;"
                . "parent.doAddMore('more-destination-" . $sourceForm . "',remoteText)");
    } elseif ($mode2 == 'add-single') {
        echo '<div id="more-destination-' . $sourceForm . '">' . $dataTable . '</div>';
        suPrintJs("remoteText=document.getElementById('more-destination-" . $sourceForm . "').innerHTML;"
                . "parent.doAddMore('more-destination-" . $sourceForm . "',remoteText)");
    } else {

        $uid = uniqid();
        echo '<div id="more-destination-' . $uid . '-' . $sourceForm . '">' . $dataTable . '</div>';
        suPrintJs("remoteText=document.getElementById('more-destination-" . $uid . "-" . $sourceForm . "').innerHTML;"
                . "parent.doAddMore('more-destination-" . $sourceForm . "',remoteText)");
    }
    suPrintJs("parent.doAddmoreIds('suForm','" . $sourceForm . "','+');");
}
