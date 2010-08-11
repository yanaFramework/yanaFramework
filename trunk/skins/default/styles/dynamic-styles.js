/**
 * for internal use only
 *
 * @author   Thomas Meyer
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

var yanaCssIndex = -1;
if (document.styleSheets != null) {
    document.writeln('<style type="text/css">.help { display: none; } </style>');
    yanaCssIndex = document.styleSheets.length-1;
    document.styleSheets[yanaCssIndex].disabled=true;
}
function yanaToggleHelp()
{
    var i = 0;
    var list = null;
    if (yanaCssIndex != -1) {
        if (document.styleSheets[yanaCssIndex].disabled) {
            document.styleSheets[yanaCssIndex].disabled = false;
            list = yanaGetElementsByClassName('icon_info_hover');
            for (i = 0; i < list.length; i++)
            {
                list[i].className = 'icon_info';
            }
            return false;
        } else {
            document.styleSheets[yanaCssIndex].disabled = true;
            list = yanaGetElementsByClassName('icon_info');
            for (i = 0; i < list.length; i++)
            {
                list[i].className = 'icon_info_hover';
            }
            return true;
        }
    } else {
        alert('We are sorry! This function is not supported by your browser.');
    }
}
function yanaToggleMenu(o)
{
    var img1 = src + "styles/icon3.gif";
    var img2 = src + "styles/icon4.gif";
    var isOpen = true;

    if ((typeof o) != 'undefined') {
        /* get list of images */
        var nodeList = yanaGetElementsByClassName('config_toolbar_menu', o);
        /* set menu */
        while (o.nextSibling)
        {
            o = o.nextSibling;
            if (o.className == 'option') {
                if (o.style.display == 'none') {
                    o.style.display = 'block';
                    isOpen = true;
                } else {
                    o.style.display = 'none';
                    isOpen = false;
                }
                break;
            }
        }
        /* set image */
        for (var i = 0; i < nodeList.length; i++)
        {
            if (isOpen) {
                nodeList[i].src = img1;
            } else {
                nodeList[i].src = img2;
            }
        }
    }
}
function yanaInitConfigMenu()
{
    if (!document.body) {
        window.setTimeout('yanaInitConfigMenu()', 500);
        return;
    } else {
        var list = document.getElementsByTagName('div');
        for (var i = 0; i < list.length; i++)
        {
            if (!list[i].className.match(/(^| )config_head( |$)/)) {
                continue;
            }
            list[i].innerHTML ='<a href="javascript://" class="buttonize" onclick="yanaToggleMenu(this.parentNode)">' +
                '<span class="icon_slideup">&nbsp;</span></a>' +
                 '<a href="javascript://" class="buttonize" onclick="return yanaToggleHelp()">' +
                '<span class="icon_info_hover">&nbsp;</span></a>' +
                list[i].innerHTML;
        }
    }
}
yanaInitConfigMenu();