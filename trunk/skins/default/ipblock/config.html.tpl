<!DOCTYPE html>

<html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
        <script type="text/javascript" language="JavaScript" src="../styles/dynamic-styles.js"></script>
        <link rel="stylesheet" type="text/css" href="../styles/config.css"/>
    </head>

<body>
    <form method="post" enctype="multipart/form-data" action="{$PHP_SELF}">
      {if $ACTION=="get_root_block"}<input type="hidden" name="action" value="set_root_block"/>
      {else}<input type="hidden" name="action" value="set_block"/>
      {/if}<input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
      <input type="hidden" name="id" value="{$ID}"/>

    <div class="config_form">
    
    <!-- BEGIN: table -->

      <div class="config_head">
          <div class="config_title" onclick="yanaToggleMenu(this.parentNode)">{lang id="ADMIN.1"}{* IP-Filter *}</div>
      </div>
    
      <div class="help">
          <div class="help_text">
              {lang id="HELP.IPBLOCK"}
          </div>
      </div>
    
      <div class="option">

        <!-- BEGIN: section -->
        <div class="optionbody">

          <label class="optionitem">
            <span class="label">{lang id="ADMIN.0"}</span>
            <textarea cols="20" rows="5" id="blacklist" name="blacklist">{$BLACKLIST}</textarea>
          </label>

          <label class="optionitem">
            <span class="label">{lang id="ADMIN.2"}</span>
            <textarea cols="20" rows="5" id="whitelist" name="whitelist">{$WHITELIST}</textarea>
          </label>

         <p align="center">
            <input type="submit" value="{lang id="BUTTON_SAVE"}"/>
            <input type="button" title="{lang id="TITLE_ABORT"}" value="{lang id="BUTTON_ABORT"}" onclick="history.back()"/>
         </p>

        </div>
        <!-- END: section -->
      </div>
    
    <!-- END: table -->
    </div>
    </form>
</body>
