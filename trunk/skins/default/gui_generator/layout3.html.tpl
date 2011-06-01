{assign var="formName" value=$form->getName()}
{assign var="deleteAction" value=$form->getDeleteAction()}
{if $form->getRowCount()}
    <!-- BEGIN insert form contents -->
    <div class="gui_generator_view">
        <ol class="gui_generator_view_list">
            {for $i=1 to max($form->getRowCount(), ! $form->hasRows())}
                <li>
                    {if $form->hasRows() && $form->getEntriesPerPage() > 1}
                        {assign var="url" value="action=$ACTION&{$formName}[entries]=1&{$formName}[layout]=2&{$formName}[page]={($form->getPage() * $form->getEntriesPerPage()) + $i - 1}"}
                        <a title='{lang id="TITLE_DETAILS"}' class="gui_generator_details buttonize" href={$url|href}>
                            <span class="icon_pointer">&nbsp;</span>
                        </a>
                    {/if}
                    {foreach from=$form item="field"}
                        {if $field->isSingleLine()}
                            <span class="{$field->getCssClass()}">
                                <span class="gui_generator_list_title">
                                    {if $form->hasRows() && $form->getEntriesPerPage() > 1 && $field->refersToTable()}
                                        <a title='{lang id="ORDER.BY"} &quot;{$field->getTitle()}&quot;'
                                           href={"action=$ACTION&{$formName}[orderby]={$field->getName()}&{$formName}[desc]=0"|href}>
                                            {$field->getTitle()}
                                        </a>
                                    {else}
                                        {$field->getTitle()}
                                    {/if}
                                </span>
                                <span class="gui_generator_list_value" title="{$field->getTitle()}">
                                  {$field}
                                </span>
                            </span>
                        {/if}
                    {/foreach}
                    {if $form->hasRows() && $form->isDeletable() && $deleteAction}
                        <a class="buttonize gui_generator_delete" title='{lang id="delete"}'
                           onclick="return confirm('{lang id="prompt_delete"}')"
                           href={"action=$deleteAction&selected_entries[]={$form->getPrimaryKey()}"|href}>
                            <span class="icon_delete">&nbsp;</span>
                        </a>
                    {/if}
                </li>
                {if $form->hasRows()}
                    {$form->nextRow()}
                {/if}
            {/for}
        </ol>
    </div>
{else}
    <div class="gui_generator_no_entries_found">{lang id="NO_ENTRIES_FOUND"}</div>
{/if}
<script type="text/javascript"><!--
    $(function() {
        $('.gui_generator_image a').fancybox();
    });
//--></script>