<div class="config_form" id="config_user_settings">
    <div class="config_head">
        <div class="config_title" onclick="yanaToggleMenu(this.parentNode)">{lang id="USER.39"}</div>
    </div>
    <div class="help">
        <div class="help_text">
            {lang id="HELP.MEDIA_HEAD"}
            {lang id="HELP.MEDIA_FOOTER"}
        </div>
    </div>
    {if is_array($tags)}
        <div class="media_tag">
            <h1>Top Tags</h1>
            {foreach from=$tags key=keyword item=fontsize name=foo}
                <a href={"action=$ACTION&where[media_keywords]=$keyword"|href} class="media_keyword_size_{$fontsize}">{$keyword}</a>
            {/foreach}
        </div>
    {/if}
    <div class="option">
    {create file="media" id="media"}
    </div>
</div>