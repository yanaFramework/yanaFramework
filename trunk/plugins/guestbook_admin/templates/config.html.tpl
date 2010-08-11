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
    <input type="hidden" name="action" value="{if !$ID}{if $PERMISSION==100}set_config_default{/if}{else}set_config_profile{/if}"/>
    <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
    <input type="hidden" name="id" value="{$ID}"/>
    <div class="config_form">
    <!-- BEGIN: table -->

        <div class="config_head">
            <div class="config_title" onclick="yanaToggleMenu(this.parentNode)">{lang id="PROGRAM_TITLE"}</div>
        </div>

        <div class="option">

            <!-- BEGIN: section -->
            <div class="optionhead">{lang id="ADMIN.51"}{* bei neuen Einträgen *}</div>
            <div class="help">
                {lang id="HELP.0"}
                {lang id="HELP.2"}
            </div>

            <div class="optionbody">

                <div class="optionitem">
                    <span class="label">{lang id="ADMIN.73"}:</span>
                    <label>
                        {lang id="YES"}
                        <input title='{lang id="ADMIN.69"}' type="radio" name="guestbook/notification"
                               {if $PROFILE.GUESTBOOK.NOTIFICATION}checked="checked"{/if} value="true" />
                    </label>
                    <label>
                        {lang id="NO"}
                        <input title='{lang id="ADMIN.69"}' type="radio" name="guestbook/notification"
                               {if !$PROFILE.GUESTBOOK.NOTIFICATION}checked="checked"{/if} value="false" />
                    </label>
                </div>

                <div class="optionitem">
                    <span class="label">{lang id="ADMIN.74"}:</span>
                    <input type="text" title='{lang id="ADMIN.70"}' size="30" name="guestbook/mail" value="{$PROFILE.GUESTBOOK.MAIL}"/>
                </div>

            </div>
            <!-- END: section -->
            <!-- {if $PROFILE.USERMODE} BEGIN: section -->
            <div class="optionhead">{lang id="ADMIN.58"}{* Spamschutz *}</div>
            <div class="help">
                {lang id="HELP.0"}
                {lang id="HELP.14"}
            </div>

            <div class="optionbody">

                <label class="optionitem">
                    <span class="label">{lang id="ADMIN.50"}:</span>
                    <select name="guestbook/flooding">
                        <option value="0" {if $PROFILE.GUESTBOOK.FLOODING==0} selected="selected" {/if}>{lang id="ADMIN.43"}</option>
                        <option value="1" {if $PROFILE.GUESTBOOK.FLOODING==1} selected="selected" {/if}>{lang id="ADMIN.44"}</option>
                        <option value="2" {if $PROFILE.GUESTBOOK.FLOODING==2} selected="selected" {/if}>2 {lang id="ADMIN.45"}</option>
                        <option value="3" {if $PROFILE.GUESTBOOK.FLOODING==3} selected="selected" {/if}>3 {lang id="ADMIN.45"}</option>
                        <option value="4" {if $PROFILE.GUESTBOOK.FLOODING==4} selected="selected" {/if}>4 {lang id="ADMIN.45"}</option>
                        <option value="5" {if $PROFILE.GUESTBOOK.FLOODING==5} selected="selected" {/if}>5 {lang id="ADMIN.45"}</option>
                        <option value="6" {if $PROFILE.GUESTBOOK.FLOODING==6} selected="selected" {/if}>6 {lang id="ADMIN.45"}</option>
                        <option value="7" {if $PROFILE.GUESTBOOK.FLOODING==7} selected="selected" {/if}>7 {lang id="ADMIN.45"}</option>
                        <option value="8" {if $PROFILE.GUESTBOOK.FLOODING==8} selected="selected" {/if}>8 {lang id="ADMIN.45"}</option>
                        <option value="9" {if $PROFILE.GUESTBOOK.FLOODING==9} selected="selected" {/if}>9 {lang id="ADMIN.45"}</option>
                        <option value="10" {if $PROFILE.GUESTBOOK.FLOODING==10} selected="selected" {/if}>10 {lang id="ADMIN.45"}</option>
                        <option value="15" {if $PROFILE.GUESTBOOK.FLOODING==15} selected="selected" {/if}>15 {lang id="ADMIN.45"}</option>
                        <option value="20" {if $PROFILE.GUESTBOOK.FLOODING==20} selected="selected" {/if}>20 {lang id="ADMIN.45"}</option>
                        <option value="30" {if $PROFILE.GUESTBOOK.FLOODING==30} selected="selected" {/if}>30 {lang id="ADMIN.45"}</option>
                        <option value="50" {if $PROFILE.GUESTBOOK.FLOODING==50} selected="selected" {/if}>50 {lang id="ADMIN.45"}</option>
                        <option value="100" {if $PROFILE.GUESTBOOK.FLOODING==100} selected="selected" {/if}>100 {lang id="ADMIN.45"}</option>
                    </select>
                </label>

            </div>

            <div class="optionhead">{lang id="ADMIN.56"}{* Einträge pro Seite *}</div>
            <div class="help">
                {lang id="HELP.0"}
                {lang id="HELP.4"}
            </div>

            <div class="optionbody">

                <label class="optionitem">
                    <span class="label">{lang id="ADMIN.67"}:</span>
                    <select name="guestbook/entPerPage">
                        <option value="1" {if $PROFILE.GUESTBOOK.ENTPERPAGE==1} selected="selected" {/if}>{lang id="ADMIN.44"}</option>
                        <option value="2" {if $PROFILE.GUESTBOOK.ENTPERPAGE==2} selected="selected" {/if}>2 {lang id="ADMIN.45"}</option>
                        <option value="3" {if $PROFILE.GUESTBOOK.ENTPERPAGE==3} selected="selected" {/if}>3 {lang id="ADMIN.45"}</option>
                        <option value="4" {if $PROFILE.GUESTBOOK.ENTPERPAGE==4} selected="selected" {/if}>4 {lang id="ADMIN.45"}</option>
                        <option value="5" {if $PROFILE.GUESTBOOK.ENTPERPAGE==5} selected="selected" {/if}>5 {lang id="ADMIN.45"}</option>
                        <option value="6" {if $PROFILE.GUESTBOOK.ENTPERPAGE==6} selected="selected" {/if}>6 {lang id="ADMIN.45"}</option>
                        <option value="7" {if $PROFILE.GUESTBOOK.ENTPERPAGE==7} selected="selected" {/if}>7 {lang id="ADMIN.45"}</option>
                        <option value="8" {if $PROFILE.GUESTBOOK.ENTPERPAGE==8} selected="selected" {/if}>8 {lang id="ADMIN.45"}</option>
                        <option value="9" {if $PROFILE.GUESTBOOK.ENTPERPAGE==9} selected="selected" {/if}>9 {lang id="ADMIN.45"}</option>
                        <option value="10" {if $PROFILE.GUESTBOOK.ENTPERPAGE==10} selected="selected" {/if}>10 {lang id="ADMIN.45"}</option>
                        <option value="15" {if $PROFILE.GUESTBOOK.ENTPERPAGE==15} selected="selected" {/if}>15 {lang id="ADMIN.45"}</option>
                        <option value="20" {if $PROFILE.GUESTBOOK.ENTPERPAGE==20} selected="selected" {/if}>20 {lang id="ADMIN.45"}</option>
                        <option value="30" {if $PROFILE.GUESTBOOK.ENTPERPAGE==30} selected="selected" {/if}>30 {lang id="ADMIN.45"}</option>
                        <option value="50" {if $PROFILE.GUESTBOOK.ENTPERPAGE==50} selected="selected" {/if}>50 {lang id="ADMIN.45"}</option>
                        <option value="100" {if $PROFILE.GUESTBOOK.ENTPERPAGE==100} selected="selected" {/if}>100 {lang id="ADMIN.45"}</option>
                    </select>
                </label>

            </div>
            <!-- END: section {/if} -->
        </div>
    </div>
<!-- END: table -->

{if $WRITEABLE}
  <p align="center">
      <input type="submit" value='{lang id="ADMIN.17"}'/>
      <input type="button" title='{lang id="TITLE_ABORT"}' value='{lang id="BUTTON_ABORT"}' onclick="history.back()"/>
  </p>
{else}
  <p align="center">{lang id="ADMIN.18"}</p>
{/if}

</form>

</body>

</html>