<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
        <script type="text/javascript" language="JavaScript" src="../styles/dynamic-styles.js"></script>
        <script type="text/javascript" language="JavaScript" src="../styles/admin-styles.js"></script>
        <link rel="stylesheet" type="text/css" href="../styles/config.css"/>
        <link rel="stylesheet" type="text/css" href="../styles/user.css"/>
        <link rel="stylesheet" type="text/css" href="../styles/admin.css"/>
    </head>

<body>

<!-- BEGIN: table -->

<div class="config_form">

  <!-- BEGIN: option -->

  <div class="config_head">
      <div class="config_title" onclick="yanaToggleMenu(this.parentNode)">{lang id="USER.26"}{* Neuen Nutzer anlegen *}</div>
  </div>

  <div class="help">
      <div class="help_text">
          <p>{lang id="HELP.MAIL"}</p>
          <p>{lang id="HELP.PASSWORD"}</p>
      </div>
  </div>

  <form method="post" enctype="multipart/form-data" action="{$PHP_SELF}" class="option" style="text-align: center; padding: 30px;">
      <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
      <input type="hidden" name="id" value="{$ID}"/>
      <input type="hidden" name="action" value="set_user_mail"/>

      <!-- BEGIN: section -->

      <div class="optionbody">

        <label class="optionitem">
          <span class="label">{lang id="USER.27"}:</span>
          <input type="text" size="16" name="mail" maxlength="256" id="text_mail"/>
        </label>

        <label class="optionitem">
          <span class="label">{lang id="USER.28"}:</span>
          <input type="text" size="16" name="username" maxlength="256" id="text_username"/>
        </label>

      </div>

      <div style="margin-top: 10px;">
        <input type="submit" value="{lang id="OK"}"/>
        <input type="button" title="{lang id="TITLE_ABORT"}" value="{lang id="BUTTON_ABORT"}" onclick="history.back()"/>
      </div>

      <!-- END: section -->

  </form>

  <!-- END: option -->

</div>

<!-- END: table -->
</body>

</html>
