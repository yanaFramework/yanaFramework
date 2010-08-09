{lang id="MAIL_1"}

{$GUESTBOOK_MESSAGE}

{lang id="MAIL_3"}
----------------------------------------

{if $GUESTBOOK_NAME}
{lang id="13"}: {$GUESTBOOK_NAME}
{/if}IP: {$REMOTE_ADDR}
{if $GUESTBOOK_DATE}
{lang id="6"}: {$GUESTBOOK_DATE|date_format:"%D, %H:%M:%S"}
{/if}{if $GUESTBOOK_MAIL}
{lang id="3"}: {$GUESTBOOK_MAIL}
{/if}{if $GUESTBOOK_HOMETOWN}
{lang id="4"}: {$GUESTBOOK_HOMETOWN}
{/if}{if $GUESTBOOK_MESSENGER}
{lang id="22"}: {$GUESTBOOK_MESSENGER}
{/if}{if $GUESTBOOK_HOMEPAGE}
{lang id="2"}: {$GUESTBOOK_HOMEPAGE}
{/if}
_______________________________________
YANA - Automailer
