<div id="{$form->getName()}-form" class="gui_generator_form">
    <div class="gui_tabs">
        {if $form->isSelectable()}
            <div class="gui_generator_toolbar">
                <form method="post" action="{$PHP_SELF}" enctype="multipart/form-data" accept-charset="UTF-8"
                      id="{$form->getName()}-search-small" class="gui_generator_search_small">
                    <input type="hidden" name="id" value="{$ID}"/>
                    <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
                    <input type="hidden" name="action" value="{$ACTION}"/>
                    <label>
                        <span class="buttonize_static"><span class="icon_magnifier_hover">&nbsp;</span></span>
                        <input name="{$form->getName()}[search]" type="text" value="{$form->getValue('search')|entities}"/>
                    </label>
                    <input value='{lang id="ok"}' type="submit"/>
                </form>
            </div>
            {if $form->getSearchAction() || $form->hasSearchableChildren()}
                <form method="post" action="{$PHP_SELF}" enctype="multipart/form-data" accept-charset="UTF-8">
                    <input type="hidden" name="id" value="{$ID}"/>
                    <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
                    <fieldset id="{$form->getName()}-search" class="gui_generator_search">
                        <legend>
                            <a class="buttonize" href="javascript://"
                               onclick="$('#{$form->getName()}-search').hide('slow')">
                                <span class="icon_magnifier">&nbsp;</span>
                                {lang id="advanced_search"}
                            </a>
                        </legend>
                        {import file="search.html.tpl" form=$form}
                    </fieldset>
                </form>
            {/if}
        {/if}
        {if $form->getInsertAction() || $form->hasInsertableChildren()}
            <form method="post" action="{$PHP_SELF}" enctype="multipart/form-data" accept-charset="UTF-8">
                <input type="hidden" name="id" value="{$ID}"/>
                <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
                <fieldset id="{$form->getName()}-new" class="gui_generator_new">
                    <legend>
                        <a class="buttonize" href="javascript://" onclick="$('#{$form->getName()}-new').hide('slow')">
                            <span class="icon_new">&nbsp;</span>
                            {lang id="new_entry"}
                        </a>
                    </legend>
                    {import file="insert.html.tpl" form=$form}
                </fieldset>
            </form>
        {/if}
        {if $form->isSelectable()}
        <form method="post" action="{$PHP_SELF}" enctype="multipart/form-data" accept-charset="UTF-8">
                <input type="hidden" name="id" value="{$ID}"/>
                <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
                <input type="hidden" name="action" value="{$ACTION}"/>
                <div class="gui_generator_edit">
                    {import file="subform.html.tpl" form=$form}
                </div>
        </form>
        {/if}
    </div>
    {if $form->isSelectable()}
        <script type="text/javascript"><!--
            $(document).ready(function() {ldelim}
                $('#{$form->getName()}-search').hide();
                $('#{$form->getName()}-search-small').hide();
                $('#{$form->getName()}-new').hide();
                $.fn.fancybox.defaults.hideOnContentClick = true;
                $.fn.fancybox.defaults.titlePosition = 'over';
                $.fn.fancybox.defaults.showCloseButton = false;
                $.fn.fancybox.defaults.type = 'image';
                {if $form->isSelectable()}
                    $('#{$form->getName()}-search-small').after(
                        '<a class="gui_generator_icon_search" href="javascript://" ' +
                        'onclick="$(\'#{$form->getName()}-search-small\').toggle(\'slow\')">' +
                        '<span class="icon_magnifier">&nbsp;</span></a>'
                        {if $form->getInsertAction() || $form->hasInsertableChildren()}
                        + '<a class="gui_generator_icon_new" href="javascript://" ' +
                        'onclick="$(\'#{$form->getName()}-new\').toggle(\'slow\')">' +
                        '<span class="icon_new">&nbsp;</span></a>'
                        {/if}
                    );
                    {if $form->getSearchAction() || $form->hasSearchableChildren()}
                    $('#{$form->getName()}-search-small').append(
                        '<a class="buttonize" href="javascript://" ' +
                        'onclick="$(\'#{$form->getName()}-search\').toggle(\'slow\')">' +
                        '<span class="icon_pointer">&nbsp;</span></a>'
                    );
                    {/if}
                {/if}
            {rdelim});
        //--></script>
    {/if}
{*
    <div class="gui_generator_footer" id="footer-{$form->getName()}">
        {* Spam protection: Captcha *}{*
        {if $PROFILE.SPAM.CAPTCHA && ($PROFILE.SPAM.PERMISSION || !$PERMISSION)}
            <label class="gui_generator_captcha" title='{lang id="SECURITY_IMAGE.DESCRIPTION"}'>
                <span class="gui_generator_mandatory" title='{lang id="MANDATORY"}'>*</span>
                {lang id="SECURITY_IMAGE.TITLE"}
                {captcha}
            </label>
        {/if}
        <input type="submit" value='{lang id="BUTTON_SAVE"}'/>
        {if $form->isUpdatable()}
            <div class="gui_generator_mandatory">{lang id="MANDATORY"}</div>
        {/if}
    </div>
*}
</div>