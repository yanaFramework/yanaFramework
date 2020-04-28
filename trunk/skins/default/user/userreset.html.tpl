<!DOCTYPE html>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
        <link rel="stylesheet" type="text/css" href="../styles/default.css"/>
        <link rel="stylesheet" type="text/css" href="../styles/btn.css"/>
    </head>
    <body>
        <h2>{lang id="user.35"}</h2>
        <form method="post" action="{$PHP_SELF}" title="{lang id="user.35"}" onsubmit="return config_pass('{lang id="USER.13"}')">
            <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
            <input type="hidden" name="id" value="{$ID}"/>
            <input type="hidden" name="action" value="reset_pwd"/>
            <input type="hidden" name="key" value="{$key}">
            {import id="PASSWORD_TEMPLATE"}
        </form>
    </body>
</html>
