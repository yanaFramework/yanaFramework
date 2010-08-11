<div id="yana_stdout">
{if $STDOUT}
    <div id="messagebox" class="errlvl_{$STDOUT.LEVEL}">
        <div class="errlvl_{$STDOUT.LEVEL}">
        {foreach item=message from=$STDOUT.MESSAGES}
             {if $message.header}<div class="message_header">{$message.header}</div>{/if}
             {if $message.text}<div class="message_text">{$message.text}</div>{/if}
        {/foreach}
        </div>
    </div>
{/if}
</div>
