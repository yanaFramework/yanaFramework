function tinyMCEinit()
{
    if (!document.body) {
        window.setTimeout('tinyMCEinit();', 500);
    } else if (typeof tinyMCE != "undefined") {
        tinyMCE.init({

            // General options
            mode : "specific_textareas",
            editor_selector: "mceEditor",

            // Plug-in options
            plugins : "contextmenu," +
                "emotions," +
                "inlinepopups," +
                "insertdatetime," +
                "noneditable," +
                "safari," +
                "searchreplace," +
                "spellchecker," +
                "tabfocus," +
                "table," +
                "template",

            // Skin options
            theme : "advanced",
            skin : "o2k7",
            skin_variant: "silver",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_statusbar_location : "bottom",
            theme_advanced_buttons1: "code,|," +
                "newdocument,|," +
                "undo,redo,|," +
                "search,replace,|," +
                "spellchecker,|," +
                "hr,charmap,insertdate,inserttime,|," +
                "link,image,|," +
                "tablecontrols",
            theme_advanced_buttons2: "formatselect,fontselect,fontsizeselect,|," +
                "bold,italic,underline,|," +
                "sub,sup,|," +
                "justifyleft,justifycenter,justifyright,|," +
                "numlist,bullist,|," +
                "outdent,indent,|," +
                "forecolor,backcolor",
            theme_advanced_buttons3: false,
            width : "100%",

            // i18n
            language: window.yanaLanguage,

            // spellchecker
            spellchecker_rpc_url: php_self + '?' + window.yanaSessionName + '=' + window.yanaSessionId +
                '&id='+ window.yanaProfileId + '&action=spellcheck'
        });
        // edit on demand
        var editor;
        // activate on focus
        var onFocus = function(e)
        {
            var element = e.target || e.srcElement;
            if (editor) {
                tinyMCE.execCommand('mceRemoveControl', false, editor);
            }
            if (!element.id) {
                element.id = 'html_' + element.name;
            }
            editor = element.id;
            tinyMCE.execCommand('mceAddControl', false, editor);
        }
        // deactivate on Esc
        var onEscKey = function(e)
        {
            // key code 27 = Esc
            if (editor && e.keyCode == 27) {
                tinyMCE.execCommand('mceRemoveControl', false, editor);
            }
        }
        // attach events to textareas
        textareas = document.getElementsByTagName('textarea');
        for (var i = 0; i < textareas.length; i++)
        {
            if (textareas[i].className && textareas[i].className.indexOf('editable') != -1) {
                if (window.addEventListener) {
                    textareas[i].addEventListener('focus', onFocus, false);
                    window.document.addEventListener('keypress', onEscKey, false);
                } else if (window.attachEvent) {
                    textareas[i].attachEvent('onfocus', onFocus);
                    window.document.attachEvent('onkeypress', onEscKey);
                }
            }
        }
    }
}
// The following, rather complicated construct, is required due to javascript loading / racing condition in IE.
// For any other browser we could just use yanaAddEventListener(), which does the same thing in just one line.
var prevFunc = null;
if (window.onload) {
    prevFunc = window.onload;
} else {
    prevFunc = function () { return true; };
}
window.onload = function () {
    tinyMCEinit();
    prevFunc();
}