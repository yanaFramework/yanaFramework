{if $form->isInsertable() && $form->getFields() && $form->getInsertAction()}
    <fieldset title="{$form->getTitle()}">
        <legend onclick="$(this).find('~ div, ~ input').toggle('slow')">{$form->getTitle()}</legend>
        {if $form->getLayout() === 0}
            {import file="layout2.html.tpl" form=$form->getInsertForm()}
        {elseif $form->getLayout() === 1}
            {import file="layout1.html.tpl" form=$form->getInsertForm()}
        {elseif $form->getLayout() === 2}
            {import file="layout2.html.tpl" form=$form->getInsertForm()}
        {elseif $form->getLayout() === 3}
            {import file="layout2.html.tpl" form=$form->getInsertForm()}
        {else}
            {import file="layout4.html.tpl" form=$form->getInsertForm()}
        {/if}
        {* Spam protection: Captcha *}
        {if $PROFILE.SPAM.CAPTCHA && ($PROFILE.SPAM.PERMISSION || !$PERMISSION)}
            <label class="gui_generator_captcha" title='{lang id="SECURITY_IMAGE.DESCRIPTION"}'>
                <span class="gui_generator_mandatory" title='{lang id="MANDATORY"}'>*</span>
                {lang id="SECURITY_IMAGE.TITLE"}
                {captcha}
            </label>
        {/if}
        <input type="submit" name="action[{$form->getInsertAction()}]" value='{lang id="button_save"}'/>
    </fieldset>
{/if}
{foreach from=$form->getForms() item="subform"}
    {if $form->getEntriesPerPage() === 1}
        {import file="insert.html.tpl" form=$subform}
    {/if}
{/foreach}