/**
 * AJAX - Yana Bridge for plugin "{$plugin->getId()}"
 *
 * This file was generated automatically by the Yana-SDK code generator.
 *
{if $plugin->getVersion()} * @version     {$plugin->getVersion}
{/if} * @package     plugins
 */

/**
 * constructor "{$plugin->getClassName()}"
 *
 * Usage:
 * <code>
 * <div id="foo"></div>
 *
 * [...]
 *
 * var o = new {$plugin->getClassName()}("foo");
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
function {$plugin->getClassName()}($id)
{ldelim}
    if ($id && typeof $id != 'string') {ldelim}
        alert('Wrong argument type for argument 1 in constructor {$plugin->getClassName()}(). String expected, found "'+(typeof $id)+'" instead.');
        return;
    {rdelim} else if (!$id) {ldelim}
        $id = "yana_stdout";
    {rdelim}

    if (typeof php_self != 'undefined') {ldelim}
        var src = php_self;
    {rdelim} else {ldelim}
        var src = "";
    {rdelim}

    if (AjaxRequest) {ldelim}
        this.http = new AjaxRequest(src);
        if (!document.getElementById($id)) {ldelim}
            alert('Invalid id in constructor {$plugin->getClassName()}(). The container "'+$id+'" was not found.');
        {rdelim} else {ldelim}
            this.http.setTarget($id);
        {rdelim}
    {rdelim} else {ldelim}
        alert('Unable to create new instance of "{$plugin->getClassName()}". A required class "AjaxRequest" was not found.');
    {rdelim}
{rdelim}

/**
 * call YANA-function by name
 *
 * @param  $func     string  name of function to execute
 * @param  $args     string  (optional) params passed to the function call
 * @param  $handler  string  (optional) reference to a function that serves as a custom event handler
 */
{$plugin->getClassName()}.prototype.callFunctionByName = function($func, $args, $handler)
{ldelim}
    if ($func && typeof $func  != 'string') {ldelim}
        alert('Wrong argument type for argument 1 in method {$plugin->getClassName()}.callFunctionByName(). String expected, found "'+(typeof $func)+'" instead.');
        return;
    {rdelim} else if ($args && typeof $args  != 'string') {ldelim}
        alert('Wrong argument type for argument 2 in method {$plugin->getClassName()}.callFunctionByName(). String expected, found "'+(typeof $args)+'" instead.');
        return;
    {rdelim} else if ($handler && typeof $handler != 'Function' && typeof $handler != 'function') {ldelim}
        alert('Wrong argument type for argument 3 in method {$plugin->getClassName()}.callFunctionByName(). Function expected, found "'+(typeof $handler)+'" instead.');
        return;
    {rdelim} else {ldelim}
        if ($handler) {ldelim}
            this.http.setHandler($handler);
        {rdelim} else {ldelim}
            this.http.setHandler(function (httpResponse, htmlNode)
            {ldelim}
                // put event handling code here !
                // See this example:
                htmlNode.innerHTML = httpResponse.responseText;
                // this will re-initialize the page (when needed)
                if (document.body.onload) {ldelim}
                    document.body.onload();
                {rdelim}                
            {rdelim});
        {rdelim}
        this.http.send('action=' + escape($func) + '&' + $args);
    {rdelim}
{rdelim}

{foreach item="method" from=$plugin->getMethods()}

/**
 * {$method->getMethodName()}
 *
 * @param  $args     string  (optional) params passed to the function call
 * @param  $handler  string  (optional) reference to a function that serves as a custom event handler
 */
{$plugin->getClassName()}.prototype.{$method->getMethodName()|replace:'_':' '|capitalize:true|replace:' ':''} = function ($args, $handler)
{ldelim}
    // you may put your own code here
    this.callFunctionByName("{$method->getMethodName()}", $args, $handler);
{rdelim}

{/foreach}