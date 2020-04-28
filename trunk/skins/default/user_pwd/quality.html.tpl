<!DOCTYPE html>

<html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
        <script type="text/javascript" language="JavaScript" src="../styles/dynamic-styles.js"></script>
        <script type="text/javascript" language="JavaScript" src="../styles/admin-styles.js"></script>
        <link rel="stylesheet" type="text/css" href="../styles/config.css"/>
        <link rel="stylesheet" type="text/css" href="../styles/user.css"/>
    </head>

<body>

<form method="post" enctype="multipart/form-data" action="{$PHP_SELF}">
    <input type="hidden" name="action" value="{$ON_SUBMIT}"/>
    <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
    <input type="hidden" name="id" value="{$ID}"/>

<div class="config_form" id="config_user_settings">

<!-- BEGIN: table -->

  <div class="config_head">
      <div class="config_title" onclick="yanaToggleMenu(this.parentNode)">{lang id="USER.OPTION.36"}</div>
  </div>

  <div class="help">
      <div class="help_text">
        {lang id="help.password_quality"}
      </div>
  </div>

  <div class="option">

        <div class="optionhead">
          {lang id="USER.OPTION.36"}
        </div>

        <div class="optionbody" align="center" style="padding: 30px 10px;">

          <div class="optionitem">
            <span class="label"> {lang id="USER.OPTION.37"}:</span>
            {slider inputName="user/password/quality" min=0 max=100 width=200 value=$PROFILE.USER.PASSWORD.QUALITY}<br />
          </div>

          <div class="optionitem">
            <span class="label">{lang id="USER.OPTION.38"}:</span>
            <select name="user/password/count">
                <option value="0" {if $PROFILE.USER.PASSWORD.COUNT==0} selected="selected" {/if}>0</option>
                <option value="1" {if $PROFILE.USER.PASSWORD.COUNT==1} selected="selected" {/if}>1</option>
                <option value="2" {if $PROFILE.USER.PASSWORD.COUNT==2} selected="selected" {/if}>2</option>
                <option value="3" {if $PROFILE.USER.PASSWORD.COUNT==3} selected="selected" {/if}>3</option>
                <option value="4" {if $PROFILE.USER.PASSWORD.COUNT==4} selected="selected" {/if}>4</option>
                <option value="5" {if $PROFILE.USER.PASSWORD.COUNT==5} selected="selected" {/if}>5</option>
            </select>
          </div>

          <div class="optionitem">
            <span class="label">{lang id="USER.OPTION.39"}:</span>
            <select name="user/password/time">
                <option value="0" {if $PROFILE.USER.PASSWORD.TIME==0} selected="selected" {/if}>{lang id="USER.OPTION.40"}</option>
                <option value="1" {if $PROFILE.USER.PASSWORD.TIME==1} selected="selected" {/if}>{lang id="USER.OPTION.41"}</option>
                <option value="3" {if $PROFILE.USER.PASSWORD.TIME==3} selected="selected" {/if}>{lang id="USER.OPTION.42"}</option>
                <option value="6" {if $PROFILE.USER.PASSWORD.TIME==6} selected="selected" {/if}>{lang id="USER.OPTION.43"}</option>
                <option value="12" {if $PROFILE.USER.PASSWORD.TIME==12} selected="selected" {/if}>{lang id="USER.OPTION.44"}</option>
            </select>
          </div>

        </div>

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
</body>

</html>