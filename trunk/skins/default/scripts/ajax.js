/**
 * this file contains a simple AJAX-class
 *
 * @author   Thomas Meyer
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

/**
 * constructor
 *
 * Purpose of this class is to send http-requests
 * to a server-side script and output the result
 * to an element in the current document,
 * identified by it's id.
 *
 * @param  $url       server-side script that handles the request
 * @param  $synchron  set transfer mode
 */
function AjaxRequest($url, $synchron)
{
    /* set "URL" property */
    if ($url && typeof $url == 'string') {
        this.url = $url;
    } else if (!$url && src > "") {
        this.url = src;
    } else {
        this.url = false;
        alert('Wrong argument type for argument 1 in constructor AjaxRequest. String expected, found "'+(typeof url)+'" instead.');
    }

    /* add empty search string where needed */
    if (this.url && !this.url.match(/\?/)) {
        this.url += '?';
        if (window.yanaProfileId) {
            this.url += 'id=' + encodeURIComponent(window.yanaProfileId) + "&";
        }
        if (window.yanaSessionName && window.yanaSessionId) {
            this.url += encodeURIComponent(window.yanaSessionName) + "=" + encodeURIComponent(window.yanaSessionId) + "&";
        }
    }

    /* create HTTP-Request */
    if (document.all) {
        try {
            this.http = new ActiveXObject('Microsoft.XMLHTTP');
        } catch (ex) {
            alert("This is an AJAX application. " +
            "Microsoft Internet Explorer requires ActiveX to be turned on, " +
            "otherwise it cannot run this application. " +
            "Please activate ActiveX via the security settings of your browser and try again.");
        }
    } else {
        this.http = new XMLHttpRequest();
    }

    if ($synchron) {
        this.isAsynchronous = false;
    } else {
        this.isAsynchronous = true;
    }

    /* create references */
    this.customHandler = false;

    /* set ready-state to false */
    this.isReady = false;

    AjaxRequest.prototype.instances[AjaxRequest.prototype.instances.length] = this;
}

/**
 * send a request
 *
 * @param  $args  will be appended to url
 */
AjaxRequest.prototype.send = function($args, $method)
{
    if (!this.url) {
        alert('Cannot send request in AjaxRequest.send(). No target url specified.');
    } else {
        if (typeof $args != 'string') {
            alert('Wrong argument type for argument 1 in method AjaxRequest.send(). String expected, found "'+(typeof $args)+'" instead.');
            $args = '';
        }
        var o = document.getElementById(this.id);
        if (o) {
            o.innerHTML = '<div class="ajax_loading">... Loading.</div>';
        }
        this.isReady = false;
        if ($method == 'post') {
            this.http.open('post', this.url + '&is_ajax_request=1', this.isAsynchronous);
            this.http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            this.http.onreadystatechange = this.handle;
            this.http.send($args);
        } else {
            this.http.open('get', this.url + '&' + $args + '&is_ajax_request=1', this.isAsynchronous);
            this.http.onreadystatechange = this.handle;
            this.http.send(null);
        }
    }
}

/**
 * set the function to handle the response
 *
 * @param  $newHandler
 */
AjaxRequest.prototype.setHandler = function($newHandler)
{
    if (typeof $newHandler != 'Function' && typeof $newHandler != 'function') {
        alert('Wrong argument type for argument 1 in method AjaxRequest.setHandler(). Function expected, found "'+(typeof $newHandler)+'" instead.');
    } else {
        this.customHandler = $newHandler;
    }
}

/**
 * set the id of the document node to ouput the response text to
 *
 * @param  $id  element-id (propably a div-tag)
 */
AjaxRequest.prototype.setTarget = function($id)
{
    if ((typeof $id) != 'string') {
        alert('Wrong argument type for argument 1 in method AjaxRequest.setTarget(). String expected, found "'+(typeof $id)+'" instead.');
    } else if (!document.getElementById($id)) {
        alert('Invalid argument 1 in method AjaxRequest.setTarget(). There is no element with the id "'+$id+'" within the current document.');
    } else {
        this.id = $id;
    }
}

/**
 * This loads HTML into the specified node and executes JavaScript including document.write()
 *
 * @param  $node  element node
 * @param  $html  HTML code
 */
AjaxRequest.prototype.replaceHTML = function($node, $html)
{
    $node.innerHTML = $html;
    if ($html.match(/<script[^>]*?>/)) { // handle javascript
        var documentText = "";
        document.write = function(text)
        {
            documentText += text;
        }
        var matches = false;
        do
        {
            if (matches) {
                func = new Function(matches[3]);
                func.call();
                $node.innerHTML = matches[1] + documentText + matches[4];
                documentText = "";
            }
            matches = $node.innerHTML.match(/^([\s\S]*?)<script( [^>]*)?>(.*)<\/script>([\s\S]*)/i);
        } while (matches)
    }
    // this will re-initialize the page (when needed)
    if (window.onload) {
        window.onload();
    }
}

/**
 * handle the response
 *
 * This implements a dispatcher, that will notify an associated custom event handler,
 * when a request has been finished.
 *
 * @name  AjaxRequest::handle()
 */
AjaxRequest.prototype.handle = function()
{
    for(var i = 0; i < AjaxRequest.prototype.instances.length; i++)
    {
        var o = AjaxRequest.prototype.instances[i];
        if (o && !o.isReady && o.http.readyState == 4) {
            var o1 = document.getElementById(o.id);
            if (o.customHandler) {
                o.customHandler(o.http, o1);
            } else if (o1) {
                o1.innerHTML = o.http.responseText;
            }
            o.isReady = true;
        }
    }
}

AjaxRequest.prototype.instances = new Array();

/**
 * convert a XML response via AJAX to a standard JavaScript-Array
 *
 * @param   $xml  XmlNode
 * @return  array
 */
function yanaXmlDecode($xml)
{
    if (!$xml) {
        return new Array();
    }

    var result = new Array();
    var node = null;

    for (var i = 0; i < $xml.childNodes.length; i++)
    {
        node = $xml.childNodes[i];
        if (node.nodeType == 1)
        {
            var id = node.getAttribute('id');
            var nodeValue = null;
            if (node.hasChildNodes()) {
                nodeValue = node.firstChild.nodeValue;
            }

            switch (node.nodeName.toLowerCase())
            {
                case 'array': case 'object':
                    result[id] = yanaXmlDecode(node);
                break;
                case 'bool': case 'boolean':
                    if (nodeValue == 'true') {
                        result[id] = true;
                    } else if (nodeValue == 'false') {
                        result[id] = false;
                    } else {
                        result[id] = (nodeValue) ? true : false;
                    }
                break;
                case 'int': case 'integer':
                    result[id] = parseInt(nodeValue);
                break;
                case 'float': case 'double':
                    result[id] = parseFloat(nodeValue);
                break;
                default:
                    result[id] = nodeValue;
                break;
            }
        }
    }

    return result;
}

/**
 * convert a SML response via AJAX to a standard JavaScript-Array
 *
 * @param   $sml  string
 * @return  array
 */
function yanaSmlDecode($sml)
{
    if (typeof $sml == 'string') {
        $sml = $sml.split("\n");
    }
    var result = new Array();
    var line = null;
    var m = null;
    var depth = 0;
    var buffer = new Array();
    var tagName = "";
    var rootName = "";

    for (var i = 0; i < $sml.length; i++)
    {
        line = $sml[i];
        if (depth > 0) {
            buffer[buffer.length] = line;
        }

        m = line.match(/^\s*<(\w[^>]*)>/);
        if (m) {

            tagName = m[1];

            /* START TAG */
            m = line.match(/^\s*<\w[^>]*>(.*?)<\/\w[^>]*>\s*$/);
            if (m) {
                if (depth == 0) {
                    /* CDATA */
                    if (m[1] == "true") {
                        result[tagName] = true;
                    } else if (m[1] == "false") {
                        result[tagName] = false;
                    } else if (m[1] != "") {
                        result[tagName]= m[1];
                    }
                }
            } else {
                if (depth == 0) {
                    rootName = tagName;
                }
                depth++;
            }

        } else {
            m = line.match(/<\/\w[^\/>]*?>/);
            if (m) {
                /* END TAG */
                if (depth == 1) {
                    result[rootName] = yanaSmlDecode(buffer);
                    rootName = "";
                    buffer = new Array();
                }
                depth--;
            }
        }

    }
    return result;
}
