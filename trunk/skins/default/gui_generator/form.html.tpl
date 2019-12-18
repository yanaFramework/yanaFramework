<div id="{$form->getName()}-form" class="gui_generator_form">
    <div class="gui_tabs">
        {if $form->isSelectable()}
            <div class="gui_generator_toolbar">
                <form method="post" action="{$PHP_SELF}" enctype="multipart/form-data" accept-charset="UTF-8"
                      id="{$form->getName()}-search-small" class="gui_generator_search_small">
                    <input type="hidden" name="id" value="{$ID}"/>
                    <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
                    {if $ACTION}<input type="hidden" name="action" value="{$ACTION}"/>{/if}
                    <label>
                        <span class="buttonize_static"><span class="icon_magnifier_hover">&nbsp;</span></span>
                        <input name="{$form->getName()}[searchterm]" type="text" value="{$form->getSearchTerm()|entities}"/>
                    </label>
                    <input value='{lang id="ok"}' type="submit"/>
                </form>
            </div>
            {if $form->getSearchAction() || $form->hasSearchableChildren()}
                <form method="post" action="{$PHP_SELF}" enctype="multipart/form-data" accept-charset="UTF-8">
                    <input type="hidden" name="id" value="{$ID}"/>
                    <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
                    <input type="hidden" name="action" value="{$form->getSearchAction()}"/>
                    <fieldset id="{$form->getName()}-search" class="gui_generator_search">
                        <legend>
                            <span class="icon_magnifier">&nbsp;</span>
                            {lang id="advanced_search"}
                        </legend>
                        {import file="search.html.tpl" form=$form}
                    </fieldset>
                </form>
            {/if}
        {/if}
        {import file="subform.html.tpl" form=$form}
    </div>
    {if $form->isSelectable()}
        <script type="text/javascript"><!--
            $(document).ready(function() {
                $('#{$form->getName()}-search').hide();
                $.fn.fancybox.defaults.hideOnContentClick = true;
                $.fn.fancybox.defaults.titlePosition = 'over';
                $.fn.fancybox.defaults.showCloseButton = false;
                $.fn.fancybox.defaults.type = 'image';
                {if $form->isSelectable() && ($form->getEvent('search') || $form->hasSearchableChildren())}
                    $('#{$form->getName()}-search-small').append(
                        '<a class="buttonize" href="javascript://" ' +
                        'onclick="$(\'#{$form->getName()}-search\').slideToggle()">' +
                        '<span class="icon_pointer">&nbsp;</span></a>'
                    );
                {/if}
            });
        //--></script>
    {/if}
</div>