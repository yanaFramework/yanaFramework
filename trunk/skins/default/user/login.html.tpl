<!DOCTYPE html>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
        <link rel="stylesheet" type="text/css" href="../styles/default.css"/>
        <link rel="stylesheet" type="text/css" href="../styles/btn.css"/>
    </head>
    <body>
        <form method="post" enctype="multipart/form-data" action="{$PHP_SELF}" id="enter_pwd_form">
            <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
            <input type="hidden" name="id" value="{$ID}"/>
            <input type="hidden" name="action" value="check_login"/>

            <h1>{lang id="USER.14"}</h1>
      
            <div class="label">
                <label>
                    {lang id="USER.16"}:
                    <input type="text" title="{lang id="USER.7"}" size="16" name="user" maxlength="20"/>
                </label>
          
                <label style="margin-left: 25px;">
                    {lang id="USER.15"}:
                    <input type="password" title="{lang id="USER.8"}" size="16" name="pass" maxlength="20"/>
                </label>
            </div>

            <div style="margin: 20px">
                <input type="submit" title="{lang id="USER.9"}" value="{lang id="OK"}"/>&nbsp;<input type="button" title="{lang id="TITLE_ABORT"}" value="{lang id="BUTTON_ABORT"}" onclick="history.back()"/>
                <a style="margin-left: 10px;" onclick="var http=new AjaxRequest(this.href);http.setTarget('reset_pwd_form');http.send('');document.getElementById('enter_pwd_form').style.display='none';return false" href={"action=set_lost_pwd"|href} class="label">{lang id="user.35"}</a>
            </div>
        </form>
        <div id="reset_pwd_form"></div>
    </body>
</html>
