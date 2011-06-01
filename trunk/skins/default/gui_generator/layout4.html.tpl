{assign var="formName" value=$form->getName()}
    <!-- BEGIN insert form contents -->
    <div class="gui_generator_config optionbody">
        {for $i=1 to max($form->getRowCount(), ! $form->hasRows())}
            {foreach from=$form item="field"}
            <div class="optionitem {$field->getCssClass()}" title="{$field->getTitle()}">
                <div class="label">
                    <!-- {if $field->isFilterable() && $form->hasRows() && $form->getEntriesPerPage() > 1}
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
                        {if $form->hasRows() && $form->getEntriesPerPage() > 1 && $field->refersToTable()}
                            <a title='{lang id="ORDER.BY"} &quot;{$field->getTitle()}&quot;'
                               href={"action=$ACTION&{$formName}[orderby]={$field->getName()}&{$formName}[desc]=0"|href}>
                                {$field->getTitle()}
                            </a>
                        {else}
                            {$field->getTitle()}
                        {/if}
                    </div>
                </div>
                {$field}
            </div>
            {/foreach}
            {if $form->hasRows()}
                {$form->nextRow()}
            {/if}
        {forelse}
            <div class="gui_generator_no_entries_found">{lang id="NO_ENTRIES_FOUND"}</div>
        {/for}
    </div>
<script type="text/javascript"><!--
    $(function() {
        $('.gui_generator_image a').fancybox();
    });
//--></script>