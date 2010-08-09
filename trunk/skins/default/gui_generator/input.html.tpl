{if !$iterator}{assign var="iterator" value=$form->getIterator()}{/if}
{if $form->getLayout() === 0}
    {import file="layout0.html.tpl" form=$form iterator=$iterator}
{elseif $form->getLayout() === 1}
    {import file="layout1.html.tpl" form=$form iterator=$iterator}
{elseif $form->getLayout() === 2}
    {import file="layout2.html.tpl" form=$form iterator=$iterator}
{elseif $form->getLayout() === 3}
    {import file="layout3.html.tpl" form=$form iterator=$iterator}
{else}
    {import file="layout4.html.tpl" form=$form iterator=$iterator}
{/if}

<!-- BEGIN page selector -->
{if $iterator->hasRows()}
<div class="gui_generator_footer">
    {$form->getListOfEntries()}
</div>
{/if}