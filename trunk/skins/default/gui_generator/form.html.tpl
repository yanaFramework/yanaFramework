<div id="{$form->getName()}-form" class="gui_generator_form">
    <div class="gui_tabs">
        {if $form->isSelectable() && ($form->getContext('update')->getRows()->count() || $form->getSearchTerm() > '')}
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
        {/if}
        {import file="subform.html.tpl" form=$form}
    </div>
</div>