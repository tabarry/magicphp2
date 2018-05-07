<?php
//Set an array to store and later list fields in tables to be used in source dropdown.
//Source is the location from which data in dropdown, checbox or radio needs to be populated.
$sourceArray = array('' => 'Source..');
//Build SQL to get data from 'structure' table to build source dropdown.
$sqlFields = "SELECT slug, structure FROM " . STRUCTURE_TABLE_NAME . " WHERE live='Yes' ORDER BY slug";
$resultFields = suQuery($sqlFields);
$resultFields = $resultFields['result'];
for ($k = 0; $k <= sizeof($resultFields) - 1; $k++) {
    $slug = $resultFields[$k]['slug'];
    $structureField = json_decode($resultFields[$k]['structure'], 1);
    for ($j = 0; $j <= sizeof($structureField) - 1; $j++) {
        //Build array to show table name and field name.
        $sourceArray[$slug . '.' . suUnstrip($structureField[$j]['Name'])] = $slug . '.' . suUnstrip($structureField[$j]['Name']);
    }
}
asort($sourceArray);
//===
//Build array to generate the 'Template' dropdown.
$templateArray = array(
    '' => 'Template..', 'name' => 'Name',
    'email' => 'Email',
    'status' => 'Status',
    'phone' => 'Phone',
    'password' => 'Password',
    'date' => 'Date');
//Build array to generate the 'Type' dropdown.
$typeArray = array(
    'textbox' => 'Textbox',
    'hidden' => 'Hidden',
    'email' => 'Email',
    'password' => 'Password',
    'phone' => 'Phone',
    'date' => 'Date',
    'textarea' => 'Textarea',
    'html_area' => 'HTML Area',
    'integer' => 'Integer',
    'decimal' => 'Decimal',
    'currency' => 'Currency',
    'dropdown' => 'Dropdown',
    'dropdown_from_db' => 'Dropdown from DB',
    'autocomplete' => 'Autocomplete',
    'searchable_dropdown' => 'Searchable Dropdown',
    'searchable_dropdown_from_db' => 'Searchable Dropdown from DB',
    'quick_pick' => 'Quick Pick',
    'quick_pick_from_db' => 'Quick Pick from DB',
    'attachment_field' => 'Attachment Field',
    'picture_field' => 'Picture Field',
    'url' => 'URL',
    'ip_address' => 'IP Address',
    'radio_button' => 'Radio Button',
    'radio_button_from_db' => 'Radio Button from DB',
    'radio_button_slider' => 'Radio Button (Slider)',
    'radio_button_from_db_slider' => 'Radio Button from DB (Slider)',
    'radio_to_dropdown_from_db' => 'Radio Button to Dropdown from DB',
    'checkbox' => 'Checkbox',
    'checkbox_from_db' => 'Checkbox from DB',
    'checkbox_switch' => 'Checkbox (Switch)',
    'checkbox_from_db_switch' => 'Checkbox from DB (Switch)',
    'separator' => 'Separator',
    'json' => 'JSON',
);
//asort($typeArray);//Sort the $typeArray.
//Extend the $typeArray to merge more values in it
$t = array('' => 'Type..');
$typeArray = array_merge($t, $typeArray);

//Build array to generate the 'Required' checkbox.
$requiredArray = array('' => 'Required..', 'yes' => 'Yes', 'no' => 'No');
//Build array to generate the 'Unique' checkbox.
$uniqueArray = array('' => 'Unique..', 'yes' => 'Yes', 'no' => 'No');
//Build array to generate the 'Show' checkbox.
$showArray = array('' => 'Show..', 'yes' => 'Yes', 'no' => 'No');
//Build array to generate the 'Order By' checkbox.
$orderByArray = array('' => 'Order By..', 'yes' => 'Yes', 'no' => 'No');
//Build array to generate the 'Search By' checkbox.
$searchByArray = array('' => 'Search By..', 'yes' => 'Yes', 'no' => 'No');

//Build array to generate the 'Width' dropdown.
//This is Bootstrap division.
$widthArray = array(
    '1' => '1/12',
    '2' => '2/12',
    '3' => '3/12',
    '4' => '4/12',
    '5' => '5/12',
    '6' => '6/12',
    '7' => '7/12',
    '8' => '8/12',
    '9' => '9/12',
    '10' => '10/12',
    '11' => '11/12',
    '12' => '12/12',
);

/* * ***
 * This is just a note for reminder on where to make changes if another control type is added.
 * * ***
 * To add a new type property/field in the magic grid, changes need to be made in the following places..
 * * 1. $controls Variables
 * * 2. doQuickBuilderPicks() JS function
 * * 3. doChangeEleName() JS function
 * * 4. doResetBuilderPicks() JS function
 * * 5. doSetAttr() JS function
 * * 6. doDisableBuilderFields() JS function
 * * 7. remote.php - add and update queries
 * *** */

//Build the grid variables
//Build name
if (isset($name)) {
    $name = suUnstrip($structure[$i]['Name']);
} else {
    $name = '';
}
//Build type
if (isset($type)) {
    $type = suUnstrip($structure[$i]['Type']);
} else {
    $type = '';
}
//Build length
if (isset($length)) {
    $length = suUnstrip($structure[$i]['Length']);
} else {
    $length = '';
}
//Build Image Width
if (isset($imageWidth)) {
    $imageWidth = suUnstrip($structure[$i]['ImageWidth']);
} else {
    $imageWidth = $getSettings['default_image_width'];
}
//Build Image Height
if (isset($imageHeight)) {
    $imageHeight = suUnstrip($structure[$i]['ImageHeight']);
} else {
    $imageHeight = $getSettings['default_image_height'];
}
//Build Width
if (isset($width)) {
    $width = suUnstrip($structure[$i]['Width']);
} else {
    $width = $getSettings['default_column_width'];
}
//Build show
if (isset($show)) {
    $show = suUnstrip($structure[$i]['Show']);
    if ($show == 'yes') {
        $showChecked = array('checked' => 'checked');
    } else {
        $showChecked = array();
    }
} else {
    $show = 'yes';
    $showChecked = array();
}
//Build order by
if (isset($orderBy)) {
    $orderBy = suUnstrip($structure[$i]['OrderBy']);
    if ($orderBy == 'yes') {
        $orderByChecked = array('checked' => 'checked');
    } else {
        $orderByChecked = array();
    }
} else {
    $orderBy = 'yes';
    $orderByChecked = array();
}
//Build search by
if (isset($searchBy)) {
    $searchBy = suUnstrip($structure[$i]['SearchBy']);
    if ($searchBy == 'yes') {
        $searchByChecked = array('checked' => 'checked');
    } else {
        $searchByChecked = array();
    }
} else {
    $searchBy = 'yes';
    $searchByChecked = array();
}
//Build extra sql
if (isset($extraSQL)) {
    $extraSQL = html_entity_decode(suUnstrip($structure[$i]['ExtraSQL']));
    $extraSQL = suUnstrip($extraSQL);
} else {
    $extraSQL = '';
}
//Build source
if (isset($source)) {
    $source = suUnstrip($structure[$i]['Source']);
} else {
    $source = '';
}
//Build unique
if (isset($unique)) {
    $unique = $structure[$i]['Unique'];
    if ($unique == 'yes') {
        $uniqueChecked = array('checked' => 'checked');
    } else {
        $uniqueChecked = array();
    }
} else {
    $unique = 'yes';
    $uniqueChecked = array();
}
//Build compositeunique
if (isset($compositeUnique)) {
    $compositeUnique = suUnstrip($structure[$i]['CompositeUnique']);
    if ($compositeUnique == 'yes') {
        $compositeUniqueChecked = array('checked' => 'checked');
    } else {
        $compositeUniqueChecked = array();
    }
} else {
    $compositeUnique = 'yes';
    $compositeUniqueChecked = array();
}
//Build default
if (isset($default)) {
    $default = html_entity_decode(suUnstrip($structure[$i]['Default']));
} else {
    $default = '';
}
//Build required for Submit
if (isset($required)) {
    $required = suUnstrip($structure[$i]['Required']);
    if ($required == 'yes') {
        $requiredChecked = array('checked' => 'checked');
    } else {
        $requiredChecked = array();
    }
} else {
    $required = 'yes';
    $requiredChecked = array();
}
//Build required for Save for Later
if (isset($requiredSaveForLater)) {
    $requiredSaveForLater = suUnstrip($structure[$i]['RequiredSaveForLater']);
    if ($requiredSaveForLater == 'yes') {
        $requiredSaveForLaterChecked = array('checked' => 'checked');
    } else {
        $requiredSaveForLaterChecked = array();
    }
} else {
    $requiredSaveForLater = 'yes';
    $requiredSaveForLaterChecked = array();
}
//Build onclick
if (isset($onclick)) {
    $onclick = html_entity_decode(suUnstrip($structure[$i]['OnClick']));
} else {
    $onclick = '';
}
//Build onchange
if (isset($onchange)) {
    $onchange = html_entity_decode(suUnstrip($structure[$i]['OnChange']));
} else {
    $onchange = '';
}
//Build onblur
if (isset($onblur)) {
    $onblur = html_entity_decode(suUnstrip($structure[$i]['OnBlur']));
} else {
    $onblur = '';
}
//Build onkeypress
if (isset($onkeypress)) {
    $onkeypress = html_entity_decode(suUnstrip($structure[$i]['OnKeyPress']));
} else {
    $onkeypress = '';
}
//Build onkeyup
if (isset($onkeyup)) {
    $onkeyup = html_entity_decode(suUnstrip($structure[$i]['OnKeyUp']));
} else {
    $onkeyup = '';
}


//Build readonly on add
if (isset($readOnlyAdd)) {
    $readOnlyAdd = suUnstrip($structure[$i]['ReadOnlyAdd']);
    if ($readOnlyAdd == 'yes') {
        $readOnlyAddChecked = array('checked' => 'checked');
    } else {
        $readOnlyAddChecked = array();
    }
} else {
    $readOnlyAdd = 'yes';
    $readOnlyAddChecked = array();
}
//Build readonly on update
if (isset($readOnlyUpdate)) {
    $readOnlyUpdate = suUnstrip($structure[$i]['ReadOnlyUpdate']);
    if ($readOnlyUpdate == 'yes') {
        $readOnlyUpdateChecked = array('checked' => 'checked');
    } else {
        $readOnlyUpdateChecked = array();
    }
} else {
    $readOnlyUpdate = 'yes';
    $readOnlyUpdateChecked = array();
}
//Build hide on update
if (isset($hideOnUpdate)) {
    $hideOnUpdate = suUnstrip($structure[$i]['HideOnUpdate']);
    if ($hideOnUpdate == 'yes') {
        $hideOnUpdateChecked = array('checked' => 'checked');
    } else {
        $hideOnUpdateChecked = array();
    }
} else {
    $hideOnUpdate = 'yes';
    $hideOnUpdateChecked = array();
}
//Build hide on add
if (isset($hideOnAdd)) {
    $hideOnAdd = suUnstrip($structure[$i]['HideOnAdd']);
    if ($hideOnAdd == 'yes') {
        $hideOnAddChecked = array('checked' => 'checked');
    } else {
        $hideOnAddChecked = array();
    }
} else {
    $hideOnAdd = 'yes';
    $hideOnAddChecked = array();
}
//Build css class
if (isset($cssClass)) {
    $cssClass = suUnstrip($structure[$i]['CssClass']);
} else {
    $cssClass = DEFAULT_CSS_CLASS;
}
//Columns distribution
$oneColWidth = '200';
$totalCols = 22 - 2; //-2 to subtract 1st and last
$tableWidth = ($oneColWidth * $totalCols) + 10;
$colWidth = round(85 / $totalCols);

//Build controls
$controls = array(
//Template
    array(
        'width' => '70',
        'type' => 'dropdown',
        'name' => '_template',
        'title' => 'Template',
        'options' => $templateArray,
        'js' => $js = "class=\"form-control\"" . "onchange=\"doQuickBuilderPicks(this,this.value," . $getSettings['default_column_width'] . "," . $getSettings['default_image_width'] . "," . $getSettings['default_image_height'] . ");\"" . " title='Template'",
        'default' => ''
    ),
    //Type
    array(
        'width' => '70',
        'type' => 'dropdown',
        'name' => '_type',
        'title' => 'Type',
        'options' => $typeArray,
        'js' => $js = "class=\"form-control\"" . "onchange=\"return doSetAttr(this," . $getSettings['default_column_width'] . "," . $getSettings['default_image_width'] . "," . $getSettings['default_image_height'] . ")\"" . " title='Type'",
        'default' => $type
    ),
    //Name
    array(
        'width' => '70',
        'type' => 'text',
        'name' => '_name',
        'title' => 'Name',
        'options' => '',
        'js' => '',
        'default' => $name,
        'required' => array('required' => 'required'),
    ),
    //Length
    array(
        'width' => '70',
        'type' => 'text',
        'name' => '_length',
        'title' => 'Length/Value',
        'options' => '',
        'js' => '',
        'default' => $length,
        'required' => array(),
    ),
    //Image Width
    array(
        'width' => '70',
        'type' => 'text',
        'name' => '_imagewidth',
        'title' => 'Image Width',
        'options' => '',
        'js' => '',
        'default' => $imageWidth,
        'required' => array(),
    ),
    //Image Height
    array(
        'width' => '70',
        'type' => 'text',
        'name' => '_imageheight',
        'title' => 'Image Height',
        'options' => '',
        'js' => '',
        'default' => $imageHeight,
        'required' => array(),
    ),
    //Width
    array(
        'width' => '50',
        'type' => 'dropdown',
        'name' => '_width',
        'title' => 'Width',
        'options' => $widthArray,
        'js' => $js = "class=\"form-control\"" . " title='Width'",
        'default' => $width
    ),
    //CSS
    array(
        'width' => '90',
        'type' => 'text',
        'name' => '_cssclass',
        'title' => 'CSS Class',
        'options' => '',
        'js' => '',
        'default' => $cssClass,
        'required' => array(),
    ),
    //Show
    array(
        'width' => '50',
        'type' => 'checkbox',
        'name' => '_show',
        'title' => 'Show',
        'options' => '',
        'js' => '',
        'default' => 'yes',
        'checked' => $showChecked
    ),
    //Order By
    array(
        'width' => '50',
        'type' => 'checkbox',
        'name' => '_orderby',
        'title' => 'Order By',
        'options' => '',
        'js' => '',
        'default' => 'yes',
        'checked' => $orderByChecked
    ),
    //Search By
    array(
        'width' => '50',
        'type' => 'checkbox',
        'name' => '_searchby',
        'title' => 'Search By',
        'options' => '',
        'js' => '',
        'default' => 'yes',
        'checked' => $searchByChecked
    ),
    //Source
    array(
        'width' => '70',
        'type' => 'dropdown',
        'name' => '_source',
        'title' => 'Source',
        'options' => $sourceArray,
        'js' => $js = "class=\"form-control\"" . " title='Source'",
        'default' => $source
    ),
    //ExtraSQL
    array(
        'width' => '90',
        'type' => 'text',
        'name' => '_extrasql',
        'title' => 'ExtraSQL',
        'options' => '',
        'js' => '',
        'default' => $extraSQL,
        'required' => array(),
    ),
    //Default
    array(
        'width' => '70',
        'type' => 'text',
        'name' => '_default',
        'title' => 'Default',
        'options' => '',
        'js' => '',
        'default' => $default,
        'required' => array(),
    ),
    //Required on Submit
    array(
        'width' => '50',
        'type' => 'checkbox',
        'name' => '_required',
        'title' => 'Required on `Submit`',
        'options' => '',
        'js' => '',
        'default' => 'yes',
        'checked' => $requiredChecked
    ),
    //Required on Save for Later
    array(
        'width' => '50',
        'type' => 'checkbox',
        'name' => '_requiredsaveforlater',
        'title' => 'Required on `Save for Later`',
        'options' => '',
        'js' => '',
        'default' => 'yes',
        'checked' => $requiredSaveForLaterChecked
    ),
    //Unique
    array(
        'width' => '50',
        'type' => 'checkbox',
        'name' => '_unique',
        'title' => 'Unique',
        'options' => '',
        'js' => '',
        'default' => 'yes',
        'checked' => $uniqueChecked
    ),
    //Composite Unique
    array(
        'width' => '50',
        'type' => 'checkbox',
        'name' => '_compositeunique',
        'title' => 'Composite Unique',
        'options' => '',
        'js' => '',
        'default' => 'yes',
        'checked' => $compositeUniqueChecked
    ),
    //onChange
    array(
        'width' => '70',
        'type' => 'text',
        'name' => '_onchange',
        'title' => 'onchange',
        'options' => '',
        'js' => '',
        'default' => $onchange,
        'required' => array(),
    ),
    //onClick
    array(
        'width' => '70',
        'type' => 'text',
        'name' => '_onclick',
        'title' => 'onclick',
        'options' => '',
        'js' => '',
        'default' => $onclick,
        'required' => array(),
    ),
    //onKeyUp
    array(
        'width' => '70',
        'type' => 'text',
        'name' => '_onkeyup',
        'title' => 'onkeyup',
        'options' => '',
        'js' => '',
        'default' => $onkeyup,
        'required' => array(),
    ),
    //onKeyPress
    array(
        'width' => '70',
        'type' => 'text',
        'name' => '_onkeypress',
        'title' => 'onkeypress',
        'options' => '',
        'js' => '',
        'default' => $onkeypress,
        'required' => array(),
    ),
    //onBlur
    array(
        'width' => '70',
        'type' => 'text',
        'name' => '_onblur',
        'title' => 'onblur',
        'options' => '',
        'js' => '',
        'default' => $onblur,
        'required' => array(),
    ),
    //Readyonly on add
    array(
        'width' => '50',
        'type' => 'checkbox',
        'name' => '_readonlyadd',
        'title' => 'Readonly on Add',
        'options' => '',
        'js' => '',
        'default' => 'yes',
        'checked' => $readOnlyAddChecked
    ),
    //Readyonly on update
    array(
        'width' => '50',
        'type' => 'checkbox',
        'name' => '_readonlyupdate',
        'title' => 'Readonly on Update',
        'options' => '',
        'js' => '',
        'default' => 'yes',
        'checked' => $readOnlyUpdateChecked
    ),
    //Hide on add
    array(
        'width' => '50',
        'type' => 'checkbox',
        'name' => '_hideonadd',
        'title' => 'Hide on Add',
        'options' => '',
        'js' => '',
        'default' => 'yes',
        'checked' => $hideOnAddChecked
    ),
    //Hide on update
    array(
        'width' => '50',
        'type' => 'checkbox',
        'name' => '_hideonupdate',
        'title' => 'Hide on Update',
        'options' => '',
        'js' => '',
        'default' => 'yes',
        'checked' => $hideOnUpdateChecked
    ),
);
?>

<div class="table-responsive1">
    <table style="width:<?php echo $tableWidth; ?>px;">
        <tr>
            <!-- 1/Move -->
            <td style="width:50px; cursor:move; ">:::::</td>
            <?php 
            $defaultHint='';
            for ($c = 0; $c <= sizeof($controls) - 1; $c++) { 
                
            if($controls[$c]['name']=='_name'){
                $defaultHint= $controls[$c]['default'];
            }   
                ?>
                                                                                                                                                                                                            <!-- <?php echo ($c + 2); ?>/<?php echo $controls[$c]['name']; ?> -->
                <td style="width:<?php echo $controls[$c]['width']; ?>px">
                    <?php
                    if ($controls[$c]['type'] == 'dropdown') {//Dropdown
                        echo suDropdown($controls[$c]['name'], $controls[$c]['options'], $controls[$c]['default'], $controls[$c]['js']);
                    } elseif ($controls[$c]['type'] == 'text') {//Textbox
                        $arg = array('type' => 'text', 'name' => $controls[$c]['name'], 'id' => $controls[$c]['name'], 'autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => $controls[$c]['title'], 'title' => $controls[$c]['title'], 'value' => $controls[$c]['default']);
                        if ($controls[$c]['name'] == '_name') {
                            $arg = array_merge($arg, array('onfocus' => "doShowHint(this.id,this.value)"));
                            $arg = array_merge($arg, array('onkeyup' => "doShowHint(this.id,this.value)"));
                            $arg = array_merge($arg, array('onchange' => "doShowHint(this.id,this.value)"));
                        }
                        $arg = array_merge($arg, $controls[$c]['required']);
                        echo suInput('input', $arg);
                        if ($controls[$c]['title'] == 'onchange' || $controls[$c]['title'] == 'onclick' || $controls[$c]['title'] == 'onkeyup' || $controls[$c]['title'] == 'onkeypress' || $controls[$c]['title'] == 'onblur') {
                            echo "<small><a href='javascript:;' onclick=\"$(this).closest('td').find('input[type=text]').val('$(this).val(doUcWords($(this).val()))');\">Title Case</a>. <a href='javascript:;' onclick=\"$(this).closest('td').find('input[type=text]').val('$(this).val(doSlugify($(this).val(),\'_\'))');\">Slugify</a></small>. ";
                        }
                    } elseif ($controls[$c]['type'] == 'checkbox') {//Checkbox
                        $arg = array('type' => 'checkbox', 'name' => $controls[$c]['name'], 'id' => $controls[$c]['name'], 'class' => 'form-control', 'value' => $controls[$c]['default']);
                        $arg = array_merge($arg, $controls[$c]['checked']);
                        echo "<label><center>" . suInput('input', $arg) . $controls[$c]['title'] . "</center></label>";
                    }
                    ?>

                    <div><input type="text" name="_hint<?php echo $controls[$c]['name']; ?>" id="_hint<?php echo $controls[$c]['name']; ?>" class="magic-hint" readonly="readonly" placeholder="" value="<?php echo $defaultHint;?>"/></div>

                </td>
            <?php } ?>



            <!-- 26/Delete icon -->
            <td style="width:50px; text-align: center;"><a href="javascript:;" onclick="return doRemove(this, '<?php echo CONFIRM_DELETE; ?>');"><i class="fa fa-trash"></i></a></td>

        </tr>
    </table>

</div>
<script>
    //Disable the magic rows
    function doDisableBuilderFields(arg, defaultWidth, defaultImageWidth, defaultImageHeight) {
        id = arg.id;
        id = id.split('_');
        id = id[1];
        //Set width
        $('#width_' + id).val(defaultWidth);
        $('#imagewidth_' + id).val(defaultImageWidth);
        $('#imageheight_' + id).val(defaultImageHeight);
        ////Disable all fields
<?php
for ($e = 2; $e <= sizeof($controls) - 1; $e++) {
    $controls[$e]['name'] = substr($controls[$e]['name'], 1, strlen($controls[$e]['name']));
    $controls[$e]['name'] = $controls[$e]['name'] . '_';

    echo "\$('#" . $controls[$e]['name'] . "' + id).prop('disabled', true);\n";
}
?>

    }
    //Function to make hint id and name
    function doMakeHint(id) {
        var mode = '<?php echo $mode; ?>';
        //Do not work on update;
        if (mode == 'update') {
            return;
        }
        if (document.getElementById('suForm').elements['_hint_template']) {
            document.getElementById('suForm').elements['_hint_template'].setAttribute('id', 'hint_template_' + id);
            document.getElementById('suForm').elements['hint_template_' + id].setAttribute('name', 'hint_template_' + id);
        }
        if (document.getElementById('suForm').elements['_hint_type']) {
            document.getElementById('suForm').elements['_hint_type'].setAttribute('id', 'hint_type_' + id);
            document.getElementById('suForm').elements['hint_type_' + id].setAttribute('name', 'hint_type_' + id);
        }

<?php
for ($e = 2; $e < sizeof($controls); $e++) {
    $ele = $controls[$e]['name'];
    $newName = 'hint_' . $ele;
    $ele = substr($controls[$e]['name'], 0, -1);
    echo "\t\tif (document.getElementById('suForm').elements['_hint_" . $ele . "']) {\n"
    . "document.getElementById('suForm').elements['_hint_" . $ele . "'].setAttribute('id', 'hint_" . $ele . "_' + id);\n"
    . "document.getElementById('suForm').elements['hint_" . $ele . "_' + id].setAttribute('name', 'hint_" . $ele . "_' + id);\n"
    . "}\n";
}
?>

    }
    //Function to show hint 
    function doShowHint(id, val) {
        var mode = '<?php echo $mode; ?>';
        //Do not work on update;
        if (mode == 'update') {
            return;
        }
        id = id.split('_');
        id = id[1];
        
        //get type of this field
        type = $('#type_' + id).val();
        if(type=='separator'){
            //Fill separator value
            $('#length_' + id).val(val)
        }
        if (document.getElementById('suForm').elements['hint_template_' + id]) {
            document.getElementById('suForm').elements['hint_template_' + id].value = val;
        }
        if (document.getElementById('suForm').elements['hint_type_' + id]) {
            document.getElementById('suForm').elements['hint_type_' + id].value = val;
        }


<?php
for ($e = 2; $e < sizeof($controls); $e++) {
    $ele = $controls[$e]['name'];
    $ele = substr($controls[$e]['name'], 0, -1);
    echo "
        if (document.getElementById('suForm').elements['hint_" . $ele . "_' + id]) {
            document.getElementById('suForm').elements['hint_" . $ele . "_' + id].value = val;
        }";
}
?>

    }
</script>
