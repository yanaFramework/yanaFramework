/**
 * AJAX - Yana Bridge for plugin "mediagallery"
 *
 * This file was generated automatically by the Yana-SDK code generator.
 *
 * @package     plugins
 */

/**
 * constructor "plugin_mediagallery"
 *
 * Usage:
 * <code>
 * <div id="foo"></div>
 *
 * [...]
 *
 * var o = new plugin_mediagallery("foo");
 * o.nameOfFunction("arg1=value1&arg2=value2");
 * </code>
 *
 * This will send a request using the given argument list
 * and output the results to the tag with the id "foo".
 *
 * For more details see the manual and API documentation.
 *
 * @access      public
 * @package     plugins
 * @subpackage  javascript
 * @param       $id  string  target id
 */
function plugin_mediagallery($id)
{
    if ($id && typeof $id != 'string') {
        alert('Wrong argument type for argument 1 in constructor plugin_mediagallery(). String expected, found "'+(typeof $id)+'" instead.');
        return;
    } else if (!$id) {
        $id = "yana_stdout";
    }

    if (typeof php_self != 'undefined') {
        var src = php_self;
    } else {
        var src="plugins/sdk/templates/plugin/";
    }

    if (AjaxRequest) {
        this.http = new AjaxRequest(src);
        if (!document.getElementById($id)) {
            alert('Invalid id in constructor plugin_mediagallery(). The container "'+$id+'" was not found.');
        } else {
            this.http.setTarget($id);
        }
    } else {
        alert('Unable to create new instance of "plugin_mediagallery". A required class "AjaxRequest" was not found.');
    }
}

/**
 * call YANA-function by name
 *
 * @param  $func     string  name of function to execute
 * @param  $args     string  (optional) params passed to the function call
 * @param  $handler  string  (optional) reference to a function that serves as a custom event handler
 */
plugin_mediagallery.prototype.callFunctionByName = function($func, $args, $handler)
{
    if ($func && typeof $func  != 'string') {
        alert('Wrong argument type for argument 1 in method plugin_mediagallery.callFunctionByName(). String expected, found "'+(typeof $func)+'" instead.');
        return;
    } else if ($args && typeof $args  != 'string') {
        alert('Wrong argument type for argument 2 in method plugin_mediagallery.callFunctionByName(). String expected, found "'+(typeof $args)+'" instead.');
        return;
    } else if ($handler && typeof $handler != 'Function' && typeof $handler != 'function') {
        alert('Wrong argument type for argument 3 in method plugin_mediagallery.callFunctionByName(). Function expected, found "'+(typeof $handler)+'" instead.');
        return;
    } else {
        if ($handler) {
            this.http.setHandler($handler);
        } else {
            this.http.setHandler(function (httpResponse, htmlNode)
            {
                // put event handling code here !
                // See this example:
                htmlNode.innerHTML = httpResponse.responseText;
                // this will re-initialize the page (when needed)
                if (document.body.onload) {
                    document.body.onload();
                }                
            });
        }
        this.http.send('action=' + escape($func) + '&' + $args);
    }
}


/**
 * mediagallery
 *
 * @param  $args     string  (optional) params passed to the function call
 * @param  $handler  string  (optional) reference to a function that serves as a custom event handler
 */
plugin_mediagallery.prototype.Mediagallery = function ($args, $handler)
{
    // you may put your own code here
    this.callFunctionByName("mediagallery", $args, $handler);
}

