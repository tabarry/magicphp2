
//Keep session live
function suStayAlive(url) {
    $.post(url);
}

//Reset form
function suReset(frmName) {


    var elements = document.getElementById(frmName).elements;



    for (i = 0; i < elements.length; i++) {

        field_type = elements[i].type.toLowerCase();

        switch (field_type) {

            case "text":
            case "password":
            case "textarea":
            case "hidden":

                elements[i].value = "";
                break;

            case "radio":
            case "checkbox":
                if (elements[i].checked) {
                    elements[i].checked = false;
                }
                break;

            case "select-one":
            case "select-multi":
                elements[i].selectedIndex = 0;
                break;

            default:
                break;
        }
    }
}
//Redirect
function suRedirect(url) {
    parent.window.location.href = url;
}

////To reload a dropdown
//function suReload(ele,url,sql){
//    $('#'+ele).load(url+'?q='+sql);
//}

//Disable submit button
function suToggleButton(arg) {
    if (arg == 1) {
        if (parent.$('#suForm')) {
            parent.$("#suForm").submit(function (event) {
                if (parent.$('#Submit')) {
                    parent.$("#Submit").html("<i class='fa fa-spinner'></i>");
                    parent.$("#Submit").css("cursor", "default");
                    parent.$("#Submit").attr("disabled", true);
                }
                if (parent.$('#save_for_later')) {
                    parent.$("#save_for_later").html("<i class='fa fa-spinner'></i>");
                    parent.$("#save_for_later").css("cursor", "default");
                    parent.$("#save_for_later").attr("disabled", true);
                }
            });
        }
    } else {
        if (parent.$('#suForm')) {
            if (parent.$('#Submit')) {
                parent.$("#Submit").html("<i class='fa fa-check'></i>");
                parent.$("#Submit").css("cursor", "Pointer");
                parent.$("#Submit").attr("disabled", false);
            }
            if (parent.$('#save_for_later')) {
                parent.$("#save_for_later").html("<i class='fa fa-save'></i>");
                parent.$("#save_for_later").css("cursor", "Pointer");
                parent.$("#save_for_later").attr("disabled", false);
            }
        }
    }
}
//Reload dropdown
function suReload(ele, url, tbl, f1, f2) {
    url = url + 'reload.php';
    $('#' + ele).load(url + '?tbl=' + tbl + '&f1=' + f1 + '&f2=' + f2);
}
//Reload dropdown
function suReload2(ele, url, tbl, f1, f2, tblb, f1b, f2b, id) {
    url = url + 'reload.php';
    $('#' + ele).load(url + '?type=chk&tbl=' + tbl + '&f1=' + f1 + '&f2=' + f2 + '&tblb=' + tblb + '&f1b=' + f1b + '&f2b=' + f2b + '&id=' + id);
}
//Search dropdown
//Sample code
//<input type="text" id="realtxt" onKeyUp="suSearchCombo(this.id,'mediafile__Category')">
function suSearchCombo(searchBox, searchCombo) {
    var input = document.getElementById(searchBox).value.toLowerCase();
    var output = document.getElementById(searchCombo).options;
    for (var i = 0; i < output.length; i++) {
        if (output[i].text.indexOf(input) >= 0) {
            output[i].selected = true;
        }
        if (document.getElementById(searchBox).value == '') {
            output[0].selected = true;
        }
    }
}
//Delete row and confirm
function delById(id, warning) {
    c = confirm(warning);
    if (c == false) {
        return false;

    } else {
        if ($('#del_icon_' + id)) {
            $('#del_icon_' + id).hide();
        }
        if ($('#edit_icon_' + id)) {
            $('#edit_icon_' + id).hide();
        }
        if ($('#preview_icon_' + id)) {
            $('#preview_icon_' + id).hide();
        }
        if ($('#duplicate_icon_' + id)) {
            $('#duplicate_icon_' + id).hide();
        }
        if ($('#restore_icon_' + id)) {
            $('#restore_icon_' + id).show();
        }

        if ($('#row_' + id + ' td')) {
            $('#row_' + id + ' td').addClass('strike-through');
            $('#row_' + id + ' td').addClass('deleted-bg');
        }

        if ($('#delchk_' + id).checked == false) {
            document.getElementById('delchk_' + id).checked = true;
        }
        return true;
    }
}


//Delete row and confirm on checkbox
function delByIdCheckbox(id, delUrl, restoreUrl) {

    //Build iframes so that simultaneous deletes can be made
    delFrame = "<iframe style='display:none;' name='delFrame_" + id + "' id='delFrame_" + id + "' width='0' height='0' src='" + delUrl + "'></iframe>";
    restoreFrame = "<iframe style='display:none;' name='restoreFrame_" + id + "' id='restoreFrame_" + id + "' width='0' height='0' src='" + restoreUrl + "'></iframe>";

    chkState = document.getElementById('delchk_' + id).checked;
    if (chkState == true) {
        if ($('#del_icon_' + id)) {
            $('#del_icon_' + id).hide();
        }
        if ($('#edit_icon_' + id)) {
            $('#edit_icon_' + id).hide();
        }
        if ($('#preview_icon_' + id)) {
            $('#preview_icon_' + id).hide();
        }
        if ($('#duplicate_icon_' + id)) {
            $('#duplicate_icon_' + id).hide();
        }
        if ($('#restore_icon_' + id)) {
            $('#restore_icon_' + id).show();
        }

        if ($('#row_' + id + ' td')) {
            $('#row_' + id + ' td').addClass('strike-through');
            $('#row_' + id + ' td').addClass('deleted-bg');
        }

        if ($('#row_' + id + ' td spanx')) {
            $('#row_' + id + ' td spanx').removeClass();
        }
        $("#last-dom").after(delFrame);
    } else {
        $("#last-dom").after(restoreFrame);
    }
}
//Restore row and confirm
function restoreById(id) {
    if ($('#del_icon_' + id)) {
        $('#del_icon_' + id).show();
    }
    if ($('#edit_icon_' + id)) {
        $('#edit_icon_' + id).show();
    }
    if ($('#preview_icon_' + id)) {
        $('#preview_icon_' + id).show();
    }
    if ($('#duplicate_icon_' + id)) {
        $('#duplicate_icon_' + id).show();
    }
    if ($('#restore_icon_' + id)) {
        $('#restore_icon_' + id).hide();
    }
    if ($('#delchk_' + id)) {
        document.getElementById('delchk_' + id).checked = false;
    }
    if ($('#row_' + id + ' td')) {
        $('#row_' + id + ' td').removeClass('strike-through');
        $('#row_' + id + ' td').removeClass('deleted-bg');
    }

    if ($('#row_' + id + ' td spanx')) {
        $('#row_' + id + ' td spanx').addClass('dashed');
    }

}
//Checkbox Area
function loadCheckbox(id, txt, fld) {
    //Add new value
    oldVal = $('#checkboxArea').html();
    newVal = "<table class=\"checkTable\" id=\"chkTbl" + id + "\"><tr><td class=\"checkTd\">" + txt + "</td><td class=\"checkTdCancel\" onclick=\"removeCheckbox('" + id + "')\"><a href=\"javascript:;\" onclick=\"removeCheckbox('" + id + "')\">x</a></td></tr><input type=\"hidden\" value=\"" + id + "\" name=\"" + fld + "[]\"></table>";
    $('#checkboxArea').html(oldVal + newVal);
    //Hide old value
    $('#chk' + id).hide();
}

function removeCheckbox(id) {
    $('#chk' + id).show();
    $('#chkTbl' + id).remove();
}

function toggleCheckboxClass(state, id) {
    if (state == 'over') {
        $('#fa' + id).removeClass('fa fa-square-o');
        $('#fa' + id).addClass('fa fa-check-square-o');
    } else {
        $('#fa' + id).removeClass('fa fa-check-square-o');
        $('#fa' + id).addClass('fa fa-square-o');

    }
}
//Password stength validator
function doStrongPassword(passwordEle, outputEle) {
    var tip = "At least 8 characters, 1 uppercase and 1 number.";
    var outputHidden = $('#' + outputEle + '_hidden');
    //TextBox left blank.
    if ($('#' + passwordEle).val().length == 0) {
        $('#' + outputEle).html('');
        return;
    }

    //Regular Expressions.
    var regex = new Array();
    regex.push("[A-Z]"); //Uppercase Alphabet.
    regex.push("[a-z]"); //Lowercase Alphabet.
    regex.push("[0-9]"); //Digit.
    regex.push("[$@$!%*#?&]"); //Special Character.

    var passed = 0;

    //Validate for each Regular Expression.
    for (var i = 0; i < regex.length; i++) {
        if (new RegExp(regex[i]).test($('#' + passwordEle).val())) {
            passed++;
        }
    }


    //Validate for length of Password.
    if (passed > 2 && $('#' + passwordEle).val().length > 8) {
        passed++;
    }

    //Display status.
    var color = "";
    var strength = "";
    switch (passed) {
        case 0:
        case 1:
            strength = tip;
            color = "red";
            break;
        case 2:
            strength = "Good";
            color = "darkorange";
            break;
        case 3:
        case 4:
            strength = "Strong";
            color = "green";
            break;
        case 5:
            strength = "Very Strong";
            color = "darkgreen";
            break;
    }
    $('#' + outputEle).html(strength);
    $('#' + outputEle).css("color", color);
    outputHidden.val(passed);
}
//Slugify text
function doSlugify(text, spaceCharacter)
{
    return text.toString().toLowerCase()
            .replace(/\s+/g, spaceCharacter)           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, spaceCharacter)         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start of text
            .replace(/-+$/, '');            // Trim - from end of text
}
//Sleep, delay, wait
function sleep(milliseconds) {
    var start = new Date().getTime();
    for (var i = 0; i < 1e7; i++) {
        if ((new Date().getTime() - start) > milliseconds) {
            break;
        }
    }
}

//Quick pick
function doQuickPick(sourceVal, targetEle) {
    eleType = document.getElementById(targetEle).type;
    if (eleType == 'textarea' || eleType == 'text') {
        doPlaceAtCursor(targetEle, sourceVal);
    }
}
//Quick pick
function doQuickPickClosest(sourceVal, arg) {
    ele = $("textarea", $(arg).parent().parent()).attr('name');
    var targetEle = $('textarea[name=' + ele + ']');
    var start = targetEle.prop("selectionStart")
    var end = targetEle.prop("selectionEnd")
    var text = targetEle.val()
    var before = text.substring(0, start)
    var after = text.substring(end, text.length)
    targetEle.val(before + sourceVal + after)
    targetEle[0].selectionStart = targetEle[0].selectionEnd = start + sourceVal.length
    targetEle.focus()
    return false

}
//Place text at cursor point
function doPlaceAtCursor(targetEle, newText) {
    var targetEle = $('#' + targetEle);
    var start = targetEle.prop("selectionStart")
    var end = targetEle.prop("selectionEnd")
    var text = targetEle.val()
    var before = text.substring(0, start)
    var after = text.substring(end, text.length)
    targetEle.val(before + newText + after)
    targetEle[0].selectionStart = targetEle[0].selectionEnd = start + newText.length
    targetEle.focus()
    return false
}
//Change image source of image placeholder
function readURL(input, targetEle) {
    var reader = new FileReader();
    reader.onload = function (e) {
        var imgSrc = e.target.result;
        if ($('#' + targetEle)) {
            $('#' + targetEle).css('background-image', 'url(' + imgSrc + ')');
        }
    }
    reader.readAsDataURL(input.files[0]);
}
//Convert to title case
function doUcWords(str) {

    return (str + '')
            .replace(/^(.)|\s+(.)/g, function ($1) {
                return $1.toUpperCase()
            })
}

//Submit form on CTRL + S
function suSave(submitButtonId, saveOnCtrlS) {
    if ($('#' + submitButtonId) && saveOnCtrlS == true) {
        $(window).keypress(function (event) {
            if (!(event.which == 115 && event.ctrlKey) && !(event.which == 19))
                return true;
            $('#' + submitButtonId).click();
            event.preventDefault();
            return false;
        });
    }
}
//Resize Iframe to its content
function doResizeIframe(obj) {
    var newHeight = obj.contentWindow.document.body.scrollHeight;
    newHeight = newHeight + 20;
    obj.style.height = newHeight + 'px';
}
//Delete/Remove the nearest element
function doRemoveTr(eleId, warning, hiddenDecrementField) {
    if (warning != '') {
        c = confirm(warning);
        if (c == true) {
            $('#' + eleId).remove();
            if (hiddenDecrementField != '') {
                //Decrement the hidden counter field
                s = '_____size_' + hiddenDecrementField;
                v = $('#' + s).val();
                v = parseInt(v) - 1;
                $('#' + s).val(v);
            }
        } else {
            return false;
        }
    } else {
        $('#' + eleId).remove();
    }
    doAddmoreIds('suForm', hiddenDecrementField, '-')
}
//Delete/Remove the nearest element
function doRemoveClosestEle(eleType, arg, warning) {

    if (warning != '') {
        c = confirm(warning);
        if (c == true) {
            $(arg).closest(eleType).remove();
        } else {
            return false;
        }
    } else {
        $(arg).closest(eleType).remove();
    }
}
//Add more row

function doAddMore(sourceForm) {
    //Increase counter
    _____size = parseInt($('#_____size_' + sourceForm).val());
    _____size += 1;
    $('#_____size_' + sourceForm).val(_____size);
    //make variable for the content and replace old id with new valid unique id
    var moreContent = urldecode($('#more-source-' + sourceForm).val());
    moreContent = moreContent.replace(/_____x/g, "_" + _____size);
    //Place content to placeholder
    $('#more-destination-' + sourceForm).append(moreContent);
}
//Fill add more values

function doFillAddMoreValues(jsonValues) {
    //alert(jsonValues);
    var obj = JSON.parse(jsonValues);
    //obj = obj['institute'];
    for (var x in obj) {
        if (obj.hasOwnProperty(x)) {
            // your code
            //alert(obj.hasOwnProperty(x)[0]);
        }
        for (var key in obj) {
            if (obj.hasOwnProperty(key))
                alert(obj[key]);
        }

    }

//    for (i = 0; i <= (document.suForm.elements.length) - 1; i++) {
//        if (document.suForm.elements[i].name == 'institute[]') {
//            document.suForm.elements[i].value = 'Hello';
//        }
//    }

}
function urldecode(url) {
    return decodeURIComponent(url.replace(/\+/g, ' '));
}

//Convert to searchable dropdown
function doChangeToSearchable(arg) {
    $(arg).chosen();
}

//convert to autocomplete
function doChangeToAutocomplete(arg, src) {

    $(arg).autocomplete(
            {source: src, minLength: 2}
    );
}
//convert to autocomplete
function doChangeToDateBox(date_format) {
    //return false;
    $('body').on('focus', ".dateBox", function () {
        $(this).datepicker({
            changeMonth: true,
            changeYear: true
        });
        $(this).datepicker('option', 'yearRange', 'c-100:c+10');
        $(this).datepicker('option', 'dateFormat', date_format);
    });
}
//Date Picker
function doDatePicker(date_format, arg) {
    //alert('here');
    //$(arg).datepicker();
    //$("#date_____1").datepicker();
        //$("#date_____1").datepicker();

//    $(arg).datepicker('destroy').datepicker({changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", yearRange: "1900:+10", showOn: 'focus'}).focus();

//    n = arg.name;
//    $('input[name='+n+']').datepicker();
//    $(arg).datepicker('destroy');
//    $(arg).datepicker({
//        changeMonth: true,
//        changeYear: true
//    });
//    $(arg).datepicker('option', 'yearRange', 'c-100:c+10');
//    $(arg).datepicker('option', 'dateFormat', date_format);
}
//Convert to HTML Area
function doChangeToHTMLArea(arg) {

    CKEDITOR.replace(arg, {
        toolbar: [
            {name: 'clipboard', groups: ['clipboard', 'undo'], items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']},
            {name: 'editing', groups: ['find', 'selection', 'spellchecker'], items: ['Scayt']},
            {name: 'links', items: ['Link', 'Unlink', 'Anchor']},
            {name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar']},
            {name: 'tools', items: ['Maximize']},
            {name: 'document', groups: ['mode', 'document', 'doctools'], items: ['Source']},
            {name: 'others', items: ['-']},
            '/',
            ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
            {name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Strike', '-', 'RemoveFormat']},
            {name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align'], items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote']},
            {name: 'styles', items: ['Styles', 'Format']},
            {name: 'about', items: ['About']}
        ]
    });

}
//Count occurences of needle in haystack
function doCountOccurences(haystack, needle) {
    var occurence = 0;
    for (i = 0; i <= haystack.length; i++) {
        if (haystack[i] == needle) {
            occurence = occurence + 1;

        }

    }
    return occurence;
}
//Allow only integers
function doOnlyIntegers() {
    $(document).on("keypress", ".integer", function (evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        //9 = tab, 13 = enter, 45 = minus, 46 = decimal, 8 = backspace
        if ((charCode < 48 || charCode > 57) && charCode != 9 && charCode != 13 && charCode != 8) {
            //if ((evt.which < 48 || evt.which > 57) && evt.which != 9 && evt.which != 45 && evt.which != 8) {
            evt.preventDefault();
        }
    });



}
//Allow only decimals
function doOnlyDecimals() {
    $(document).on("keypress", ".decimal", function (evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        //9 = tab, 13 = enter, 45 = minus, 46 = decimal, 8 = backspace
        if ((charCode < 48 || charCode > 57) && charCode != 9 && charCode != 13 && charCode != 45 && charCode != 46 && charCode != 8) {
            evt.preventDefault();
        }

    });
}
//Allow only integers
function doOnlyIntegers2(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    //9 = tab, 13 = enter, 45 = minus, 46 = decimal, 8 = backspace
    if ((charCode < 48 || charCode > 57) && charCode != 9 && charCode != 13 && charCode != 8) {
        evt.preventDefault();
    }
}

//Allow only decimals
function doOnlyDecimals2(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    //9 = tab, 13 = enter, 45 = minus, 46 = decimal, 8 = backspace
    if ((charCode < 48 || charCode > 57) && charCode != 9 && charCode != 13 && charCode != 45 && charCode != 46 && charCode != 8) {
        evt.preventDefault();
    }
}