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
        {if !empty($PROFILE.LOGO)}
            <div id="index_logo"><img border="0" alt="" src='{$PROFILE.LOGO}'/></div>
        {/if}
        {applicationBar}
        {toolbar}

{import id="STDOUT" STDOUT=$STDOUT}

{import template=$SYSTEM_INSERT}

</body>

</html>
