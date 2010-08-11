/* Profile Settings */
body
{ldelim}
{if $PROFILE.BGCOLOR}
    background-color: {$PROFILE.BGCOLOR};{/if}
{if $PROFILE.BGIMAGE}
    background-image: url("{$PROFILE.BGIMAGE}");
{/if}
{rdelim}
body, p
{ldelim}
{if $PROFILE.PSIZE}
    font-size: {$PROFILE.PSIZE};{/if}
{if $PROFILE.PCOLOR}
    color: {$PROFILE.PCOLOR};{/if}
{if $PROFILE.PFONT}
    font-family: {$PROFILE.PFONT};{/if}
{rdelim}
.label,
.description,
.comment
{ldelim}
{if $PROFILE.PFONT}
    font-family: {$PROFILE.PFONT};{/if}
{rdelim}
h1, h2, h3, .header
{ldelim}
{if $PROFILE.HSIZE}
    font-size: {$PROFILE.HSIZE};{/if}
{if $PROFILE.HFONT}
    font-family: {$PROFILE.HFONT};{/if}
{rdelim}
h1, h3
{ldelim}
{if $PROFILE.HCOLOR}
    color: {$PROFILE.HCOLOR};{/if}
{rdelim}
