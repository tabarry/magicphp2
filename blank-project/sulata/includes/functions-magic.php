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

//Build form fields
if (!function_exists('suBuildField')) {

    function suBuildField($arr, $mode, $labelRequirement = 'No') {//mode is add or update
        global $getSettings, $today, $duplicate, $addAccess, $save_for_later;
        if ($duplicate == TRUE) {
            $mode = 'add';
        }
        $clearIcon = '';
        //Unstrip
        $arr['Slug'] = suUnstrip($arr['Slug']);
        $arr['_____value'] = $arr['_____value'];
        $arr['CssClass'] = suUnstrip($arr['CssClass']);
        $arr['OnClick'] = suUnstrip($arr['OnClick']);
        $arr['OnKeyUp'] = suUnstrip($arr['OnKeyUp']);
        $arr['OnKeyPress'] = suUnstrip($arr['OnKeyPress']);
        $arr['OnBlur'] = suUnstrip($arr['OnBlur']);
        $arr['OnChange'] = suUnstrip($arr['OnChange']);
        $arr['Required'] = suUnstrip($arr['Required']);
        $arr['RequiredSaveForLater'] = suUnstrip($arr['RequiredSaveForLater']);
        $arr['Default'] = suUnstrip($arr['Default']);
        $arr['ReadOnlyAdd'] = suUnstrip($arr['ReadOnlyAdd']);
        $arr['ReadOnlyUpdate'] = suUnstrip($arr['ReadOnlyUpdate']);
        $arr['HideOnUpdate'] = suUnstrip($arr['HideOnUpdate']);
        $arr['Name'] = suUnstrip($arr['Name']);
        $arr['Length'] = suUnstrip($arr['Length']);
        switch ($arr['Type']) {
            //Textbox
            case "textbox":
                $arg = array('type' => 'text', 'name' => $arr['Slug'], 'id' => $arr['Slug'], 'autocomplete' => 'off');
                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
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
                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $requiredStar = '*';
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }
                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($mode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
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
                        $clearIcon = "<a id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . suUnstrip($arr['Name'])));
                }
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
                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                            $requiredStar = '*';
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }
                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($mode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }

                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }

                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name']));
                }
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
                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
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
                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
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
                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                } else {
                    $arg = array_merge($arg, array('value' => $_SERVER['REMOTE_ADDR']));
                }
                //Build title
                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suInput('input', $arg);
                echo '</span>';
                break;
            //Decimal
            case "decimal":
                $arg = array('type' => 'text', 'name' => $arr['Slug'], 'id' => $arr['Slug'], 'autocomplete' => 'off');
                //Parsley
                $arg = array_merge($arg, array('data-parsley-type' => 'number', 'data-parsley-trigger' => 'keyup'));

                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }

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
                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                            $requiredStar = '*';
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }
                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($mode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name']));
                }
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
                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                            $requiredStar = '*';
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }
                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($mode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name']));
                }
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
                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                            $requiredStar = '*';
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }
                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($mode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }


                if ($mode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name']));
                }
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));

                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suInput('input', $arg);
                echo '</span>';
                break;

            //Dropdown
            case "dropdown":
                $arg = array();
                $moreArg = '';

                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
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
                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                            $requiredStar = '*';
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }
                if ($mode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
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
                $optionsItems = explode(',', $arr['Length']);
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
                if ($mode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                            $requiredStar = '*';
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }

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
                $extraSql = html_entity_decode(suUnstrip($arr['ExtraSQL']));


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
                if ($mode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                            $requiredStar = '*';
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }

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

                $optionsItems = explode(',', $arr['Length']);
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
                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                            $requiredStar = '*';
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }

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
                $extraSql = html_entity_decode(suUnstrip($arr['ExtraSQL']));


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
                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                            $requiredStar = '*';
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }
                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => suDecrypt($arr['Default'])));
                    $arg2 = array_merge($arg2, array('value' => suDecrypt($arr['Default'])));
                }
                if ($mode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));
                $arg2 = array_merge($arg2, array('title' => 'Confirm ' . $arr['Name']));

                //Build label if required
                if ($labelRequirement == 'Yes') {
                    if ($getSettings['toggle_password'] == '1') {
                        $togglePassword = "<a href='javascript:;' onmousedown=\"doTogglePassword('" . $arr['Slug'] . "','password','" . CONFIRM_PASSWORD_POSTFIX . "')\" onmouseup=\"doTogglePassword('" . $arr['Slug'] . "','text','" . CONFIRM_PASSWORD_POSTFIX . "')\"><i class='fa fa-eye'></i></a>";
                    } else {
                        $togglePassword = '';
                    }

                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": " . $togglePassword . "</label>";
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
                $js = "$('#" . $arr['Slug'] . "_file').html($('#" . $arr['Slug'] . "').val()); ";
                $arg = array('type' => 'file', 'name' => $arr['Slug'], 'id' => $arr['Slug'], 'class' => 'hide', 'onchange' => $js);


                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }

                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }

                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }


                if ($mode == 'add') {
                    if ($arr['Required'] == 'yes') {
                        if ($save_for_later == 'No') {
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                            $requiredStar = '*';
                        } else {
                            if ($arr['RequiredSaveForLater'] == 'yes') {
                                $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                                $arg = array_merge($arg, array('required' => 'required'));
                                $requiredStar = '*';
                            } else {
                                $requiredStar = '*';
                            }
                        }
                    } else {
                        $requiredStar = '';
                    }
                } else {
                    $requiredStar = '';
                }

                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($mode == 'update') {
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
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));
                echo suInput('input', $arg);
                if ($mode == 'update') {
                    $arg2 = array('type' => 'hidden', 'name' => RESERVED_PREVIOUS_PREFEX . $arr['Slug'], 'id' => RESERVED_PREVIOUS_PREFEX . $arr['Slug'], 'value' => $arr['_____value']);
                    echo suInput('input', $arg2);
                }


                $js = "$('#" . $arr['Slug'] . "').trigger('click');";
                $arg = array('name' => $arr['Slug'] . '_clip', 'id' => $arr['Slug'] . '_clip', 'class' => 'form-control', 'onclick' => $js, 'style' => 'text-align:left;cursor:pointer;');
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
                $js = "$('#" . $arr['Slug'] . "_file').html($('#" . $arr['Slug'] . "').val()); ";
                $arg = array('type' => 'file', 'name' => $arr['Slug'], 'id' => $arr['Slug'], 'class' => 'hide', 'onchange' => $js);

                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }

                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }

                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }
                if ($arr['OnChange'] != '') {
                    $arg = array_merge($arg, array('onchange' => $arr['OnChange']));
                }

                if ($mode == 'add') {
                    if ($arr['Required'] == 'yes') {
                        if ($save_for_later == 'No') {
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                            $requiredStar = '*';
                        } else {
                            if ($arr['RequiredSaveForLater'] == 'yes') {
                                $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                                $arg = array_merge($arg, array('required' => 'required'));
                                $requiredStar = '*';
                            } else {
                                $requiredStar = '*';
                            }
                        }
                    } else {
                        $requiredStar = '';
                    }
                } else {
                    $requiredStar = '';
                }

                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }

                if ($mode == 'update') {
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
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));
                echo suInput('input', $arg);
                if ($mode == 'update') {
                    $arg2 = array('type' => 'hidden', 'name' => RESERVED_PREVIOUS_PREFEX . $arr['Slug'], 'id' => RESERVED_PREVIOUS_PREFEX . $arr['Slug'], 'value' => $arr['_____value']);
                    echo suInput('input', $arg2);
                }

                $js = "$('#" . $arr['Slug'] . "').trigger('click');";
                $arg = array('name' => $arr['Slug'] . '_clip', 'id' => $arr['Slug'] . '_clip', 'class' => 'form-control', 'onclick' => $js, 'style' => 'text-align:left;cursor:pointer;');
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
                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                            $requiredStar = '*';
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }
                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }

                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name']));
                }
                //Handle Extra SQL
                if ($arr['ExtraSQL'] != '') {
                    $extraSql = sucrypt(html_entity_decode(suUnstrip($arr['ExtraSQL'])));
                    $extraSql = "&extra=" . $extraSql;
                } else {
                    $extraSql = '';
                }
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
                if ($mode == 'add') {
                    if ($arr['Default'] != '') {
                        $arr['Default'] = explode(',', $arr['Default']);
                    }
                } else {
                    if ($arr['Default'] != '') {
                        $arr['Default'] = $arr['_____value'];
                    }
                }

                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }

                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }

                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        //$arg = array_merge($arg, array('required' => 'required'));
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $requiredStar = '*';
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            //$arg = array_merge($arg, array('required' => 'required'));
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }
                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }

                //Build label
                echo "<p><label>" . $requiredStar . $arr['Name'] . ":</label></p>";

                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));


                $options = explode(',', $arr['Length']);
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

                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }

                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }

                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        //$arg = array_merge($arg, array('required' => 'required'));
                        $requiredStar = '*';
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            //$arg = array_merge($arg, array('required' => 'required'));
                            $requiredStar = '*';
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }
                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }

                //Build label
                echo "<p><label>" . $requiredStar . $arr['Name'] . ":</label></p>";

                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));


                $options = explode(',', $arr['Length']);
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

                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }

                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }

                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $requiredStar = '*';
//                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
//                        $arg = array_merge($arg, array('required' => 'required'));
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $requiredStar = '*';
//                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
//                            $arg = array_merge($arg, array('required' => 'required'));
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }
                if ($arr['Default'] != '') {
                    //$arg = array_merge($arg, array('value' => $arr['Default']));
                }

                //Build label
                echo "<p><label>" . $requiredStar . $arr['Name'] . ":</label></p>";

                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));


                $options = explode(',', $arr['Length']);
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
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }

                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }

                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $requiredStar = '*';
//                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
//                        $arg = array_merge($arg, array('required' => 'required'));
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $requiredStar = '*';
//                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
//                            $arg = array_merge($arg, array('required' => 'required'));
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }
                if ($arr['Default'] != '') {
                    //$arg = array_merge($arg, array('value' => $arr['Default']));
                }

                //Build label
                echo "<p><label>" . $requiredStar . $arr['Name'] . ":</label></p>";

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
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }

                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }

                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        //$arg = array_merge($arg, array('required' => 'required'));
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $requiredStar = '*';
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            //$arg = array_merge($arg, array('required' => 'required'));
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }
                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }

                //Build label
                echo "<p><label>" . $requiredStar . $arr['Name'] . ":</label></p>";

                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));


                $tableField = explode('.', $arr['Source']);
                $table = $tableField[0];
                $field = $tableField[1];
                $field = suSlugifyStr($field, '_');
                $extraSql = html_entity_decode(suUnstrip($arr['ExtraSQL']));


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
            //Radio Button to Dropdown from DB
            case "radio_to_dropdown_from_db":

                $arg = array();


                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }
                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }

                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }

                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        //$arg = array_merge($arg, array('required' => 'required'));
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $requiredStar = '*';
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            //$arg = array_merge($arg, array('required' => 'required'));
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }



                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));


                $tableField = explode('.', $arr['Source']);
                $table = $tableField[0];
                $field = $tableField[1];
                $field = suSlugifyStr($field, '_');
                $extraSql = html_entity_decode(suUnstrip($arr['ExtraSQL']));

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
                //If div width is 6/12 columns
                if ($arr['Width'] <= 6) {
                    //If options are <=2 then make radio buttons
                    if (sizeof($options) <= 2) {
                        //Build label
                        echo "<p><label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ":</label></p>";
                        echo '<span id="data_span_' . $arr['Slug'] . '">';
                        echo suRadio($arr['Slug'], $options, $arr['Default'], $arg, 'regular');
                        echo '</span>';
                    } else {
                        //Build label
                        if ($labelRequirement == 'Yes') {
                            echo "<p><label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ":</label></p>";
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
                } else {//If div width is greater than 6
                    //If options to populate are <=6 then make radio buttons
                    if (sizeof($options) <= 6) {
                        echo "<p><label>" . $requiredStar . $arr['Name'] . ":</label></p>";
                        echo '<span id="data_span_' . $arr['Slug'] . '">';
                        echo suRadio($arr['Slug'], $options, $arr['Default'], $arg, 'regular');
                        echo '</span>';
                    } else {
                        //Build label
                        if ($labelRequirement == 'Yes') {
                            echo "<p><label>" . $requiredStar . $arr['Name'] . ":</label></p>";
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
                if ($mode == 'add') {
                    if ($arr['Default'] != '') {
                        $arr['Default'] = explode(',', $arr['Default']);
                    }
                } else {
                    if ($arr['_____value'] != '') {
                        $arr['Default'] = $arr['_____value'];
                    }
                }

                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }

                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }

                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        //$arg = array_merge($arg, array('required' => 'required'));
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $requiredStar = '*';
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            //$arg = array_merge($arg, array('required' => 'required'));
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }

                //Build label
                echo "<p><label>" . $requiredStar . $arr['Name'] . ":</label></p>";

                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));


                $tableField = explode('.', $arr['Source']);
                $table = $tableField[0];
                $field = $tableField[1];
                $field = suSlugifyStr($field, '_');
                $extraSql = html_entity_decode(suUnstrip($arr['ExtraSQL']));

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
                echo suCheckbox($arr['Slug'], $options, $arr['Default'], $arg, 'regular');
                echo '</span>';
                break;


            //Checkbox from DB Switch
            case "checkbox_from_db_switch":
                $arg = array();
                if ($arr['_____value'] != '') {
                    $arr['Default'] = $arr['_____value'];
                }

                if ($arr['OnClick'] != '') {
                    $arg = array_merge($arg, array('onclick' => $arr['OnClick']));
                }

                if ($arr['OnBlur'] != '') {
                    $arg = array_merge($arg, array('onblur' => $arr['OnBlur']));
                }

                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        //$arg = array_merge($arg, array('required' => 'required'));
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $requiredStar = '*';
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            //$arg = array_merge($arg, array('required' => 'required'));
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }
                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }

                //Build label
                echo "<p><label>" . $requiredStar . $arr['Name'] . ":</label></p>";

                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));


                $tableField = explode('.', $arr['Source']);
                $table = $tableField[0];
                $field = $tableField[1];
                $field = suSlugifyStr($field, '_');
                $extraSql = html_entity_decode(suUnstrip($arr['ExtraSQL']));

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
                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $requiredStar = '*';
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }
                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($mode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label> <sup id='sup_" . $arr['Slug'] . "'>" . $getSettings['site_currency'] . "</sup>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name'] . " (" . $getSettings['site_currency'] . ")"));
                }
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
                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $requiredStar = '*';
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }
                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                } else {
                    $arr['Default'] = $today;
                }
                if ($mode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name']));
                }
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
                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $requiredStar = '*';
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }
                if ($arr['Default'] != '') {
                    //$arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($mode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
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
                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $requiredStar = '*';
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }
                if ($arr['Default'] != '') {
                    //$arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($mode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name']));
                }

//Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));
                if ($arg['type'] == 'hidden') {
                    echo suInput('input', $arg);
                } else {
                    echo '<span id="data_span_' . $arr['Slug'] . '">';
                    echo suInput('textarea', $arg, $arr['Default'], TRUE);
                    echo '</span>';
                    if ($mode = 'update') {
                        $charCount = strlen($arr['Default']);
                    } else {
                        $charCount = 0;
                    }
                    if ($arr['Length'] > 0) {
                        echo "<div class='color-gray pull-right' id='" . $arr['Slug'] . "_charcount'>" . $charCount . "/" . $arr['Length'] . "</div>";
                        if ($mode = 'update') {
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
                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $requiredStar = '*';
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }
                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($mode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name']));
                }
                //Build quick picks
                $options = explode(',', $arr['Length']);
                //sort($options);
                //doQuickPick(sourceVal, targetEle, errorMsg);
                $quickPicks = '';
                for ($q = 0; $q <= sizeof($options) - 1; $q++) {
                    $options[$q] = trim($options[$q]);
                    $qArg = "'" . $options[$q] . "','" . $arr['Slug'] . "','MSG'";
                    $quickPicks .= '<a href="javascript:;" onclick="doQuickPick(' . $qArg . ')">' . $options[$q] . '</a>. ';
                }

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
                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $requiredStar = '*';
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }
                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($mode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
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
                $extraSql = html_entity_decode(suUnstrip($arr['ExtraSQL']));

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
                        $qArg = "'" . $options[$q] . "','" . $arr['Slug'] . "','MSG'";
                        $quickPicks .= '<a href="javascript:;" onclick="doQuickPick(' . $qArg . ')">' . $options[$q] . '</a>. ';
                    }
                    echo "<div><i>Quick Picks:</i> " . $quickPicks . "</div>";
                }
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

                if ($mode == 'update') {
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
                if ($mode == 'add') {
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
                if ($arr['Required'] == 'yes') {
                    if ($save_for_later == 'No') {
                        $requiredStar = '*';
                        $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                        $arg = array_merge($arg, array('required' => 'required'));
                    } else {
                        if ($arr['RequiredSaveForLater'] == 'yes') {
                            $requiredStar = '*';
                            $arg = array_merge($arg, array('data-parsley-required' => 'true'));
                            $arg = array_merge($arg, array('required' => 'required'));
                        } else {
                            $requiredStar = '*';
                        }
                    }
                } else {
                    $requiredStar = '';
                }
                if ($arr['Default'] != '') {
                    $arg = array_merge($arg, array('value' => $arr['Default']));
                }
                if ($mode == 'add') {
                    if ($arr['ReadOnlyAdd'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['ReadOnlyUpdate'] == 'yes') {
                        $arg = array_merge($arg, array('readonly' => 'readonly'));
                    }
                }
                if ($mode == 'update') {
                    if ($arr['HideOnUpdate'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                if ($mode == 'add') {
                    if ($arr['HideOnAdd'] == 'yes') {
                        unset($arg['Type']);
                        $arg = array_merge($arg, array('type' => 'hidden'));
                    }
                }
                //Build label if required
                if ($labelRequirement == 'Yes') {
                    //Show clear field icon if set in settings
                    if ($getSettings['show_clear_field'] == 1) {
                        $clearIcon = "<a id='clear_" . $arr['Slug'] . "' href='javascript:;' onclick=\"$('#" . $arr['Slug'] . "').val('')\"><i class='fa fa-times-circle-o'></i></a>";
                    } else {
                        $clearIcon = "";
                    }
                    echo "<label id='lbl_" . $arr['Slug'] . "'>" . $requiredStar . $arr['Name'] . ": {$clearIcon}</label>";
                } else {
                    $arg = array_merge($arg, array('placeholder' => $requiredStar . $arr['Name']));
                }
                //Build title
                $arg = array_merge($arg, array('title' => $arr['Name']));
                echo '<span id="data_span_' . $arr['Slug'] . '">';
                echo suInput('input', $arg);
                echo '</span>';
                break;
        }
    }

}
//Build preview fields
if (!function_exists('suPreviewField')) {

    function suPreviewField($arr) {
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

                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "</td>";

                    break;

                //Dropdown from DB
                case "dropdown_from_db":

                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "</td>";
                    break;



                //Searchable Dropdown
                case "searchable_dropdown":

                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "</td>";
                    break;


                //Searchable Dropdown from database
                case "searchable_dropdown_from_db":

                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "</td>";
                    break;

                //Password
                case "password":
                    //Do nothing
                    break;
                //picture
                case "picture_field":
                    if (file_exists(ADMIN_UPLOAD_PATH . $arr['_____value']) && ($arr['_____value'] != '')) {

                        //$picture = "<a target='_blank' href='" . UPLOAD_URL . $arr['_____value'] . "' class='imgThumb' style='background:url(" . UPLOAD_URL . $arr['_____value'] . ")'></a><p><a target='_blank' href='" . BASE_URL . 'files/' . $arr['_____value'] . "'>" . suUnMakeUploadPath($arr['_____value']) . "</a></p>";
                        $picture = "<p><span class='imgThumb' style='background:url(" . UPLOAD_URL . $arr['_____value'] . ")'></span></p>";
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
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "</td>";

                    break;


                //Checkbox Switch
                case "checkbox_switch":

                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "</td>";
                    break;
                //Radio
                case "radio_button":
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "</td>";
                    break;



                //Radio SLider
                case "radio_button_slider":
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "</td>";
                    break;



                //Radio from DB Slider
                case "radio_button_from_db_slider":

                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "</td>";
                    break;
                //Radio Button to Dropdown from DB
                case "radio_to_dropdown_from_db":

                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "</td>";
                    break;


                //Checkbox from DB
                case "checkbox_from_db":

                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "</td>";

                    break;


                //Checkbox from DB Switch
                case "checkbox_from_db_switch":
                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . suUnstrip($arr['_____value']) . "</td>";

                    break;

                //Currency
                case "currency":

                    $tableData = "<td id='preview-td-1-" . suUnstrip($arr['Slug']) . "' width='30%'><strong>" . suUnstrip($arr['Name']) . ":</strong></td><td id='preview-td-2-" . suUnstrip($arr['Slug']) . "'>" . $getSettings['site_currency'] . ' ' . suUnstrip($arr['_____value']) . "</td>";
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
