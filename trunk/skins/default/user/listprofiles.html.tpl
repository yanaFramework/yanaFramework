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

<div class="config_form" id="config_user_settings">

<!-- BEGIN: table -->

  <div class="config_head">
      <div class="config_title" onclick="yanaToggleMenu(this.parentNode)">{lang id="USER.21"}{* Profilverwaltung *}</div>
  </div>

  <div class="help">
      <div class="help_text">
        {lang id="HELP.PROFILE"}
      </div>
  </div>

  <div class="option">

    <div class="optionbody" align="center" style="padding: 30px;">
        {create file="user_admin" id="userprofile"}
    </div>

  </div>

<!-- END: table -->
</div>
</body>

</html>