<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
        <style type="text/css">
{import file="../default/default.css.tpl"}
        </style>
    </head>

<body>
<div align="center">

<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="24" width="24" valign="top" align="left"><img alt="" border="0" src="data/box-lo.gif" width="24" height="24"/></td>
    <td height="24" style="background-image: url('data/box-o.gif')">&nbsp;</td>
    <td height="24" width="24" valign="top" align="right"><img alt="" border="0" src="data/box-ro.gif" width="24" height="24"/></td>
  </tr>
  <tr>
    <td width="24" style="background-image: url('data/box-l.gif')">&nbsp;</td>
    <td align="center" valign="middle" style="padding: 10px; background: #C4C4C4;">
      <div class="header" style="margin: 10px; padding: 10px; text-align: left;">
              <div class="description" style="float: right">{visitorCount}&nbsp;&nbsp;&nbsp;&nbsp;</div>
              <img src="data/icon.gif" style="vertical-align: bottom; margin-right: 5px;" alt=""/>{lang id="PROGRAM_TITLE"}
              <div id="index_rss">{rss}</div>
              {if $DESCRIPTION}<div class="description"><img src="data/arrow.gif" alt="&bull;"/> {$DESCRIPTION|embeddedTags}</div>{/if}
      </div>
      <div id="toolbar">
          {toolbar}
      </div>
{import id="STDOUT" STDOUT=$STDOUT}
      <div align="center">{import file=$SYSTEM_INSERT}</div>
    </td>
    <td width="24" style="background-image: url('data/box-r.gif')">&nbsp;</td>
  </tr>
  <tr>
    <td height="24" width="24" valign="bottom" align="left"><img alt="" border="0" src="data/box-lu.gif" width="24" height="24"/></td>
    <td height="24" style="background-image: url('data/box-u.gif')">&nbsp;</td>
    <td height="24" width="24" valign="bottom" align="right"><img alt="" border="0" src="data/box-ru.gif" width="24" height="24"/></td>
  </tr>
</table>
</div>

</body>

</html>
