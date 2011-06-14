<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
        <link rel="stylesheet" type="text/css" href="../styles/default.css"/>
        <link rel="stylesheet" type="text/css" href="../styles/btn.css"/>
        <link rel="stylesheet" type="text/css" href="../styles/menu.css"/>
        <link rel="stylesheet" type="text/css" href="../styles/gui_generator.css"/>
        <link rel="stylesheet" type="text/css" href="../styles/admin.css"/>
        <link rel="stylesheet" type="text/css" href="../styles/stdout.css"/>
    </head>

<body>{if $USER_IS_EXPERT}{$isExpertStyle=""}{else}{$isExpertStyle="display:none;"}{/if}
<div id="table_configmenu">

  <h1>{lang id="CONFIGMENU"}</h1>

  <div class="label" id="table_config_profile">
    <span class="buttonize_static"><span class="icon_show_hover">&nbsp;</span></span>
    {lang id="INDEX_9"}:
    {if $ID}
      &quot;{$ID}&quot;
    {else}
      &ndash; {lang id="INDEX_10"} &ndash;
    {/if}
    {if $PERMISSION==100}
      &nbsp; &nbsp; &nbsp;
      <a title='{lang id="INDEX_12"}' href={"action=clear_server_cache&target=index"|href}>
        <span class="buttonize"><span class="icon_delete">&nbsp;</span></span>
        {lang id="INDEX_12"}
      </a>
    {/if}
    <!-- Change User-mode -->
    &nbsp; &nbsp; &nbsp;
    {if !$USER_IS_EXPERT}
      <a title="{lang id="ADMIN.14"}" href={"action=config_usermode"|href} onclick="config_usermode(this, '{lang id="ADMIN.13"}', 'config_is_expert', document.getElementById('table_configmenu'));return false">
         <span class="buttonize"><span class="icon_change">&nbsp;</span></span>
        {lang id="ADMIN.14"}
      </a>
    {else}
      <a title="{lang id="ADMIN.13"}" href={"action=config_usermode"|href} onclick="config_usermode(this, '{lang id="ADMIN.14"}', 'config_is_expert', document.getElementById('table_configmenu'));return false">
         <span class="buttonize"><span class="icon_change">&nbsp;</span></span>
        {lang id="ADMIN.13"}
      </a>
    {/if}

    <div class="config_is_expert" style="{$isExpertStyle}">
      <div>&nbsp;</div>

      <form method="post" action="{$PHP_SELF}" class="col_left">
        <input type="hidden" name="action" value="{$ACTION}"/>
        <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>

        <label>
          <span class="buttonize_static"><span class="icon_edit_hover">&nbsp;</span></span>
          {lang id="INDEX_11"}
          <select name="id">
              <optgroup label='{lang id="ADMIN.5"}'>
                <option value="{$ID}">{if $ID}{$ID}{else}{lang id="INDEX_10"}{/if}</option>
              </optgroup>
              <optgroup label="{lang id="ADMIN.6"}">
                <option value="" class="profile_default">{lang id="INDEX_10"}</option>
              {foreach key=FILE item=NAME from=$PROFILES}
                <option value="{$NAME}">{$NAME}</option>
              {/foreach}
              </optgroup>
          </select>
        </label>

        <input type="submit" value='{lang id="OK"}'/>

      </form>
      <form method="post" action="{$PHP_SELF}" class="col_right">
        <input type="hidden" name="action" value="config_create_profile"/>
        <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>

        <label>
          <span class="buttonize_static"><span class="icon_new_hover">&nbsp;</span></span>
          {lang id="INDEX_4"}
          <input type="text" name="id" size="15"/>
        </label>

        <input type="submit" value='{lang id="OK"}'/>

      </form>
    </div>

  </div>

  <div id="config_update">{updateCheck}</div>


  <div class="config_title">
    {lang id="USER.10"}
  </div>

 {import id="password_template"}

  <div class="config_title">
    {if $PERMISSION == 100}
      <div class="config_is_expert" id="config_refresh_pluginlist" style="{$isExpertStyle}">
        <a class="label" onclick="refresh_pluginlist('config_plugins', this.href); return false" href={"action=refresh_pluginlist"|href}>
          <span class="buttonize"><span class="icon_reload">&nbsp;</span></span>
          {lang id="ADMIN.10"}
        </a>
      </div>
    {/if}
    {lang id="ADMIN.9"}
  </div>
  <form action="{$PHP_SELF}?{$SESSION_NAME}={$SESSION_ID}&amp;id={$ID}" method="post">
    <input type="hidden" name="action" value="save_pluginlist"/>
    <div id="config_plugins">
{import id="index_plugins" PLUGINS=$PLUGINS PERMISSION=$PERMISSION USER_IS_EXPERT=$USER_IS_EXPERT}
    </div>
{if $PERMISSION == 100}
      <div class="config_is_expert" style="{$isExpertStyle}">
          <input type="submit" value='{lang id="BUTTON_SAVE"}' onclick="return confirm('{lang id="ADMIN.20"}')"/>
      </div>
{/if}
  </form>

  <div class="config_is_expert" style="{$isExpertStyle}">

    <div class="config_title">
      {lang id="ADMIN.24"}
    </div>

    <div class="index_options" title="{lang id="ADMIN.24"}">

      <form action="{$PHP_SELF}" method="post">
        <input type="hidden" name="action" value="{if !$ID}{if $PERMISSION==100}set_config_default{/if}{else}set_config_profile{/if}"/>
        <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
        <input type="hidden" name="id" value="{$ID}"/>

        <ul>
{foreach key=FILE item=NAME from=$SKINFILES}
          <li{if $FILE == $PROFILE.SKIN} title="{lang id="ADMIN.1"}" class="selected_option"{/if}>
          <label>
          {if $PERMISSION > 59}
            <input type="radio" name="skin" value="{$FILE}"{if $FILE == $PROFILE.SKIN} checked="checked"{/if}/>
            &nbsp;
          {/if}
            <a href={"action=about&type=skin&target="|cat:$FILE|href}>{$NAME}</a>
          </label>
          </li>
{/foreach}
        </ul>
        {if $PERMISSION > 59}
        <div>
          <input type="submit" value='{lang id="BUTTON_SAVE"}'/>
        </div>
        {/if}
      </form>
    </div>

    <div class="config_title">
      {lang id="ADMIN.27"}
    </div>

    <div class="index_options" title="{lang id="ADMIN.27"}">
      <ul>
{foreach key=FILE item=NAME from=$LANGUAGEFILES}
          <li>
              <a href={"action=about&type=language&target="|cat:$FILE|href}>{$NAME}</a>
          </li>
{/foreach}
      </ul>
    </div>

  </div>

</div>
</body>

</html>