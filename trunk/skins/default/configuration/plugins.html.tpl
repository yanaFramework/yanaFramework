{*
  @param  array   $PLUGINS
  @param  int     $PERMISSION
  @param  bool    $USER_IS_EXPERT
*}
{if $USER_IS_EXPERT}{$isExpertStyle=""}{else}{$isExpertStyle="display:none;"}{/if}
<input type="hidden" value="" name="plugins[]"/>
<ul>
{foreach item="PLUGIN" from=$PLUGINS}
       {$setupCount=$PLUGIN.SETUP|count}
       {if ($PLUGIN.ACTIVE == 0 || $setupCount == 0)}{$pluginClass="config_is_expert"}{else}{$pluginClass=""}{/if}
    <li {if $pluginClass}style="{$isExpertStyle}"{/if}{if $PLUGIN.ACTIVE == 1}title="{lang id="ADMIN.23"}" class="selected_option {$pluginClass}"{elseif $PLUGIN.ACTIVE == 2}title="{lang id="ADMIN.41"}" class="default_selected_option {$pluginClass}"{else}title="{lang id="ADMIN.26"}" class="unselected_option {$pluginClass}"{/if}>
        <a href={"action=about&type=plugin&target="|cat:$PLUGIN.ID|href}>
            <img class="config_plugin_image" alt="" src="{$PLUGIN.IMAGE}" style="border: 0;"/>
        </a>
        <span class="title">
            <span class="config_is_expert" style="{$isExpertStyle}">
              {if $PERMISSION == 100 && (($PLUGIN.ACTIVE > -1 && $PLUGIN.ACTIVE < 2) || !$PLUGIN.ACTIVE)}
                <input type="hidden" value="{$PLUGIN.ID}" name="pluginlist[]"/>
                <input type="checkbox" value="{$PLUGIN.ID}" {if !empty($PLUGIN.ACTIVE)} checked="checked" {/if} name="plugins[]" title="{lang id="ADMIN.22"}"/>
                &nbsp;
              {else}
                <span style="margin-right: 20px;">&nbsp;</span>
              {/if}
            </span>
            {if $PLUGIN.ACTIVE > 0 && $setupCount == 1}
            <a class="plugin_setup" title="{$PLUGIN.SETUP.0.TITLE}" href={"action="|cat:$PLUGIN.SETUP.0.ACTION|href}>
                {$PLUGIN.NAME}
            </a>
            {else}
              {$PLUGIN.NAME}
            {/if}
        </span>
          {if $PLUGIN.ACTIVE > 0 && $setupCount > 1}
        <ul class="plugin_setup">
            {foreach item="SETUP" from=$PLUGIN.SETUP}
            <li><a href={"action="|cat:$SETUP.ACTION|href}>{$SETUP.TITLE}</a></li>
            {/foreach}
        </ul>
          {/if}
    </li>
{/foreach}
</ul>