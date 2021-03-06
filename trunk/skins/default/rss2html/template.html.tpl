<!DOCTYPE html>

<html>

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
            {if !empty($ENTRY.LINK)}<a href={$ENTRY.LINK}>{/if}
            {$ENTRY.TITLE}
            {if !empty($ENTRY.LINK)}</a>{/if}
        </p>

        <p align="justify">{$ENTRY.DESCRIPTION}</p>

        <p style="font-size: x-small; font-style: italic; text-align: left;">
            {if !empty($ENTRY.AUTHOR)}({$ENTRY.AUTHOR}){/if}
            {if !empty($ENTRY.PUBDATE)}{$ENTRY.PUBDATE}{/if}
        </p>

    </div>

    {/foreach}

</div>

</body>
</html>
