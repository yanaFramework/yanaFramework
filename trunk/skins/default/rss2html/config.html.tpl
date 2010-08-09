<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
        <script type="text/javascript" language="JavaScript" src="../styles/dynamic-styles.js"></script>
        <link rel="stylesheet" type="text/css" href="../styles/config.css"/>
    </head>

<body>
    <form method="post" enctype="multipart/form-data" action="{$PHP_SELF}">
      <input type="hidden" name="action" value="set_rss_to_html_config"/>
      <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
      <input type="hidden" name="id" value="{$ID}"/>

    <div class="config_form">
    
    <!-- BEGIN: table -->
    
      <div class="config_head">
          <div class="config_title" onclick="yanaToggleMenu(this.parentNode)">{lang id="RSS_CONFIG.TITLE"}{* RSS-Feed *}</div>
      </div>

      <div class="help">
          <div class="help_text">
              {lang id="RSS_CONFIG.OPTIONS.5"}:
              <a href={"action=GET_NEWS"|href} target="_blank">{$PHP_SELF}?action=GET_NEWS{if $ID}&amp;id={$ID}{/if}</a>
          </div>
      </div>

      <div class="option">

        <!-- BEGIN: section -->
        <div class="optionbody">

          <label class="optionitem">
            <span class="label">{lang id="RSS_CONFIG.OPTIONS.0"}:</span>
            <input name="RSS/FILE" type="text" value="{if $PROFILE.RSS.FILE}{$PROFILE.RSS.FILE}{else}plugins/rss/test.rss{/if}"/>
          </label>

          <label class="optionitem">
            <span class="label">{lang id="RSS_CONFIG.OPTIONS.1"}:</span>
            <select name="RSS/MAX">
                <option value="1" {if $PROFILE.RSS.MAX==1} selected="selected" {/if}>1 {lang id="RSS_CONFIG.TXT_ENTRY"}</option>
                <option value="2" {if $PROFILE.RSS.MAX==2} selected="selected" {/if}>2 {lang id="RSS_CONFIG.TXT_ENTRIES"}</option>
                <option value="3" {if $PROFILE.RSS.MAX==3} selected="selected" {/if}>3 {lang id="RSS_CONFIG.TXT_ENTRIES"}</option>
                <option value="4" {if $PROFILE.RSS.MAX==4} selected="selected" {/if}>4 {lang id="RSS_CONFIG.TXT_ENTRIES"}</option>
                <option value="5" {if $PROFILE.RSS.MAX==5 || !$PROFILE.RSS.MAX} selected="selected" {/if}>5 {lang id="RSS_CONFIG.TXT_ENTRIES"}</option>
                <option value="6" {if $PROFILE.RSS.MAX==6} selected="selected" {/if}>6 {lang id="RSS_CONFIG.TXT_ENTRIES"}</option>
                <option value="7" {if $PROFILE.RSS.MAX==7} selected="selected" {/if}>7 {lang id="RSS_CONFIG.TXT_ENTRIES"}</option>
                <option value="8" {if $PROFILE.RSS.MAX==8} selected="selected" {/if}>8 {lang id="RSS_CONFIG.TXT_ENTRIES"}</option>
                <option value="9" {if $PROFILE.RSS.MAX==9} selected="selected" {/if}>9 {lang id="RSS_CONFIG.TXT_ENTRIES"}</option>
                <option value="10" {if $PROFILE.RSS.MAX==10} selected="selected" {/if}>10 {lang id="RSS_CONFIG.TXT_ENTRIES"}</option>
                <option value="15" {if $PROFILE.RSS.MAX==15} selected="selected" {/if}>15 {lang id="RSS_CONFIG.TXT_ENTRIES"}</option>
                <option value="20" {if $PROFILE.RSS.MAX==20} selected="selected" {/if}>20 {lang id="RSS_CONFIG.TXT_ENTRIES"}</option>
                <option value="30" {if $PROFILE.RSS.MAX==30} selected="selected" {/if}>30 {lang id="RSS_CONFIG.TXT_ENTRIES"}</option>
                <option value="50" {if $PROFILE.RSS.MAX==50} selected="selected" {/if}>50 {lang id="RSS_CONFIG.TXT_ENTRIES"}</option>
                <option value="100" {if $PROFILE.RSS.MAX==100} selected="selected" {/if}>100 {lang id="RSS_CONFIG.TXT_ENTRIES"}</option>
            </select>
          </label>

         <p align="center">
           <input type="submit" value='{lang id="BUTTON_SAVE"}'/>
           <input type="button" title='{lang id="TITLE_ABORT"}' value='{lang id="BUTTON_ABORT"}' onclick="history.back()"/>
         </p>

        </div>
        <!-- END: section -->
      </div>
    
    <!-- END: table -->
    </div>


    </form>
</body>
</html>
