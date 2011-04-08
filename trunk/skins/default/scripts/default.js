/**
 * this file provides some basic functions used by various features of the Yana framework
 *
 * @author   Thomas Meyer
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

var yana_menu_open;
var yana_menu_auto_close = false;

/**
 * open / close a node of a html-menu
 *
 * see the manual on chapter "skins and templates: expandable HTML-menues"
 * (toc might be subject to change)
 *
 * @param  $node  HtmlNode
 * @param  $defaultDisplay  should be 'none' or 'block', defaults to 'block'
 */
function yanaMenu($node, $defaultDisplay)
{
    var i = 0;
    var o = $node;
    while (o.nextSibling != null && o.nodeName != 'ul' && o.className != 'menu' && i < 500)
    {
        o = o.nextSibling;
        i++;
    }
    if (!o || !o.style) {
        return;
    }
    if (yana_menu_auto_close && yana_menu_open && yana_menu_open.style) {
        var test = o;
        var is_ancestor = false;
        while (test.parentNode) 
        {
            if (test.parentNode == yana_menu_open) {
                is_ancestor = true;
                break;
            }
            test = test.parentNode;
        }
        if (!is_ancestor) {
            yana_menu_open.style.display = 'none';
        }
    }
    if (o.style.display == 'none') {
        if ($defaultDisplay) {
            o.style.display = $defaultDisplay;
        } else {
            o.style.display = '';
            yana_menu_open = o;
        }
    } else {
        o.style.display = 'none';
    }
    yanaMenuCookie(true);
}

/**
 * open / close a node of a horizontal html-menu
 *
 * Only necessary for IE6.
 *
 * see the manual on chapter "skins and templates: expandable HTML-menues"
 * (toc might be subject to change)
 *
 * @param  $node   HtmlNode
 * @param  $state  true = open, false = close
 */
function yanaHMenu($node, $state)
{
    if ($node) {
        $node.className = $node.className.replace(' menu_hover', '');
        if ($state) {
            $node.className = $node.className + ' menu_hover';
        }
    }
}

/**
 * restore or remember which menu items are opened or closed
 *
 * @param  $set  true = remember items, false = restore items
 */
function yanaMenuCookie($set)
{
    var documentnodelist = document.getElementsByTagName('ul');
    var id = '';
    for (var j = 0; j < documentnodelist.length; j++)
    {
        if (documentnodelist[j].className == 'menu root') {
            var menuId = '';
            if (documentnodelist[j].id) {
                menuId = documentnodelist[j].id;
            }
            var nodelist = documentnodelist[j].getElementsByTagName('ul');
            for (var i = 0; i < nodelist.length; i++)
            {
                if (nodelist[i].className == 'menu') {
                    id = 'yanaMenu' + menuId + '_' + j + '_' + i;
                    if ($set) {
                        if (nodelist[i].style.display) {
                            yanaSetCookie(id, nodelist[i].style.display);
                        } else {
                            yanaSetCookie(id, '');
                        }
                    } else {
                        displayState = yanaGetCookie(id);
                        if (displayState) {
                            nodelist[i].style.display = 'none';
                        } else if (displayState != null) {
                            nodelist[i].style.display = '';
                        }
                    }
                }
            }
        }
    }
    return true;
}

/**
 * close all nodes of a html-menu
 *
 * see the manual on chapter "skins and templates: expandable HTML-menues"
 * (toc might be subject to change)
 */
function yanaCloseAll()
{
    var nodelist = document.getElementsByTagName('ul');
    for (var i = 0; i < nodelist.length; i++)
    {
        if (nodelist[i].className == 'menu') {
            nodelist[i].style.display = 'none';
        }
    }
}

/**
 * set a cookie-var
 *
 * Set the var identified by $key to the new value $val.
 * Returns bool(true) on success and bool(false) on error.
 *
 * @param   $key  name as string
 * @param   $val  value as string
 * @return  bool
 */
function yanaSetCookie($key, $val)
{
    if (navigator.cookieEnabled) {
        if (!document.cookie) {
            document.cookie = 'expires=' + (new Date((new Date()).getTime() + 3600000)).toGMTString() + ';';
        }
        document.cookie = $key + '=' + $val;
        document.yanaCookie = null;
        return true;
    } else {
        return false;
    }
}

/**
 * get a cookie-var
 *
 * Returns the cookie's content.
 *
 * If $key is not provided, it returns all values as an associative array,
 * with names of the entries being the keys.
 *
 * Else the value with the name $key is returned instead.
 * If the value does not exist, the constant NULL is returned.
 *
 * @param   $key  name as string
 * @return  mixed
 */
function yanaGetCookie($key)
{
    if (document.cookie) {
        if (!document.yanaCookie) {
            var result = new Array();
            var regExp = /([^\s=]+)\s*=\s*([^;]+);?/g;
            while (matches = regExp.exec(document.cookie))
            {
                result[ matches[1] ] = matches[2];
            }
            document.yanaCookie = result;
        } else {
            result = document.yanaCookie;            
        }
        if (typeof $key != 'undefined') {
            if (typeof result[$key] != 'undefined') {
                return result[$key];
            } else {
                return null;
            }
        } else {
            return result;
        }
    } else {
        if (typeof $key != 'undefined') {
            return null;
        } else {
            return new Array();
        }
    }
}

/**
 * check if a string in a textarea field exceeds the maximum length
 *
 * Returns bool(false) if maximum length is exceeded and
 * bool(true) otherwise.
 *
 * @param   $node    TextareaNode that should be checked
 * @param   $length  maximum length as integer
 * @param   $event   event of type 'keypress'
 * @return  bool
 */
function yanaMaxLength($node, $length, $event)
{
    if ($length <= 0) {
        return true;
    }
    var o = $node;
    var len = $length;
    var e = $event;
    if (o.value.length >= len) {
        if (e) {
            if (e.keyCode && e.keyCode == 8 || e.keyCode == 46 || (e.keyCode >= 37 && e.keyCode <= 40)) {
                return true;
            } else if (e.which && e.which == 8 || e.which == 46 || (e.which >= 37 && e.which <= 40)) {
                return true;
            }
        }
        o.value = o.value.substring(0, len);
        return false;
    } else {
        return true;
    }
}

/**
 * show html preview of a text
 *
 * @deprecated  since 2.9.3
 */
function preview($target, $source)
{
    if (!window.focusedTextarea) {
        return;
    }
    var source = window.focusedTextarea;
    if (document.getElementById(source) && document.getElementById($target)) {
        source = document.getElementById(source).value;
        var target = document.getElementById($target);
        target.innerHTML = '<span style="font-size:11px;font-weight:bold">&nbsp; '+preview_js+'</span><br><iframe src="'+php_self+'?action=preview&amp;id='+window.yanaProfileId+'&amp;eintraege='+escape(source)+'" name="preview_window" height="80" width="380" style="border: 2px inset #EEEEEE;" scrolling="no"></iframe>';
    }
}

/**
 * add an emot-icon to message pane
 *
 * see the manual on chapter "templates and skins: new functions - smilies"
 * (toc might be subject to change)
 *
 * @param   $icon   string
 * @param   $event  event object
 */
function yanaAddIcon($icon, $event)
{
    var e = $event;
    if (!window.focusedTextarea) {
        return;
    }
    var targ = document.getElementById(window.focusedTextarea);
    var caretPosition = 0;
    if (document.selection) {
        caretPosition = targ.value.length + 1;
        if (targ.createTextRange) {
            range = document.selection.createRange().duplicate();
            while (range.parentElement() == targ && range.move("character", 1) == 1)
            {
                --caretPosition;
            }
         }
         if (caretPosition == targ.value.length + 1) {
             caretPosition = 0;
         }
    } else if (targ.selectionEnd) {
        caretPosition = targ.selectionEnd;
    } else {
        caretPosition = targ.value.length;
    }
    targ.value = targ.value.slice(0, caretPosition) + $icon + ' ' + targ.value.slice(caretPosition, targ.value.length);
    if ((typeof e != 'undefined') && e.preventDefault) {
        e.preventDefault();
    }
}

/**
 * auto-loading function to fix IE-Bugs
 */
function yanaFixIE()
{
    if (navigator.appName == "Microsoft Internet Explorer") {
        var buttons = document.getElementsByTagName('button');
        for (var i = 0; i < buttons.length; i++)
        {
            var button = buttons.item(i);
            if (button.type == 'submit') {
                var o = button.attributes.getNamedItem('value');
                if (o && o.value) {
                    button.onclick = function() {this.value = o.value;};
                }
            }
        }
    }
    return true;
}

/**
 * auto-loading function to prepare textarea tags
 *
 * used by: preview, smilies, embedded tags
 */
function yanaInitTextareas()
{
    yanaAddEventListener('onmouseover', function(e, node) {window.focusedTextarea = node.id;}, 'textarea');
    yanaAddEventListener('onkeydown', function(e, node) {window.focusedTextarea = node.id;}, 'textarea');
    yanaAddEventListener(
        'onkeypress', function(e, node) { if (node.maxlength) yanaMaxLength(node, node.maxlength, e);}, 'textarea'
    );
    yanaAddEventListener('onsubmit', yanaCheckEmbTags, 'form');
    yanaAddEventListener('onload', yanaInitTextareas, 'body');
    return true;
}

/**
 * auto-loading function to prepare content area tag
 *
 * used by: preview, smilies, embedded tags
 */
function yanaInitContentHover()
{
    var o = document.getElementById('index_content');
    var delay = 4000;
    var command = 'document.getElementById("index_content").className="index_content_hover";';
    if (o) {
        o.onmouseover = function() {
            o.timer = window.setTimeout(command, delay);
        }
        o.onclick = function() {
            if (o.timer) {
                window.clearTimeout(o.timer);
            }
            o.className = '';
        }
        o.onmouseout = function() {
            if (o.timer) {
                window.clearTimeout(o.timer);
                o.timer = false;
            }
            o.className = '';
        }
        o.onmousemove = function() {
            o.className = '';
            if (o.timer) {
                window.clearTimeout(o.timer);
                o.timer = window.setTimeout(command, delay);
            }
        }
    }
    yanaAddEventListener('onmouseover', function(e, node) {window.focusedTextarea = node.id;}, 'textarea');
    yanaAddEventListener('onkeydown', function(e, node) {window.focusedTextarea = node.id;}, 'textarea');
    yanaAddEventListener('onsubmit', yanaCheckEmbTags, 'form');
    yanaAddEventListener('onload', yanaInitTextareas, 'body');
    return true;
}

/**
 * check for correct syntax of embedded tags in all textarea fields
 *
 * This is to be called automatically when the user submits a form.
 */
function yanaCheckEmbTags($event, $formNode, $silent)
{
    var ajax = new AjaxRequest("?action=chkembtags&id=" + window.yanaProfileId + "&", true);
    var params = "";
    for (var i = 0; i < $formNode.elements.length; i++)
    {
        var node = $formNode.elements[i];
        if (node.tagName.match(/textarea/i)) {
            params += "&text[]=" + node.value.replace('&', '%26');
        }
    }
    if (params != "") {
        ajax.send(params, 'post');
        var error = yanaXmlDecode(ajax.http.responseXML);
        if (error && error['root'] && error['root']['text']) {
            node.focus();
            return confirm(error['root']['text']);
        }
    }
    return true;
}

/**
 * add a custom event listener
 */
function yanaAddEventListener ($eventType, $userFunction, $node, $silent)
{
    eventType = $eventType.toLowerCase();
    if (!$silent && !eventType.match(/^(onabort|onblur|onchange|onclick|ondblclick|onerror|onfocus|onkeydown|onkeypress|onkeyup|onload|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onreset|onselect|onsubmit|onunload)$/)) {
        alert('Warning: the event type "' + eventType + '" is unknown!');
    }
    var node = null;
    if ((typeof $node).match(/string/i)) {
        node = document.getElementsByTagName($node);
        for (var i = 0; i < node.length; i++)
        {
            yanaAddEventListener(eventType, $userFunction, node[i], $silent);
        }
    } else {
        node = $node;
    }

    if (typeof node != 'undefined') {

        var prevFunc = null;
        if (node[eventType]) {
            prevFunc = node[eventType];
        } else {
            prevFunc = function() {return true;};
        }
        node[eventType] = function(event)
        {
            if (!event) {
                event = window.event;
            }
            if (prevFunc(event, node) == false || $userFunction(event, node) == false) {
                return false;
            }
        };

        return true;
    } else {
        return false;
    }
}

/**
 * add a custom event listener to window.onload
 */
function yanaOnLoad($userFunction)
{
    var prevFunc = null;
    if (window.onload) {
        prevFunc = window.onload;
    } else {
        prevFunc = function() {return true;};
    }
    window.onload = function()
    {
        return prevFunc() && $userFunction();
    };
}

/**
 * auto-loading function to remove noscript areas
 */
function yanaHideNoScript()
{
    var list = document.getElementsByTagName('span');
    for (var i = 0; i < list.length; i++)
    {
        if (list[i].className == 'yana_noscript') {
            list[i].style.display = 'none';
        }
    }
    return true;
}

/**
 * simple portlet implementation
 */
function yanaPortlet($url, $id, $args, $title)
{
    if (!document.body) {
        window.setTimeout("yanaPortlet('" + $action + "', '" + $id + "', '" + $args + "', '" + $title + "')", 500);
        return;
    }
    var o = document.getElementById($id);
    if (!o) {
        document.write('<div id="' + $id + '"></div>');
        o = document.getElementById($id);
    }
    o.className = 'yana_portlet';
    o.innerHTML = '';
    if ($title) {
        o.title = $title;
        var title = document.createElement('div');
        title.className = 'yana_portlet_title';
        title.textContent = $title;
        o.appendChild(title);
    }
    var content = document.createElement('div');
    content.className = 'yana_portlet_content';
    $id += '_content';
    content.id = $id;
    o.appendChild(content);
    var ajax = new AjaxRequest($url);
    ajax.setTarget($id);
    ajax.send($args);
}

/**
 * return all elements that match a given class name
 */
function yanaGetElementsByClassName($className, $node)
{
    var node = null;
    if (typeof $node == 'undefined') {
        node = document.body;
    } else {
        node = $node;
    }
    var regEx = new RegExp("(^| )" + $className + "( |$)");
    if (!node.hasChildNodes()) {
        return new Array();
    } else {
        node = node.firstChild;
        var result = new Array();
        var result1 = new Array();
        while (true)
        {
            if (regEx.test(node.className)) {
                result[result.length] = node;
            }
            if (node.hasChildNodes()) {
                result1 = yanaGetElementsByClassName($className, node);
                for (var i = 0; i < result1.length; i++)
                {
                    result[result.length] = result1[i];
                }
            }
            if (node.nextSibling) {
                node = node.nextSibling;
            } else {
                break;
            }
        }
        return result;
    }
}

if (typeof document.getElementsByClassName == 'undefined') {
    document.getElementsByClassName = yanaGetElementsByClassName;
}

yanaOnLoad(yanaFixIE);
yanaOnLoad(yanaMenuCookie);
yanaOnLoad(yanaInitTextareas);
yanaOnLoad(yanaHideNoScript);
yanaOnLoad(yanaInitContentHover);
