{if $iterator->getRowCount()}
    <!-- BEGIN insert form contents -->
    <div class="gui_generator_config optionbody">
        {section name="update" loop=$iterator->getRowCount()}
            {foreach from=$iterator item="field"}
            <div class="optionitem {$iterator->getCssClass()}" title="{$field->getTitle()}">
                <div class="label">
                    <!-- {if $field->isFilterable() && $iterator->hasRows() && $form->getEntriesPerPage() > 1}
                        BEGIN: filter settings
                    -->
                    <div class="gui_generator_filter">
                        <a href={"action=$ACTION"|href} class="gui_generator_arrow"
                           onclick="return yanaApplyFilter(this, '{$form->getName()}[filter][{$field->getName()}]', '{$field->getFilterValue()|entities}', '{lang id="WHERE.PROMPT"}')">
                            {if $field->hasFilter()}
                                <span class="icon_filter_hover" title='{lang id="WHERE.EDIT"}'>&nbsp;</span>
                            {else}
                                <span class="icon_filter" title='{lang id="WHERE.SET"}'>&nbsp;</span>
                            {/if}
                        </a>
                    </div>
                    <!--
                        END: filter settings
                    {/if}-->

                    <div class="gui_generator_description">
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
                </div>
                {$iterator}
            </div>
            {/foreach}
            {if $iterator->hasRows()}
                {$iterator->nextRow()}
            {/if}
        {/section}
    </div>
{else}
    <div class="gui_generator_no_entries_found">{lang id="NO_ENTRIES_FOUND"}</div>
{/if}
<script type="text/javascript"><!--
    $(function() {ldelim}
        $('.gui_generator_image a').fancybox();
    {rdelim});
//--></script>