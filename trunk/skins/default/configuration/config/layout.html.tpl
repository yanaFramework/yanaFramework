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
  <input type="hidden" name="action" value="{if !$ID}{if $PERMISSION==100}set_config_default{/if}{else}set_config_profile{/if}"/>
  <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
  <input type="hidden" name="id" value="{$ID}"/>

<div class="config_form">

<!-- BEGIN: table -->

  <div class="config_head">
      <div class="config_title" onclick="yanaToggleMenu(this.parentNode)">{lang id="ADMIN.29"}</div>
  </div>

  <div class="help">
      <div class="help_text">
        {lang id="HELP.13"}
      </div>
  </div>

  <div class="option">

    <!-- BEGIN: section -->

        <div class="optionhead">{lang id="ADMIN.53"}{* Seitenlayout *}</div>
        <div class="optionbody">

          <label class="optionitem">
            <span class="label">{lang id="ADMIN.76"}:</span>
            <input id="bgcolor" type="color" size="8" name="bgcolor" value="{$PROFILE.BGCOLOR|entities}"/>
          </label>

          <label class="optionitem">
            <span class="label">{lang id="ADMIN.77"}:</span>
            <input type="text" size="15" name="bgimage" value="{$PROFILE.BGIMAGE|entities}"/>
          </label>

          <label class="optionitem">
            <span class="label">{lang id="ADMIN.78"}:</span>
            <input type="text" size="15" name="logo" value="{$PROFILE.LOGO|entities}"/>
          </label>

        </div>

        <div class="optionhead">{lang id="ADMIN.54"}{* Überschriften *}</div>
        <div class="optionbody">

          <label class="optionitem">
            <span class="label">{lang id="ADMIN.79"}:</span>
            <select name="hsize">
                <option value="" {if empty($PROFILE.HSIZE)} selected="selected" {/if}>{lang id="ADMIN.46"}</option>
                <option value="12pt" {if $PROFILE.HSIZE=="12pt"} selected="selected" {/if}>12pt</option>
                <option value="13pt" {if $PROFILE.HSIZE=="13pt"} selected="selected" {/if}>13pt</option>
                <option value="14pt" {if $PROFILE.HSIZE=="14pt"} selected="selected" {/if}>14pt</option>
                <option value="15pt" {if $PROFILE.HSIZE=="15pt"} selected="selected" {/if}>15pt</option>
                <option value="16pt" {if $PROFILE.HSIZE=="16pt"} selected="selected" {/if}>16pt</option>
                <option value="17pt" {if $PROFILE.HSIZE=="17pt"} selected="selected" {/if}>17pt</option>
                <option value="18pt" {if $PROFILE.HSIZE=="18pt"} selected="selected" {/if}>18pt</option>
                <option value="19pt" {if $PROFILE.HSIZE=="19pt"} selected="selected" {/if}>19pt</option>
                <option value="20pt" {if $PROFILE.HSIZE=="20pt"} selected="selected" {/if}>20pt</option>
                <option value="22pt" {if $PROFILE.HSIZE=="22pt"} selected="selected" {/if}>22pt</option>
                <option value="24pt" {if $PROFILE.HSIZE=="24pt"} selected="selected" {/if}>24pt</option>
                <option value="30pt" {if $PROFILE.HSIZE=="30pt"} selected="selected" {/if}>30pt</option>
                <option value="36pt" {if $PROFILE.HSIZE=="36pt"} selected="selected" {/if}>36pt</option>
            </select>
          </label>

          <label class="optionitem">
            <span class="label">{lang id="ADMIN.80"}:</span>
            <input id="hcolor" type="color" size="15" name="hcolor" value="{$PROFILE.HCOLOR|entities}"/>
          </label>

          <label class="optionitem">
            <span class="label">{lang id="ADMIN.68"}:</span>
            <select name="hfont">
                <optgroup label="{lang id="ADMIN.11"}">
                  <option value="{$PROFILE.HFONT|entities}" selected="selected">{if !empty($PROFILE.HFONT)}{$PROFILE.HFONT|entities}{else}{lang id="ADMIN.46"}{/if}</option>
                </optgroup>
                <optgroup label="{lang id="ADMIN.12"}">
                  <option value="">{lang id="ADMIN.46"}</option>
                  <option value="Arial, Helvetica, sans-serif" style="font-family: Arial, Helvetica, sans-serif">Arial / Helvetica</option>
                  <option value="Times New Roman, serif"       style="font-family: Times New Roman, serif">Times New Roman</option>
                  <option value="Courier New, monospace"       style="font-family: Courier New, monospace">Courier New</option>
                </optgroup>
                <optgroup label="{lang id="ADMIN.48"}">
                  <option value="sans-serif"                   style="font-family: sans-serif">sans-serif</option>
                  <option value="serif"                        style="font-family: serif">serif</option>
                  <option value="cursive"                      style="font-family: cursive">cursive</option>
                  <option value="fantasy"                      style="font-family: Times New Roman, serif">fantasy</option>
                  <option value="monospace"                    style="font-family: monospace">monospace</option>
                </optgroup>
            </select>
          </label>

        </div>

        <div class="optionhead">{lang id="ADMIN.55"}{* Text *}</div>
        <div class="optionbody">

          <label class="optionitem">
            <span class="label">{lang id="ADMIN.79"}:</span>
            <select name="psize">
                <option value="" {if empty($PROFILE.PSIZE)} selected="selected" {/if}>{lang id="ADMIN.46"}</option>
                <option value="7pt" {if $PROFILE.PSIZE=="7pt"} selected="selected" {/if}>7pt</option>
                <option value="8pt" {if $PROFILE.PSIZE=="8pt"} selected="selected" {/if}>8pt</option>
                <option value="9pt" {if $PROFILE.PSIZE=="9pt"} selected="selected" {/if}>9pt</option>
                <option value="10pt" {if $PROFILE.PSIZE=="10pt"} selected="selected" {/if}>10pt</option>
                <option value="11pt" {if $PROFILE.PSIZE=="11pt"} selected="selected" {/if}>11pt</option>
                <option value="12pt" {if $PROFILE.PSIZE=="12pt"} selected="selected" {/if}>12pt</option>
                <option value="13pt" {if $PROFILE.PSIZE=="13pt"} selected="selected" {/if}>13pt</option>
                <option value="14pt" {if $PROFILE.PSIZE=="14pt"} selected="selected" {/if}>14pt</option>
                <option value="15pt" {if $PROFILE.PSIZE=="15pt"} selected="selected" {/if}>15pt</option>
                <option value="16pt" {if $PROFILE.PSIZE=="16pt"} selected="selected" {/if}>16pt</option>
                <option value="17pt" {if $PROFILE.PSIZE=="17pt"} selected="selected" {/if}>17pt</option>
                <option value="18pt" {if $PROFILE.PSIZE=="18pt"} selected="selected" {/if}>18pt</option>
            </select>
          </label>

          <label class="optionitem">
            <span class="label">{lang id="ADMIN.80"}:</span>
            <input id="pcolor" type="color" size="15" name="pcolor" value="{$PROFILE.PCOLOR|entities}"/>
          </label>

          <label class="optionitem">
            <span class="label">{lang id="ADMIN.68"}:</span>
            <select name="pfont">
                <optgroup label="{lang id="ADMIN.11"}">
                  <option value="{$PROFILE.PFONT|entities}" selected="selected">{if !empty($PROFILE.PFONT)}{$PROFILE.PFONT|entities}{else}{lang id="ADMIN.46"}{/if}</option>
                </optgroup>
                <optgroup label="{lang id="ADMIN.12"}">
                  <option value="">{lang id="ADMIN.46"}</option>
                  <option value="Arial, Helvetica, sans-serif" style="font-family: Arial, Helvetica, sans-serif">Arial / Helvetica</option>
                  <option value="Times New Roman, serif"       style="font-family: Times New Roman, serif">Times New Roman</option>
                  <option value="sans-serif"                   style="font-family: sans-serif">sans-serif</option>
                  <option value="serif"                        style="font-family: serif">serif</option>
                  <option value="cursive"                      style="font-family: cursive">cursive</option>
                  <option value="fantasy"                      style="font-family: Times New Roman, serif">fantasy</option>
                  <option value="monospace"                    style="font-family: monospace">monospace</option>
                </optgroup>
            </select>
          </label>

        </div>

        <div class="optionhead">{lang id="ADMIN.57"}{* Darstellung von Datums- und Zeitangaben *}</div>
        <div class="help">
              {lang id="HELP.0"}
              {lang id="HELP.5"}
        </div>

        <div class="optionbody">

          <label class="optionitem">
            <span class="label">{lang id="ADMIN.59"}:</span>
            <select name="timeformat">
                <option value="0" {if $PROFILE.TIMEFORMAT==0} selected="selected" {/if}>1.1.2005</option>
                <option value="1" {if $PROFILE.TIMEFORMAT==1} selected="selected" {/if}>10:01:15</option>
                <option value="2" {if $PROFILE.TIMEFORMAT==2} selected="selected" {/if}>1.1.2005 10:01:15</option>
                <option value="3" {if $PROFILE.TIMEFORMAT==3} selected="selected" {/if}>1.1.2005 10:01:15 GMT+0100</option>
                <option value="4" {if $PROFILE.TIMEFORMAT==4} selected="selected" {/if}>{lang id="ADMIN.47"}</option>
                <option value="5" {if $PROFILE.TIMEFORMAT==5} selected="selected" {/if}>{lang id="ADMIN.47"} 10:01:15</option>
            </select>
          </label>

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