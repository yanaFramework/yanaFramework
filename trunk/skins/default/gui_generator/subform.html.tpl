{if $form->getFields() || $form->hasAllInput()}
    <fieldset id="{$form->getName()}-edit" class="gui_generator_pane">
        {if $form->getTitle()}<legend onclick="$(this).find('~ div, ~ input').toggle('slow');">{$form->getTitle()}</legend>{/if}
        <form method="post" action="{$PHP_SELF}" enctype="multipart/form-data" accept-charset="UTF-8" class="gui_generator_toolbar" id="{$form->getName()}-toolbar">
            <input type="hidden" name="id" value="{$ID}"/>
            <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
            {if $ACTION}<input type="hidden" name="action" value="{$ACTION}"/>{/if}
            <div class="gui_generator_settings" id="{$form->getName()}-settings">
                <!-- BEGIN settings {assign var="formName" value=$form->getName()} -->
                <span class="buttonize_static"><span class="icon_edit_hover">&nbsp;</span></span>
                <!-- BEGIN select layout -->
                <span class="gui_select_layout">
                    <a href={"action={$ACTION}&{$formName}[layout]=0"|href}>
                        <img class="gui_layout_preview" title="Layout 1" alt="" src="layout_select_00.png"/>
                        <img class="gui_layout" alt="[1]" src="layout_select_00.png"/>
                    </a>
                    <a href={"action={$ACTION}&{$formName}[layout]=1"|href}>
                        <img class="gui_layout_preview" title="Layout 2" alt="" src="layout_select_01.png"/>
                        <img class="gui_layout" alt="[2]" src="layout_select_01.png"/>
                    </a>
                    <a href={"action={$ACTION}&{$formName}[layout]=2"|href}>
                        <img class="gui_layout_preview" title="Layout 3" alt="" src="layout_select_02.png"/>
                        <img class="gui_layout" alt="[3]" src="layout_select_02.png"/>
                    </a>
                    <a href={"action={$ACTION}&{$formName}[layout]=3"|href}>
                        <img class="gui_layout_preview" title="Layout 4" alt="" src="layout_select_03.png"/>
                        <img class="gui_layout" alt="[4]" src="layout_select_03.png"/>
                    </a>
                    <a href={"action={$ACTION}&{$formName}[layout]=4"|href}>
                        <img class="gui_layout_preview" title="Layout 5" alt="" src="layout_select_04.png"/>
                        <img class="gui_layout" alt="[5]" src="layout_select_04.png"/>
                    </a>
                    <a href={"action={$ACTION}&{$formName}[layout]=5"|href}>
                        <img class="gui_layout_preview" title="Layout 6" alt="" src="layout_select_05.png"/>
                        <img class="gui_layout" alt="[6]" src="layout_select_05.png"/>
                    </a>
                    <a href={"action={$ACTION}&{$formName}[layout]=6"|href}>
                        <img class="gui_layout_preview" title="Layout 7" alt="" src="layout_select_06.png"/>
                        <img class="gui_layout" alt="[7]" src="layout_select_06.png"/>
                    </a>
                </span>
                <!-- BEGIN select entries per page -->
                <label class="comment">{lang id="BUTTON_ENTRIES"}
                    <select onchange="document.location.replace('{"action={$ACTION}&{$formName}[entries]="|url}'+this.options[this.selectedIndex].value);">
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
                {if $form->hasFilter()}
                    <input type="submit" title='{lang id="WHERE.DELETE"}' name="{$form->getName()}[dropfilter]" value='{lang id="WHERE.DELETE"}'/>
                {/if}
            </div>
        </form>
        {if $form->isSelectable() && ($form->getSearchAction() || $form->hasSearchableChildren())}
            <form method="post" action="{$PHP_SELF}" enctype="multipart/form-data" accept-charset="UTF-8">
                <input type="hidden" name="id" value="{$ID}"/>
                <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
                <input type="hidden" name="action" value="{$form->getSearchAction()}"/>
                <fieldset id="{$form->getName()}-search" class="gui_generator_search">
                    <legend>
                        <span class="icon_magnifier">&nbsp;</span>
                        {lang id="advanced_search"}
                    </legend>
                    {import file="search.html.tpl" form=$form}
                </fieldset>
            </form>
        {/if}
        {if $form->isSelectable() && !$form->getContext('update')->getRows()->count()}
            <div class="gui_generator_no_entries_found">{lang id="NO_ENTRIES_FOUND"}</div>
            {if $form->hasFilter()}
                <form method="post" action="{$PHP_SELF}" enctype="multipart/form-data" accept-charset="UTF-8">
                    <input type="hidden" name="id" value="{$ID}"/>
                    <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
                    {if $ACTION}<input type="hidden" name="action" value="{$ACTION}"/>{/if}
                    <input type="submit" title='{lang id="WHERE.DELETE"}' name="{$form->getName()}[dropfilter]" value='{lang id="WHERE.DELETE"}'/>
                </form>
            {/if}
        {/if}
        {if $form->isInsertable() && $form->getInsertAction()}
            <form method="post" action="{$PHP_SELF}" enctype="multipart/form-data" accept-charset="UTF-8" id="{$form->getName()}-new">
                <input type="hidden" name="id" value="{$ID}"/>
                <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
                <input type="hidden" name="action" value="{$form->getInsertAction()}"/>
                <fieldset class="gui_generator_new">
                    <legend>
                        <span class="icon_new">&nbsp;</span>
                        {lang id="new_entry"}
                    </legend>
                    {if $form->getLayout() === 0}
                        {import file="layout2.html.tpl" form=$form->getInsertForm()}
                    {elseif $form->getLayout() === 1}
                        {import file="layout1.html.tpl" form=$form->getInsertForm()}
                    {elseif $form->getLayout() === 2}
                        {import file="layout2.html.tpl" form=$form->getInsertForm()}
                    {elseif $form->getLayout() === 3}
                        {import file="layout2.html.tpl" form=$form->getInsertForm()}
                    {else}
                        {import file="layout4.html.tpl" form=$form->getInsertForm()}
                    {/if}
                    {* Spam protection: Captcha *}
                    {if $PROFILE.SPAM.AVAILABLE && $PROFILE.SPAM.CAPTCHA && ($PROFILE.SPAM.PERMISSION || !$PERMISSION)}
                        <label class="gui_generator_captcha" title='{lang id="SECURITY_IMAGE.DESCRIPTION"}'>
                            <span class="gui_generator_mandatory" title='{lang id="MANDATORY"}'>*</span>
                            {lang id="SECURITY_IMAGE.TITLE"}
                            {captcha}
                        </label>
                    {/if}
                    <div class="gui_generator_buttons">
                        <input type="submit" name="action[{$form->getInsertAction()}]" value='{lang id="button_save"}'/>
                    </div>
                </fieldset>
            </form>
        {/if}
        {if $form->isSelectable() && $form->getExportAction()}
            <form method="post" action="{$PHP_SELF}" enctype="multipart/form-data" accept-charset="UTF-8" id="{$form->getName()}-export">
                <input type="hidden" name="id" value="{$ID}"/>
                <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
                <input type="hidden" name="action" value="{$form->getExportAction()}"/>
                <fieldset class="gui_generator_export">
                    <legend>
                        <span class="icon_download">&nbsp;</span>
                        {lang id="export_as_csv"}
                    </legend>
                    <div class="gui_generator_config">
                        <div class="optionbody" title='{lang id="export_as_csv"}' id="{$form->getName()}-export">
                            <div class="optionitem gui_generator_export_column" id="{$form->getName()}-export-col">
                                <div class="label">{lang id="csv_column_delimiter"}</div>
                                <select name="col">
                                    <option value="1">;</option>
                                    <option value="2">,</option>
                                    <option value="3">{lang id="char_tab"}</option>
                                </select>
                            </div>
                            <div class="optionitem gui_generator_export_row" id="{$form->getName()}-export-row">
                                <div class="label">{lang id="csv_row_delimiter"}</div>
                                <select name="row">
                                    <option value="1">{lang id="char_new_line"}</option>
                                    <option value="2">;</option>
                                </select>
                            </div>
                            <div class="optionitem  gui_generator_export_string" id="{$form->getName()}-export-string">
                                <div class="label">{lang id="csv_string_delimiter"}</div>
                                <select name="text">
                                    <option value="1">"</option>
                                    <option value="2">'</option>
                                    <option value="3">{lang id="char_none"}</option>
                                </select>
                            </div>
                            <div class="optionitem gui_generator_export_header" id="{$form->getName()}-export-header">
                                <div class="label">{lang id="csv_has_header"}</div>
                                <label class="gui_generator_bool">
                                    <input type="radio" name="header" value="1" checked="checked">
                                    {lang id="yes"}
                                </label>
                                <label class="gui_generator_bool">
                                    <input type="radio" name="header" value="0">
                                    {lang id="no"}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="gui_generator_buttons">
                        <input type="submit" name="action[{$form->getExportAction()}]" value='{lang id="ok"}'/>
                    </div>
                </fieldset>
            </form>
        {/if}
        {if $form->isSelectable() && $form->getContext('update')->getRows()->count()}
            {$updateForm=$form->getUpdateForm()}
            <form method="post" action="{$PHP_SELF}" enctype="multipart/form-data" accept-charset="UTF-8" class="gui_generator_edit">
                <input type="hidden" name="id" value="{$ID}"/>
                <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
                <input type="hidden" name="action" value="{$form->getUpdateAction()}"/>
                <div class="gui_generator_edit">
                    <div class="gui_generator_header">
                        {$updateForm->getHeader()}
                    </div>

                    {if $form->getLayout() === 0}
                        {import file="layout0.html.tpl" form=$updateForm}
                    {elseif $form->getLayout() === 1}
                        {import file="layout1.html.tpl" form=$updateForm}
                    {elseif $form->getLayout() === 2}
                        {import file="layout2.html.tpl" form=$updateForm}
                    {elseif $form->getLayout() === 3}
                        {import file="layout3.html.tpl" form=$updateForm}
                    {elseif $form->getLayout() === 4}
                        {import file="layout4.html.tpl" form=$updateForm}
                    {elseif $form->getLayout() === 5}
                        {import file="layout5.html.tpl" form=$updateForm}
                    {elseif $form->getLayout() === 6}
                        {import file="layout6.html.tpl" form=$updateForm}
                    {else}
                        {import file="layout0.html.tpl" form=$updateForm}
                    {/if}

                    <!-- BEGIN page selector -->
                    <div class="gui_generator_footer">
                        {$updateForm->getFooter()}
                    </div>
                    {if $form->isUpdatable() && $form->getUpdateAction() && $updateForm->getRowCount()}
                        <div class="gui_generator_buttons">
                            {* Spam protection: Captcha *}
                            {if $PROFILE.SPAM.AVAILABLE && $PROFILE.SPAM.CAPTCHA && ($PROFILE.SPAM.PERMISSION || !$PERMISSION)}
                                <label class="gui_generator_captcha" title='{lang id="SECURITY_IMAGE.DESCRIPTION"}'>
                                    <span class="gui_generator_mandatory" title='{lang id="MANDATORY"}'>*</span>
                                    {lang id="SECURITY_IMAGE.TITLE"}
                                    {captcha}
                                </label>
                            {/if}
                            <input type="submit" name="action[{$form->getUpdateAction()}]" value='{lang id="button_save"}'/>
                        </div>
                    {/if}
                </div>
            </form>
        {/if}
        <script type="text/javascript"><!--
            $(document).ready(function() {
                $('#{$form->getName()}-settings').hide();
                var subFormText = 
                {if $form->getEntriesPerPage() == 1}
                    '<a class="gui_generator_icon_up buttonize" title=\'{lang id="title_overview"}\'' +
                    'href={"action={$ACTION}&{$formName}[entries]=5&{$formName}[layout]={$form->getTemplate()}&{$formName}[page]=0"|href}>' +
                    '<span class="icon_upload">&nbsp;</span></a>' +
                {/if}
                {if $form->isInsertable() && $form->getInsertAction()}
                    '<a class="gui_generator_icon_new buttonize" href="javascript://" ' +
                    'onclick="return yanaGuiToggleVisibility(\'{$formName}-new\');">' +
                    '<span class="icon_new">&nbsp;</span>&nbsp;{lang id="new_entry"}</a>' +
                {/if}
                {if $form->isSelectable() && ($form->getEvent('search') || $form->hasSearchableChildren()) && $form->getContext('update')->getRows()->count()}
                    '<a class="gui_generator_icon_search buttonize" href="javascript://"' +
                    'onclick="return yanaGuiToggleVisibility(\'{$form->getName()}-search\');">' +
                    '<span class="icon_magnifier">&nbsp;</span>&nbsp;{lang id="advanced_search"}</a>' +
                {/if}
                {if $form->isSelectable() && $form->getExportAction() && $form->getContext('update')->getRows()->count()}
                    '<a class="gui_generator_icon_export buttonize" href="javascript://"' +
                    'onclick="return yanaGuiToggleVisibility(\'{$form->getName()}-export\');">' +
                    '<span class="icon_download">&nbsp;</span>&nbsp;{lang id="export_as_csv"}</a>' +
                {/if}
                    '<a class="gui_generator_icon_settings buttonize" href="javascript://"' +
                    'onclick="return yanaGuiToggleVisibility(\'{$form->getName()}-settings\');">' +
                    '<span class="icon_edit">&nbsp;</span>&nbsp;{lang id="view_settings"}</a>';
                $('#{$form->getName()}-toolbar').prepend(subFormText);
                $('#{$form->getName()}-search').hide();
                $('#{$form->getName()}-export').hide();
                {if !($form->isInsertable() && !$form->hasFilter() && $form->getInsertAction() && $form->isSelectable() && !$form->getContext('update')->getRows()->count())}
                    $('#{$form->getName()}-new').hide();
                {/if}
                $.fn.fancybox.defaults.hideOnContentClick = true;
                $.fn.fancybox.defaults.titlePosition = 'over';
                $.fn.fancybox.defaults.showCloseButton = false;
                $.fn.fancybox.defaults.type = 'image';
            });
        //--></script>
    </fieldset>
{/if}
{if $form->getEntriesPerPage() === 1 || $form->getSearchTerm() || !$form->getFields()}
    {foreach from=$form->getForms() item="subform"}
        {import file="subform.html.tpl" form=$subform}
    {/foreach}
{/if}