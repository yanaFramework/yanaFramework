{$updateForm = $form->getUpdateForm()}
{if $form->getLayout() === 0}
    {import file="layout0.html.tpl" form=$updateForm}
{elseif $form->getLayout() === 1}
    {import file="layout1.html.tpl" form=$updateForm}
{elseif $form->getLayout() === 2}
    {import file="layout2.html.tpl" form=$updateForm}
{elseif $form->getLayout() === 3}
    {import file="layout3.html.tpl" form=$updateForm}
{elseif $form->getLayout() === 4}
    {import file="layout4.html.tpl" form=$updateForm}
{elseif $form->getLayout() === 5}
    {import file="layout5.html.tpl" form=$updateForm}
{else}
    {import file="layout0.html.tpl" form=$updateForm}
{/if}

<!-- BEGIN page selector -->
{if $updateForm->hasRows()}
<div class="gui_generator_footer">
    {$updateForm->getFooter()}
</div>
{/if}