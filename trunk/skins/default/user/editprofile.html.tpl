<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
        <link rel="stylesheet" type="text/css" href="../styles/admin.css"/>
    </head>

<body>

<div class="config_form" id="user_profile">

<!-- BEGIN: table -->

  <div class="config_head">
      <div class="config_title" onclick="yanaToggleMenu(this.parentNode)">{$USER.USER_ID}{lang id="USER.PROFIL.0"}{* Foo's Profil *}</div>
  </div>

  <div class="help">
      <div class="help_text">
{import file="_thead.html.tpl"}
      </div>
  </div>

  <div class="option">

    <div class="optionbody">
      {create file="user_admin" id="userdetails" template="1"}

      <fieldset class="label" style="text-align: center; margin: auto;">
        <legend>{lang id="FORMAT_TEXT"}</legend>
        {embeddedTags show="b,i,u,h,emp,img,url,mail,mark,color,smilies"}
        {preview}
      </fieldset>
    </div>

  </div>

<!-- END: table -->

</div>

</body>

</html>