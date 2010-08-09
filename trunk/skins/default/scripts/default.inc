<?php
/**
 * <<JavaScript>> common functions
 *
 * This file provides some basic JavaScript functions used for various features of the Yana framework.
 *
 * @author      Thomas Meyer
 * @package     yana
 * @subpackage  javascript
 * @license     http://creativecommons.org/licenses/by-nc-sa/2.5/
 */

/**
 * <<JavaScript>> open / close a node of a html-menu
 *
 * This function is used for userdefined, vertical treemenus in HTML.
 * It opens or closes a folder, depending on it's current state.
 *
 * An example HTML code for such menu is included in the documentation
 * of template functions.
 * See the manual on chapter "skins and templates: expandable HTML-menues".
 * (toc might be subject to change)
 *
 * @access  public
 * @name    yanaMenu()
 * @param   HtmlNode  $node
 * @param   string    $defaultDisplay  should be 'none' or 'block', defaults to 'block'
 */
function yanaMenu($node, $defaultDisplay)
{
}

/**
 * <<JavaScript>> restore or remember which menu items are opened or closed
 *
 * @access  public
 * @name    yanaMenuCookie()
 * @param   bool  $set  true = remember items, false = restore items
 */
function yanaMenuCookie($set)
{
}

/**
 * <<JavaScript>> close all nodes of a html-menu
 *
 * see the manual on chapter "skins and templates: expandable HTML-menues"
 * (toc might be subject to change)
 *
 * @access  public
 * @name    yanaCloseAll()
 */
function yanaCloseAll()
{
}

/**
 * <<JavaScript>> set a cookie-var
 *
 * Set the var identified by $key to the new value $val.
 * Returns bool(true) on success and bool(false) on error.
 *
 * @access  public
 * @name    yanaSetCookie()
 * @param   string  $key  name
 * @param   string  $val  value
 * @return  bool
 */
function yanaSetCookie($key, $val)
{
}

/**
 * <<JavaScript>> get a cookie-var
 *
 * Returns the cookie's content.
 *
 * If $key is not provided, it returns all values as an associative array,
 * with names of the entries being the keys.
 *
 * Else the value with the name $key is returned instead.
 * If the value does not exist, the constant NULL is returned.
 *
 * @access  public
 * @name    yanaGetCookie()
 * @param   string  $key  name
 * @return  mixed
 */
function yanaGetCookie($key = null)
{
}

/**
 * <<JavaScript>> check if a string in a textarea field exceeds the maximum length
 *
 * Returns bool(false) if maximum length is exceeded and
 * bool(true) otherwise.
 *
 * @access  public
 * @name    yanaMaxLength()
 * @param   TextareaNode  $node    node that should be checked
 * @param   int           $length  maximum length
 * @param   event         $event   event of type 'keypress'
 * @return  bool
 */
function yanaMaxLength($node, $length, $event)
{
}

/**
 * <<JavaScript>> show html preview of a text
 *
 * Note that this function is deprecated.
 * You should use the template function [%preview%] instead.
 *
 * @access  public
 * @name    preview()
 * @param   string  $target
 * @param   string  $source
 * @deprecated  since 2.9.3
 */
function preview($target, $source)
{
}

/**
 * <<JavaScript>> add an emot. icon to message pane
 *
 * This inserts the text $icon in the currently selected textarea field.
 *
 * If the second argument is an event object, the function will prevent
 * it from firing the default event handler. This argument is optional.
 *
 * You may also want to see the manual on chapter "templates and skins:
 * new functions - smilies" (toc might be subject to change)
 *
 * @access  public
 * @name    yanaAddIcon()
 * @param   string  $icon
 * @param   event   $event  event object
 */
function yanaAddIcon($icon, $event = null)
{
}

/**
 * <<JavaScript>> add a custom event listener to a HTML node
 *
 * This registers an user defined javascript event listener.
 *
 * This function allows you to:
 * <ol>
 * <li>add multiple event listeners for the same node and event type (e.g. you can set forms[0].onsubmit to trigger multiple functions)</li>
 * <li>add an event listener to all tags of a certain name (e.g. add an input handling function to the "onchange" event of all textarea tags)</li>
 * </ol>
 *
 * The argument $eventType defines the type of event to subscribe to. It can be any of the following:
 * <ol>
 * <li>onabort</li>
 * <li>onblur</li>
 * <li>onchange</li>
 * <li>onclick</li>
 * <li>ondblclick</li>
 * <li>onerror</li>
 * <li>onfocus</li>
 * <li>onkeydown</li>
 * <li>onkeypress</li>
 * <li>onkeyup</li>
 * <li>onload</li>
 * <li>onmousedown</li>
 * <li>onmousemove</li>
 * <li>onmouseout</li>
 * <li>onmouseover</li>
 * <li>onmouseup</li>
 * <li>onreset</li>
 * <li>onselect</li>
 * <li>onsubmit</li>
 * <li>onunload</li>
 * </ol>
 *
 * Note that - even though you may set an event handler on any node - some of them are limited to certain tags.
 * E.g. "onsubmit" is limited to "form" tags. See your favourite JavaScript reference for more details.
 *
 * You are best adviced NOT to use proprietary event types like "ondrag" or "oncontextmenu".
 * However: if you do, the function will present you with a warning. To surpress this warning, set the argument
 * $silent to bool(true).
 *
 * The argument $userFunction is an existing function name.
 * Note that this function will be given 2 arguments. The first is the event object.
 * The second is a reference to the target node.
 * (While you may also get the target node using event.target in Firefox and Opera, this is not supported in Internet Explorer.
 * So this should work in both)
 *
 * A brief example:
 * <code>
 * function myEventHandler($event, $node)
 * {
 *     if ($event.type == 'change') {
 *         alert("New value = " + $node.value);
 *         return true;
 *     } else {
 *         return false;
 *     }
 * }
 * yanaAddEventListener('onchange', myEventHandler, 'textarea');
 * </code>
 *
 * Note that returning bool(false) will stop propagation of the event.
 *
 * The argument $node has 2 synopsis.
 * <ol>
 * <li>If $node is a node object, like document.forms[0], the event listener listens to events on this node only.</li>
 * <li>If $node is a tag name, like 'form', the event listener listens to all nodes which have the specified tag name.</li>
 * </ol>
 *
 * Note that this function respects event handlers defined in the HTML code.
 *
 * Where multiple event handlers are present, the behaviour is as follows:
 * <ol>
 * <li>call event handler defined in HTML code (if any)</li>
 * <li>call any user defined event handler in the order in which they were registered</li>
 * </ol>
 *
 * Note: if any of the called functions return a value that evaluates to bool(false), the event handler
 * will stop propagation of the event and return bool(false).
 * E.g. when handling form.onsubmit with multiple functions, that all checking the form's contents,
 * then the form will not submit if ANY of the included checks fails.
 *
 * Returns bool(true) on success and bool(false) on error.
 *
 * @access  public
 * @name    yanaAddEventListener()
 * @param   string           $eventType     e.g. 'onchange', 'onmouseover', aso.
 * @param   function         $userFunction  some user function
 * @param   string|HtmlNode  $node          a tag name, like 'input', or a HTML node, like document.forms[0]
 * @param   bool             $silent        mute warning for proprietary event types
 * @return  bool
 * @since   2.9.6
 */
function yanaAddEventListener ($eventType, $userFunction, $node, $silent)
{
}

/**
 * <<JavaScript>> check for correct syntax of embedded tags in all textarea fields
 *
 * Call this on a HTML form to check all included fields.
 * This function will issue a warning to the user, if a textarea field contains an error
 * and ask the user to confirm, if he wants to proceed anyway.
 * You may mute this warning by setting $silent to bool(true).
 *
 * Returns bool(true) if all fields are valid (or the user confirms to proceed with errors) and bool(false) otherwise.
 *
 * Example of usage:
 * <code>
 * <form onsubmit="return yanaCheckEmbTags(event, this, false)">
 * </code>
 *
 * @access  public
 * @name    yanaAddEventListener()
 * @param   event     $event     (ignored)
 * @param   HtmlNode  $formNode  HTML form to check
 * @param   bool      $silent    mute warning for syntax errors
 * @return  bool
 * @since   2.9.6
 */
function yanaCheckEmbTags($event, $formNode, $silent)
{
}

/**
 * <<JavaScript>> return all elements that match a given class name
 *
 * This function returns a node list containing all nodes,
 * whose class name is equal to the one given.
 *
 * Note: this function may also be called as
 * document.getElementsByClassName($className) - if no such
 * function exists, it is created.
 *
 * This is meant to be an addition to the well known standard
 * functions getElementById() and getElementsByTagName().
 *
 * The second argument allows you to use any other root node
 * then document.body, which makes you able to search
 * through sub-trees.
 *
 * @access  public
 * @name    yanaGetElementsByClassName()
 * @param   string    $className   name of class attribute
 * @param   HtmlNode  $node        root node
 * @return  array
 * @since   2.9.6
 */
function yanaGetElementsByClassName($className, $node = 'document.body')
{
}
?>
