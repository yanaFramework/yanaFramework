<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
        <script type="text/javascript" language="javascript" src="index.js"></script>
    </head>

<body style="text-align: left; width: 760px; margin: auto;">

<!-- BEGIN document head -->

<script type="text/javascript" language="javascript">
/* BEGIN translation table */
var language = new Array();
language[0] = '{lang id="SDK.JS_0"}';
language[1] = '{lang id="SDK.JS_1"}';
/* END translation table */
</script>

<noscript>

    <p>JavaScript has to be activated to view this page.</p>

</noscript>

<!-- END   document head -->

<!-- BEGIN document menu -->
<div id="navigation_top" title="Navigation">
    <span style="margin-right: 20px; vertical-align: 4px;">
        {lang id="SDK.NAV.0"}:
    </span>
    <button id="btn_previous" onclick="previous()" disabled="disabled">{lang id="SDK.NAV.1"}</button>
    <button id="btn_next" onclick="next()">{lang id="SDK.NAV.2"}</button>
</div>

<!-- END   document menu -->

  <div id="utilities">

        <div id="toc2">
        <!-- BEGIN toolbar -->

          <div class="list_of_steps" id="page_1">

            <div style="float: right">
                <a href="javascript://" title="{lang id="ADMIN.15"}"
                   class="buttonize">
                   <span onclick="return toggleHelp(this)" class="icon_info_hover">&nbsp;</span>
                </a>
            </div>

            <button type="button" onclick="show(1,1,1)">{lang id="SDK.STEP1"}</button>
              <img alt="" src="sml.gif"/>
            <button type="button" onclick="show(1,2,1)">{lang id="SDK.STEP2"}</button>
              <img alt="" src="sml.gif"/>
            <button type="button" onclick="show(1,3,1)">{lang id="SDK.STEP3"}</button>
              <img alt="" src="sml.gif"/>
            <button type="button" onclick="show(1,4,1)">{lang id="SDK.STEP4"}</button>
              <img alt="" src="sml.gif"/>
            <button type="button" onclick="show(1,5,1)">{lang id="SDK.COMPLETE"}</button>

          </div>

        <!-- END toolbar -->
        </div>

        <div id="toc3">
        <!-- BEGIN toolbar -->

          <div class="toolbar" id="page_1_1">

            <button type="button" onclick="show(1,1,1)">{lang id="SDK.APP"}</button>
            <button type="button" onclick="show(1,1,2)">{lang id="SDK.AUTHOR"}</button>
            <button type="button" onclick="show(1,1,3)">{lang id="SDK.STEP1_3"}</button>

          </div>

        <!-- END toolbar -->
        <!-- BEGIN toolbar -->

          <div class="toolbar" id="page_1_2">

            <button type="button" onclick="show(1,2,1)">{lang id="SDK.STEP2_2"}</button>
            <button type="button" onclick="show(1,2,2)">{lang id="SDK.STEP2_3"}</button>

          </div>

        <!-- END toolbar -->
        </div>

      <div id="pages">

        <!-- BEGIN utility -->
        <form method="post" action="{$PHP_SELF}" enctype="multipart/form-data" id="utility_1">
          <input type="hidden" name="action" value="SDK_WRITE_PLUGIN"/>
          <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>

          <!-- BEGIN section title -->

            <div class="section_title">{lang id="SDK.TITLE"}</div>

          <!-- END section title -->

            <div class="page" id="page_1_1_1" title="1 {lang id="SDK.STEP1"} &ndash; {lang id="SDK.STEP1_1"}">

                <img id="image1" {if $PLUGIN.image} src="{$IMG_SRC}{$PLUGIN.image}" {else}src="{$IMG_SRC}config.png"{/if} class="additional" title="{lang id="PREVIEW"}" alt="{lang id="PREVIEW"}"/>

                <table summary='{lang id="SDK.STEP1_1"}'>
                  <tr title="{lang id="SDK.DESC_ID"}: a-z, 0-9, '-', '_', ä, ö, ü, ß, ' '">
                    <th>{lang id="SDK.FIELD2"}</th>
                    <td><input id="input_name" type="text" name="name" maxlength="15" value="{$PLUGIN.name|default:'mein Plugin'}" class="mandatory"/></td>
                  </tr>
                  <tr>
                    <th>{lang id="SDK.DESCRIPTION"}</th>
                    <td><textarea id="textarea_description" rows="4" cols="30" name="description" class="optional">{$PLUGIN.description}</textarea></td>
                  </tr>
                  <tr>
                    <th>{lang id="SDK.IMG"}</th>
                    <td>
                      <select name="image" class="optional" onchange="document.getElementById('image1').src='{$IMG_SRC}'+this.value" onkeyup="document.getElementById('image1').src='{$IMG_SRC}'+this.value">
                        {foreach item=FILE from=$FILES}
                        {if $FILE != "blank.png"}
                        <option value="{$FILE}" {if $PLUGIN.image == $FILE || ( !$PLUGIN.image && $FILE == 'config.png' )} selected="selected" {/if}>{$FILE}</option>
                        {/if}
                        {/foreach}
                      </select>
                    </td>
                  </tr>
                </table>

            </div>

            <div class="page" id="page_1_1_2" title="1 {lang id="SDK.STEP1"} &ndash; {lang id="SDK.STEP1_2"}">

                <table summary='{lang id="SDK.STEP1_2"}'>
                  <tr>
                    <th>{lang id="SDK.FIELD3"}</th>
                    <td><input type="text" name="author" value="{$PLUGIN.author|default:'Max Mustermann'}" class="optional"/></td>
                  </tr>
                  <tr>
                    <th>{lang id="SDK.FIELD5"}</th>
                    <td><input type="text" name="url" value="{$PLUGIN.url|default:'http://www.domain.tld'}" class="optional"/></td>
                  </tr>
                </table>

            </div>

            <div class="page" id="page_1_1_3" title="1 {lang id="SDK.STEP1"} &ndash; {lang id="SDK.STEP1_3"}">

                <table summary="{lang id="SDK.STEP1_3"}">
                  <tr>
                    <th>{lang id="SDK.FIELD6"}</th>
                    <td><select name="priority" class="mandatory">
                          <option {if $PLUGIN.priority == 'lowest'} selected="selected" {/if} value="lowest">{lang id="SDK.PRIORITY.LOWEST"}</option>
                          <option {if $PLUGIN.priority == 'low'} selected="selected" {/if} value="low">{lang id="SDK.PRIORITY.LOW"}</option>
                          <option {if $PLUGIN.priority == 'normal' || ! $PLUGIN.priority} selected="selected" {/if} value="normal">{lang id="SDK.PRIORITY.NORMAL"}</option>
                          <option {if $PLUGIN.priority == 'high'} selected="selected" {/if} value="high">{lang id="SDK.PRIORITY.HIGH"}</option>
                          <option {if $PLUGIN.priority == 'highest'} selected="selected" {/if} value="highest">{lang id="SDK.PRIORITY.HIGHEST"}</option>
                        </select>
                    </td>
                  </tr>
                  <tr>
                    <th>{lang id="SDK.FIELD7"}</th>
                    <td><select name="type" class="mandatory">
                          <option {if $PLUGIN.type == 'default'} selected="selected" {/if} value="default">default</option>
                          <option {if $PLUGIN.type == 'primary'} selected="selected" {/if} value="primary">primary</option>
                          <option {if $PLUGIN.type == 'config'} selected="selected" {/if} value="config">config</option>
                          <option {if $PLUGIN.type == 'read'} selected="selected" {/if} value="read">read</option>
                          <option {if $PLUGIN.type == 'write'} selected="selected" {/if} value="write">write</option>
                          <option {if $PLUGIN.type == 'security'} selected="selected" {/if} value="security">security</option>
                          <option {if $PLUGIN.type == 'library'} selected="selected" {/if} value="library">library</option>
                        </select>
                    </td>
                  </tr>
                  <tr>
                    <th title="{lang id="SDK.DESC8"}">{lang id="SDK.FIELD8"}</th>
                    <td><input type="text" id="input2" name="parent" class="optional" value="{$PLUGIN.parent}"/>
                        <select onchange="document.getElementById('input2').value=this.value" class="additional">
                          <optgroup label="{lang id="CHOOSE_OPTION"}">
                            <option value="" style="background: #fca">{lang id="SDK.NONE"}</option>
                            <option value="config" style="background: #ddd">config</option>
                            <option value="guestbook" style="background: #ddd">guestbook</option>
                            <option value="ipblock" style="background: #ddd">ipblock</option>
                            <option value="rss" style="background: #ddd">rss</option>
                            <option value="sdk" style="background: #ddd">sdk</option>
                            <option value="user" style="background: #ddd">user</option>
                          </optgroup>
                        </select>
                    </td>
                  </tr>
                  <tr>
                    <th>{lang id="SDK.FIELD9"}</th>
                    <td title="{lang id="SDK.DESC9"}"><input type="text" class="optional" name="package" value="{$PLUGIN.package}"/></td>
                  </tr>
                </table>

            </div>

            <div class="page" id="page_1_2_1" title="2 {lang id="SDK.STEP2"} &ndash; {lang id="SDK.STEP2_2"}">

                <p class="help">
                    {lang id="HELP.3"}
                </p>

                <table summary="Interface">
                  <tr title="{lang id="SDK.DESC_ID"}: a-z, 0-9, '-', '_'">
                    <th>{lang id="SDK.INTERFACE.0"}</th>
                    <td><input type="text" id="interface_action" value="" class="mandatory"/></td>
                  </tr>
                  <tr>
                    <th>{lang id="SDK.INTERFACE.1"}</th>
                    <td>
                      <select id="interface_type" class="mandatory">
                        <option value="default">default</option>
                        <option value="primary">primary</option>
                        <option value="config">config</option>
                        <option value="read">read</option>
                        <option value="write">write</option>
                        <option value="security">security</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <th>{lang id="SDK.FIELD11.2"}</th>
                    <td class="optional">
                        <input type="text" id="interface_template" class="optional" value=""/>
                        <select onchange="document.getElementById('interface_template').value=this.value" class="additional">
                            <option value="">{lang id="CHOOSE_OPTION"}</option>
                            <option value="MESSAGE">{lang id="SDK.FIELD11.3.0"}</option>
                            <option value="NULL">{lang id="SDK.FIELD11.3.1"}</option>
                        </select>
                    </td>
                  </tr>
                  <tr title="{lang id="SDK.INTERFACE.DESC"}">
                    <th>{lang id="SDK.INTERFACE.5"}</th>
                    <td>
                      <label>{lang id="SDK.FIELD11.5.0"}:
                          <select id="interface_group" class="optional">
                              <option value="">{lang id="SDK.FIELD11.4"}</option>
                              {foreach from=$GROUPS key="value" item="text"}
                              <option value="{$value}">{$text}</option>
                              {/foreach}
                          </select>
                      </label>
                      <label>{lang id="SDK.FIELD11.5.1"}:
                          <select id="interface_role" class="optional">
                              <option value="">{lang id="SDK.FIELD11.4"}</option>
                              {foreach from=$ROLES key="value" item="text"}
                              <option value="{$value}">{$text}</option>
                              {/foreach}
                          </select>
                      </label>
                      <label>{lang id="SDK.FIELD11.5.2"}:
                          <input type="text" id="interface_permission" class="optional" value="0" size="3"/>
                      </label>
                    </td>
                  </tr>
                  <tr>
                    <th>{lang id="SDK.FIELD12.0"}</th>
                    <td>
                        <input type="text" id="interface_menu" class="optional" value=""/>
                        <select onchange="document.getElementById('interface_menu').value=this.value" class="additional">
                            <option value="">{lang id="CHOOSE_OPTION"}</option>
                            <option value="start">{lang id="SDK.FIELD12.1"}</option>
                            <option value="setup">{lang id="SDK.FIELD12.2"}</option>
                        </select>
                    </td>
                  </tr>
                </table>

                <button type="button" class="optional" onclick="addInterface()">{lang id="SDK.FIELD11.6"}</button>

                <fieldset style="visibility: hidden;">
                    <legend>{lang id="SDK.INTERFACE.LIST"}</legend>

                    <table summary="Schnittstelle" cellspacing="5" id="table_preview">
                        <thead>
                            <tr>
                                <th>{lang id="SDK.INTERFACE.0"}</th>
                                <th>{lang id="SDK.INTERFACE.1"}</th>
                                <th>{lang id="SDK.FIELD11.2"}</th>
                                <th>{lang id="SDK.FIELD11.5.0"}</th>
                                <th>{lang id="SDK.FIELD11.5.1"}</th>
                                <th>{lang id="SDK.FIELD11.5.2"}</th>
                                <th>{lang id="SDK.FIELD12.0"}</th>
                            </tr>
                        </thead>
                        <tbody id="interface_preview">
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>

                </fieldset>
            </div>

            <div class="page" id="page_1_2_2" title="2 {lang id="SDK.STEP2"} &ndash; {lang id="SDK.STEP2_4"}">

                <p class="help">
                    <b>{lang id="HELP.4"}:</b>
                    {lang id="SDK.INTERFACE.0"},
                    {lang id="SDK.INTERFACE.1"},
                    {lang id="SDK.FIELD11.2"},
                    {lang id="SDK.FIELD11.5.0"},
                    {lang id="SDK.FIELD11.5.1"},
                    {lang id="SDK.FIELD11.5.2"},
                    {lang id="SDK.FIELD12.0"}
                </p>

                <textarea name="interface" id="interface" class="optional" cols="80" rows="10">{$PLUGIN.interface}</textarea>

            </div>

            <div class="page" id="page_1_3_1" title="3 {lang id="SDK.STEP3"} &ndash; {lang id="SDK.STEP3_2"}">

                <p class="help">
                    {lang id="HELP.0"}{lang id="HELP.6"}
                </p>

                <label class="mandatory" style="padding: 5px;">{lang id="SDK.SOURCE"} <input name="sourcefile" type="file" class="mandatory" onchange="if (!this.value.match(/\S+\.xml/i)) {ldelim} alert('{lang id="HELP.7"}'); this.value = ''; this.className = 'invalid'; {rdelim} else {ldelim} this.className = 'mandatory'; {rdelim}" value="{$PLUGIN.sourcefile}"/></label>

            </div>

            <div class="page" id="page_1_4_1" title="4 {lang id="SDK.STEP4"} &ndash; {lang id="SDK.STEP4_1"}">

                <p class="help">
                    {lang id="HELP.2"}
                </p>
                <ol>
{foreach from=$LIST_OF_DBMS item=label key=dbms}
                    <li style="padding: 5px; margin: 5px;" class="optional">
                        <label>
                            <input name="{$dbms}" type="file" class="optional" onchange="if (!this.value.match(/\S+\.sql$/i)) {ldelim} alert('{lang id="HELP.8"}'); this.value = ''; this.className = 'invalid'; {rdelim} else {ldelim} this.className = 'optional'; {rdelim}" value="{$PLUGIN.sqlfile}"/>
                            {$label}
                        </label>
                    </li>
{/foreach}
                </ol>
          </div>

            <div class="page" id="page_1_5_1" title="5 {lang id="SDK.COMPLETE"}">

                <p class="help">
                    {lang id="HELP.5"}
                </p>

                <div align="center">
                    <label>
                      <input type="checkbox" value="true" name="overwrite" {if $PLUGIN.overwrite}checked="checked"{/if}/>
                      {lang id="SDK.REPLACE_FILES"}
                    </label>
                </div>

                <div align="center">
                    <input type="submit" value="{lang id="BUTTON_SAVE"}" />
                </div>

          </div>

        </form>
        <!-- END utility -->

    </div>

  </div>

<script language="javascript" type="text/javascript">
firstPage();
</script>

</body>

</html>
