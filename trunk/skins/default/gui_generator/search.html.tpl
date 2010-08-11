{if $form->isSelectable()}
    {if $form->getFields() && $form->getSearchAction()}
        <fieldset>
            <legend onclick="$(this).find('~ div, ~ input').toggle('slow')">{$form->getTitle()}</legend>
            {assign var="iterator" value=$form->getSearchIterator()}
            {if $form->getLayout() < 3}
                {import file="layout2.html.tpl" form=$form iterator=$iterator}
            {elseif $form->getLayout() === 3}
                {import file="layout3.html.tpl" form=$form iterator=$iterator}
            {else}
                {import file="layout4.html.tpl" form=$form iterator=$iterator}
            {/if}
            <input type="submit" name="action[{$form->getSearchAction()}]" value='{lang id="ok"}'/>
        </fieldset>
    {/if}
    {foreach from=$form->getForms() item="subform"}
        {import file="search.html.tpl" form=$subform}
    {/foreach}
{/if}