/**
 * for internal use only
 *
 * @author   Thomas Meyer
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

function yanaToggleOption(optionhead)
{
    var i = 0;
    var o = optionhead;
    var is_open = false;
    while (o.nextSibling != null && o.className != 'optionbody' && i < 500)
    {
        o = o.nextSibling;
        i++;
    }
    if (!o || !o.style) {
        return;
    }
    if (o.style.display == 'none') {
        is_open = true;
        o.style.display = 'block';
    } else {
        is_open = false;
        o.style.display = 'none';
    }
    o = optionhead;
    while (o.previousSibling != null && o.className != 'config_menu_closed' && o.className != 'config_menu_open' && i < 500)
    {
        o = o.previousSibling;
        i++;
    }
    if (!o || !o.style) {
        return;
    }
    if (is_open) {
        o.className = 'config_menu_open';
    } else {
        o.className = 'config_menu_closed';
    }
}

function yanaCloseConfig()
{
    var divList = document.getElementsByTagName('div');
    var spanList = document.getElementsByTagName('span');
    var i = 0;

    for (i=0; i<divList.length; i++)
    {
        if (divList[i].className == 'optionbody') {
            divList[i].style.display = 'none';
        }
    }

    for (i=0; i<spanList.length; i++)
    {
        if (spanList[i].className == 'config_menu_open') {
            spanList[i].className = 'config_menu_closed';
        }
    }
}
