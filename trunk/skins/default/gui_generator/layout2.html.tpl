<!-- BEGIN insert form contents -->
{assign var="deleteAction" value=$form->getDeleteAction()}
{assign var="formName" value=$form->getName()}
{section name="update" loop=$iterator->getRowCount()}
    <div class="gui_generator_view gui_generator_view_details">
        {if $iterator->hasRows()}
            <div class="gui_generator_toolbar">
                {if $form->isDeletable() && $deleteAction}
                    <a class="gui_generator_delete buttonize"
                       onclick="return confirm('{lang id="prompt_delete"}')"
                       href={"action=$deleteAction&selected_entries[]="|cat:$iterator->primaryKey()|href}>
                        <span class="icon_delete">&nbsp;</span>
                    </a>
                {/if}
                {if $form->getEntriesPerPage() > 1}
                    {assign var="url" value="action=$ACTION&$formName"|cat:"[entries]=1&$formName"|cat:"[layout]=2&$formName"|cat:"[page]="|cat:$iterator->getPage()}
                    <a title='{lang id="TITLE_DETAILS"}' class="gui_generator_details buttonize" href={$url|href}>
                        <span class="icon_pointer">&nbsp;</span>
                    </a>
                {/if}
            </div>
        {/if}
        <table summary="form" cellpadding="0" cellspacing="0">
            {foreach from=$iterator item="field"}
                <tr style="text-align: left;">
                    <th class="{$iterator->getCssClass()}">
                        <!-- {if $field->isFilterable() && $iterator->hasRows() && $form->getEntriesPerPage() > 1}
                            BEGIN: filter settings
                        -->
                        <a href={"action=$ACTION"|href} class="gui_generator_filter"
                           onclick="return yanaApplyFilter(this, '{$form->getName()}[filter][{$field->getName()}]', '{$iterator->getValue()|entities}', '{lang id="WHERE.PROMPT"}')">
                            {if $field->hasFilter()}
                                <span class="icon_filter_hover" title='{lang id="WHERE.EDIT"}'>&nbsp;</span>
                            {else}
                                <span class="icon_filter" title='{lang id="WHERE.SET"}'>&nbsp;</span>
                            {/if}
                        </a>
                        <!--
                            END: filter settings
                        {/if}-->

                        {if $iterator->hasRows() && $form->getEntriesPerPage() > 1 && $field->refersToTable()}
                            {assign var="url" value="action=$ACTION&$formName"|cat:"[orderby]=$field&$formName"|cat:"[desc]"}
                            <a href={"$url=0"|href} class="gui_generator_sort" title='{lang id="ORDER.ASCENDING"}'>
                                {if $field->getName() == $form->getOrderByField() && !$form->isDescending() }
                                    <span class="icon_arrowup_hover">&nbsp;</span>
                                {else}
                                    <span class="icon_arrowup">&nbsp;</span>
                                {/if}
                            </a>
                            <a href={"$url=1"|href}  class="gui_generator_sort" title='{lang id="ORDER.DESCENDING"}'>
                                {if $field->getName() == $form->getOrderByField() && $form->isDescending() }
                                    <span class="icon_arrowdown_hover">&nbsp;</span>
                                {else}
                                    <span class="icon_arrowdown">&nbsp;</span>
                                {/if}
                            </a>
                            <div class="gui_generator_description">
                                {if !$field->isNullable()}
                                    <span class="gui_generator_mandatory" title="{lang id="MANDATORY"}">*</span>
                                {/if}
                                <a title='{lang id="ORDER.BY"} &quot;{$field->getTitle()}&quot;' href={"$url=0"|href}>
                                    {$field->getTitle()}
                                </a>
                            </div>
                        {else}
                            <div class="gui_generator_description">
                                {if !$field->isNullable() && $form->isInsertable()}
                                    <span class="gui_generator_mandatory" title="{lang id="MANDATORY"}">*</span>
                                {/if}
                                {$field->getTitle()}
                            </div>
                        {/if}
                    </th>
                    <td title="{$field->getTitle()}" class="{$iterator->getCssClass()}">
                    {$iterator}
                    </td>
                </tr>
            {/foreach}
        </table>
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