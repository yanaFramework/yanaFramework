/**
 * for internal use only
 *
 * @author   Thomas Meyer
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

function show(i)
{
    if (i < 0) {
        i = 0;
    }
    
    /* deactivate all */
    var j = 0;
    o = document.getElementById('page' + j++);
    while (o)
    {
        o.className = 'search_invisible';
        o = document.getElementById('page' + j++);
    }

    if (i >= j) {
        i = j - 1;
    }

    /* activate selected */
    o = document.getElementById('page' + i);
    if (o) {
        o.className = 'search_visible';
    }    
}

function whatsRelated()
{
    var infoSeite = window.open(language['whatsrelated.html'], "_blank", "width=480, height=500, scrollbars=yes");
    infoSeite.caller = this;
    infoSeite.focus();
}

function about()
{
    var infoSeite = window.open(language['about.html'], "_blank", "width=480, height=500, scrollbars=yes");
    infoSeite.caller = this;
    infoSeite.focus();
}
