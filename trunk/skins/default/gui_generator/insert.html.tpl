{if $form->isInsertable() && $form->getFields() && $form->getInsertAction() && ($form->getEntriesPerPage() > 1 || !$form->getForms())}
    <div title="{$form->getTitle()}">
        {assign var="iterator" value=$form->getInsertIterator()}
        {if $form->getLayout() === 0}
            {import file="layout2.html.tpl" form=$form iterator=$iterator}
        {elseif $form->getLayout() === 1}
            {import file="layout1.html.tpl" form=$form iterator=$iterator}
        {elseif $form->getLayout() === 2}
            {import file="layout2.html.tpl" form=$form iterator=$iterator}
        {elseif $form->getLayout() === 3}
            {import file="layout2.html.tpl" form=$form iterator=$iterator}
        {else}
            {import file="layout4.html.tpl" form=$form iterator=$iterator}
        {/if}
        <input type="submit" name="action[{$form->getInsertAction()}]" value='{lang id="button_save"}'/>
    </div>
{/if}
{foreach from=$form->getForms() item="subform"}
    {if !$subform->getKey() || $form->getEntriesPerPage() === 1}
        {import file="insert.html.tpl" form=$subform}
    {/if}
{/foreach}