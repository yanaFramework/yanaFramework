<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
        <link rel="stylesheet" type="text/css" href="../styles/default.css"/>
        <link rel="stylesheet" type="text/css" href="../styles/btn.css"/>
    </head>

<body>
    <form method="post" enctype="multipart/form-data" action="{$PHP_SELF}">
      <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
      <input type="hidden" name="id" value="{$ID}"/>
      <input type="hidden" name="action" value="get_lost_pwd"/>

      <h2>{lang id="user.35"}</h2>

      <div class="label">
        <label class="pwd_lost_description">{lang id="user.27"}: </label>
          <label>
              <input type="text" title="email" size="16" name="user" maxlength="20"/>
          </label>
          <label>
              {lang id="security_image.title"} :
              {captcha}
          </label>
      </div>

      <div style="margin: 20px;">
          <input type="submit" title="{lang id="USER.9"}" value="{lang id="OK"}"/>&nbsp;<input type="button" title="{lang id="TITLE_ABORT"}" value="{lang id="BUTTON_ABORT"}" onclick="document.location.href='{'action=login'|url}'"/>
      </div>
    </form>
</body>
</html>
