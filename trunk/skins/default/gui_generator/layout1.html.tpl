{assign var="deleteAction" value=$form->getDeleteAction()}
{assign var="formName" value=$form->getName()}
<!-- BEGIN form contents -->
{for $i=1 to max($form->getRowCount(), ! $form->hasRows())}
    <div class="gui_generator_view gui_generator_view_seperated">
        <div class="gui_generator_table">
            <!-- Head -->
            <div class="gui_generator_thead">
                {if $form->hasRows() &&  $form->getEntriesPerPage() > 1}
                    {assign var="url" value="action=$ACTION&{$formName}[entries]=1&{$formName}[layout]=2&{$formName}[page]={($form->getPage() * $form->getEntriesPerPage()) + $i - 1}"}
                    <a title='{lang id="TITLE_DETAILS"}' class="gui_generator_details buttonize" href={$url|href}>
                        <span class="icon_pointer">&nbsp;</span>
                    </a>
                {/if}
                {foreach name="for_base" from=$form item="field"}
                    {if $field->isSingleLine()}
                    <div class="gui_generator_tr {$field->getCssClass()} {if $field->getValue() == null}gui_generator_empty{/if}">
                        <div class="gui_generator_th">
                            {if !$field->isNullable()}
                                 <span class="gui_generator_mandatory" title="{lang id="MANDATORY"}">*</span>
                            {/if}
                            {if $form->hasRows() && $form->getEntriesPerPage() > 1 && $field->refersToTable() && !$field->isFile()}
                                <a title='{lang id="ORDER.BY"} &quot;{$field->getTitle()}&quot;' href={"action=$ACTION&{$formName}[orderby]={$field->getName()}&{$formName}[desc]=0"|href}>
                                    {$field->getTitle()}
                                </a>
                            {else}
                                {$field->getTitle()}
                            {/if}
                        </div>
                        <div class="gui_generator_td">
                            {$field}
                        </div>
                    </div>
                    {/if}
                {/foreach}
                {if $form->hasRows() && $form->isDeletable() && $deleteAction}
                    <a class="buttonize gui_generator_delete" title='{lang id="delete"}'
                       onclick="return confirm('{lang id="prompt_delete"}');"
                       href={"action=$deleteAction&selected_entries[]="|cat:$form->getPrimaryKey()|href}>
                        <span class="icon_delete">&nbsp;</span>
                    </a>
                {/if}
            </div>
            <!-- Body {$form->rewind()} -->
            <div class="gui_generator_tbody">
                {foreach from=$form item="field"}
                    {if $field->isMultiLine()}
                        <div class="gui_generator_tr {$field->getCssClass()}">
                            <div class="label" align="left">
                                {if !$field->isNullable()}
                                    <span class="gui_generator_mandatory" title="{lang id="MANDATORY"}">*</span>
                                {/if}
                                {$field->getTitle()}
                            </div>
                            {$field}
                        </div>
                    {/if}
                {/foreach}
            </div>
        </div>
        {if $form->hasRows()}
            {$form->nextRow()}
        {/if}
    </div>
{forelse}
    <div class="gui_generator_no_entries_found">{lang id="NO_ENTRIES_FOUND"}</div>
{/for}
<script type="text/javascript"><!--
    $(function() {
        $('.gui_generator_image a').fancybox();
    });
//--></script>