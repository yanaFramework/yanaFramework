/* Profile Settings */
body
{
{if !empty($PROFILE.BGCOLOR)}
    background-color: {$PROFILE.BGCOLOR};{/if}
{if !empty($PROFILE.BGIMAGE)}
    background-image: url("{$PROFILE.BGIMAGE}");
{/if}
}
body, p
{
{if !empty($PROFILE.PSIZE)}
    font-size: {$PROFILE.PSIZE};{/if}
{if !empty($PROFILE.PCOLOR)}
    color: {$PROFILE.PCOLOR};{/if}
{if !empty($PROFILE.PFONT)}
    font-family: {$PROFILE.PFONT};{/if}
}
{if !empty($PROFILE.PFONT)}
.label,
.description,
.comment
{
    font-family: {$PROFILE.PFONT};
}
{/if}
h1, h2, h3, .header
{
{if !empty($PROFILE.HSIZE)}
    font-size: {$PROFILE.HSIZE};{/if}
{if !empty($PROFILE.HFONT)}
    font-family: {$PROFILE.HFONT};{/if}
}
{if !empty($PROFILE.HCOLOR)}
h1, h3
{
    color: {$PROFILE.HCOLOR};
}
{/if}