<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
        <script type="text/javascript" language="JavaScript" src="../styles/dynamic-styles.js"></script>
        <link rel="stylesheet" type="text/css" href="default.css"/>
        <link rel="stylesheet" type="text/css" href="../styles/config.css"/>
    </head>

<body>

<form name="eintrag" method="post" enctype="multipart/form-data" action="{$PHP_SELF}">
      <input type="hidden" name="action" value="{$NEXT_ACTION}"/>
      <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
      <input type="hidden" name="id" value="{$ID}"/>

<div class="config_form">

<!-- BEGIN: table -->

  <div class="config_head">
      <div class="config_title" onclick="yanaToggleMenu(this.parentNode)">{lang id="ADMIN.58"}{* Spamschutz *}</div>
  </div>

  <div class="help">
      <div class="help_text">
          {lang id="HELP.1"}
      </div>
  </div>

  <div class="option">

    <!-- BEGIN: section -->
    <div id="antispam_security_level">
        <div class="optionhead">{lang id="SPAM.0"}{* Sicherheitsstufe *}</div>
        <div class="help">
          {lang id="HELP.0"}
          {lang id="HELP.2"}
        </div>
        <div class="optionbody">
          <ol>
            <li id="antispam_level_high" onmouseover="ieShowCss(true, this)" onmouseout="ieShowCss(false, this)">
              <label>
                <input type="radio" value="3" name="spam/level" onclick="userSettings(false);wordFilterSettings(true);" {if $PROFILE.SPAM.LEVEL == 3}checked="checked"{/if}/>
                {lang id="SPAM.LEVEL.3"}                
              </label>
              <div class="description">
                {lang id="SPAM.DESC.FORM"}<br />
                {lang id="SPAM.DESC.CAPTCHA"}<br />
                {lang id="SPAM.DESC.LOGIN"}<br />
                {lang id="SPAM.DESC.WORDS"}
              </div>
            </li>
            <li id="antispam_level_medium" onmouseover="ieShowCss(true, this)" onmouseout="ieShowCss(false, this)">
              <label>
                <input type="radio" value="2" name="spam/level" onclick="userSettings(false);wordFilterSettings(true);" {if $PROFILE.SPAM.LEVEL == 2}checked="checked"{/if}/>
                {lang id="SPAM.LEVEL.2"}                
              </label>
              <div class="description">
                {lang id="SPAM.DESC.FORM"}<br />
                {lang id="SPAM.DESC.CAPTCHA"}<br />
                {lang id="SPAM.DESC.NOLOGIN"}<br />
                {lang id="SPAM.DESC.WORDS"}
              </div>
            </li>
            <li id="antispam_level_low" onmouseover="ieShowCss(true, this)" onmouseout="ieShowCss(false, this)">
              <label>
                <input type="radio" value="1" name="spam/level" onclick="userSettings(false);wordFilterSettings(false);" {if $PROFILE.SPAM.LEVEL == 1}checked="checked"{/if}/>
                {lang id="SPAM.LEVEL.1"}
              </label>
              <div class="description">
                {lang id="SPAM.DESC.FORM"}<br />
                {lang id="SPAM.DESC.NOCAPTCHA"}<br />
                {lang id="SPAM.DESC.NOLOGIN"}<br />
                {lang id="SPAM.DESC.NOWORDS"}
              </div>
            </li>
            <li id="antispam_level_off" onmouseover="ieShowCss(true, this)" onmouseout="ieShowCss(false, this)">
              <label>
                <input type="radio" value="0" name="spam/level" onclick="userSettings(false);wordFilterSettings(false);" {if $PROFILE.SPAM.LEVEL == 0}checked="checked"{/if}/>
                {lang id="SPAM.LEVEL.0"}
              </label>
              <div class="description">
                {lang id="SPAM.DESC.NONE"}
              </div>
            </li>
            <li id="antispam_level_user" onmouseover="ieShowCss(true, this)" onmouseout="ieShowCss(false, this)">
              <label>
                <input type="radio" value="-1" name="spam/level" onclick="userSettings(true);wordFilterSettings(document.getElementById('input_word_filter').checked);" {if $PROFILE.SPAM.LEVEL == -1}checked="checked"{/if}/>
                {lang id="SPAM.LEVEL.USER"}                
              </label>
              <div class="description">
                {lang id="SPAM.DESC.USER"}
              </div>
            </li>
          </ol>
        </div>
    </div>
    <!-- END: section -->

    <!-- BEGIN: section -->
    <div id="antispam_user_settings">
        <div class="optionhead">{lang id="SPAM.1"}{* benutzerdefinierte Einstellungen *}</div>
        <div class="optionbody">
            <div class="label">{lang id="SPAM.2"}{* Soll ein "CAPTCHA" verwendet werden? *}</div>
            <div class="help">
              {lang id="HELP.0"}
              {lang id="HELP.3"}
            </div>
            <label>
              {lang id="YES"}
              <input type="radio" name="spam/captcha" {if !empty($PROFILE.SPAM.CAPTCHA)}checked="checked"{/if} value="true"/>
            </label>
            <label>
              {lang id="NO"}
            <input type="radio" name="spam/captcha" {if empty($PROFILE.SPAM.CAPTCHA)} checked="checked" {/if} value="false"/>
            </label>
    
            <br />
        
            <div class="label">{lang id="SPAM.3"}{* Sollen auch angemeldete Benutzer geprüft werden? *}</div>
            <div class="help">
              {lang id="HELP.0"}
              {lang id="HELP.4"}
            </div>
            <label>
              {lang id="YES"}
              <input type="radio" name="spam/permission" {if !empty($PROFILE.SPAM.PERMISSION)}checked="checked"{/if} value="true"/>
            </label>
            <label>
              {lang id="NO"}
            <input type="radio" name="spam/permission" {if empty($PROFILE.SPAM.PERMISSION)} checked="checked" {/if} value="false"/>
            </label>
    
            <br />

            <div class="label">{lang id="SPAM.4"}{* Sollen abgewiesene Einträge (Spam) protkolliert werden? *}</div>
            <div class="help">
              {lang id="HELP.0"}
              {lang id="HELP.5"}
            </div>
            <label>
              {lang id="YES"}
              <input type="radio" name="spam/log" {if empty($PROFILE.LOGGING)}disabled="disabled"{/if} {if !empty($PROFILE.SPAM.LOG)}checked="checked"{/if} value="true"/>
            </label>
            <label>
              {lang id="NO"}
            <input type="radio" name="spam/log" {if empty($PROFILE.LOGGING)}disabled="disabled"{/if} {if empty($PROFILE.SPAM.LOG)} checked="checked" {/if} value="false"/>
            </label>
    
            <br />
        
            <div class="label">{lang id="SPAM.5"}{* Soll das doppelte Absenden von Formularen verhindert werden? *}</div>
            <div class="help">
              {lang id="HELP.0"}
              {lang id="HELP.6"}
            </div>
            <label>
              {lang id="YES"}
              <input type="radio" name="spam/form_id" {if !empty($PROFILE.SPAM.FORM_ID)}checked="checked"{/if} value="true"/>
            </label>
            <label>
              {lang id="NO"}
            <input type="radio" name="spam/form_id" {if empty($PROFILE.SPAM.FORM_ID)} checked="checked" {/if} value="false"/>
            </label>
        
            <br />
        
            <div class="label">{lang id="SPAM.6"}{* Soll der vom Browser gesendete Header geprüft werden? *}</div>
            <div class="help">
              {lang id="HELP.0"}
              {lang id="HELP.7"}              
            </div>
            <label>
              {lang id="YES"}
              <input type="radio" name="spam/header" {if !empty($PROFILE.SPAM.HEADER)}checked="checked"{/if} value="true"/>
            </label>
            <label>
              {lang id="NO"}
            <input type="radio" name="spam/header" {if empty($PROFILE.SPAM.HEADER)} checked="checked" {/if} value="false"/>
            </label>
        
            <br />
        
            <div class="label">{lang id="SPAM.7"}{* Soll nach verdächtigen Wörtern durchsucht werden? *}</div>
            <div class="help">
              {lang id="HELP.0"}
              {lang id="HELP.8"}
            </div>
            <label>
              {lang id="YES"}
              <input type="radio" name="spam/word_filter" {if !empty($PROFILE.SPAM.WORD_FILTER)}checked="checked"{/if} value="true" id="input_word_filter" onclick="wordFilterSettings(true);"/>
            </label>
            <label>
              {lang id="NO"}
            <input type="radio" name="spam/word_filter" {if empty($PROFILE.SPAM.WORD_FILTER)} checked="checked" {/if} value="false" onclick="wordFilterSettings(false);"/>
            </label>
        </div>
    </div>
    <!-- END: section -->

    <!-- BEGIN: section -->
    <div id="antispam_words">
        <div class="optionhead">{lang id="SPAM.8"}{* Liste geblockter Wörter *}</div>
        <div class="optionbody">
          <div class="label">{lang id="SPAM.9"}{* Wörter, welche nicht enthalten sein dürfen *}:</div>
          <ol id="antispam_list">
              <li id="antispam_reference">
                <input type="text" name="spam/words[]"/>
                <a class="buttonize" href="javascript://remove item" onclick="yanaRemoveItem(this)" title="{lang id="USER.OPTION.12"}">
                    <span class="icon_delete" style="vertical-align: -5px; margin-right: 5px;">&nbsp;</span>{lang id="USER.OPTION.12"}
                </a>
              </li>
    {foreach item="item" from=$PROFILE.SPAM.WORDS}{if $item}
              <li>
                <input type="text" name="spam/words[]" value="{$item}"/>
                <a class="buttonize" href="javascript://remove item" onclick="yanaRemoveItem(this)" title="{lang id="USER.OPTION.12"}">
                    <span class="icon_delete" style="vertical-align: -5px; margin-right: 5px;">&nbsp;</span>{lang id="USER.OPTION.12"}
                </a>
              </li>
    {/if}{/foreach}
          </ol>
          <div style="margin-left: 5px;" class="comment">
            <a class="buttonize" href="javascript://add item" onclick="yanaAddItem(document.getElementById('antispam_reference'))" title="{lang id="BUTTON_NEW"}">
                <span class="icon_new" style="vertical-align: -5px; margin-right: 5px;">&nbsp;</span>{lang id="BUTTON_NEW"}
            </a>
          </div>
          <div style="margin-top: 10px;">
            <div class="label">{lang id="SPAM.10"}</div>
            <div class="help">
              {lang id="HELP.0"}
              {lang id="HELP.9"}
            </div>
            <label>
              {lang id="YES"}
              <input type="radio" name="spam/reg_exp" {if !empty($PROFILE.SPAM.REG_EXP)}checked="checked"{/if} value="true" id="input_word_filter"/>
            </label>
            <label>
              {lang id="NO"}
            <input type="radio" name="spam/reg_exp" {if empty($PROFILE.SPAM.REG_EXP)} checked="checked" {/if} value="false"/>
            </label>
          </div>
        </div>
    </div>
    <!-- END: section -->
  </div>

<!-- END: table -->
</div>

{if $WRITEABLE}
  <p align="center">
    <input type="submit" value="{lang id="ADMIN.17"}"/>
    <input type="button" title="{lang id="TITLE_ABORT"}" value="{lang id="BUTTON_ABORT"}" onclick="history.back()"/>
  </p>
{else}
  <p align="center">{lang id="ADMIN.18"}</p>
{/if}

</form>

<script type="text/javascript">
{if $PROFILE.SPAM.LEVEL ne -1}
userSettings(false);
{/if}
{if $PROFILE.SPAM.LEVEL == 1 || $PROFILE.SPAM.LEVEL == 0 || ($PROFILE.SPAM.LEVEL == -1 && !$PROFILE.SPAM.WORD_FILTER)}
wordFilterSettings(false);
{/if}
</script>

</body>

</html>
