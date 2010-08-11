<?php
/**
 * this file contains a simple AJAX-class
 *
 * @author      Thomas Meyer
 * @package     yana
 * @subpackage  javascript
 * @license     http://creativecommons.org/licenses/by-nc-sa/2.5/
 */

/**
 * <<JavaScript>> convert XML to an array
 *
 * This does convert a XML response, recieved via AJAX
 * from the Yana Framework to a standard JavaScript-Array.
 * In some cases this might be easier then using the DOM
 * model.
 *
 * Note that this will only work for requests send to the Yana framework,
 * where the server-side script has encoded the response using
 * the framework's function {@link function_XMLencode()}.
 *
 * @access  private
 * @param   XmlNode  $xml
 * @return  array
 * @name    yanaXmlDecode()
 * @since   2.9.5
 */
function yanaXmlDecode($xml)
{
}

/**
 * <<JavaScript>> convert SML to an array
 *
 * This does convert a SML response, recieved via AJAX
 * from the Yana Framework to a standard JavaScript-Array.
 * This is an alternative to using XML via the DOM model.
 *
 * Note that this will only work for requests send to the Yana framework,
 * where the server-side script has encoded the response using
 * the framework's function {@link SML::encode()}.
 *
 * @access  private
 * @param   string  $sml
 * @return  array
 * @name    yanaSmlDecode()
 * @since   2.9.6
 */
function yanaSmlDecode($sml)
{
}

/**
 * <<JavaScript>> Ajax handling class
 *
 * Purpose of this class is to send http-requests
 * to a server-side script and output the result
 * to an element in the current document,
 * identified by it's id.
 *
 * Simple example of usage:
 * <code>
 * // create new instance
 * $http = new AjaxRequest('index.php');
 * // choose where to put the result
 * $http.setTarget('id_of_html_tag');
 * // provide a function to handle the returned result
 * $http.setHandler(function ($httpResponse, $htmlNode)
 *   {
 *     $htmlNode.innerHTML = $httpResponse.responseText;
 *     // this will re-initialize the page (when needed)
 *     if (document.body.onload) {
 *       document.body.onload();
 *     }
 *   });
 * // send request to server
 * $http.send('action=' + 'function_name' + '&' + $arguments);
 * </code>
 *
 * Note: if you use the Yana SDK to create code for a new
 * plug-in, it will automatically provide you with a
 * ready-to-use implementation of a mock object that provides
 * all your available PHP functions via AJAX.
 * Also note that you don't have to change your PHP code to
 * make it "AJAX-ready".
 * A basic AJAX-functionality is already provided by the framework
 * itself, so you don't necessarily need to care about this issue.
 *
 * @package     yana
 * @subpackage  javascript
 */
class AjaxRequest
{

    /**
     * array containing references to all instances of this class
     *
     * This is required for the internal dispatcher to notify objects on new events.
     *
     * @access  public
     * @var     array
     */ var $instances = array();

    /**
     * URL of server-side (PHP-)script to handle requests
     *
     * @access  public
     * @var     string
     */ var $url = "";

    /**
     * Request object of current request (if any)
     *
     * @access  public
     * @var     XMLHttpRequest
     */ var $http = null;

    /**
     * Custom callback function
     *
     * Will be called when a request is ready
     *
     * @access  public
     * @var     function
     */ var $customHandler = null;

    /**
     * Ready to send new request
     *
     * True, when the latest request has already been handled.
     * False, when a recently issued request is still pending.
     *
     * @access  public
     * @var     bool
     */ var $isReady = false;

    /**
     * transfer mode
     *
     * True = send asynchronous request.
     * False = send synchronous request.
     *
     * @access  public
     * @var     bool
     */ var $isAsynchronous = true;

    /**
     * create a new instance
     *
     * The argument $url is the URL to the server-side (PHP-)script that will handle the AJAX-request(s).
     *
     * @param  string  $url       server-side script that handles the request
     * @param  bool    $synchron  set transfer mode
     */
    function AjaxRequest($url = null, $synchron = false)
    {
    }

    /**
     * send a request
     *
     * This will send an asynchronous request to the server.
     *
     * Note: this function does not return a result. Instead the
     * previously specified callback function will be called when the
     * result is finished.
     *
     * To specify a callback function use {@link AjaxRequest::setHandler()}.
     *
     * Important note: due to technical restrictions, all parameters send via
     * $method = "get" will be converted to ISO Latin-1.
     * If you need to send UTF-8 encoded parameters, please set $method to
     * "post" instead.
     *
     * The syntax of the parameter $args is the same as for any URL parameter
     * string: "key1=value&key2=value".
     * When using the GET-method (which is the default), the values should be
     * escaped using the function escape().
     * When using the POST-method, please do not use escape() on the values,
     * as this may produce unicode escape sequences, which are not
     * automatically decoded by the server.
     * Instead just call: value.replace('&', '%26');
     *
     * @access  public
     * @name    AjaxRequest::send()
     * @param   string  $args    will be appended to url
     * @param   string  $method  either "get" or "post"
     */
    function send($args, $method = "get")
    {
    }
    
    /**
     * set the function to handle the response
     *
     * @access  public
     * @name    AjaxRequest::setHandler()
     * @param   function  $newHandler
     */
    function setHandler($newHandler)
    {
    }
    
    /**
     * set the id of the document node to ouput the response text to
     *
     * By default the result text of the request is copied to the
     * innerHTML property of the node, identified by the id set via
     * this function. However, you may overwrite this behaviour by
     * setting up your own event handler via {@link AjaxRequest::setHandler()}.
     * In this case you may use the id-property as a hint for where
     * you intended the output to be placed.
     *
     * @access  public
     * @name    AjaxRequest::setTarget()
     * @param   string  $id
     */
    function setTarget($id)
    {
    }
    
    /**
     * handle the response
     *
     * This implements a dispatcher, that will notify an associated custom event handler,
     * when a request has been finished.
     *
     * @access  private
     * @name    AjaxRequest::handle()
     */
    function handle()
    {
    }
}
?>
