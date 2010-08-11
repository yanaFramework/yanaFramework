/**
 * for internal use only
 *
 * @author   Thomas Meyer
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

window.yanaSdkCurrentUtility = 1;
window.yanaSdkCurrentSection = 1;
window.yanaSdkCurrentPage = 1;
window.yanaSdkShowHelp = true;

function showToc2(id1)
{
    var o = document.getElementById('toc2').firstChild;
    while (o.nextSibling)
    {
        if (o && o.nodeType == 1) {
            if (o.id == 'page_' + id1) {
                o.style.display = 'block';
            } else {
                o.style.display = 'none';
            }
        }
        o = o.nextSibling;
    }
    window.yanaSdkCurrentUtility = id1;
}

function showToc3(id1, id2)
{
    var o = document.getElementById('toc3').firstChild;
    while (o.nextSibling)
    {
        if (o && o.nodeType == 1) {
            if (o.id == 'page_' + id1 + '_' + id2) {
                o.style.display = 'block';
            } else {
                o.style.display = 'none';
            }
        }
        o = o.nextSibling;
    }
    window.yanaSdkCurrentSection = id2;
}

function showUtility(id1)
{
    var o = document.getElementById('pages').firstChild;
    while (o.nextSibling)
    {
        if (o && o.nodeType == 1) {
            if (o.id == 'utility_' + id1) {
                o.style.display = 'block';
            } else {
                o.style.display = 'none';
            }
        }
        o = o.nextSibling;
    }
}

function showPage(id1, id2, id3, direction)
{
    if (!id1) {
        id1 = window.yanaSdkCurrentUtility;
    }
    var o = document.getElementById('utility_' + id1);
    if (direction) {
        o = o.lastChild;
    } else {
        o = o.firstChild;
    }
    var found = false;
    var m = null;
    while (typeof o != 'undefined')
    {
        if (o && o.nodeType == 1) {
            if (o.className == 'page_global' || o.className == 'section_title') {
                /* intentionally left blank */
            } else if (!found && id2 == 0  && id3 == 0 && (m = (o.id).match(/page_\d+_(\d+)_(\d+)/))) {
                id2 = m[1];
                showToc3(id1, id2);
                id3 = m[2];
                found = true;
                o.style.display = 'block';
            } else if (!found && o.id == 'page_' + id1 + '_' + id2 + '_' + id3) {
                found = true;
                o.style.display = 'block';
            } else {
                o.style.display = 'none';
            }
        }
        if (direction) {
            if (o.previousSibling) {
                o = o.previousSibling;
            } else {
                break;
            }
        } else {
            if (o.nextSibling) {
                o = o.nextSibling;
            } else {
                break;
            }
        }
    }
    window.yanaSdkCurrentPage = id3;
}

function show(id1, id2, id3, direction)
{
    if (!id1) {
        id1 = window.yanaSdkCurrentUtility;
    } else {
        window.yanaSdkCurrentUtility = id1;
    }
    if (!id2) {
        id2 = window.yanaSdkCurrentSection;
    } else {
        window.yanaSdkCurrentSection = id2;
    }
    if (!id3) {
        id3 = window.yanaSdkCurrentPage;
    } else {
        window.yanaSdkCurrentPage = id3;
    }
    if (typeof direction == 'undefined') {
        direction = false;
    }
    showToc2(id1);
    showToc3(id1, id2);
    showUtility(id1);
    showPage(id1, id2, id3, direction);
    var o1 = document.getElementById('btn_next');
    var o2 = document.getElementById('btn_previous');
    if (o1) {
        o1.disabled = false;
    }
    if (o2) {
        o2.disabled = false;
    }
    return;
}

function firstPage()
{
    show(0, 1, 1);
}

function lastPage()
{
    showPage(0, 0, 0, true);
    show(0, 0, 0);
}

function next()
{
    browse(+1, 'btn_next');
}

function previous()
{
    browse(-1, 'btn_previous');
}

function browse(offset, id)
{
    var pageList = new Array();
    var o = document.getElementById('utility_' + window.yanaSdkCurrentUtility).firstChild;
    while (o.nextSibling)
    {
        if (o && o.nodeType == 1) {
            if ((m = o.id.match(/page_\d+_(\d+)_\d+/))) {
                if (pageList[m[1]]) {
                    pageList[m[1]]++;
                } else {
                    pageList[m[1]] = 1;
                }
            }
        }
        o = o.nextSibling;
    }

    var id1 = window.yanaSdkCurrentUtility;
    var id2 = window.yanaSdkCurrentSection;
    var id3 = window.yanaSdkCurrentPage;
    var o1 = document.getElementById(id);
    var isBoundage = false;

    if (document.getElementById('page_' + id1 + '_' + id2 + '_' + (id3 + offset))) {
        show(id1, id2, id3 + offset);
    } else if (document.getElementById('page_' + id1 + '_' + (id2 + offset) + '_' + 1)) {
        id2 += offset;
        if (offset > 0) {
            show(id1, id2, 1);
        } else {
            if (pageList[id2]) {
                show(id1, id2, pageList[id2]);
            } else {
                show(id1, id2, 1);
            }
        }
    } else {
        isBoundage = true;
    }

    if (o1) {
        o1.disabled = isBoundage;
    }
}

function addInterface()
{
    if (document.getElementById('interface_action').value == "") {
        alert(language[0]);
        return false; 
    }
    var o = document.getElementById('interface');
    var newInterface = "";

    newInterface += document.getElementById('interface_action').value + ',';
    newInterface += document.getElementById('interface_type').value + ',';
    newInterface += document.getElementById('interface_template').value + ',';
    newInterface += document.getElementById('interface_group').value + ',';
    newInterface += document.getElementById('interface_role').value + ',';
    newInterface += document.getElementById('interface_permission').value + ',';
    newInterface += document.getElementById('interface_menu').value + "\n";

    o.value += newInterface;

    addToPreview(newInterface);
    return true;
}

function addToPreview(entry)
{
    var preview = document.getElementById('interface_preview');
    document.getElementById('table_preview').parentNode.style.visibility = 'inherit';
    var mytr     = new Object();
    var mytd     = new Object();
    var mytext = new String();

    var entries = entry.split("\n");
    for (var i=0; i<entries.length; i++) 
    {
        entries[i] = (entries[i]).split(",");

        if (entries[i][0]) {
            mytr = document.createElement('tr');
    
            for (var j=0; j<(entries[i]).length; j++) {
    
                mytd = document.createElement('td');
    
                if (j==2) {
                    if (entries[i][j]==1)
                    mytext = document.createTextNode(language[1]);
                    else
                    mytext = document.createTextNode('default');
                } else {
                    mytext = document.createTextNode(entries[i][j]);
                }
                mytd.appendChild(mytext);
                mytr.appendChild(mytd);
    
            }
    
            preview.appendChild(mytr);
        }

    }
    
}

function toggleHelp(icon)
{
    icon.className = (window.yanaSdkShowHelp) ? 'icon_info' : 'icon_info_hover';
    var pTags = document.getElementsByTagName('p');
    for (var i = 0; i < pTags.length; i++)
    {
        if (pTags[i].className == 'help') {
            pTags[i].style.display = (window.yanaSdkShowHelp) ? 'none' : 'block';
        }
    }
    window.yanaSdkShowHelp = !window.yanaSdkShowHelp;
    return false;
}
