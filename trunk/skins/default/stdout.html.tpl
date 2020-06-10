<div id="yana_stdout">
{if $STDOUT && count($STDOUT) > 0}
    <div id="messagebox" class="errlvl_{$STDOUT->getLevel()}">
        <div class="errlvl_{$STDOUT->getLevel()}">
        {foreach item=message from=$STDOUT}
             {if $message->getHeader()}<div class="message_header">{$message->getHeader()}</div>{/if}
             {if $message->getText()}<div class="message_text">{$message->getText()}</div>{/if}
        {/foreach}
        </div>
    </div>
{/if}
</div>
