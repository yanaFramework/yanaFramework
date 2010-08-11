{assign var="formName" value=$form->getName()}
{assign var="deleteAction" value=$form->getDeleteAction()}
{if $iterator->getRowCount()}
    <!-- BEGIN insert form contents -->
    <div class="gui_generator_view">
        <ol class="gui_generator_view_list">
            {section name="update" loop=$iterator->getRowCount()}
                <li>
                    {if $iterator->hasRows() && $form->getEntriesPerPage() > 1}
                        {assign var="url" value="action=$ACTION&$formName"|cat:"[entries]=1&$formName"|cat:"[layout]=2&$formName"|cat:"[page]="|cat:$iterator->getPage()}
                        <a title='{lang id="TITLE_DETAILS"}' class="gui_generator_details buttonize" href={$url|href}>
                            <span class="icon_pointer">&nbsp;</span>
                        </a>
                    {/if}
                    {foreach from=$iterator item="field"}
                        {if $iterator->isSingleLine()}
                            <span class="{$iterator->getCssClass()}">
                                <span class="gui_generator_list_title">
                                    {if $iterator->hasRows() && $form->getEntriesPerPage() > 1 && $field->refersToTable()}
                                        {assign var="url" value="action=$ACTION&$formName"|cat:"[orderby]=$field&$formName"|cat:"[desc]"}
                                        <a title='{lang id="ORDER.BY"} &quot;{$field->getTitle()}&quot;' href={"$url=0"|href}>
                                            {$field->getTitle()}
                                        </a>
                                    {else}
                                        {$field->getTitle()}
                                    {/if}
                                </span>
                                <span class="gui_generator_list_value" title="{$field->getTitle()}">
                                  {$iterator}
                                </span>
                            </span>
                        {/if}
                    {/foreach}
                    {if $iterator->hasRows() && $form->isDeletable() && $deleteAction}
                        <a class="buttonize gui_generator_delete" title='{lang id="delete"}'
                           onclick="return confirm('{lang id="prompt_delete"}')"
                           href={"action=$deleteAction&selected_entries[]="|cat:$iterator->primaryKey()|href}>
                            <span class="icon_delete">&nbsp;</span>
                        </a>
                    {/if}
                </li>
                {if $iterator->hasRows()}
                    {$iterator->nextRow()}
                {/if}
            {/section}
        </ol>
    </div>
{else}
    <div class="gui_generator_no_entries_found">{lang id="NO_ENTRIES_FOUND"}</div>
{/if}
<script type="text/javascript"><!--
    $(function() {ldelim}
        $('.gui_generator_image a').fancybox();
    {rdelim});
//--></script>