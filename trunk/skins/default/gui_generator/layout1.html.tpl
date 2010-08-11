{assign var="deleteAction" value=$form->getDeleteAction()}
{assign var="formName" value=$form->getName()}
<!-- BEGIN form contents -->
{section name="view" loop=$iterator->getRowCount()}
    <div class="gui_generator_view gui_generator_view_seperated">
        <div class="gui_generator_table">
            <!-- Head -->
            <div class="gui_generator_thead">
                {if $iterator->hasRows() &&  $form->getEntriesPerPage() > 1}
                    {assign var="url" value="action=$ACTION&$formName"|cat:"[entries]=1&$formName"|cat:"[layout]=2&$formName"|cat:"[page]="|cat:$iterator->getPage()}
                    <a title='{lang id="TITLE_DETAILS"}' class="gui_generator_details buttonize" href={$url|href}>
                        <span class="icon_pointer">&nbsp;</span>
                    </a>
                {/if}
                {foreach name="for_base" from=$iterator item="field"}
                    {if $iterator->isSingleLine()}
                    <div class="gui_generator_tr {$iterator->getCssClass()} {if $iterator->getValue() == null}gui_generator_empty{/if}">
                        <div class="gui_generator_th">
                            {if !$field->isNullable()}
                                 <span class="gui_generator_mandatory" title="{lang id="MANDATORY"}">*</span>
                            {/if}
                            {if $iterator->hasRows() && $form->getEntriesPerPage() > 1 && $field->refersToTable()}
                                {assign var="url" value="action=$ACTION&$formName"|cat:"[orderby]=$field&$formName"|cat:"[desc]"}
                                <a title='{lang id="ORDER.BY"} &quot;{$field->getTitle()}&quot;' href={"$url=0"|href}>
                                    {$field->getTitle()}
                                </a>
                            {else}
                                {$field->getTitle()}
                            {/if}
                        </div>
                        <div class="gui_generator_td">
                            {$iterator}
                        </div>
                    </div>
                    {/if}
                {/foreach}
                {if $iterator->hasRows() && $form->isDeletable() && $deleteAction}
                    <a class="buttonize gui_generator_delete" title='{lang id="delete"}'
                       onclick="return confirm('{lang id="prompt_delete"}')"
                       href={"action=$deleteAction&selected_entries[]="|cat:$iterator->primaryKey()|href}>
                        <span class="icon_delete">&nbsp;</span>
                    </a>
                {/if}
            </div>
            <!-- Body {$iterator->rewind()} -->
            <div class="gui_generator_tbody">
                {foreach from=$iterator item="field"}
                    {if $iterator->isMultiLine()}
                        <div class="gui_generator_tr {$iterator->getCssClass()}">
                            <div class="label" align="left">
                                {if !$field->isNullable()}
                                    <span class="gui_generator_mandatory" title="{lang id="MANDATORY"}">*</span>
                                {/if}
                                {$field->getTitle()}
                            </div>
                            {$iterator}
                        </div>
                    {/if}
                {/foreach}
            </div>
        </div>
        {if $iterator->hasRows()}
            {$iterator->nextRow()}
        {/if}
    </div>
{sectionelse}
    <div class="gui_generator_no_entries_found">{lang id="NO_ENTRIES_FOUND"}</div>
{/section}
<script type="text/javascript"><!--
    $(function() {ldelim}
        $('.gui_generator_image a').fancybox();
    {rdelim});
//--></script>