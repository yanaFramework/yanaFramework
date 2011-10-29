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
<div align="center" id="skinSimple">

<table border="0" cellspacing="0" cellpadding="0" style="width: 90%;">
  <tr>
    <td height="24" width="24" valign="top" align="left"><img border="0" src="data/box-lo.gif" width="24" height="24"/></td>
    <td height="24" style="background-image: url('data/box-o.gif')">&nbsp;</td>
    <td height="24" width="24" valign="top" align="right"><img border="0" src="data/box-ro.gif" width="24" height="24"/></td>
  </tr>
  <tr>
    <td width="24" style="background-image: url('data/box-l.gif')">&nbsp;</td>
    <td bgcolor="#EEEEEE" align="center" valign="middle">

      <table border="0" cellspacing="0" cellpadding="0" style="width: 100%; height: 100%;">
          <tr>
            <td colspan="2" class="header" style="padding: 5px; text-align: left;">
              {applicationBar}
            </td>
          </tr>
          <tr>
            <td colspan="2" id="toolbar">
              {toolbar}
            </td>
          </tr>
          <tr>
            <td align="center">
{if !empty($PROFILE.LOGO)}
        <div><img border="0" alt="" src={$PROFILE.LOGO}/></div>
{/if}
{import id="STDOUT" STDOUT=$STDOUT}
{import file=$SYSTEM_INSERT}
            </td>
          </tr>
      </table>
    <td width="24" style="background-image: url('data/box-r.gif')">&nbsp;</td>
  </tr>
  <tr>
    <td height="24" width="24" valign="bottom" align="left"><img border="0" src="data/box-lu.gif" width="24" height="24"/></td>
    <td height="24" style="background-image: url('data/box-u.gif')">&nbsp;</td>
    <td height="24" width="24" valign="bottom" align="right"><img border="0" src="data/box-ru.gif" width="24" height="24"/></td>
  </tr>
</table>
</div>

</body>

</html>
