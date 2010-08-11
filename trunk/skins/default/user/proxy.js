/**
 * for internal use only
 *
 * @author   Thomas Meyer
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

/**
 * switch between beginner's and expert's mode
 */
function config_usermode($o, $message, $className, $baseNode)
{
    if (typeof $o.currentMessage != 'undefined') {
        $message = $o.currentMessage;
    }
    $o.currentMessage = $o.title;
    $o.title = $message;
    var nodes = yanaGetElementsByClassName($className, $baseNode);
    for (var i = 0; i < nodes.length; i++)
    {
        nodes[i].style.display = (nodes[i].style.display != 'none') ? 'none' : '';
    }
    var ajax = new AjaxRequest(php_self);
    ajax.setTarget('yana_stdout');
    ajax.send('action=config_usermode');
}