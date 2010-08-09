/**
 * AJAX - Yana Bridge for plugin "guestbook"
 *
 * This file was generated automatically by the Yana-SDK code generator.
 *
 * @package     plugins
 */

/**
 * constructor "YanaGuestbook"
 *
 * @access      public
 * @package     plugins
 * @subpackage  guestbook
 * @param       $id
 */
function YanaGuestbook ($id)
{
    if ($id && typeof $id != 'string') {
        alert('Wrong argument type for argument 1 in constructor YanaGuestbook(). String expected, found "'+(typeof $id)+'" instead.');
        return;
    } else if (!$id) {
        $id = "yana_stdout";
    }

    if (!src) {
        var src = window.location.href;
    }

    if (AjaxRequest) {
        this.http = new AjaxRequest(src);
        if (!document.getElementById($id)) {
            alert('Invalid id in constructor YanaGuestbook(). The container "'+$id+'" was not found.');
        } else {
            this.http.setTarget($id);
        }
    } else {
        alert('Unable to create new instance of "YanaGuestbook". A required class "AjaxRequest" was not found.');
    }
}

/**
 * call YANA-function by name
 *
 * @param   $func     name of function to execute
 * @param   $args     (optional) params passed to the function call
 * @param   $handler  (optional) reference to a function that serves as a custom event handler
 * @name    YanaGuestbook::callFunctionByName()
 */
YanaGuestbook.prototype.callFunctionByName = function($func, $args, $handler)
{
    if ($func && typeof $func  != 'string') {
        alert('Wrong argument type for argument 1 in method YanaGuestbook.callFunctionByName(). String expected, found "'+(typeof $func)+'" instead.');
        return;
    } else if ($args && typeof $args  != 'string') {
        alert('Wrong argument type for argument 2 in method YanaGuestbook.callFunctionByName(). String expected, found "'+(typeof $args)+'" instead.');
        return;
    } else if ($handler && typeof $handler != 'Function' && typeof $handler != 'function') {
        alert('Wrong argument type for argument 3 in method YanaGuestbook.callFunctionByName(). Function expected, found "'+(typeof $handler)+'" instead.');
        return;
    } else {
        if ($handler) {
            this.http.setHandler($handler);
        } else {
            this.http.setHandler(function (httpResponse, htmlNode)
            {
                // put event handling code here !
                // See this example:
                this.replaceHTML(htmlNode, httpResponse.responseText);
                YanaGuestbook.initPage();
            });
        }
        this.http.send('action=' + escape($func) + '&' + $args);
    }
}

/* BEGIN custom events */

/**
 * call a PHP function via AJAX
 *
 * @static
 * @access  public
 * @param   $func  function name
 * @param   $id    dataset-id
 * @param   $args  function arguments
 * @param   $form  form-id
 */
YanaGuestbook.prototype.guestbookRequest = function ($func, $id, $args, $form)
{
    var guestbook = new YanaGuestbook($id);
    if (typeof $form == 'string') {
        var o = document.getElementById($form);
        if (typeof o != 'undefined' && typeof o.elements  != 'undefined') {
            for (var i = 0; i < o.elements.length; i++)
            {
                if (o.elements[i].name) {
                    $args += '&' + escape(o.elements[i].name.toLowerCase()) + '=' + escape(o.elements[i].value);
                }
            }
        }
    }
    guestbook.callFunctionByName($func, $args);
}

/**
 * call a PHP function via AJAX
 *
 * @static
 * @access  public
 */
YanaGuestbook.initPage = function()
{
    if (!document.body) {
        window.setTimeout("YanaGuestbook.initPage()", 500);
        return;
    }
    var head = document.getElementById('guestbook_form_new_head');
    var content = document.getElementById('guestbook_form_new_content');
    if (typeof head != 'undefined' && typeof content != 'undefined') {
        content.className = 'guestbook_form_content_hidden';
        head.style.cursor = 'pointer';
        head.onclick = function()
        {
            var content = document.getElementById('guestbook_form_new_content');
            if (content.className == 'guestbook_form_content_hidden') {
                content.className =  'guestbook_form_content';
            } else {
                content.className =  'guestbook_form_content_hidden';
            }
        }
    }
}
YanaGuestbook.initPage();