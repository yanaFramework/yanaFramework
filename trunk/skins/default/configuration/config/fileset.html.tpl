<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
        <script type="text/javascript" language="JavaScript" src="../../styles/dynamic-styles.js"></script>
        <link rel="stylesheet" type="text/css" href="../../styles/config.css"/>
    </head>

<body>
<form method="post" enctype="multipart/form-data" action="{$PHP_SELF}">
  <input type="hidden" name="action" value="{if $ID == 'default'}set_config_default{else}set_config_profile{/if}"/>
  <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
  <input type="hidden" name="id" value="{$ID}"/>

<div class="config_form">

<!-- BEGIN: table -->

  <div class="config_head">
      <div class="config_title" onclick="yanaToggleMenu(this.parentNode)">{lang id="ADMIN.31"}</div>
  </div>

  <div class="help">
      <div class="help_text">
        {lang id="HELP.15"}
      </div>
  </div>

  <div class="option">

    <!-- BEGIN: section -->

        <div class="optionhead">{lang id="ADMIN.61"}{* Datenquelle *}</div>

        <div class="optionbody" style="padding: 30px;">

<!-- {if $ID != 'default'} -->
          <label class="optionitem">
            <span class="label">{lang id="ADMIN.65"}:</span>
            <input type="text" size="25" name="profile_id" value="{$PROFILE.PROFILE_ID|entities}" title="{lang id="PROFILE_ID"}"/>
          </label>

          <div class="help">
              {lang id="HELP.0"}
              {lang id="HELP.1"}
          </div>
<!-- {/if} -->

          <label class="optionitem">
            <span class="label">{lang id="ADMIN.66"}:</span>
            <input type="text" size="25" name="smileydir" value="{$PROFILE.SMILEYDIR|entities}" title="{lang id="DIR"}"/>
          </label>

          <div class="help">
              {lang id="HELP.0"}
              {lang id="HELP.9"}
          </div>

        </div>
        <div class="optionhead">{lang id="ADMIN.28"}</div>
        <div class="help">
              {lang id="HELP.0"}
              {lang id="HELP.3"}
        </div>

        <div class="optionbody" style="padding: 30px">

          <div class="optionitem">
            <span class="label">{lang id="ADMIN.75"}:</span>
            <input type="text" title="{lang id="ADMIN.72"}" size="30" name="mail" value="{$PROFILE.MAIL|entities}"/>
          </div>

        </div>
        
<!-- {if $ID == 'default'} -->
        <div class="optionhead">{lang id="ADMIN.60"}{* Community-Freigabe *}</div>
        <div class="help">
              {lang id="HELP.0"}
              {lang id="HELP.7"}
        </div>

        <div class="optionbody" style="padding: 30px;">

            <label class="optionitem">
              <input type="radio" size="20" name="auto" value="true" {if !empty($PROFILE.AUTO)} checked="checked" {/if}/>
              {lang id="ADMIN.63"}{* Community *}
            </label>

            <div class="help">
              {lang id="HELP.0"}
              {lang id="HELP.17"}
            </div>

            <br />

            <label class="optionitem">
              <input type="radio" size="20" name="auto" value="false" {if empty($PROFILE.AUTO)}checked="checked"{/if}/>
              {lang id="ADMIN.64"}{* Default Website *}
            </label>

            <div class="help">
              {lang id="HELP.0"}
              {lang id="HELP.18"}
            </div>

        </div>
<!-- {/if} -->

        <div class="optionhead">{lang id="ADMIN.82"}{* Protokollierung *}</div>
        <div class="help">
              {lang id="HELP.0"}
              {lang id="ADMIN.83"}
        </div>

        <div class="optionbody">

          <div class="optionitem" align="center">
            <span class="label">{lang id="ADMIN.73"}:</span>
            <label>{lang id="YES"}<input type="radio" name="logging" value="true" {if !empty($PROFILE.LOGGING)} checked="checked" {/if}/></label>
            <label>{lang id="NO"}<input type="radio" name="logging" value="false" {if empty($PROFILE.LOGGING)}checked="checked"{/if}/></label>
          </div>

<!-- {if $ID == 'default'} -->
          <label class="optionitem" style="margin: auto;">
            <span class="label">{lang id="ADMIN.84"}:</span>
            <select name="log_length">
              <option value="{$PROFILE.LOG_LENGTH|entities}" selected="selected">{$PROFILE.LOG_LENGTH|entities}</option>
              <option value="10">10</option>
              <option value="20">20</option>
              <option value="30">30</option>
              <option value="40">40</option>
              <option value="50">50</option>
              <option value="100">100</option>
              <option value="200">200</option>
              <option value="500">500</option>
              <option value="1000">1000</option>
              <option value="2000">2000</option>
              <option value="5000">5000</option>
            </select>
          </label>
<!-- {/if} -->

          <div class="optionitem" align="center">
              <span class="label">{lang id="ADMIN.86"}:</span>
              <label>{lang id="YES"}<input type="radio" name="log/use_mail" value="true" {if !empty($PROFILE.LOG.USE_MAIL)} checked="checked" {/if}/></label>
              <label>{lang id="NO"}<input type="radio" name="log/use_mail" value="false" {if empty($PROFILE.LOG.USE_MAIL)}checked="checked"{/if}/></label>
          </div>

          <div class="help" style="margin: 0px 30px;">
              {lang id="HELP.0"}
              {lang id="HELP.10"}
          </div>

          <label class="optionitem" style="margin: auto;">
              <span class="label">{lang id="ADMIN.74"}:</span>
              <input title="{lang id="ADMIN.70"}" type="text" name="log/mail" value="{$PROFILE.LOG.MAIL|entities}"/>
          </label>


<!-- {if !empty($PROFILE.LOGGING)} -->
          <p align="center"><input type="button" onclick="document.location.href='{"action=config_read_log"|url}';" value="{lang id="ADMIN.62"}"/></p>
<!-- {/if} -->

        </div>

    <!-- END: section -->
  </div>

<!-- END: table -->
</div>

<!-- NEW SECTION -->
{if $WRITEABLE}
  <p align="center">
    <input type="submit" value="{lang id="ADMIN.17"}"/>
    <input type="button" title="{lang id="TITLE_ABORT"}" value="{lang id="BUTTON_ABORT"}" onclick="history.back()"/>
  </p>
{else}
  <p align="center">{lang id="ADMIN.18"}</p>
{/if}
</form>

</body>

</html>
