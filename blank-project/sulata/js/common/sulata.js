
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

        if ($('#delchk_' + id)) {
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
function doQuickPick(sourceVal, targetEle, errorMsg) {
    eleType = document.getElementById(targetEle).type;
    if (eleType == 'textarea' || eleType == 'text') {
        doPlaceAtCursor(targetEle, sourceVal);
    } else {
        alert(errorMsg);
    }
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
     var newHeight =obj.contentWindow.document.body.scrollHeight;
     newHeight=newHeight+20;
    obj.style.height = newHeight + 'px';
  }