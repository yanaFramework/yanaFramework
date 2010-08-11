<script type="text/javascript">
<!--{literal}
var passWordSafetyMayClose = false;
$(function(){
  $("#user_pwd_new").blur(
    function() {
      passWordSafetyMayClose = true;
      window.setTimeout(function() {
            if (passWordSafetyMayClose) {
                var progressBar = $("#config_pass .floating_menu");
                progressBar.hide(500);
            }
        },
        2000);
    }
  );
});
// -->{/literal}
</script>

<form method="post" action="{$PHP_SELF}" onsubmit="return config_pass('{lang id="USER.13"}')" id="config_pass" class="label" style="padding: 10px;">
    <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
    <input type="hidden" name="id" value="{$ID}"/>
    <input type="hidden" name="action" value="set_pwd"/>
    {if $SESSION_USER_ID}<input type="hidden" name="user_id" value="{$SESSION_USER_ID}"/>{/if}

    <span class="buttonize_static"><span class="icon_key_hover">&nbsp;</span></span>
    {if !$SESSION_USER_ID}
    <label class="config_pass_user">
        {lang id="USER.16"}
        <input title='{lang id="USER.7"}' type="text" name="user_id" size="10"/>
    </label>
    {/if}
    <label class="config_pass_old">
        {lang id="USER.5"}
        <input title='{lang id="USER.8"}' type="password" name="old_pwd" size="10"/>
    </label>
    <label class="config_pass_new">
        {lang id="USER.6"}
        <input type="password" id="user_pwd_new" name="new_pwd" size="10" onkeyup="yanaPasswordCheck(this.id)"/>
    </label>
    <label class="config_pass_repeat">
        {lang id="USER.6A"}
        <input type="password" id="user_pwd_repeat" name="repeat_pwd" size="10"/>
    </label>
    <input type="submit" value="{lang id="BUTTON_SAVE"}"/>
    <div class="floating_menu">
        <div class="header comment">
            <img src="../styles/icon5.gif" border="0" alt="{lang id="USER.6D"}" style="float: right;" onclick="yanaPasswordSetWidth(yanaToggleHelp(this))"/>
            {lang id="USER.6B"}
        </div>
        <div class="progressbar" style="width:140px;margin:auto;">0%</div>
        <div class="help">
            {lang id="USER.6C"}
        </div>
    </div>
</form>