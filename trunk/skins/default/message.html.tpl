<!DOCTYPE html>

<html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
        <style type="text/css">
{import file="default.css.tpl"}
        </style>
<!-- {if $ACTION !== 'null'}    -->
<!-- {if $STDOUT.LEVEL=='error'} -->
        <meta http-equiv="Refresh" content="9; URL={"action=$ACTION&target=$TARGET"|url}"/>
<!-- {elseif $STDOUT.LEVEL=='alert' || $STDOUT.LEVEL=='warning'} -->
        <meta http-equiv="Refresh" content="3; URL={"action=$ACTION&target=$TARGET"|url}"/>
<!-- {else} -->
        <meta http-equiv="Refresh" content="1; URL={"action=$ACTION&target=$TARGET"|url}"/>
<!-- {/if} -->
<!-- {/if} -->
    </head>

<body>
    <table align="center" summary="" id="message_pane">
      <tr>
        <td align="center" valign="middle">
<!-- {if !empty($PROFILE.LOGO)} -->
        <img id="message_pane_logo" border="0" alt="Logo" src='{$PROFILE.LOGO|entities}'/>
<!-- {/if} -->
        <div id="message_pane_section">
<!-- {if $STDOUT.LEVEL=='error'} -->
            <img id="message_pane_icon" alt="image" src="styles/symbol_error_xl.gif"/>
<!-- {elseif $STDOUT.LEVEL=='warning'} -->
            <img id="message_pane_icon" alt="image" src="styles/symbol_alert_xl.gif"/>
<!-- {elseif $STDOUT.LEVEL=='alert'} -->
            <img id="message_pane_icon" alt="image" src="styles/symbol_notice_xl.gif"/>
<!-- {else} -->
            <img id="message_pane_icon" alt="image" src="styles/symbol_message_xl.gif"/>
<!-- {/if} -->
            <div id="message_pane_text">
{foreach item=message from=$STDOUT.MESSAGES}
            <h1>{$message.header}</h1>
            <p>{$message.text}</p>
{/foreach}
<!-- {if $ACTION !== 'null'} -->
                 <p id="message_pane_redirect"><a class="comment" href={"action=$ACTION&target=$TARGET"|href}>{lang id="REDIRECT"}</a></p>
<!-- {/if} -->
            </div>
        </div>
      </td>
    </tr>
  </table>

</body>

</html>
