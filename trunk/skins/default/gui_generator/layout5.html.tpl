{assign var="deleteAction" value=$form->getDeleteAction()}
{assign var="formName" value=$form->getName()}
<!-- BEGIN form contents -->
    <div id="{$formName}-photodesk" class="gui_generator_view gui_generator_view_gallery">
{for $i=1 to max($form->getRowCount(), ! $form->hasRows())}
        <div class="pd_photo">
            <div class="pd_hold">
                {foreach from=$form item="field"}
                    {if $field->getType() === "image"}
                        {$field}
                    {/if}
                {/foreach}
                {$form->rewind()}
                <div class="pd_description">
                    {foreach from=$form item="field"}
                        {if ($field->getType() == 'string' || $field->getType() == 'file')}{$field}{/if}
                    {/foreach}
                </div>
            </div>
            <span {if $deleteAction}onclick="if (confirm('{lang id="prompt_delete"}')) document.location.href = '{"action=$deleteAction&selected_entries[]={$form->getPrimaryKey()}"|url}'"{/if} title='{lang id="delete"}' class="gui_generator_delete delete"></span>
        </div>
        {if $form->hasRows()}
            {$form->nextRow()}
        {/if}
{forelse}
        <div class="gui_generator_no_entries_found">{lang id="NO_ENTRIES_FOUND"}</div>
{/for}
    </div>
<script type="text/javascript"><!--
    $(function() {ldelim}
        $('#{$formName}-photodesk').photoDesk({ldelim}
            photoW: 100,
            photoH: 100,
            showShuffle: false,
            showViewAll: false
        {rdelim});
    {rdelim});
//--></script>