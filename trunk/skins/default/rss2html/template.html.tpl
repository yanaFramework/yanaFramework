<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
    </head>

<body>

<div align="center">

    <div style="float: left;">
      <a target="_blank" href="{$FILE}"><img alt="RSS" align="left" border="0" src="rss.png" hspace="5" width="28" height="13"/></a>
    </div>
    
    {foreach item=ENTRY from=$RSS}
    
    <div style="border: 3px double #DDD; background: #EEE; width: 50%; margin: 10px; padding: 10px;">

        <p style="font-size: large; font-weight: 800;">
            {if $ENTRY.LINK}<a href={$ENTRY.LINK}>{/if}
            {$ENTRY.TITLE}
            {if $ENTRY.LINK}</a>{/if}
        </p>

        <p align="justify">{$ENTRY.DESCRIPTION}</p>

        <p style="font-size: x-small; font-style: italic; text-align: left;">
            {if $ENTRY.AUTHOR}({$ENTRY.AUTHOR}){/if}
            {if $ENTRY.PUBDATE}{$ENTRY.PUBDATE}{/if}
        </p>

    </div>

    {/foreach}

</div>

</body>
</html>
