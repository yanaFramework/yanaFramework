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

    <div class="white_box" style="float: right;">
      <img alt="" {if $USER.USERPROFILE_IMAGE && $USER.USERPROFILE_IMAGE_ACTIVE == 1}src={"action=get_profile_image&target="|cat:$USER.USER_ID|href}{else} src="{$DATADIR}userpic.gif" {/if} style="min-width: 30px; min-height: 30px; max-width: 320px; max-height: 320px"/>
    <!-- {if $USER.USERPROFILE_IMAGE && ! $USER.USERPROFILE_IMAGE_ACTIVE} -->
      <p class="comment">{lang id="USER.OPTION.18"}</p>
    <!-- {/if} -->
    </div>

    <div class="optionbody" style="padding: 30px;">


      <div id="user_description">
        {lang id="USER.OPTION.16"}:<br />
        {$USER.USERPROFILE_DESCRIPTION|embeddedTags|smilies}
      </div>

      <div style="margin: 20px; clear: both;">
        <input type="button" value="{lang id="BUTTON_PREVIOUS"}" onclick="history.back()"/>
      </div>

    </div>


  </div>

<!-- END: table -->

</div>


</body>

</html>
