<!DOCTYPE html>

<html>

    <head>
        <link rel="stylesheet" type="text/css" href="../styles/default.css"/>
        <link rel="stylesheet" type="text/css" href="../styles/btn.css"/>
        <link rel="stylesheet" type="text/css" href="../styles/menu.css"/>
        <link rel="stylesheet" type="text/css" href="../styles/gui_generator.css"/>
        <link rel="stylesheet" type="text/css" href="../styles/admin.css"/>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="TITLE"}</title>
    </head>

<body>
  <li {if $PLUGIN.ACTIVE == 1}title="{lang id="ADMIN.23"}" class="plugin_setup selected_option"{elseif $PLUGIN.ACTIVE == 2}title="{lang id="ADMIN.41"}" class="plugin_setup default_selected_option"{else}title="{lang id="ADMIN.26"}" class="plugin_setup unselected_option"{/if}>
  <!-- BEGIN OPTION -->
{if !empty($PLUGIN.ACTIVE)}
  <div onmouseover="this.className='plugin_hover'" onmouseout="this.className=''">
      <img alt="&bull;" border="0" src="data/icon_plugins.gif" class="plugin_icon"/>
{/if}

      <div class="plugin_active">
{if $PERMISSION == 100}
          <input type="hidden" value="{$PLUGIN.ID}" name="pluginlist[]"/>
{if ($PLUGIN.ACTIVE > -1 && $PLUGIN.ACTIVE < 2)}
          <input type="checkbox" value="{$PLUGIN.ID}" {if $PLUGIN.ACTIVE > 0} checked {/if} name="plugins[]" title="{lang id="ADMIN.22"}"/>
{else}
          &nbsp;
{/if}
{else}
{if !empty($PLUGIN.ACTIVE)}
          <span class="icon_true">&nbsp;</span>
{else}
          <span class="icon_false">&nbsp;</span>
{/if}
{/if}
      </div>

      <div class="plugin_description">
          <a target="about" href={"action=about&type=plugin&target="|cat:$PLUGIN.ID|href}>{if !empty($PLUGIN.PARENT)}<img src="arrow.gif" alt="=&gt;" border="0"/>{/if}{$PLUGIN.NAME|truncate:37:"..."}</a><br />
          {$PLUGIN.DESCRIPTION|truncate:53:"..."}
      </div>

      <div class="plugin_setup_button">
{if $PERMISSION == 100 &&  $PLUGIN.ACTIVE > 0}
          <input style="margin-left: 20px;" type="button" onclick="this.document.location.href='{"action=$ACTION&target=$TARGET"|url}';" value="{lang id="SETUP"}"/>
          <input style="margin-left: 5px;" type="button" onclick="window.open('{$INFO.UPDATE}','_blank')" title="{lang id="ADMIN.8"}" value="Update"/>
{/if}
      </div>
{if !empty($PLUGIN.ACTIVE)}
  </div>
{/if}
  <!-- END OPTION -->

  </li>

</body>

</html>
