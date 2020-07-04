function show(id)
{
    showNone();
    var o = document.getElementById(id);
    if (o && o.className == 'hidden-page') {
        o.className = 'page';
    }
    documentTitle(o);
}
function showNone()
{
    var o = document.getElementById('pages').firstChild;
    do
    {
        if (o.className == 'page') {
            o.className = 'hidden-page';
        }
        o = o.nextSibling;
    } while (o)
}
function firstPage()
{
    showNone();
    var o = document.getElementById('pages').firstChild;
    do
    {
        if (o.className == 'hidden-page') {
            o.className = 'page';
            documentTitle(o);
            break;
        }
        o = o.nextSibling;
    } while (o)
    document.getElementById('previous').disabled = true;
}
function lastPage()
{
    showNone();
    var o = document.getElementById('pages').lastChild;
    do
    {
        if (o.className == 'hidden-page') {
            o.className = 'page';
            documentTitle(o);
            break;
        }
        o = o.previousSibling;
    } while (o)
    document.getElementById('next').disabled = true;
}
function next()
{
    document.getElementById('previous').disabled = false;
    var o = document.getElementById('pages');
    var test = false;
    if (o.firstChild) {
        o = o.firstChild;
        do
        {
            if (o.className == 'page') {
                o.className = 'hidden-page';
                if (o.nextSibling) {
                    o = o.nextSibling;
                    do
                    {
                        if (o.className == 'hidden-page') {
                            o.className = 'page';
                            test = true;
                            documentTitle(o);
                            break;
                        } else {
                            test = false;
                            o = o.nextSibling;
                        }
                    } while (o)
                    break;
                }
            }
            o = o.nextSibling;
        } while (o)
    }
    if (test === false) {
        lastPage();
    }
}
function previous()
{
    document.getElementById('next').disabled = false;
    var o = document.getElementById('pages').lastChild;
    var test = false;
    do
    {
        if (o.className == 'page') {
            o.className = 'hidden-page';
            o = o.previousSibling;
            do
            {
                if (o.className == 'hidden-page') {
                    o.className = 'page';
                    test = true;
                    documentTitle(o);
                    break;
                } else {
                    o = o.previousSibling;
                }
            } while (o)
            break;
        } else {
            o = o.previousSibling;
        }
    } while (o)
    if (test == false) {
        firstPage();
    }
}
function documentTitle(o)
{
    if (o && o.id && o.id.match(/^stop_/i)) {
        showButtonNext(false);
    } else {
        showButtonNext(true);
    }
    o = o.firstChild;
    var document_title = document.getElementById('document_title');
    if (o) {
        do
        {
            if (o.nodeName == 'H1' || o.nodeName == 'h1') {
                document_title.innerHTML = o.innerHTML;
                return true;
            } else if (o.firstChild) {
                if (documentTitle(o)) {
                    return true;
                }
            }
            o = o.nextSibling;
        } while (o);
    }
    return false;
}
function showButtonNext(newState)
{
    var o = document.getElementById('next');
    if (newState) {
        o.disabled = false;
    } else {
        o.disabled = true;
    }

}
function license_accept(id)
{
    var o = document.getElementById('stop_'+id);
    if (o) {
        o.id = id;
    }
    next();
}
function license_reject()
{
    alert(language['license_rejected']);
    window.location.href = language['php_self']+'?action=abort';
}
function menu(o)
{
    if (o) {
        if (o.className !== "item_closed") {
            o.className = "item_closed";
        } else {
            o.className = "item_open";
        }
    }
}
function install(id, list, nodes, pageId)
{
    showButtonNext(false);

    ajax = new AjaxRequest(language['php_self']);
    ajax.setHandler(installFinished);
    ajax.id = id;

    var param = "";
    if (list) {
        for (var i = 0; i < list.length; i++)
        {
            if (list[i].checked) {
                param += '&components[]='+escape(list[i].value);
            }
        }
    }
    animate_progressbar();
    if (nodes) {
        for (var i = 0; i < nodes.length; i++)
        {
            nodes[i].disabled = true;
        }
    }

    var o = document.getElementById('stop_'+pageId);
    if (o) {
        o.id = pageId;
    }
    ajax.send('?action=install'+param);
}
function abort()
{
    window.location.href = language['php_self']+'?action=abort';
}
function choose(nodelist, isChecked)
{
    if (isChecked) {
        isChecked = true;
    } else {
        isChecked = false;
    }
    if (nodelist) {
        for (var i = 0; i < nodelist.length; i++)
        {
            if (!nodelist[i].disabled) {
                nodelist[i].checked = isChecked;
            }
        }
    }
}
var timeout;
var animation_state = 13;
function installFinished()
{
    var o = AjaxRequest.prototype.instance.http;
    if (o.readyState == 4) {
        if(timeout) {
           window.clearTimeout(timeout);
        }
        animation_state = 13;
        var o1 = document.getElementById('progressbar');
        if (o1) {
            if (o.responseText.match(/^1/)) {
                o1.innerHTML = '<span id="install_success">'+language['install_success']+'</span>';
                showButtonNext(true);
            } else {
                o1.innerHTML = '<span id="install_failure">'+language['install_failure']+'</span>';
            }
            if (o.responseText.match(/^[10][\w\d-_\.\/]+\.\w{3,4}$/i)) {
                document.getElementById(ajax.id).src = o.responseText.replace(/^\d/, '');
                showDetails(ajax.id, true);
            }
        }
    }
}
function animate_progressbar()
{
    var o = document.getElementById('progressbar');
    if (animation_state == 13) {
        o.innerHTML = '';
        animation_state = 0;
    } else {
        o.innerHTML += '<img src="data/progressbar_slider01.png">';
        animation_state++;
    }
    if (o.style.display != 'block') {
        o.style.display = 'block';
    }
    timeout = window.setTimeout('animate_progressbar()', 500);
}
function showDetails(id, state)
{
    var o = document.getElementById(id);
    if (o && o.style) {
        if (state) {
            o.style.display = 'block';
        } else {
            o.style.display = 'none';
        }
    }
}
function adminPassword(id1, id2, pwdFrame, nodes)
{
    var o1 = document.getElementById(id1);
    var o2 = document.getElementById(id2);
    var o3 = document.getElementById(pwdFrame);
    if (o1 && o2 && o3) {
        if (o1.value == '' || o1.value.length < 8) {
            alert(language['pass_too_short']);
        } else if (o1.value != o2.value) {
            alert(language['pass_miss']);
        } else {
            showDetails(pwdFrame, true);
            if (nodes) {
                for (var i = 0; i < nodes.length; i++)
                {
                    nodes[i].disabled = true;
                }
            }
            o3.src = language['php_self']+'?action=admin&pass='+o1.value;
        }
    }
}
function test(id, details)
{
    var o = document.getElementById(id);
    if (o) {
        o.style.borderWidth = '1px';
        o.style.height = '60vh';
        showDetails(id, true);
        if (details) {
            o.src = language['php_self']+'?action=test&details=1';
        } else {
            o.src = language['php_self']+'?action=test&details=0';
        }
    }
}
function cleanup(id)
{
    var o = document.getElementById(id);
    if (o.checked) {
        document.location = language['php_self']+'?action=cleanup&start=1';
    } else {
        document.location = language['php_self']+'?action=cleanup&start=0';
    }        
}
function AjaxRequest(url)
{
    if (typeof url == 'string') {
        this.url = url;
    } else {
        this.url = false;
        alert('Wrong argument type for argument 1 in constructor AjaxRequest. String expected, found "'+(typeof url)+'" instead.');
    }
    if (document.all){
        this.http = new ActiveXObject('Microsoft.XMLHTTP');
    } else {
        this.http = new XMLHttpRequest();
    }
    AjaxRequest.prototype.instance = this;
}
function AjaxRequest_send(args)
{
    if (!this.url) {
        alert('Cannot send request in AjaxRequest.send(). No target url specified.');
    } else {
        if (typeof args != 'string') {
            alert('Wrong argument type for argument 1 in method AjaxRequest.send(). String expected, found "'+(typeof args)+'" instead.');
            args = '';
        }
        this.http.open('get', this.url + args, true);
        this.http.onreadystatechange = this.handle;
        this.http.send(null);
    }
}
function AjaxRequest_setHandler(newHandler)
{
    if (! (typeof newHandler).match(/Function/i)) {
        alert('Wrong argument type for argument 1 in method AjaxRequest.setHandler(). Function expected, found "'+(typeof newHandler)+'" instead.');
    } else {
        this.handle = newHandler;
    }
}
function AjaxRequest_handle()
{
    var o = AjaxRequest.prototype.instance;
    if (o.http.readyState == 4) {
        alert(o.http.responseText);
    }
}
AjaxRequest.prototype.send = AjaxRequest_send;
AjaxRequest.prototype.handle = AjaxRequest_handle;
AjaxRequest.prototype.setHandler = AjaxRequest_setHandler;
var ajax = new Object();