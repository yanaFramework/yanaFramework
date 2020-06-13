{if $form->isSelectable() && $form->getFields() && $form->getSearchAction()}
    {if $form->getLayout() < 3}
        {import file="layout2.html.tpl" form=$form->getSearchForm()}
    {elseif $form->getLayout() === 3}
        {import file="layout3.html.tpl" form=$form->getSearchForm()}
    {else}
        {import file="layout4.html.tpl" form=$form->getSearchForm()}
    {/if}    
    <div class="gui_generator_buttons">
        <input type="submit" name="action[{$form->getSearchAction()}]" value='{lang id="ok"}'/>
    </div>
{/if}