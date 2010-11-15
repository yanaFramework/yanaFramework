seiten = new Array();
ausgabe = new Array();

var trefferProSeite = 10;
var global = (document.location.href).substring(0, (document.location.href).lastIndexOf("/")) + "/";
var datasource = global + "suchdaten.zip";
var suchbegriffe = new Array();

datasource = datasource.replace("|", ":"); // Netscape 4
datasource = datasource.replace("file://localhost/", "file:///"); // Opera

var selLang = 'de';

if (m = top.location.href.match(/\Wlang=(\w+)/i)) {
    if (lang[m[1]]) {
        selLang = m[1];
    }
}
lang[selLang].list = '';
for (i in lang)
{
    lang[selLang].list += '<a target="_top" href="javascript:top.document.location.search=\'lang=' + i + '\'">' + lang[i].lang + '</a>';
}
lang = lang[selLang];

window.top.document.title = document.title = lang.title;

/********************************************************************************************/

function initiate()
{
    document.searchApplet.initiate(datasource);
};

function suche(trefferArray, anfrage)
{
    ausgabe = new Array();
    ausgabe = (new String(trefferArray)).split("\n ");
    suchbegriffe = anfrage.split(" ");

    for (var i = 1; i < ausgabe.length; i++)
    {
        ausgabe[i] = (ausgabe[i]).split(",");
    };
    out(1);
};

function out(beginn)
{
    var neueSeite = window.open('', 'searchmain');
    neueSeite.document.clear();
    neueSeite.document.writeln(head);
    neueSeite.document.writeln('<div class="searchterms">' + lang.term.replace(/%TERM%/i, ausgabe[0])  + '</div>');
    neueSeite.document.writeln(suchErgebnis(beginn));
    neueSeite.document.writeln(foot);
    neueSeite.document.close();
};

var head =
  '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"\n\n>' +
  '<html>\n\n' +
  '<head>\n' +
  '  <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">\n' +
  '  <title>' + lang.title +  '</title>\n' +
  '  <link rel="stylesheet" type="text/css" href="data/index.css">\n' +
  '  <style type="text/css">.copyright { color: #000; font-size: 9pt; font-family: \'Arial\', \'Helvetica\', color: #000;' +
  'background: #e0e8ff; padding: 5px; margin: 5px 0px; border-bottom: 1px solid steelblue; text-decoration: none}</style>\n' +
  '</head>\n\n' +
  '<body>';

var foot =
  '<p class="copyright">powered by <a href="http://yanaframework.net" target="_blank">yanaframework.net</a></p>\n' +
  '</body>\n\n' +
  '</html>';

function suchErgebnis(beginn)
{
    var ende = null;
    if (beginn + trefferProSeite > ausgabe.length)
    {
        ende = ausgabe.length;
    } else {
        ende = beginn + trefferProSeite;
    }
    var ergebnis = "";

    if (ausgabe.length > trefferProSeite) {
        ergebnis += '<table align="center" border="0" class="pagelist">' +
        '<colgroup><col width="20%"><col width="40%"><col width="20%"></colgroup>\n' +
        '<tr><td style="text-align:right;" class="prevpage">\n';
        if (beginn > 1) {
            ergebnis += '<a href="javascript:eval(parent.searchform.out('+((beginn-trefferProSeite > 0) ? beginn-trefferProSeite : 0)+'))">' + lang.prev + '</a>';
        } else {
            ergebnis += '&nbsp;';
        };
        ergebnis += '</td><td style="text-align:center;" class="currentpage">' +
        lang.view.replace(/%FIRST%/i, beginn).replace(/%LAST%/i, (ende -1)).replace(/%ALL%/i, (ausgabe.length -1)) +
        '\n</td><td class="nextpage">';
        if (ende != ausgabe.length) {
            ergebnis += '<a href="javascript:eval(parent.searchform.out('+ende+'))" align="left">' + lang.next + '</a>';
        } else {
            ergebnis += '&nbsp;';
        };
        ergebnis += '</td></tr></table>\n';
    };

    ergebnis += '<table align="center" border="0"><tr><td>\n';
  
      for (var i = beginn; i < ende; i++)
      {
          ergebnis += '<div class="doc">' +
              '<div class="doctitle"><a href="' + (
                  (document.dir != null && document.body.innerHTML) ?
                  'javascript:eval(parent.searchform.mark(\''+ escape((ausgabe[i])[0]) +'\'))' :
                  (ausgabe[i])[0]
              ) +
              '" target="_self">'+ (ausgabe[i])[1] +'</a></div>' +
              '<div class="docdescription">'+ (ausgabe[i])[2] +'</div>' +
              '<div class="docurl">'+ (ausgabe[i])[0] +'</div>' +
              '</div>\n';
      };

    ergebnis += '</td></tr></table>\n';
    return ergebnis;
};

var applet =
  '<applet id="searchApplet" code="suche" archive="suche.jar" width="1" height="1" style="float: left"><param name="database" value="'+datasource+'"></applet>';

var searchform =
  '<form id="seachform" onsubmit="suche(document.getElementById(\'searchApplet\').suchen(document.getElementById(\'searchterm\').value),document.getElementById(\'searchterm\').value); return false;">\n' +
  '<input id="searchterm" name="searchterm" type="text">&nbsp;<input type="submit" value="' + lang.search + '">\n' +
  '<a class="help" href="javascript:about()">' + lang.help + '</a>\n' +
  '<div class="language">' + lang.sel.replace(/%LIST%/i, lang.list) + '</div>\n' +
  '</form>';

function mark(page)
{
    parent.frames['searchmain'].location.replace(unescape(page));
    for (var i = 0; i < suchbegriffe.length; i++)
    {
        setTimeout('if (document.dir != null && parent.searchmain.document.body.innerHTML) { parent.searchmain.document.body.innerHTML = parent.searchmain.document.body.innerHTML.replace(/((^|>)[^<]*?)([\\w&#;]*'+suchbegriffe[i]+'[\\w&#;]*)([^<]*?(<|$))/ig, \'$1<span style=\"background:#ff0;color:#000;\">$3</span>$4\'); };', 1000);
    }
};

function related()
{
    var infoSeite = window.open(lang.dir + "/related.html", "yanaSearchRelated", "width=350, height=500, scrollbars=yes");
    infoSeite.focus();
};

function about()
{
    var infoSeite = window.open(lang.dir + "/about.html", "yanaSearchAbout", "width=350, height=500, scrollbars=yes");
    infoSeite.focus();
};

// © 2008, Thomas Meyer yanaframework.net