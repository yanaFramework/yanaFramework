{if $form->isSelectable()}
    {if $form->getFields()}
        {assign var="iterator" value=$form->getIterator()}
        {if $iterator->getRowCount()}
            <fieldset id="{$form->getName()}-edit">
                {if $form->getTitle()}<legend onclick="$(this).find('~ div, ~ input').toggle('slow')">{$form->getTitle()}</legend>{/if}
                <div class="gui_generator_toolbar" id="{$form->getName()}-toolbar">
                    <div class="gui_generator_settings" id="{$form->getName()}-settings">
                        <!-- BEGIN settings {assign var="formName" value=$form->getName()} -->
                        <span class="buttonize_static"><span class="icon_edit_hover">&nbsp;</span></span>
                        <!-- BEGIN select layout -->
                        <span class="gui_select_layout">
                            <a href={"action=$ACTION&$formName%5Blayout]=0"|href}>
                                <img class="gui_layout_preview" title="Layout 1" alt="" src="layout_select_00.png"/>
                                <img class="gui_layout" alt="[1]" src="layout_select_00.png"/>
                            </a>
                            <a href={"action=$ACTION&$formName%5Blayout]=1"|href}>
                                <img class="gui_layout_preview" title="Layout 2" alt="" src="layout_select_01.png"/>
                                <img class="gui_layout" alt="[2]" src="layout_select_01.png"/>
                            </a>
                            <a href={"action=$ACTION&$formName%5Blayout]=2"|href}>
                                <img class="gui_layout_preview" title="Layout 3" alt="" src="layout_select_02.png"/>
                                <img class="gui_layout" alt="[3]" src="layout_select_02.png"/>
                            </a>
                            <a href={"action=$ACTION&$formName%5Blayout]=3"|href}>
                                <img class="gui_layout_preview" title="Layout 4" alt="" src="layout_select_03.png"/>
                                <img class="gui_layout" alt="[4]" src="layout_select_03.png"/>
                            </a>
                            <a href={"action=$ACTION&$formName%5Blayout]=4"|href}>
                                <img class="gui_layout_preview" title="Layout 5" alt="" src="layout_select_02.png"/>
                                <img class="gui_layout" alt="[5]" src="layout_select_02.png"/>
                            </a>
                            <a href={"action=$ACTION&$formName%5Blayout]=5"|href}>
                                <img class="gui_layout_preview" title="Layout 6" alt="" src="layout_select_02.png"/>
                                <img class="gui_layout" alt="[6]" src="layout_select_02.png"/>
                            </a>
                        </span>
                        <!-- BEGIN select entries per page -->
                        <label class="comment">{lang id="BUTTON_ENTRIES"}
                            <select onchange="document.location.replace('{"action=$ACTION&$formName%5Bentries]="|url}'+this.options[this.selectedIndex].value)">
                                <option value="{$form->getEntriesPerPage()}">{$form->getEntriesPerPage()}</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="75">75</option>
                                <option value="100">100</option>
                            </select>
                        </label>
                        <!-- BEGIN remove filter -->
                        {if $form->hasFilter() }
                            <input type="submit" title='{lang id="WHERE.DELETE"}' name="{$form->getName()}[dropfilter]" value='{lang id="WHERE.DELETE"}'/>
                        {/if}
                    </div>
                </div>
                <div class="gui_generator_edit">
                    {if $form->getLayout() === 0}
                        {import file="layout0.html.tpl" form=$form iterator=$iterator}
                    {elseif $form->getLayout() === 1}
                        {import file="layout1.html.tpl" form=$form iterator=$iterator}
                    {elseif $form->getLayout() === 2}
                        {import file="layout2.html.tpl" form=$form iterator=$iterator}
                    {elseif $form->getLayout() === 3}
                        {import file="layout3.html.tpl" form=$form iterator=$iterator}
                    {elseif $form->getLayout() === 4}
                        {import file="layout4.html.tpl" form=$form iterator=$iterator}
                    {elseif $form->getLayout() === 5}
                        {import file="layout5.html.tpl" form=$form iterator=$iterator}
                    {else}
                        {import file="layout0.html.tpl" form=$form iterator=$iterator}
                    {/if}
                    <!-- BEGIN page selector -->
                    <div class="gui_generator_footer">
                        {$form->getListOfEntries()}
                    </div>
                    {if $form->isUpdatable() && $form->getUpdateAction() && $iterator->getRowCount()}
                        <div class="gui_generator_buttons">
                            <input type="submit" name="action[{$form->getUpdateAction()}]" value='{lang id="button_save"}'/>
                        </div>
                    {/if}
                </div>
            </fieldset>
            <script type="text/javascript"><!--
                $('#{$form->getName()}-settings').hide();
                $('#{$form->getName()}-toolbar').append(
                {if $form->getEntriesPerPage() == 1}
                    '<a class="gui_generator_icon_up buttonize" title=\'{lang id="title_overview"}\'' +
                    'href={"action=$ACTION&$formName%5Bentries]=5&$formName"|cat:"[layout]="|cat:$form->getTemplate()|href}>' +
                    '<span class="icon_upload">&nbsp;</span></a>' +
                {/if}
                    '<a class="gui_generator_icon_settings buttonize" href="javascript://"' +
                    'onclick="return yanaGuiToggleVisibility(\'{$form->getName()}-settings\');">' +
                    '<span class="icon_edit">&nbsp;</span></a>'
                );
            //--></script>
        {else}
            {if $form->getInsertAction()}
                <form method="post" action="{$PHP_SELF}" enctype="multipart/form-data" accept-charset="UTF-8" class="gui_generator_new">
                    <input type="hidden" name="id" value="{$ID}"/>
                    <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
                    <fieldset>
                        <legend>{$form->getTitle()} ({lang id="NO_ENTRIES_FOUND"}): {lang id="new_entry"}</legend>
                        {import file="insert.html.tpl" form=$form}
                    </fieldset>
                </form>
            {else}
                <div class="gui_generator_no_entries_found">{lang id="NO_ENTRIES_FOUND"}</div>
            {/if}
        {/if}
    {/if}
    {foreach from=$form->getForms() item="subform"}
        {if $form->getEntriesPerPage() === 1}
        {import file="subform.html.tpl" form=$subform}
        {/if}
    {/foreach}
{/if}