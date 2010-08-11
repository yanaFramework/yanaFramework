<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
        <script type="text/javascript" language="JavaScript" src="../styles/dynamic-styles.js"></script>
        <script type="text/javascript" language="JavaScript" src="../styles/admin-styles.js"></script>
        <link rel="stylesheet" type="text/css" href="../styles/config.css"/>
        <link rel="stylesheet" type="text/css" href="../styles/user.css"/>
    </head>
<body>
<!--
    {if $USER_IS_EXPERT}
        {assign var="isExpertStyle" value=""}
        {assign var="nonExpertStyle" value="display:none;"}
    {else}
        {assign var="isExpertStyle" value="display:none;"}
        {assign var="nonExpertStyle" value=""}
    {/if}
-->
<div class="config_form" id="config_user_settings">
    <div class="config_head">
        {if !$USER_IS_EXPERT}
            <a title="{lang id="ADMIN.14"}" class="buttonize" href="javascript://"
               onclick="config_usermode(this, '{lang id="ADMIN.13"}', 'config_is_expert', document.getElementById('config_user_settings'))">
                <span class="icon_change">&nbsp;</span>
            </a>
        {else}
            <a title="{lang id="ADMIN.13"}" class="buttonize" href="javascript://"
               onclick="config_usermode(this, '{lang id="ADMIN.14"}', 'config_is_expert', document.getElementById('config_user_settings'))">
                <span class="icon_change">&nbsp;</span>
            </a>
        {/if}
        <div class="config_title" onclick="yanaToggleMenu(this.parentNode)">{lang id="USER.32"}</div>
    </div>
    <div class="help">
        <div class="help_text">
            {lang id="HELP.GRANTS_USER"}
            <span class="config_is_expert" style="{$isExpertStyle}">{lang id="HELP.GRANTS_INFO"}<br /></span>
            {lang id="HELP.GRANTS_EXECUTE"}
        </div>
    </div>
    <div class="option">
        <!-- Expert option -->
        <div class="optionbody config_is_expert" style="{$isExpertStyle}">
            <form id="user_settings" class="proxy_optionalbody" method="post" action="{$PHP_SELF}" enctype="multipart/form-data" accept-charset="UTF-8">
                <input type="hidden" name="action" value="set_user_proxy"/>
                <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
                <input type="hidden" name="id" value="{$ID}"/>
                <div class="gui_generator_edit">
                    {foreach from=$PROFILES item="profile"}
                      <div class="optionhead">{lang id="user.option.29"}: {$profile}</div>
                      {if $RULES[$profile]}
                        <table class="gui_generator_multi_entry proxy_table" summary="form" cellpadding="0" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;"><span class="icon_edit_hover">&nbsp;</span></th>
                                    <th>{lang id="user.option.1"}</th>
                                    <th>{lang id="user.option.24"}</th>
                                </tr>
                            </thead>
                            <tbody>
                              {foreach from=$RULES[$profile] key="ruleId" item="grant" name="row"}
                                  <tr class="gui_generator_{if ($smarty.foreach.row.iteration % 2) == 0 }even{else}odd{/if}_row">
                                      <td><input id="rule_{$ruleId}" type="checkbox" name="rules[]" value="{$ruleId}" /></td>
                                      <td><label for="rule_{$ruleId}">{$grant.GROUP_ID}</label></td>
                                      <td><label for="rule_{$ruleId}">{$grant.ROLE_ID}</label></td>
                                  </tr>
                              {/foreach}
                            </tbody>
                        </table>
                      {/if}
                      {if $LEVELS[$profile]}
                        <label class="optionitem" style="margin: 10px 18px">
                            <input type="checkbox" name="levels[]" value="{$profile}"/>
                            {lang id="user.option.23"}: {$LEVELS[$profile].SECURITY_LEVEL}
                        </label>
                      {/if}
                      <br class="proxy_clear" />
                    {/foreach}
                    </div>
                    {* create a selectbox with all Users*}
                    <div class="gui_generator_footer">
                        <div class="proxy_right">
                            <label>{lang id="USER.33"}:
                                <select name="user">
                                    {foreach from=$USERLIST key=key item=item}
                                        <option value="{$item}">{$item}</option>
                                    {/foreach}
                                </select>
                            </label>
                            <input type="submit" name="button" value="{lang id="user.option.27"}"/>
                        </div>
                        <div class="proxy_left">
                            <label>
                                <input type="checkbox" value="" title="{lang id="user.option.30"}" onclick="$('#user_settings input:checkbox').attr('checked', this.checked)" class="proxy_checkbox"/>
                                {lang id="user.option.30"}
                            </label>
                        </div>
                    </div>
                    <br class="proxy_clear" />
            </form>
        </div>
        <!-- Non-Expert option -->
        <div class="optionbody config_is_expert" style="text-align: center;{$nonExpertStyle}">
            <form class="proxy_optionalbody" method="post" action="{$PHP_SELF}" enctype="multipart/form-data" accept-charset="UTF-8">
                <input type="hidden" name="action" value="set_user_proxy"/>
                <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
                <input type="hidden" name="id" value="{$ID}"/>
                {foreach from=$PROFILES item="profile"}
                    {foreach from=$RULES[$profile] key="ruleId" item="grant"}
                        <input type="hidden" name="rules[]" value="{$ruleId}" />
                    {/foreach}
                    {if $LEVELS[$profile]}
                        <input type="hidden" name="levels[]" value="{$profile}"/>
                    {/if}
                {/foreach}
                <img src="proxy_assign.png" alt=""/>
                <p>
                    <label>{lang id="USER.33"}:
                        <select name="user">
                            {foreach from=$USERLIST key=key item=item}
                                <option value="{$item}">{$item}</option>
                            {/foreach}
                        </select>
                    </label>
                    <input type="submit" name="button" value="{lang id="user.option.27"}"/>
                </p>
            </form>
        </div>
    </div>
  {if $GRANTED_USERS}
    <div class="config_head">
        {if !$USER_IS_EXPERT}
            <a title="{lang id="ADMIN.14"}" class="buttonize" href="javascript://"
               onclick="config_usermode(this, '{lang id="ADMIN.13"}', 'config_is_expert', document.getElementById('config_user_settings'))">
                <span class="icon_change">&nbsp;</span>
            </a>
        {else}
            <a title="{lang id="ADMIN.13"}" class="buttonize" href="javascript://"
               onclick="config_usermode(this, '{lang id="ADMIN.14"}', 'config_is_expert', document.getElementById('config_user_settings'))">
                <span class="icon_change">&nbsp;</span>
            </a>
        {/if}
        <div class="config_title" onclick="yanaToggleMenu(this.parentNode)">{lang id="user.option.31"}</div>
    </div>
    <div class="help">
        <div class="help_text">
            {lang id="HELP.GRANTS_REMOVE_USER"}
            <span class="config_is_expert" style="{$isExpertStyle}">{lang id="HELP.GRANTS_REMOVE_EXPERT"}</span>
            <span class="config_is_expert" style="{$nonExpertStyle}">{lang id="HELP.GRANTS_REMOVE_NON_EXPERT"}</span>
        </div>
    </div>
    <div class="option">
        <!-- Expert option -->
        <div class="optionbody config_is_expert" style="{$isExpertStyle}">
            {* create a list of all users the allready become some rights from current user *}
            <form class="proxy_optionalbody" method="post" action="{$PHP_SELF}" enctype="multipart/form-data" accept-charset="UTF-8">
              <input type="hidden" name="action" value="remove_user_proxy"/>
              <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
              <input type="hidden" name="id" value="{$ID}"/>

                <div class="gui_generator_edit">
                    {foreach from=$GRANTED_USERS item="user"}
                        <div class="optionhead">
                            <label style="float: right;">
                                <input class="proxy_mark_all_checkbox" type="checkbox" value="" title="{lang id="user.option.30"}" onclick="$('#granted_{$user} input:checkbox').attr('checked', this.checked)"/>
                                {lang id="user.option.30"}
                            </label>
                            {lang id="user.33"}: {$user}
                        </div>
                        <div class="proxy_granted_user" id="granted_{$user}">
                            {foreach from=$GRANTED_PROFILES item="profile"}
                                {if $GRANTED_RULES[$profile][$user] || $GRANTED_LEVELS[$profile][$user]}
                                <div class="optionhead">{lang id="user.option.29"}: {$profile}</div>
                                  {if $GRANTED_RULES[$profile][$user]}
                                    <table class="gui_generator_multi_entry proxy_table" summary="form" cellpadding="0" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;"><span class="icon_edit_hover">&nbsp;</span></th>
                                                <th>{lang id="user.option.1"}</th>
                                                <th>{lang id="user.option.24"}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                          {foreach from=$GRANTED_RULES[$profile][$user] key="ruleId" item="grant" name="row2"}
                                              <tr class="gui_generator_{if ($smarty.foreach.row2.iteration % 2) == 0 }even{else}odd{/if}_row">
                                                  <td><input id="granted_rule_{$ruleId}" type="checkbox" name="rules[]" value="{$ruleId}" /></td>
                                                  <td><label for="granted_rule_{$ruleId}">{$grant.GROUP_ID}</label></td>
                                                  <td><label for="granted_rule_{$ruleId}">{$grant.ROLE_ID}</label></td>
                                              </tr>
                                          {/foreach}
                                        </tbody>
                                    </table>
                                  {/if}
                                  {if $GRANTED_LEVELS[$profile][$user]}
                                    <label class="optionitem" style="margin: 10px 18px">
                                        <input type="checkbox" name="levels[]" value="{$GRANTED_LEVELS[$profile][$user].SECURITY_ID}"/>
                                        {lang id="user.option.23"}: {$GRANTED_LEVELS[$profile][$user].SECURITY_LEVEL}
                                    </label>
                                  {/if}
                                  <br class="proxy_clear" />
                                {/if}
                           {/foreach}
                        </div>
                    {/foreach}
                    <div class="proxy_remove_button">
                        <input type="submit" name="button" value="{lang id="button_delete"}"/>
                    </div>
                </div>
            </form>
        </div>
        <!-- Non-Expert option -->
        <div class="optionbody config_is_expert" style="text-align: center;{$nonExpertStyle}">
            {* create a list of all users the allready become some rights from current user *}
            <form class="proxy_optionalbody" method="post" action="{$PHP_SELF}" enctype="multipart/form-data" accept-charset="UTF-8">
              <input type="hidden" name="action" value="remove_user_proxy"/>
              <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
              <input type="hidden" name="id" value="{$ID}"/>
              {foreach from=$GRANTED_USERS item="user"}
                  {foreach from=$GRANTED_PROFILES item="profile"}
                      {foreach from=$GRANTED_RULES[$profile][$user] key="ruleId" item="grant"}
                          <input type="hidden" name="rules[]" value="{$ruleId}" />
                      {/foreach}
                      {if $GRANTED_LEVELS[$profile][$user]}
                          <input type="hidden" name="levels[]" value="{$GRANTED_LEVELS[$profile][$user].SECURITY_ID}"/>
                      {/if}
                  {/foreach}
              {/foreach}
              <img src="proxy_remove.png" alt=""/>
              <p>
                  <label>{lang id="USER.33"}:
                      <select name="user">
                          {foreach from=$GRANTED_USERS item=item}
                              <option value="{$item}">{$item}</option>
                          {/foreach}
                      </select>
                  </label>
                  <input type="submit" name="button" value="{lang id="remove"}"/>
              </p>
            </form>
        </div>
    </div>
  {/if}
  </div>
</body>
</html>