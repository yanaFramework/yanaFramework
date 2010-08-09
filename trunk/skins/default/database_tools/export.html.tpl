<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
        <script type="text/javascript" language="javascript" src="index.js"></script>
    </head>

<body>
    <div class="config_form">

<!-- BEGIN: table -->
    <form method="post" enctype="multipart/form-data" action="{$PHP_SELF}">
    <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
    <input type="hidden" name="id" value="{$ID}"/>

      <div class="config_head">
          <div class="config_title" onclick="yanaToggleMenu(this.parentNode)">{lang id="DB_TOOLS.EXPORTSQL"}</div>
      </div>

      <div class="help">
          <div class="help_text">
              {lang id="HELP.2"}
          </div>
      </div>

    <div class="option">

        <div class="optionbody">
          <input type="hidden" name="action" value="db_tools_exportsql"/>
            <label class="optionitem">
              <span class="label">{lang id="DB_TOOLS.FIELD1"}:</span>
              <select name="dbms">
{foreach from=$LIST_OF_EXPORTABLE_DBMS key=value item=name}
                <option value="{$value}" {if $SELECTED_DBMS == $value }selected="selected"{/if}>{$name}</option>
{/foreach}
              </select>
            </label>
            <div class="optionhead">
                {lang id="DB_TOOLS.FIELD2"}:
            </div>
            <div class="optionitem">
{foreach from=$DATABASE_LIST key=database_key item=database_name}
              <div>
                  <span class="label">{$database_key+1}.</span>
                  <label>
                      <input type="checkbox" name="list[]" value="{$database_name}" checked="checked"/>
                      {$database_name}
                  </label>
              </div>
{/foreach}
            </div>
            <p align="center"><input type="submit" value="{lang id="BUTTON_SUBMIT"}"/></p>
        </div>

        <!-- END: section -->
      </div>

    </form>
<!-- END: table -->

<!-- BEGIN: table -->
    <form method="post" enctype="multipart/form-data" action="{$PHP_SELF}">
    <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
    <input type="hidden" name="id" value="{$ID}"/>


      <div class="config_head">
          <div class="config_title" onclick="yanaToggleMenu(this.parentNode)">{lang id="DB_TOOLS.EXPORTXML"}</div>
      </div>

      <div class="help">
          <div class="help_text">
              {lang id="HELP.6"}
          </div>
      </div>

    <div class="option">

        <div class="optionbody">
          <input type="hidden" name="action" value="DB_TOOLS_EXPORTXML"/>
            <div class="optionhead">
                {lang id="DB_TOOLS.FIELD7"}:
            </div>
            <div class="optionitem">
{foreach from=$DATABASE_LIST key=database_key item=database_name}
              <div>
                  <span class="label">{$database_key+1}.</span>
                  <label>
                    <input type="checkbox" name="list[]" value="{$database_name}" checked="checked"/>
                    {$database_name}
                  </label>
              </div>
{/foreach}
            </div>
            <p align="center"><input type="submit" value="{lang id="BUTTON_SUBMIT"}"/></p>
        </div>

        <!-- END: section -->
      </div>


    </form>
<!-- END: table -->

    </div>
</body>

</html>
