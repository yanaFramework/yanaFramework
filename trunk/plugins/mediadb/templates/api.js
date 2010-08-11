/**
 * AJAX - Yana Bridge for plugin "mediadb"
 *
 * This file was generated automatically by the Yana-SDK code generator.
 *
 * @package     plugins
 */

/**
 * constructor "plugin_mediadb"
 *
 * Usage:
 * <code>
 * <div id="foo"></div>
 *
 * [...]
 *
 * var o = new plugin_mediadb("foo");
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
function plugin_mediadb($id)
{
    if ($id && typeof $id != 'string') {
        alert('Wrong argument type for argument 1 in constructor plugin_mediadb(). String expected, found "'+(typeof $id)+'" instead.');
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
            alert('Invalid id in constructor plugin_mediadb(). The container "'+$id+'" was not found.');
        } else {
            this.http.setTarget($id);
        }
    } else {
        alert('Unable to create new instance of "plugin_mediadb". A required class "AjaxRequest" was not found.');
    }
}

/**
 * call YANA-function by name
 *
 * @param  $func     string  name of function to execute
 * @param  $args     string  (optional) params passed to the function call
 * @param  $handler  string  (optional) reference to a function that serves as a custom event handler
 */
plugin_mediadb.prototype.callFunctionByName = function($func, $args, $handler)
{
    if ($func && typeof $func  != 'string') {
        alert('Wrong argument type for argument 1 in method plugin_mediadb.callFunctionByName(). String expected, found "'+(typeof $func)+'" instead.');
        return;
    } else if ($args && typeof $args  != 'string') {
        alert('Wrong argument type for argument 2 in method plugin_mediadb.callFunctionByName(). String expected, found "'+(typeof $args)+'" instead.');
        return;
    } else if ($handler && typeof $handler != 'Function' && typeof $handler != 'function') {
        alert('Wrong argument type for argument 3 in method plugin_mediadb.callFunctionByName(). Function expected, found "'+(typeof $handler)+'" instead.');
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
 * mediadb_search_mediafolder
 *
 * @param  $args     string  (optional) params passed to the function call
 * @param  $handler  string  (optional) reference to a function that serves as a custom event handler
 */
plugin_mediadb.prototype.MediadbSearchMediafolder = function ($args, $handler)
{
    // you may put your own code here
    this.callFunctionByName("mediadb_search_mediafolder", $args, $handler);
}


/**
 * mediadb_insert_mediafolder
 *
 * @param  $args     string  (optional) params passed to the function call
 * @param  $handler  string  (optional) reference to a function that serves as a custom event handler
 */
plugin_mediadb.prototype.MediadbInsertMediafolder = function ($args, $handler)
{
    // you may put your own code here
    this.callFunctionByName("mediadb_insert_mediafolder", $args, $handler);
}


/**
 * mediadb_update_mediafolder
 *
 * @param  $args     string  (optional) params passed to the function call
 * @param  $handler  string  (optional) reference to a function that serves as a custom event handler
 */
plugin_mediadb.prototype.MediadbUpdateMediafolder = function ($args, $handler)
{
    // you may put your own code here
    this.callFunctionByName("mediadb_update_mediafolder", $args, $handler);
}


/**
 * mediadb_delete_mediafolder
 *
 * @param  $args     string  (optional) params passed to the function call
 * @param  $handler  string  (optional) reference to a function that serves as a custom event handler
 */
plugin_mediadb.prototype.MediadbDeleteMediafolder = function ($args, $handler)
{
    // you may put your own code here
    this.callFunctionByName("mediadb_delete_mediafolder", $args, $handler);
}


/**
 * mediadb_export_mediafolder
 *
 * @param  $args     string  (optional) params passed to the function call
 * @param  $handler  string  (optional) reference to a function that serves as a custom event handler
 */
plugin_mediadb.prototype.MediadbExportMediafolder = function ($args, $handler)
{
    // you may put your own code here
    this.callFunctionByName("mediadb_export_mediafolder", $args, $handler);
}


/**
 * mediadb_search_mediacount
 *
 * @param  $args     string  (optional) params passed to the function call
 * @param  $handler  string  (optional) reference to a function that serves as a custom event handler
 */
plugin_mediadb.prototype.MediadbSearchMediacount = function ($args, $handler)
{
    // you may put your own code here
    this.callFunctionByName("mediadb_search_mediacount", $args, $handler);
}


/**
 * mediadb_insert_mediacount
 *
 * @param  $args     string  (optional) params passed to the function call
 * @param  $handler  string  (optional) reference to a function that serves as a custom event handler
 */
plugin_mediadb.prototype.MediadbInsertMediacount = function ($args, $handler)
{
    // you may put your own code here
    this.callFunctionByName("mediadb_insert_mediacount", $args, $handler);
}


/**
 * mediadb_update_mediacount
 *
 * @param  $args     string  (optional) params passed to the function call
 * @param  $handler  string  (optional) reference to a function that serves as a custom event handler
 */
plugin_mediadb.prototype.MediadbUpdateMediacount = function ($args, $handler)
{
    // you may put your own code here
    this.callFunctionByName("mediadb_update_mediacount", $args, $handler);
}


/**
 * mediadb_delete_mediacount
 *
 * @param  $args     string  (optional) params passed to the function call
 * @param  $handler  string  (optional) reference to a function that serves as a custom event handler
 */
plugin_mediadb.prototype.MediadbDeleteMediacount = function ($args, $handler)
{
    // you may put your own code here
    this.callFunctionByName("mediadb_delete_mediacount", $args, $handler);
}


/**
 * mediadb_export_mediacount
 *
 * @param  $args     string  (optional) params passed to the function call
 * @param  $handler  string  (optional) reference to a function that serves as a custom event handler
 */
plugin_mediadb.prototype.MediadbExportMediacount = function ($args, $handler)
{
    // you may put your own code here
    this.callFunctionByName("mediadb_export_mediacount", $args, $handler);
}

