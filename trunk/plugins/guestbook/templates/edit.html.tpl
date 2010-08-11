<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
        <link rel="stylesheet" type="text/css" href="../styles/default.css"/>
    </head>

<body>
    <form action="{$PHP_SELF}" method="post" class="guestbook_form" id="guestbook_form_edit{$TARGET}" onsubmit="if (yanaCheckEmbTags(event,document.getElementById('guestbook_form_edit{$TARGET}'))){ldelim}YanaGuestbook.prototype.guestbookRequest('{$ACTION_EDIT_WRITE}','guestbook_form_edit{$TARGET}','','guestbook_form_edit{$TARGET}');window.setTimeout('document.getElementById(\'guestbook_entry{$TARGET}\').style.display=\'block\';document.getElementById(\'guestbook_form{$TARGET}\').style.display=\'none\';YanaGuestbook.prototype.guestbookRequest(\'{$ACTION_ENTRY}\',\'guestbook_entry{$TARGET}\',\'target={$TARGET}\');',5000);{rdelim};return false">
      <input type="hidden" name="id" value="{$ID}"/>
      <input type="hidden" name="action" value="{$ACTION_EDIT_WRITE}"/>
      <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
      <input type="hidden" name="target" value="{$TARGET}"/>

      <div class="guestbook_head" id="guestbook_form_new_head">{lang id="16"}</div>

      <div class="guestbook_form_content">
    <!-- {if $PROFILE.SPAM.CAPTCHA && ($PROFILE.SPAM.PERMISSION || !$PERMISSION)} -->
          <div class="label" style="display: block; height: 30px;">
            <span class="gui_generator_mandatory">*&nbsp;</span>
            {lang id="SECURITY_IMAGE.TITLE"}, {lang id="SECURITY_IMAGE.DESCRIPTION"}:
            {captcha}
          </div>
    <!-- {/if} -->

          <label class="label" style="display: block; float: left; width: 50%; text-align: right; height: 30px;">
            <span class="gui_generator_mandatory">*&nbsp;</span>{lang id="21"}:
            <input type="text" title="{lang id="12"}" size="22" name="name" maxlength="60" value="{$ROW.GUESTBOOK_NAME}"/>
          </label>
    
          <label class="label" style="display: block; margin-left: 50%; height: 30px;">
            <img border="0" src="data/mail.gif" hspace="8" width="23" height="20" alt="{lang id="3"}"/>
            <input type="text" title="{lang id="27"}" size="22" name="mail" maxlength="60" value="{$ROW.GUESTBOOK_MAIL}" onchange="if(null==(this.value.match(/^[äöüß\w\d-_\.]+\@[äöüß\w\d-_\.]+\.[\w\d-_\.]+$/i))){ldelim}alert('{lang id="INVALID_INPUT"}');this.value='';this.className='invalid';{rdelim}else{ldelim}this.className='';{rdelim}"/>
          </label>

          <label class="label" style="display: block; float: left; width: 50%; text-align: right; height: 30px;">
            {lang id="4"}:<img border="0" src="data/location.gif" hspace="12" alt=""/>
            <input type="text" title="{lang id="26"}" size="22" name="hometown" maxlength="60" value="{$ROW.GUESTBOOK_HOMETOWN}"/>
          </label>
    
          <label class="label" style="display: block; margin-left: 50%; height: 30px;">
            <img border="0" src="data/messenger.gif" hspace="12" width="20" height="20" alt="{lang id="22"}"/>
            <input type="text" title="{lang id="29"}" size="10" name="messenger" maxlength="60" value="{$ROW.GUESTBOOK_MESSENGER}"/>
            <select name="msgtyp">
                <option {if $ROW.GUESTBOOK_MSGTYP == 'ICQ'}selected="selected"{/if}value="ICQ" style="background-color: yellowgreen; color: black">ICQ</option>
                <option {if $ROW.GUESTBOOK_MSGTYP == 'AOL'}selected="selected"{/if}value="AOL" style="background-color: yellow; color: black">AOL</option>
                <option {if $ROW.GUESTBOOK_MSGTYP == 'YAHOO'}selected="selected"{/if}value="YAHOO" style="background-color: orange; color: black">Yahoo!</option>
                <option {if $ROW.GUESTBOOK_MSGTYP == 'MSN'}selected="selected"{/if}value="MSN" style="background-color: tomato; color: black">MSN</option>
            </select>
          </label>
    
          <label class="label" style="display: block; float: left; width: 50%; text-align: right; height: 30px;">
            {lang id="2"}:<img border="0" src="data/homepage.gif" hspace="10" width="23" height="20" alt=""/>
            <input type="text" title="{lang id="28"}" size="22" name="homepage" maxlength="60" value="{$ROW.GUESTBOOK_HOMEPAGE}"/>
          </label>
    
          <label class="label" style="display: block; margin-left: 50%; height: 30px;">
            {lang id="RATE_0"}
            <select name="opinion">
                <option value="0" style="background-color: #EEEEEE; color: black">{lang id="23"}:</option>
                <option {if $ROW.GUESTBOOK_OPINION == 1}selected="selected"{/if} value="1" style="background-color: #C0F050; color: black">{lang id="RATE_1"}</option>
                <option {if $ROW.GUESTBOOK_OPINION == 2}selected="selected"{/if} value="2" style="background-color: #A0D050; color: black">{lang id="RATE_2"}</option>
                <option {if $ROW.GUESTBOOK_OPINION == 3}selected="selected"{/if} value="3" style="background-color: #FFD050; color: black">{lang id="RATE_3"}</option>
                <option {if $ROW.GUESTBOOK_OPINION == 4}selected="selected"{/if} value="4" style="background-color: #FFB050; color: black">{lang id="RATE_4"}</option>
            </select>
          </label>

          <br />

      <div class="label" align="left">
        <span class="gui_generator_mandatory">*&nbsp;</span>{lang id="8"}:<br />
        <textarea rows="5" name="message" id="edit_guestbook_message{$ROW.GUESTBOOK_ID}" cols="60">{$ROW.GUESTBOOK_MESSAGE}</textarea>
      </div>
    
      <div align="left">
    
        <!-- BEGIN: Embedded Tags -->
    
        <div class="label">{lang id="FORMAT_TEXT"}:<br />
        {embeddedTags show="b,i,u,url,mark,color,smilies"}
        </div>
        <!-- END: Embedded Tags -->
    
        <!-- BEGIN: Preview -->
        {preview}

        <!-- END: Preview -->
        <div style="margin-top: 10px;">
          <input type="submit" value='{lang id="BUTTON_SAVE"}'/>
          <input type="button" title='{lang id="TITLE_ABORT"}' value="{lang id="BUTTON_ABORT"}" onclick="document.getElementById('guestbook_form{$TARGET}').style.display='none';document.getElementById('guestbook_entry{$TARGET}').style.display='block';"/>
        </div>

      </div>

      <p class="gui_generator_mandatory">{lang id="MANDATORY"}</p>

      </div>
    </form>
</body>

</html>
