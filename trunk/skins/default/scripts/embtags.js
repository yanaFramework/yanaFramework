/**
 * for internal use only
 *
 * @author   Thomas Meyer
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

/**
 * add embedded tag to textarea
 *
 * @param  tag
 * @param  e
 */
function yanaAddEmbTag(tag, e)
{
    if (!window.focusedTextarea) {
        alert(noselection);
        return;
    }
    var targetTextarea = document.getElementById(window.focusedTextarea);
    var startTag = tag.match(/^\[[^\]]+\]/);
    var endTag = tag.match(/\[\/[^\]]+\]$/);
    var currentSelection = "";

    if (!endTag) {
        
        targetTextarea.value += tag;

    } else if (document.selection && document.selection.createRange().parentElement().id == targetTextarea.id) {

        currentSelection = document.selection.createRange().text;
        if (currentSelection == '') {
            alert(noselection);
            return;
        } else {
            document.selection.createRange().text = startTag + currentSelection + endTag;
        }

    } else if (window.getSelection) {

        currentSelection = targetTextarea.value.slice(targetTextarea.selectionStart,targetTextarea.selectionEnd);
        if (currentSelection == '') {
            alert(noselection);
            return;
        } else {
            targetTextarea.value = targetTextarea.value.slice(0, targetTextarea.selectionStart) +
            startTag + currentSelection + endTag +
            targetTextarea.value.slice(targetTextarea.selectionEnd, targetTextarea.textLength);
        }

    } else {

        targetTextarea.value += tag;

    }
    targetTextarea.focus();
    if (e.preventDefault) {
        e.preventDefault();
    }
}

/**
 * prepare an ajax request
 *
 * @param  $id
 */
function yanaPreviewStart($id)
{
    if (!document.body || !AjaxRequest){
        window.setTimeout('yanaPreviewStart("' + $id + '")', 500);
        return;
    } else {
        if (!window.yanaPreview) {
            window.yanaPreview = new Array();
        }
        window.yanaPreview[$id] = new AjaxRequest(php_self + "?action=preview&id=" + window.yanaProfileId);
        window.yanaPreview[$id].setTarget($id);
        window.yanaPreview[$id].setHandler(yanaPreviewHandle);
    }
}

/**
 * show html preview of a text
 *
 * send request
 *
 * @param  $id
 */
function yanaPreviewSend($id)
{
    if (!window.focusedTextarea) {
        return;
    }
    var source = window.focusedTextarea;
    if (document.getElementById(source)) {
        var args = document.getElementById(source).value;
        args = args.replace(/\n/g, '[br]');
        args = args.replace('&', '%26');
        window.yanaPreview[$id].send("&eintraege=" + args, 'post');
    }
}

/**
 * show html preview of a text
 *
 * handle response
 *
 * @param  $http
 * @param  $target
 */
function yanaPreviewHandle($http, $target)
{
    var responseText = $http.responseText.replace(/<body[^>]>(.*)<\/body>/, '$1');
    if (responseText) {
        if (document.all) {
            $target.innerHTML = '<div class="fieldset" style="width:' + yanaPreviewWidth[$target.id] + ';">' +
            '<div class="gui_preview_head">' + preview_js  + '</div>' +
            '<div class="gui_preview_body" style="height:' + yanaPreviewHeight[$target.id] + ';' +
            'width:' + yanaPreviewWidth[$target.id] + ';">' +
            $http.responseText.replace(/<body[^>]>(.*)<\/body>/, '$1') +
            '</div></div>';
        } else {
            $target.innerHTML = '<fieldset style="width:' + yanaPreviewWidth[$target.id] + ';">' +
            '<legend class="gui_preview_head">' + preview_js  + '</legend>' +
            '<div class="gui_preview_body" style="height:' + yanaPreviewHeight[$target.id] + ';' +
            'width:' + yanaPreviewWidth[$target.id] + ';">' +
            $http.responseText.replace(/<body[^>]>(.*)<\/body>/, '$1') +
            '</div></fieldset>';
        }
    } else {
        $target.innerHTML = '';
    }
}
