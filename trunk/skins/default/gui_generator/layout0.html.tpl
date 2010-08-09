<div class="gui_generator_view">
    {assign var="deleteAction" value=$form->getDeleteAction()}
    {assign var="formName" value=$form->getName()}
    <table summary="form" cellpadding="0" cellspacing="0" class="gui_generator_view_default gui_generator_multi_entry">
      <!-- Heading -->
        <thead>
            <tr>
                {if $iterator->hasRows() && $form->getEntriesPerPage() > 1}
                    <th title='{lang id="TITLE_DETAILS"}'>
                        <div class="gui_generator_details">
                            <span class="icon_show">&nbsp;</span>
                        </div>
                    </th>
                {/if}
                <!-- BEGIN insert form contents -->
                {foreach from=$iterator item="field"}
                    {if $iterator->isSingleline()}
                    <th class="{$iterator->getCssClass()}">
                        <!-- {if $field->isFilterable() && $iterator->hasRows()}
                            BEGIN: filter settings
                        -->
                        <a class="gui_generator_filter" href={"action=$ACTION"|href}
                           onclick="return yanaApplyFilter(this, '{$form->getName()}[filter][{$field->getName()}]', '{$field->getFilterValue()|entities}', '{lang id="WHERE.PROMPT"}')">
                            {if $field->hasFilter() && $form->getEntriesPerPage() > 1}
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
                            {if !$field->isNullable() && $form->isInsertable()}
                                <span class="gui_generator_mandatory" title="{lang id="MANDATORY"}">*</span>
                            {/if}
                            <div class="gui_generator_description">{$field->getTitle()}</div>
                        {/if}

                    </th>
                    {/if}
                {/foreach}
                {if $iterator->hasRows() && $form->isDeletable() && $deleteAction}
                    <th title='{lang id="delete"}'>
                        <div class="gui_generator_delete">
                            <span class="icon_delete">&nbsp;</span>
                        </div>
                    </th>
                {/if}
            </tr>
        </thead>
    {if $iterator->getRowCount()}
      <!-- Entries {$iterator->rewind()}{assign var="formName" value=$form->getName()} -->
        <tbody>
            {section name="update" loop=$iterator->getRowCount()}
                <tr class="gui_generator_{cycle values='even,odd'}_row">
                    {if $iterator->hasRows() && $form->getEntriesPerPage() > 1}
                        <td title='{lang id="TITLE_DETAILS"}'>
                            {assign var="url" value="action=$ACTION&$formName"|cat:"[entries]=1&$formName"|cat:"[layout]=2&$formName"|cat:"[page]="|cat:$iterator->getPage()}
                            <a class="gui_generator_details buttonize" href={$url|href}>
                                <span class="icon_pointer">&nbsp;</span>
                            </a>
                        </td>
                    {/if}
                    {foreach from=$iterator item="field"}
                        {if $iterator->isSingleline()}
                            <td title="{$field->getTitle()}" class="{$iterator->getCssClass()}">
                                {$iterator}
                            </td>
                        {/if}
                    {/foreach}
                    {if $iterator->hasRows() && $form->isDeletable() && $deleteAction}
                        <td title='{lang id="delete"}'>
                            <a class="gui_generator_delete buttonize"
                               onclick="return confirm('{lang id="prompt_delete"}')"
                               href={"action=$deleteAction&selected_entries[]="|cat:$iterator->primaryKey()|href}>
                                <span class="icon_delete">&nbsp;</span>
                            </a>
                        </td>
                    {/if}
                </tr>
                {if $iterator->hasRows()}
                    {$iterator->nextRow()}
                {/if}
            {/section}
        </tbody>
    {/if}
    </table>
</div>
{if !$iterator->getRowCount()}
    <div class="gui_generator_no_entries_found">{lang id="NO_ENTRIES_FOUND"}</div>
{/if}
<script type="text/javascript"><!--
    $(function() {ldelim}
        $('.gui_generator_image a').fancybox();
    {rdelim});
//--></script>