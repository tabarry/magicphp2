/*
 * Javascript library for this site only. Framework Javascript library is in sulata.js file *
 */
//Clone rows in forms
function doCloneRow(src, dest) {
    $("#" + src).clone().insertBefore("#" + dest);
    doChangeEleName();
}
//Delete row in form
function doRemove(arg, msg) {
    c = confirm(msg);
    if (c == true) {
//$(arg).parent().parent().parent().parent().parent().remove();
        $(arg).closest('table').remove();
    }
}
//Generate UID
function doUid() {
    function s4() {
        return Math.floor((1 + Math.random()) * 0x10000)
                .toString(16)
                .substring(1);
    }
    return s4() + s4() + '_' + s4() + '_' + s4() + '_' +
            s4() + '_' + s4() + s4() + s4();
}

//Change element's name and id
var uid = 0;
var hintId = 0;
function doChangeEleName() {
    hintId = hintId + 1;

    //Replace hint
    doMakeHint(hintId);
    for (i = 0; i <= document.forms[0].elements.length - 1; i++) {

        ele = document.forms[0].elements[i].name;

        //Replace template
        if (document.forms[0].elements[i].name == '_template') {
            uid = uid + 1;
            n = 'template_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }

        //Replace name
        if (document.forms[0].elements[i].name == '_name') {
            n = 'name_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace type
        if (document.forms[0].elements[i].name == '_type') {
            n = 'type_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace length
        if (document.forms[0].elements[i].name == '_length') {
            n = 'length_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace image width
        if (document.forms[0].elements[i].name == '_imagewidth') {
            n = 'imagewidth_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace image length
        if (document.forms[0].elements[i].name == '_imageheight') {
            n = 'imageheight_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace width
        if (document.forms[0].elements[i].name == '_width') {
            n = 'width_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace source
        if (document.forms[0].elements[i].name == '_source') {
            n = 'source_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace default
        if (document.forms[0].elements[i].name == '_default') {
            n = 'default_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace required
        if (document.forms[0].elements[i].name == '_required') {
            n = 'required_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace required save for later
        if (document.forms[0].elements[i].name == '_requiredsaveforlater') {
            n = 'requiredsaveforlater_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace unique
        if (document.forms[0].elements[i].name == '_unique') {
            n = 'unique_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace composite unique
        if (document.forms[0].elements[i].name == '_compositeunique') {
            n = 'compositeunique_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace show
        if (document.forms[0].elements[i].name == '_show') {
            n = 'show_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace CSS Class
        if (document.forms[0].elements[i].name == '_cssclass') {
            n = 'cssclass_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace order by
        if (document.forms[0].elements[i].name == '_orderby') {
            n = 'orderby_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace search by
        if (document.forms[0].elements[i].name == '_searchby') {
            n = 'searchby_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace extra sql
        if (document.forms[0].elements[i].name == '_extrasql') {
            n = 'extrasql_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace onchange
        if (document.forms[0].elements[i].name == '_onchange') {
            n = 'onchange_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace onclick
        if (document.forms[0].elements[i].name == '_onclick') {
            n = 'onclick_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace onkeyup
        if (document.forms[0].elements[i].name == '_onkeyup') {
            n = 'onkeyup_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace onkeypress
        if (document.forms[0].elements[i].name == '_onkeypress') {
            n = 'onkeypress_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace onblur
        if (document.forms[0].elements[i].name == '_onblur') {
            n = 'onblur_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace readonlyadd
        if (document.forms[0].elements[i].name == '_readonlyadd') {
            n = 'readonlyadd_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace readonlyupdate
        if (document.forms[0].elements[i].name == '_readonlyupdate') {
            n = 'readonlyupdate_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace hideonupdate
        if (document.forms[0].elements[i].name == '_hideonupdate') {
            n = 'hideonupdate_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }
        //Replace hideonuadd
        if (document.forms[0].elements[i].name == '_hideonadd') {
            n = 'hideonadd_' + uid;
            document.forms[0].elements[i].name = n;
            document.forms[0].elements[i].id = n;
        }

    }


}
//Quick fill the row type
function doQuickBuilderPicks(arg, val, defaultWidth, defaultImageWidth, defaultImageHeight) {
    id = arg.id;
    id = id.split('_');
    id = id[1];
    //Disable all fields
    doDisableBuilderFields(arg, defaultWidth, defaultImageWidth, defaultImageHeight);
//--//
    $('#source_' + id).prop('disabled', true);
    //Set Name
    if (val == 'name') {
        sel = 'textbox';
        $('#name_' + id).val('Name');
        $('#type_' + id).val(sel);
        $('#length_' + id).val('100');
        $('#width_' + id).val(defaultWidth);
        $('#show_' + id).prop('checked', true);
        $('#orderby_' + id).prop('checked', true);
        $('#searchby_' + id).prop('checked', true);
        $('#source_' + id + ' option')[0].selected = true;
        $('#extrasql_' + id).val('');
        $('#default_' + id).val('');
        $('#required_' + id).prop('checked', true);
        $('#requiredsaveforlater_' + id).prop('checked', true);
        $('#unique_' + id).prop('checked', false);
        $('#compositeunique_' + id).prop('checked', false);
        $('#onchange_' + id).val('');
        $('#onclick_' + id).val('');
        $('#onkeyup_' + id).val('');
        $('#onkeypress_' + id).val('');
        $('#onblur_' + id).val('');
        $('#readonlyadd_' + id).prop('checked', false);
        $('#readonlyupdate_' + id).prop('checked', false);
        $('#hideupdate_' + id).prop('checked', false);
        //--//
        $('#name_' + id).prop('disabled', false);
        $('#length_' + id).prop('disabled', false);
        $('#width_' + id).prop('disabled', false);
        $('#cssclass_' + id).prop('disabled', false);
        $('#show_' + id).prop('disabled', false);
        $('#orderby_' + id).prop('disabled', false);
        $('#searchby_' + id).prop('disabled', false);
        $('#extrasql_' + id).prop('disabled', false);
        $('#default_' + id).prop('disabled', false);
        $('#required_' + id).prop('disabled', false);
        $('#requiredsaveforlater_' + id).prop('disabled', false);
        $('#unique_' + id).prop('disabled', false);
        $('#compositeunique_' + id).prop('disabled', false);
        $('#onchange_' + id).prop('disabled', false);
        $('#onclick_' + id).prop('disabled', false);
        $('#onkeyup_' + id).prop('disabled', false);
        $('#onkeypress_' + id).prop('disabled', false);
        $('#onblur_' + id).prop('disabled', false);
        $('#readonlyadd_' + id).prop('disabled', false);
        $('#readonlyupdate_' + id).prop('disabled', false);
        $('#hideonupdate_' + id).prop('disabled', false);
        $('#hideonadd_' + id).prop('disabled', false);
        //Set Email
    } else if (val == 'email') {
        sel = 'email';
        $('#name_' + id).val('Email');
        $('#type_' + id).val(sel);
        $('#length_' + id).val('50');
        $('#width_' + id).val(defaultWidth);
        $('#show_' + id).prop('checked', true);
        $('#searchby_' + id).prop('checked', true);
        $('#orderby_' + id).prop('checked', true);
        $('#source_' + id + ' option')[0].selected = true;
        $('#extrasql_' + id).val('');
        $('#default_' + id).val('');
        $('#required_' + id).prop('checked', true);
        $('#requiredsaveforlater_' + id).prop('checked', true);
        $('#unique_' + id).prop('checked', true);
        $('#compositeunique_' + id).prop('checked', false);
        $('#onchange_' + id).val('');
        $('#onclick_' + id).val('');
        $('#onkeyup_' + id).val('');
        $('#onkeypress_' + id).val('');
        $('#onblur_' + id).val('');
        $('#readonlyadd_' + id).prop('checked', false);
        $('#readonlyupdate_' + id).prop('checked', false);
        $('#hideonupdate_' + id).prop('checked', false);
        $('#hideonadd_' + id).prop('checked', false);
//--//
        $('#name_' + id).prop('disabled', false);
        $('#length_' + id).prop('disabled', false);
        $('#width_' + id).prop('disabled', false);
        $('#cssclass_' + id).prop('disabled', false);
        $('#show_' + id).prop('disabled', false);
        $('#orderby_' + id).prop('disabled', false);
        $('#searchby_' + id).prop('disabled', false);
        $('#extrasql_' + id).prop('disabled', false);
        $('#default_' + id).prop('disabled', false);
        $('#required_' + id).prop('disabled', false);
        $('#requiredsaveforlater_' + id).prop('disabled', false);
        $('#unique_' + id).prop('disabled', false);
        $('#compositeunique_' + id).prop('disabled', false);
        $('#onchange_' + id).prop('disabled', false);
        $('#onclick_' + id).prop('disabled', false);
        $('#onkeyup_' + id).prop('disabled', false);
        $('#onkeypress_' + id).prop('disabled', false);
        $('#onblur_' + id).prop('disabled', false);
        $('#readonlyadd_' + id).prop('disabled', false);
        $('#readonlyupdate_' + id).prop('disabled', false);
        $('#hideonupdate_' + id).prop('disabled', false);
        $('#hideonadd_' + id).prop('disabled', false);
        //Set Status
    } else if (val == 'status') {
        sel = 'dropdown';
        $('#name_' + id).val('Status');
        $('#type_' + id).val(sel);
        $('#length_' + id).val("Active,Inactive");
        $('#width_' + id).val(defaultWidth);
        $('#show_' + id).prop('checked', true);
        $('#orderby_' + id).prop('checked', true);
        $('#searchby_' + id).prop('checked', false);
        $('#source_' + id + ' option')[0].selected = true;
        $('#extrasql_' + id).val('');
        $('#default_' + id).val('Active');
        $('#required_' + id).prop('checked', true);
        $('#requiredsaveforlater_' + id).prop('checked', true);
        $('#unique_' + id).prop('checked', false);
        $('#compositeunique_' + id).prop('checked', false);
        $('#onchange_' + id).val('');
        $('#onclick_' + id).val('');
        $('#onkeyup_' + id).val('');
        $('#onkeypress_' + id).val('');
        $('#onblur_' + id).val('');
        $('#readonlyadd_' + id).prop('checked', false);
        $('#readonlyupdate_' + id).prop('checked', false);
        $('#hideonupdate_' + id).prop('checked', false);
        $('#hideonadd_' + id).prop('checked', false);
        //--//
        $('#name_' + id).prop('disabled', false);
        $('#length_' + id).prop('disabled', false);
        $('#width_' + id).prop('disabled', false);
        $('#cssclass_' + id).prop('disabled', false);
        $('#show_' + id).prop('disabled', false);
        $('#orderby_' + id).prop('disabled', false);
        $('#searchby_' + id).prop('disabled', false);
        $('#extrasql_' + id).prop('disabled', false);
        $('#default_' + id).prop('disabled', false);
        $('#required_' + id).prop('disabled', false);
        $('#requiredsaveforlater_' + id).prop('disabled', false);
        $('#unique_' + id).prop('disabled', false);
        $('#compositeunique_' + id).prop('disabled', false);
        $('#onchange_' + id).prop('disabled', false);
        $('#onclick_' + id).prop('disabled', false);
        $('#onkeyup_' + id).prop('disabled', false);
        $('#onkeypress_' + id).prop('disabled', false);
        $('#onblur_' + id).prop('disabled', false);
        $('#readonlyadd_' + id).prop('disabled', false);
        $('#readonlyupdate_' + id).prop('disabled', false);
        $('#hideonupdate_' + id).prop('disabled', false);
        $('#hideonadd_' + id).prop('disabled', false);
        //Set Phone
    } else if (val == 'phone') {
        sel = 'phone';
        $('#name_' + id).val('Phone');
        $('#type_' + id).val(sel);
        $('#length_' + id).val('15');
        $('#width_' + id).val(defaultWidth);
        $('#show_' + id).prop('checked', true);
        $('#orderby_' + id).prop('checked', true);
        $('#searchby_' + id).prop('checked', true);
        $('#source_' + id + ' option')[0].selected = true;
        $('#extrasql_' + id).val('');
        $('#default_' + id).val('');
        $('#required_' + id).prop('checked', true);
        $('#requiredsaveforlater_' + id).prop('checked', true);
        $('#unique_' + id).prop('checked', false);
        $('#compositeunique_' + id).prop('checked', false);
        $('#onchange_' + id).val('');
        $('#onclick_' + id).val('');
        $('#onkeyup_' + id).val('');
        $('#onkeypress_' + id).val('');
        $('#onblur_' + id).val('');
        $('#readonlyadd_' + id).prop('checked', false);
        $('#readonlyupdate_' + id).prop('checked', false);
        $('#hideonupdate_' + id).prop('checked', false);
        $('#hideonadd_' + id).prop('checked', false);
        //--//
        $('#name_' + id).prop('disabled', false);
        $('#length_' + id).prop('disabled', false);
        $('#width_' + id).prop('disabled', false);
        $('#cssclass_' + id).prop('disabled', false);
        $('#show_' + id).prop('disabled', false);
        $('#orderby_' + id).prop('disabled', false);
        $('#searchby_' + id).prop('disabled', false);
        $('#extrasql_' + id).prop('disabled', false);
        $('#default_' + id).prop('disabled', false);
        $('#required_' + id).prop('disabled', false);
        $('#requiredsaveforlater_' + id).prop('disabled', false);
        $('#unique_' + id).prop('disabled', false);
        $('#compositeunique_' + id).prop('disabled', false);
        $('#onchange_' + id).prop('disabled', false);
        $('#onclick_' + id).prop('disabled', false);
        $('#onkeyup_' + id).prop('disabled', false);
        $('#onkeypress_' + id).prop('disabled', false);
        $('#onblur_' + id).prop('disabled', false);
        $('#readonlyadd_' + id).prop('disabled', false);
        $('#readonlyupdate_' + id).prop('disabled', false);
        $('#hideonupdate_' + id).prop('disabled', false);
        $('#hideonadd_' + id).prop('disabled', false);
        //Set Password
    } else if (val == 'password') {
        sel = 'password';
        $('#name_' + id).val('Password');
        $('#type_' + id).val(sel);
        $('#length_' + id).val('50');
        $('#width_' + id).val(defaultWidth);
        $('#source_' + id + ' option')[0].selected = true;
        $('#extrasql_' + id).val('');
        $('#default_' + id).val('');
        $('#required_' + id).prop('checked', true);
        $('#requiredsaveforlater_' + id).prop('checked', true);
        $('#unique_' + id).prop('checked', false);
        $('#compositeunique_' + id).prop('checked', false);
        $('#onchange_' + id).val('');
        $('#onclick_' + id).val('');
        $('#onkeyup_' + id).val('');
        $('#onkeypress_' + id).val('');
        $('#onblur_' + id).val('');
        $('#readonlyadd_' + id).prop('checked', false);
        $('#readonlyupdate_' + id).prop('checked', false);
        $('#hideonupdate_' + id).prop('checked', false);
        $('#hideonadd_' + id).prop('checked', false);
        //==
        $('#name_' + id).prop('disabled', false);
        $('#length_' + id).prop('disabled', false);
        $('#width_' + id).prop('disabled', false);
        $('#cssclass_' + id).prop('disabled', false);
        $('#show_' + id).prop('disabled', false);
        $('#orderby_' + id).prop('disabled', false);
        $('#searchby_' + id).prop('disabled', false);
        $('#extrasql_' + id).prop('disabled', false);
        $('#default_' + id).prop('disabled', false);
        $('#required_' + id).prop('disabled', false);
        $('#requiredsaveforlater_' + id).prop('disabled', false);
        $('#onchange_' + id).prop('disabled', false);
        $('#onclick_' + id).prop('disabled', false);
        $('#onkeyup_' + id).prop('disabled', false);
        $('#onkeypress_' + id).prop('disabled', false);
        $('#onblur_' + id).prop('disabled', false);
        $('#readonlyadd_' + id).prop('disabled', false);
        $('#readonlyupdate_' + id).prop('disabled', false);
        $('#hideonupdate_' + id).prop('disabled', false);
        $('#hideonadd_' + id).prop('disabled', false);
        //Set Date
    } else if (val == 'date') {
        sel = 'date';
        $('#name_' + id).val('Date');
        $('#type_' + id).val(sel);
        $('#length_' + id).val('10');
        $('#width_' + id).val(defaultWidth);
        $('#show_' + id).prop('checked', true);
        $('#orderby_' + id).prop('checked', true);
        $('#source_' + id + ' option')[0].selected = true;
        $('#extrasql_' + id).val('');
        $('#default_' + id).val('');
        $('#required_' + id).prop('checked', true);
        $('#requiredsaveforlater_' + id).prop('checked', true);
        $('#unique_' + id).prop('checked', false);
        $('#compositeunique_' + id).prop('checked', false);
        $('#onchange_' + id).val('');
        $('#onclick_' + id).val('');
        $('#onkeyup_' + id).val('');
        $('#onkeypress_' + id).val('');
        $('#onblur_' + id).val('');
        $('#readonlyadd_' + id).prop('checked', false);
        $('#readonlyupdate_' + id).prop('checked', false);
        $('#hideonupdate_' + id).prop('checked', false);
        $('#hideonadd_' + id).prop('checked', false);
        //--//
        $('#name_' + id).prop('disabled', false);
        $('#length_' + id).prop('disabled', false);
        $('#width_' + id).prop('disabled', false);
        $('#cssclass_' + id).prop('disabled', false);
        $('#show_' + id).prop('disabled', false);
        $('#orderby_' + id).prop('disabled', false);
        $('#searchby_' + id).prop('disabled', false);
        $('#extrasql_' + id).prop('disabled', false);
        $('#default_' + id).prop('disabled', false);
        $('#required_' + id).prop('disabled', false);
        $('#requiredsaveforlater_' + id).prop('disabled', false);
        $('#unique_' + id).prop('disabled', false);
        $('#compositeunique_' + id).prop('disabled', false);
        $('#onchange_' + id).prop('disabled', false);
        $('#onclick_' + id).prop('disabled', false);
        $('#onkeyup_' + id).prop('disabled', false);
        $('#onkeypress_' + id).prop('disabled', false);
        $('#onblur_' + id).prop('disabled', false);
        $('#readonlyadd_' + id).prop('disabled', false);
        $('#readonlyupdate_' + id).prop('disabled', false);
        $('#hideonupdate_' + id).prop('disabled', false);
        $('#hideonadd_' + id).prop('disabled', false);
        //Everything else
    } else {
        doResetBuilderPicks(arg, defaultWidth, defaultImageWidth, defaultImageHeight);
    }
}

//Reset the magic rows
function doResetBuilderPicks(arg, defaultWidth, defaultImageWidth, defaultImageHeight) {
    id = arg.id;
    id = id.split('_');
    id = id[1];
    $('#name_' + id).val('');
    $('#type_' + id).val('');
    $('#length_' + id).val('');
    $('#imageheight_' + id).val(defaultImageHeight);
    $('#imagewidth_' + id).val(defaultImageWidth);
    $('#width_' + id).val(defaultWidth);
    $('#show_' + id).prop('checked', false);
    $('#orderby_' + id).prop('checked', false);
    $('#searchby_' + id).prop('checked', false);
    $('#source_' + id + ' option')[0].selected = true;
    $('#extrasql_' + id).val('');
    $('#default_' + id).val('');
    $('#required_' + id).prop('checked', false);
    $('#requiredsaveforlater_' + id).prop('checked', false);
    $('#unique_' + id).prop('checked', false);
    $('#compositeunique_' + id).prop('checked', false);
    $('#onchange_' + id).val('');
    $('#onclick_' + id).val('');
    $('#onkeyup_' + id).val('');
    $('#onkeypress_' + id).val('');
    $('#onblur_' + id).val('');
    $('#readonlyadd_' + id).prop('checked', false);
    $('#readonlyupdate_' + id).prop('checked', false);
    $('#hideonupdate_' + id).prop('checked', false);
    $('#hideonadd_' + id).prop('checked', false);
}

//Wordcount
function doWordCount(sourceEle, targetEle) {
    maxChars = $("#" + sourceEle).data("maxlength");
    l = $.trim($("#" + sourceEle).val()).length;
    //Print character count
    if (maxChars > 0) {
        $('#' + targetEle).html(l + '/' + maxChars);
    } else {
        $('#' + targetEle).html('');
    }
//Stop extra characters
    if (l > maxChars) {
        var txt = $("#" + sourceEle);
        $('#' + targetEle).html(maxChars + '/' + maxChars);
        txt.val(txt.val().slice(0, -1));
    }
//Reduce the text to fit maxlength
    txt = $("#" + sourceEle).val();
    txt = txt.substr(0, maxChars);
    $("#" + sourceEle).val(txt);
}
//Set length of data types
function doSetAttr(arg, defaultWidth, defaultImageWidth, defaultImageHeight) {
    id = arg.id;
    n = id.split('_');
    n = n[1];
    v = arg.value;
    //Disable all fields
    doDisableBuilderFields(arg, defaultWidth, defaultImageWidth, defaultImageHeight);
    if (v == 'textbox') {//Set Text length
        $('#length_' + n).val('100');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#length_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#orderby_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#unique_' + n).prop('disabled', false);
        $('#compositeunique_' + n).prop('disabled', false);
        $('#onchange_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onkeyup_' + n).prop('disabled', false);
        $('#onkeypress_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
        $('#readonlyadd_' + n).prop('disabled', false);
        $('#readonlyupdate_' + n).prop('disabled', false);
        $('#hideonupdate_' + n).prop('disabled', false);
        $('#hideonadd_' + n).prop('disabled', false);
    } else if (v == 'email') {//Set Email length
        $('#length_' + n).val('50');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#length_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#orderby_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#unique_' + n).prop('disabled', false);
        $('#compositeunique_' + n).prop('disabled', false);
        $('#onchange_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onkeyup_' + n).prop('disabled', false);
        $('#onkeypress_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
        $('#readonlyadd_' + n).prop('disabled', false);
        $('#readonlyupdate_' + n).prop('disabled', false);
        $('#hideonupdate_' + n).prop('disabled', false);
        $('#hideonadd_' + n).prop('disabled', false);
    } else if (v == 'password') {//Set Password Length
        $('#length_' + n).val('50');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#length_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#orderby_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#onchange_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onkeyup_' + n).prop('disabled', false);
        $('#onkeypress_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
        $('#readonlyadd_' + n).prop('disabled', false);
        $('#readonlyupdate_' + n).prop('disabled', false);
        $('#hideonupdate_' + n).prop('disabled', false);
        $('#hideonadd_' + n).prop('disabled', false);
    } else if (v == 'phone') {//Set Phone Length
        $('#length_' + n).val('15');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#length_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#orderby_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#unique_' + n).prop('disabled', false);
        $('#compositeunique_' + n).prop('disabled', false);
        $('#onchange_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onkeyup_' + n).prop('disabled', false);
        $('#onkeypress_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
        $('#readonlyadd_' + n).prop('disabled', false);
        $('#readonlyupdate_' + n).prop('disabled', false);
        $('#hideonupdate_' + n).prop('disabled', false);
        $('#hideonadd_' + n).prop('disabled', false);
    } else if (v == 'date') {//Set Date Length
        $('#length_' + n).val('10');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#length_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#orderby_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#unique_' + n).prop('disabled', false);
        $('#compositeunique_' + n).prop('disabled', false);
        $('#onchange_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onkeyup_' + n).prop('disabled', false);
        $('#onkeypress_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
        $('#readonlyadd_' + n).prop('disabled', false);
        $('#readonlyupdate_' + n).prop('disabled', false);
        $('#hideonupdate_' + n).prop('disabled', false);
        $('#hideonadd_' + n).prop('disabled', false);
    } else if (v == 'url') {//Set URL Length
        $('#length_' + n).val('255');
        $('#default_' + n).val('http://');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#length_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#orderby_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#unique_' + n).prop('disabled', false);
        $('#compositeunique_' + n).prop('disabled', false);
        $('#onchange_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onkeyup_' + n).prop('disabled', false);
        $('#onkeypress_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
        $('#readonlyadd_' + n).prop('disabled', false);
        $('#readonlyupdate_' + n).prop('disabled', false);
        $('#hideonupdate_' + n).prop('disabled', false);
        $('#hideonadd_' + n).prop('disabled', false);
    } else if (v == 'hidden') {//Set hidden Length
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
    } else if (v == 'json') {//Set json Length
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
    } else if (v == 'ip_address') {//Set IP Length
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
    } else if (v == 'textarea') {//Set textarea Length
        $('#length_' + n).val('');
        $('#width_' + id).val(12);
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#length_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#onchange_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onkeyup_' + n).prop('disabled', false);
        $('#onkeypress_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
        $('#readonlyadd_' + n).prop('disabled', false);
        $('#readonlyupdate_' + n).prop('disabled', false);
        $('#hideonupdate_' + n).prop('disabled', false);
        $('#hideonadd_' + n).prop('disabled', false);
    } else if (v == 'html_area') {//Set html area Length
        $('#length_' + n).val('');
        $('#width_' + id).val(12);
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#length_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#onchange_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onkeyup_' + n).prop('disabled', false);
        $('#onkeypress_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
        $('#readonlyadd_' + n).prop('disabled', false);
        $('#readonlyupdate_' + n).prop('disabled', false);
        $('#hideonupdate_' + n).prop('disabled', false);
        $('#hideonadd_' + n).prop('disabled', false);
    } else if (v == 'integer') {//Integer
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#length_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#orderby_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#unique_' + n).prop('disabled', false);
        $('#compositeunique_' + n).prop('disabled', false);
        $('#onchange_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onkeyup_' + n).prop('disabled', false);
        $('#onkeypress_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
        $('#readonlyadd_' + n).prop('disabled', false);
        $('#readonlyupdate_' + n).prop('disabled', false);
        $('#hideonupdate_' + n).prop('disabled', false);
        $('#hideonadd_' + n).prop('disabled', false);
    } else if (v == 'decimal') {//Decimal
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#length_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#orderby_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#unique_' + n).prop('disabled', false);
        $('#compositeunique_' + n).prop('disabled', false);
        $('#onchange_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onkeyup_' + n).prop('disabled', false);
        $('#onkeypress_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
        $('#readonlyadd_' + n).prop('disabled', false);
        $('#readonlyupdate_' + n).prop('disabled', false);
        $('#hideonupdate_' + n).prop('disabled', false);
        $('#hideonadd_' + n).prop('disabled', false);
    } else if (v == 'currency') {//Currency
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#length_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#orderby_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#unique_' + n).prop('disabled', false);
        $('#compositeunique_' + n).prop('disabled', false);
        $('#onchange_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onkeyup_' + n).prop('disabled', false);
        $('#onkeypress_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
        $('#readonlyadd_' + n).prop('disabled', false);
        $('#readonlyupdate_' + n).prop('disabled', false);
        $('#hideonupdate_' + n).prop('disabled', false);
        $('#hideonadd_' + n).prop('disabled', false);
    } else if (v == 'quick_pick') {//Quick picks
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#length_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#onchange_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onkeyup_' + n).prop('disabled', false);
        $('#onkeypress_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
        $('#readonlyadd_' + n).prop('disabled', false);
        $('#readonlyupdate_' + n).prop('disabled', false);
        $('#hideonupdate_' + n).prop('disabled', false);
        $('#hideonadd_' + n).prop('disabled', false);
    } else if (v == 'quick_pick_from_db') {//Quick picks from db
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#source_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#onchange_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onkeyup_' + n).prop('disabled', false);
        $('#onkeypress_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
        $('#readonlyadd_' + n).prop('disabled', false);
        $('#readonlyupdate_' + n).prop('disabled', false);
        $('#hideonupdate_' + n).prop('disabled', false);
        $('#hideonadd_' + n).prop('disabled', false);
//===
    } else if (v == 'dropdown') {//dropdown
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#length_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#orderby_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#unique_' + n).prop('disabled', false);
        $('#compositeunique_' + n).prop('disabled', false);
        $('#onchange_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onkeyup_' + n).prop('disabled', false);
        $('#onkeypress_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
        $('#readonlyadd_' + n).prop('disabled', false);
        $('#readonlyupdate_' + n).prop('disabled', false);
        $('#hideonupdate_' + n).prop('disabled', false);
        $('#hideonadd_' + n).prop('disabled', false);
    } else if (v == 'dropdown_from_db') {//dropdown from db
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#orderby_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#source_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#unique_' + n).prop('disabled', false);
        $('#compositeunique_' + n).prop('disabled', false);
        $('#onchange_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onkeyup_' + n).prop('disabled', false);
        $('#onkeypress_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
        $('#readonlyadd_' + n).prop('disabled', false);
        $('#readonlyupdate_' + n).prop('disabled', false);
        $('#hideonupdate_' + n).prop('disabled', false);
        $('#hideonadd_' + n).prop('disabled', false);
//===
    } else if (v == 'searchable_dropdown') {//searchable dropdown
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#length_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#orderby_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#source_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#unique_' + n).prop('disabled', false);
        $('#compositeunique_' + n).prop('disabled', false);
        $('#onchange_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onkeyup_' + n).prop('disabled', false);
        $('#onkeypress_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
        $('#readonlyadd_' + n).prop('disabled', false);
        $('#readonlyupdate_' + n).prop('disabled', false);
        $('#hideonupdate_' + n).prop('disabled', false);
        $('#hideonadd_' + n).prop('disabled', false);
    } else if (v == 'searchable_dropdown_from_db') {//searchable dropdown from db
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#orderby_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#source_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#unique_' + n).prop('disabled', false);
        $('#compositeunique_' + n).prop('disabled', false);
        $('#onchange_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onkeyup_' + n).prop('disabled', false);
        $('#onkeypress_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
        $('#readonlyadd_' + n).prop('disabled', false);
        $('#readonlyupdate_' + n).prop('disabled', false);
        $('#hideonupdate_' + n).prop('disabled', false);
        $('#hideonadd_' + n).prop('disabled', false);
//======
    } else if (v == 'radio_button') {//radio
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#length_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#unique_' + n).prop('disabled', false);
        $('#compositeunique_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
    } else if (v == 'radio_button_from_db') {//radio from db
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#source_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#unique_' + n).prop('disabled', false);
        $('#compositeunique_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
//======                
    } else if (v == 'radio_to_dropdown_from_db') {//radio to dropdown from db
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#source_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#unique_' + n).prop('disabled', false);
        $('#compositeunique_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
//======
    } else if (v == 'radio_button_slider') {//radio slider
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#length_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#unique_' + n).prop('disabled', false);
        $('#compositeunique_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
    } else if (v == 'radio_button_from_db_slider') {//radio slider from db
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#source_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#unique_' + n).prop('disabled', false);
        $('#compositeunique_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
//======
    } else if (v == 'checkbox') {//Checkbox
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#length_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#orderby_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#unique_' + n).prop('disabled', false);
        $('#compositeunique_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
    } else if (v == 'checkbox_from_db') {//Checkbox from db
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#orderby_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#source_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#unique_' + n).prop('disabled', false);
        $('#compositeunique_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
//======
    } else if (v == 'checkbox_switch') {//Checkbox switch
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#length_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#orderby_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#unique_' + n).prop('disabled', false);
        $('#compositeunique_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
    } else if (v == 'checkbox_from_db_switch') {//Checkbox switch from db
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#orderby_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#source_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#unique_' + n).prop('disabled', false);
        $('#compositeunique_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
    } else if (v == 'autocomplete') {//Autocomplete
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#orderby_' + n).prop('disabled', false);
        $('#searchby_' + n).prop('disabled', false);
        $('#source_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#default_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#unique_' + n).prop('disabled', false);
        $('#compositeunique_' + n).prop('disabled', false);
        $('#onchange_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onkeyup_' + n).prop('disabled', false);
        $('#onkeypress_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
        $('#readonlyadd_' + n).prop('disabled', false);
        $('#readonlyupdate_' + n).prop('disabled', false);
    } else if (v == 'attachment_field') {//Attachment
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#onchange_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
    } else if (v == 'picture_field') {//Picture
        $('#length_' + n).val('');
        $('#default_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#imagewidth_' + n).prop('disabled', false);
        $('#imageheight_' + n).prop('disabled', false);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#extrasql_' + n).prop('disabled', false);
        $('#required_' + n).prop('disabled', false);
        $('#requiredsaveforlater_' + n).prop('disabled', false);
        $('#onchange_' + n).prop('disabled', false);
        $('#onclick_' + n).prop('disabled', false);
        $('#onblur_' + n).prop('disabled', false);
    } else if (v == 'separator') {//Separator
        $('#length_' + n).val('');
        $('#cssclass_' + n).val('');
        //--//
        $('#name_' + n).prop('disabled', false);
        $('#length_' + n).prop('disabled', false);
        $('#width_' + n).prop('disabled', false);
        $('#width_' + n).prop('selectedIndex', 11);
        $('#cssclass_' + n).prop('disabled', false);
        $('#show_' + n).prop('disabled', false);
        $('#orderby_' + n).prop('disabled', true);
        $('#searchby_' + n).prop('disabled', true);
        $('#extrasql_' + n).prop('disabled', true);
        $('#default_' + n).prop('disabled', true);
        $('#required_' + n).prop('disabled', true);
        $('#requiredsaveforlater_' + n).prop('disabled', true);
        $('#unique_' + n).prop('disabled', true);
        $('#compositeunique_' + n).prop('disabled', true);
        $('#onchange_' + n).prop('disabled', true);
        $('#onclick_' + n).prop('disabled', true);
        $('#onkeyup_' + n).prop('disabled', true);
        $('#onkeypress_' + n).prop('disabled', true);
        $('#onblur_' + n).prop('disabled', true);
        $('#readonlyadd_' + n).prop('disabled', true);
        $('#readonlyupdate_' + n).prop('disabled', true);
        $('#hideonupdate_' + n).prop('disabled', false);
        $('#hideonadd_' + n).prop('disabled', false);
    } else {//Reset Length
        $('#width_' + n).prop('selectedIndex', 5);
        $('#length_' + n).val('');
        $('#default_' + n).val('');
    }

}
//Open overlay with iframe
function doOverlay(arg, url) {

    if (arg.selectedIndex == 1 && arg.options[arg.selectedIndex].text == '+') {
        $("#overlayDiv").click();
        window.overlayFrame.location.href = url;
        arg.selectedIndex = 0;
    }
}
//Function to resort select menu
function sortSelect(selElem, selectedValue, showPlus) {

    var x = document.getElementById(selElem);
    //Store first value of select menu in a variable
    firstValue = x.options[0].text;
    //Declare array for sorted variables
    var opt = [];
    //Start loop from second value of select menu
    for (i = 2; i < x.options.length; i++) {
//Push text of select to array
        opt.push(x.options[i].text);
    }
//Sort the array
    opt.sort();
    //Clear the select menu
    x.options.length = 0;
    //Populate the sorted items in select menu
    for (i = 0; i < opt.length; i++) {
        x.options.add(new Option(opt[i], opt[i]), x.options[i]);
    }

//Add previous valye at index 0
    x.options.add(new Option(firstValue, ""), x.options[0]);
    //Add a plus sign at position index 1
    if (showPlus == '+') {
        x.options.add(new Option("+", ""), x.options[1]);
    }

    x.selectedIndex = 0;
    //Select the first element of select menu
    if (selectedValue == 0) {
        x.selectedIndex = 0;
    } else {
        for (i = 0; i < x.options.length; i++) {
            if (x.options[i].text == selectedValue)
                x.selectedIndex = i;
        }

    }
}
//Change theme
function doChangeTheme(arg) {
    remote.window.location.href = 'themes.php?theme=' + arg;
}
//Toggle password field
function doTogglePassword(field, type, confirmPasswordPostfix) {
    if (type == 'password') {
        $('#' + field).prop('type', 'text');
        $('#' + field + confirmPasswordPostfix).prop('type', 'text');
    } else {
        $('#' + field).prop('type', 'password');
        $('#' + field + confirmPasswordPostfix).prop('type', 'password');
    }
}
//Inline field editing
var _____v = ''; //variable to store old value
function doInlineEdit(doWhat, url, eleToShow, eleToHide, tableToUpdate, fieldToUpdate, recordId) {

    if (doWhat == 'show') {

        if ($('#' + eleToShow).prop('type') == 'hidden') {
            $('#' + eleToShow).prop('type', 'text');
            $('#' + eleToShow).focus();
            _____v = $('#' + eleToHide).html();
            $('#' + eleToHide).html('');
        }
    } else {
        if ($('#' + eleToShow).prop('type') == 'text') {
            $('#' + eleToHide).html($('#' + eleToShow).val());
            $('#' + eleToShow).prop('type', 'hidden');
            //Submit form
            if (_____v != $('#' + eleToShow).val()) {
                remote.window.location.href = url + 'remote.php/update-single/' + tableToUpdate + '/' + recordId + '/' + fieldToUpdate + '?v=' + escape($('#' + eleToShow).val());
            }
        }
    }
}
//Trigger enter event
function doEnter(e, arg) {
    oldVal = arg.value;
    //Do not do anything on Esc
    if (e.keyCode == 27) {
        arg.value = _____v;
        $(arg).blur();
        return false;
    }
//Update on enter
    if (e.keyCode == 13) {
        //If empty, replace with old value
        if (arg.value == '') {
            arg.value = _____v;
        }
        $(arg).blur();
        return false;
    }
}
//Check uncheck all checkboxes on page
function doCheckUncheck(arg) {
    for (i = 0; i <= document.suForm.elements.length - 1; i++) {
        t = document.suForm.elements[i];

        if (t.type == 'checkbox') {
            if (arg == 1) {
                t.checked = true;
            } else {
                t.checked = false;
            }
        }
    }
}

//Check uncheck all checkboxes on page with passed value
function doCheckUncheckValued(arg, value) {
    for (i = 0; i <= document.suForm.elements.length - 1; i++) {
        t = document.suForm.elements[i];

        if (t.type == 'checkbox') {
            if (t.value == value) {
                if (arg == 1) {
                    t.checked = true;
                } else {
                    t.checked = false;
                }
            }
        }
    }
}

//Check uncheck all checkboxes on page with passed value
var _____delChkBoxes = 0;
function doCheckUncheckNamed(eleName) {

    var all = document.getElementsByTagName("*");

    for (var i = 0, max = all.length; i < max; i++) {
        // Do something with the element here
        if (all[i].tagName == 'INPUT' && all[i].type == 'checkbox' && all[i].name == 'delchk') {
            if (_____delChkBoxes == 0) {
                all[i].checked = true;
            } else {
                all[i].checked = false;
            }
        }

    }
    if (_____delChkBoxes == 0) {
        _____delChkBoxes = 1;

    } else {
        _____delChkBoxes = 0;

    }

}

//Proloader
function doPreloader(eleToHide, eleToShow) {
    //Preloader
    if ($('#' + eleToHide)) {
        $('#' + eleToHide).hide();
    }
    if ($('#' + eleToShow)) {
        $('#' + eleToShow).show();
        $('#' + eleToShow).css('visibility', 'visible');
    }
    //==
}
//Equalise Columns
function doEqualiseColumns() {
    windowHeight = $(window).height();
    contentHeight = $('.content-area').height();
    sideHeight = $('.sidebar-area').height();
    if (sideHeight < contentHeight) {
        $('.sidebar-area').height(contentHeight + 'px');
    }
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
//Delete all records
function doDelAll() {
    //Get checkboxes in table
    var ancestor = document.getElementById('records-table');
    var descendents = ancestor.getElementsByTagName('*');
    var i, e;
    for (i = 0; i < descendents.length; ++i) {
        e = descendents[i];
        //Pick checkbox only
        if (e.type == 'checkbox' && e.id.indexOf("delchk_") != -1) {
            $("#" + e.id).click();
        }

    }
}