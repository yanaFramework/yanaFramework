<!DOCTYPE html>

<html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
        <script type="text/javascript" language="javascript" src="index.js"></script>
    </head>

<body>
    <form method="post" enctype="multipart/form-data" action="{$PHP_SELF}">
    <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
    <input type="hidden" name="id" value="{$ID}"/>

    <div class="config_form">

<!-- BEGIN: table -->

      <div class="config_head">
          <div class="config_title" onclick="yanaToggleMenu(this.parentNode)">{lang id="DB_TOOLS.IMPORT"}</div>
      </div>

      <div class="help">
          <div class="help_text">
              {lang id="HELP.1"}
          </div>
      </div>

    <div class="option">

        <div class="optionbody" style="padding: 30px">

            <label class="optionitem">
              <input type="radio" name="action" value="db_tools_write_config" checked="checked" />
              {lang id="DB_TOOLS.FIELD3"}
            </label>

            <div class="help">
              {lang id="HELP.0"}
              {lang id="HELP.3"}
            </div>

            <div class="optionitem">
              <label style="font-weight: bold; margin-right: 20px;">
                <input type="radio" name="action" value="db_tools_importmdb2" />
                {lang id="DB_TOOLS.FIELD4"}
              </label>
              <label>
                {lang id="DB_TOOLS.FIELD5"}:
                <input type="file" name="mdb2"/>
              </label>
            </div>

            <div class="help">
              {lang id="HELP.0"}
              {lang id="HELP.4"}
            </div>

            <div class="optionitem">
              <label style="font-weight: bold; margin-right: 20px;">
                <input type="radio" name="action" value="db_tools_importdbdesigner4" />
                {lang id="DB_TOOLS.FIELD6"}
              </label>

              <label>
                {lang id="DB_TOOLS.FIELD5"}:
                <input type="file" name="dbdesigner4"/>
              </label>

            </div>

            <div class="help">
              {lang id="HELP.0"}
              {lang id="HELP.5"}
            </div>

        </div>

        <!-- END: section -->
      </div>


<!-- END: table -->
    </div>

    <p align="center">
        <input type="submit" value='{lang id="BUTTON_SUBMIT"}'/>
        <input type="button" title='{lang id="TITLE_ABORT"}' value="{lang id="BUTTON_ABORT"}" onclick="history.back()"/>
    </p>

    </form>
</body>

</html>
