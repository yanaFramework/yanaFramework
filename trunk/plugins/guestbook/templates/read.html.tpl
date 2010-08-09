<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
        <link rel="stylesheet" type="text/css" href="../styles/default.css"/>
    </head>

<body align="center" id="guestbook">
<!-- {assign var="ID" value=$ID|default:"default"} -->

{import file="new.html.tpl"}

{foreach item=CURRENT from=$ROWS}
    <div class="guestbook_entry" id="guestbook_form{$CURRENT.GUESTBOOK_ID}" style="display: none;"></div>
    <div class="guestbook_entry" id="guestbook_entry{$CURRENT.GUESTBOOK_ID}">
{import file="entry.html.tpl" CURRENT=$CURRENT}
    </div>
{foreachelse}
{lang id="NO_ENTRIES_FOUND"}
{/foreach}

<p class="description" style="padding: 5px;">
    <!-- {if $PAGE > 0 } --><!-- {assign var="NEXT_PAGE" value=$PAGE-$ENTRIES_PER_PAGE } -->
    <a onclick="YanaGuestbook.prototype.guestbookRequest('{$ACTION}','guestbook','sort={$SORT}&desc={$DESC}&entries={$ENTRIES_PER_PAGE}&where={$WHERE}&page={$NEXT_PAGE}');return false" href={"action=$ACTION&sort=$SORT&desc=$DESC&entries=$ENTRIES_PER_PAGE&where=$WHERE&page=$NEXT_PAGE"|href} title="{lang id="TITLE_PREVIOUS"}">{lang id="BUTTON_PREVIOUS"}</a>
    <!-- {/if} -->
    {foreach item="ENTRY" from=$LIST_OF_ENTRIES}
        <!-- {if $ENTRY.TOO_MANY } -->
        &nbsp;...&nbsp;
        <!-- {elseif $PAGE < $ENTRY.FIRST - 1 || $PAGE > $ENTRY.LAST - 1 } --><!-- {assign var="NEXT_PAGE" value=$ENTRY.FIRST-1 } -->
        <a onclick="YanaGuestbook.prototype.guestbookRequest('{$ACTION}','guestbook','sort={$SORT}&desc={$DESC}&entries={$ENTRIES_PER_PAGE}&where={$WHERE}&page={$NEXT_PAGE}');return false" href={"action=$ACTION&sort=$SORT&desc=$DESC&entries=$ENTRIES_PER_PAGE&where=$WHERE&page=$NEXT_PAGE"|href} title="{lang id="TITLE_LIST"}">
            [{$ENTRY.FIRST}-{$ENTRY.LAST}]
        </a>
        <!-- {else} -->
        [{$ENTRY.FIRST}-{$ENTRY.LAST}]
        <!-- {/if} -->
    {/foreach}
    <!-- {if $PAGE + $ENTRIES_PER_PAGE < $LAST_PAGE } --><!-- {assign var="NEXT_PAGE" value=$PAGE+$ENTRIES_PER_PAGE } -->
    <a onclick="YanaGuestbook.prototype.guestbookRequest('{$ACTION}','guestbook','sort={$SORT}&desc={$DESC}&entries={$ENTRIES_PER_PAGE}&where={$WHERE}&page={$NEXT_PAGE}');return false" href={"action=$ACTION&sort=$SORT&desc=$DESC&entries=$ENTRIES_PER_PAGE&where=$WHERE&page=$NEXT_PAGE"|href} title="{lang id="TITLE_NEXT"}">{lang id="BUTTON_NEXT"}</a>
    <!-- {/if} -->
</p>
</body>

</html>
