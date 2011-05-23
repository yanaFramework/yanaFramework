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
                        {if $field->isSingleLine()}
                            <span class="optionitem {$field->getCssClass()}" title="{$field->getTitle()}">
                                <!-- {if $field->isFilterable() && $form->hasRows() && $form->getEntriesPerPage() > 1}
                                    BEGIN: filter settings
                                -->
                                <span class="gui_generator_filter">
                                    <a href={"action=$ACTION"|href} class="gui_generator_arrow"
                                       onclick="return yanaApplyFilter(this, '{$form->getName()}[filter][{$field->getName()}]', '{$field->getFilterValue()|entities}', '{lang id="WHERE.PROMPT"}')">
                                        {if $field->hasFilter()}
                                            <span class="icon_filter_hover" title='{lang id="WHERE.EDIT"}'>&nbsp;</span>
                                        {else}
                                            <span class="icon_filter" title='{lang id="WHERE.SET"}'>&nbsp;</span>
                                        {/if}
                                    </a>
                                </span>
                                <!--
                                    END: filter settings
                                {/if}-->

                                <span class="gui_generator_description">
                                    {if !$field->isNullable()}
                                        <span class="gui_generator_mandatory" title="{lang id="MANDATORY"}">*</span>
                                    {/if}
                                    {if $form->hasRows() && $form->getEntriesPerPage() > 1}
                                        {assign var="url" value="action=$ACTION&$formName"|cat:"[orderby]={$field->getName()}&$formName"|cat:"[desc]"}
                                        <a title='{lang id="ORDER.BY"} &quot;{$field->getTitle()}&quot;' href={"$url=0"|href}>
                                            {$field->getTitle()}
                                        </a>
                                    {else}
                                        {$field->getTitle()}
                                    {/if}
                                </span>
                                {$field}
                            </span>
                        {/if}
                    {/foreach}
                </div>
            </div>
            <span onclick="if (confirm('{lang id="prompt_delete"}')) document.location.href = '{"action=$deleteAction&selected_entries[]="|cat:$form->getPrimaryKey()|href}'" title='{lang id="delete"}' class="gui_generator_delete delete"></span>
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
            photoW: 75,
            photoH: 75,
            showShuffle: false,
            showViewAll: false
        {rdelim});
    {rdelim});
//--></script>