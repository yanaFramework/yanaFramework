/**
 * for internal use only
 *
 * @author   Thomas Meyer
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

function config_pass ($message)
{
    if (document.getElementById('user_pwd_new').value == document.getElementById('user_pwd_repeat').value) {
        return true;
    } else {
        document.getElementById('user_pwd_new').className = 'invalid';
        document.getElementById('user_pwd_repeat').className = 'invalid';
        alert($message);
        return false;
    }
}

function config_usermode($o, $message, $className, $baseNode)
{
    if (typeof $o.currentMessage != 'undefined') {
        $message = $o.currentMessage;
    }
    $o.currentMessage = $o.title;
    $o.title = $o.innerHTML = $message;
    var nodes = yanaGetElementsByClassName($className, $baseNode);
    for (var i = 0; i < nodes.length; i++)
    {
        nodes[i].style.display = (nodes[i].style.display != 'none') ? 'none' : '';
    }
    var ajax = new AjaxRequest($o.href);
    ajax.setTarget('yana_stdout');
    ajax.send('');
}

function refresh_pluginlist($id, $url)
{
    var http = new AjaxRequest($url);
    http.setTarget($id);
    http.setHandler(function ($httpResponse, $htmlNode)
    {
        if ($httpResponse.responseText) {
            // flush event queue
            AjaxRequest.prototype.instances = new Array();
            var o = document.getElementById('yana_stdout');
            if (o) {
                o.innerHTML = $httpResponse.responseText;
            }
            // add new ajax request
            var http2 = new AjaxRequest(php_self);
            http2.setTarget($id);
            http2.send('action=index_plugins');
        }
    });
    http.send('');
}

function yanaPasswordSetWidth(isHelp)
{
    if (!isHelp) {
        var dimWidthInput = 140;
    } else {
        var dimWidthInput = 420;
    }
    $("#config_pass .floating_menu").css("width", dimWidthInput + 'px');
}

function yanaPasswordCheck(id)
{
    passWordSafetyMayClose = false;
    var o = $('#' + id);
    var val = o.val();

    // calculate security factor
    var securityLevel = 0;
    var maxSecurityLevel = 8;

    if (val.match(/\W/)) {
        securityLevel++;
    }

    if (val.length > 4) {
        securityLevel++;
    }

    if (val.length > 7) {
        securityLevel++;
    }

    if (val.length > 11) {
        securityLevel++;
    }

    if (val.match(/\d/)) {
        securityLevel++;
    }

    if (val.match(/\d\D+\d/)) {
        securityLevel++;
    }

    if (val.match(/[A-Z]/)) {
        securityLevel++;
    }

    if (val.match(/[A-Z][^A-Z]+[A-Z]/)) {
        securityLevel++;
    }

    var offsetInput = o.offset();
    var dimHeightInput = o.height();

    var progressBar = $("#config_pass .floating_menu");
    progressBar.show(500);
    var progressBarbar = progressBar.find(".progressbar");
    securityLevel = parseInt((securityLevel / maxSecurityLevel) * 100);
    progressBar.css('position','absolute');
    progressBar.css('top',offsetInput.top + dimHeightInput);
    progressBar.css('left',offsetInput.left);
    progressBarbar.css('background-position', '-' + (4 * securityLevel) + 'px 0px');
    progressBarbar.empty().append(securityLevel + "%");
}