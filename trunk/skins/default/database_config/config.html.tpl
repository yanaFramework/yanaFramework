<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Settings</title>
        <link rel="stylesheet" type="text/css" href="../styles/config.css"/>
        <script type="text/javascript" language="JavaScript" src="../scripts/default.js"></script>
        <script type="text/javascript" language="JavaScript" src="../styles/dynamic-styles.js"></script>
        <script type="text/javascript" language="JavaScript"><!--
            var src="";
        //--></script>
    </head>
<body>
    <form method="post" enctype="multipart/form-data" action="{$PHP_SELF}">
    <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
    <input type="hidden" name="id" value="{$ID}"/>

    <div class="config_form">

<!-- BEGIN: table -->

      <div class="config_head">
          <div class="config_title" onclick="yanaToggleMenu(this.parentNode)">{lang id="INDEX_7"}{* Datenbank *}</div>
      </div>
    
      <div class="help">
          <div class="help_text">
              {lang id="HELP.19"}
          </div>
      </div>
    
      <div class="option">
        <!-- BEGIN: section -->

        <div class="optionhead" id="title_of_settings">{lang id="DATABASE.11"}{* Verbindung herstellen *}</div>

        <div class="optionbody" id="list_of_settings">

          <div class="help">
              {lang id="HELP.0"}
              {lang id="HELP.20"}
          </div>

          <div class="optionitem">
            <span class="label">{lang id="DATABASE.10"}:</span>
            <label>
              {lang id="YES"}
              <input type="radio" name="active" {if $DATABASE_ACTIVE}checked="checked"{/if} value="true" {if $PROFILE.USERMODE} onclick="document.getElementById('autoinstall').style.display='block';document.getElementById('autosync').style.display='block';" {/if}/>
            </label>
            <label>
              {lang id="NO"}
              <input type="radio" name="active" {if !$DATABASE_ACTIVE} checked="checked" {/if} value="false" {if $PROFILE.USERMODE} onclick="document.getElementById('autoinstall').style.display='none';document.getElementById('autosync').style.display='block';" {/if}/>
            </label>
          </div>

          <label class="optionitem">
            <span class="label">{lang id="DATABASE.9"}:</span>
            <select name="dbms">
                <option value="db2" {if    $DATABASE_DBMS=="db2"   }selected="selected"{/if}>DB2</option>
                <option value="dbase" {if  $DATABASE_DBMS=="dbase" }selected="selected"{/if}>dBase</option>
                <option value="fbsql" {if  $DATABASE_DBMS=="fbsql" }selected="selected"{/if}>FrontBase</option>
                <option value="ibase" {if  $DATABASE_DBMS=="ibase" }selected="selected"{/if}>InterBase</option>
                <option value="ifx" {if    $DATABASE_DBMS=="ifx"   }selected="selected"{/if}>Informix</option>
                <option value="access" {if $DATABASE_DBMS=="access"}selected="selected"{/if}>Microsoft Access</option>
                <option value="mssql" {if  $DATABASE_DBMS=="mssql" }selected="selected"{/if}>Microsoft SQL-Server</option>
                <option value="mysql" {if  $DATABASE_DBMS=="mysql" }selected="selected"{/if}>MySQL &lt;=4.0</option>
                <option value="mysqli" {if $DATABASE_DBMS=="mysqli"}selected="selected"{/if}>MySQL &gt;=4.1 (PHP5)</option>
                <option value="oci8" {if   $DATABASE_DBMS=="oci8"  }selected="selected"{/if}>Oracle OCI8</option>
                <option value="pgsql" {if  $DATABASE_DBMS=="pgsql" }selected="selected"{/if}>PostgreSQL</option>
                <option value="sybase" {if $DATABASE_DBMS=="sybase"}selected="selected"{/if}>Sybase</option>
            </select>
          </label>

          <div class="help">
              {lang id="HELP.0"}
              {lang id="HELP.21"}
          </div>

          <label class="optionitem">
            <span class="label">{lang id="DATABASE.0"}:</span>
            <input type="text" title="{$LANGUAGE.DATABASE.1|entities}" name="host" value="{$DATABASE_HOST}"/>
          </label>

          <div class="help">
              {lang id="HELP.0"}
              {lang id="HELP.22"}
          </div>

          <label class="optionitem">
            <span class="label">{lang id="DATABASE.2"}:</span>
            <input type="text" title="{$LANGUAGE.DATABASE.3|entities}, {lang id="DATABASE.7"}" name="port" value="{$DATABASE_PORT}"/>
          </label>

          <div class="help">
              {lang id="HELP.0"}
              {lang id="HELP.23"}
          </div>

          <label class="optionitem">
            <span class="label">{lang id="DATABASE.4"}:</span>
            <input type="text" name="user" value="{$DATABASE_USER}"/>
          </label>

          <label class="optionitem">
            <span class="label">{lang id="DATABASE.5"}:</span>
            <input type="password" name="password" value="{$DATABASE_PASSWORD}"/>
          </label>

<!--{*
          <div class="help">
              {lang id="HELP.0"}
              {lang id="HELP.24"}
          </div>

          <label class="optionitem">
            <span class="label">{lang id="DATABASE.6"}:</span>
            <input type="text" title="{lang id="DATABASE.7"}" name="prefix" value="{$DATABASE_PREFIX}"/>
          </label>
*}-->

          <label class="optionitem">
            <span class="label">{lang id="DATABASE.8"}:</span>
            <input type="text" name="name" value="{$DATABASE_NAME}"/>
          </label>

          <label class="optionitem" id="autoinstall" {if !$PROFILE.USERMODE} style="display: none" {/if}>
            <span class="label">{lang id="DATABASE.21"}:</span>{* Tabellen automatisch anlegen *}
            <input type="checkbox" name="autoinstall" value="true" checked="checked"/>
          </label>

          <label class="optionitem" id="autosync" {if !$PROFILE.USERMODE} style="display: none" {/if}>
            <span class="label">{lang id="DATABASE.22"}:</span>{* Datensätze kopieren *}
            <input type="checkbox" name="autosync" value="true" checked="checked"/>
          </label>
          <script type="text/javascript">
          <!--
          document.getElementById('autoinstall').style.display='none';
          document.getElementById('autosync').style.display='none';
          //-->
          </script>

        </div>

        <div class="optionhead" id="title_of_databases">{lang id="DATABASE.16"}</div>

        <div class="optionbody">
          <div id="db_sync"
              <div class="help">
                  {lang id="HELP.0"}
                  <span style="font-weight: bold;">{lang id="DATABASE.14"}.</span><br />
                  {lang id="HELP.25"}
              </div>
          </div>
            <div id="db_install">
              <div class="help">
                  {lang id="HELP.0"}
                  <span style="font-weight: bold;">{lang id="DATABASE.12"}.</span><br />
                  {lang id="HELP.6"}
              </div>
          </div>
          <div id="db_backup">
              <div class="help">
                  {lang id="HELP.0"}
                  <span style="font-weight: bold;">{lang id="DATABASE.15"}.</span><br />
                  {lang id="HELP.26"}
              </div>
          </div>

          <div id="list_of_databases">
            {lang id="DATABASE.13"}
            <ol>
{foreach from=$DATABASE_LIST item=database_name}
                <li>
                  <label>
                    <input type="checkbox" name="list[]" value="{$database_name}" checked="checked"/>
                    {$database_name}
                  </label>
                </li>
{/foreach}
            </ol>
          </div>

          <div id="db_options" style="display: none;">
            <label>
              {lang id="DATABASE.20"}
              <select name="target_dbms">
                <option value="db2" {if    $DATABASE_DBMS=="db2"   }selected="selected"{/if}>DB2</option>
                <option value="mssql" {if  $DATABASE_DBMS=="mssql"||$DATABASE_DBMS=="access" }selected="selected"{/if}>Microsoft SQL</option>
                <option value="mysql" {if  $DATABASE_DBMS=="mysql" ||$DATABASE_DBMS=="mysqli" }selected="selected"{/if}>MySQL</option>
                <option value="oci8" {if   $DATABASE_DBMS=="oci8"  }selected="selected"{/if}>Oracle OCI8</option>
                <option value="pgsql" {if  $DATABASE_DBMS=="pgsql" }selected="selected"{/if}>PostgreSQL</option>
              </select>
            </label>

            <label>
              <input type="checkbox" name="options[structure]" value="1"/>
              {lang id="DATABASE.17"}
            </label>

            <label>
              <input type="checkbox" name="options[data]" value="1" checked="checked"/>
              {lang id="DATABASE.18"}
            </label>

            <label>
              <input type="checkbox" name="options[zip]" value="1"/>
              {lang id="DATABASE.19"}
            </label>
          </div>

          <p align="center">
            <select name="action" onchange="yanaSelectAction(this.options[this.selectedIndex].value)">
              <option value="set_db_configuration">{lang id="BUTTON_SAVE"}</option>
              <option value="db_install">{lang id="DATABASE.12"}</option>
              <option value="db_sync">{lang id="DATABASE.14"}</option>
              <option value="db_backup">{lang id="DATABASE.15"}</option>
            </select>
            <script language="javascript" type="text/javascript">yanaSelectAction()</script>
            <input type="submit" value="{lang id="BUTTON_SUBMIT"}"/>
            <input type="button" title="{lang id="TITLE_ABORT"}" value="{lang id="BUTTON_ABORT"}" onclick="history.back()"/>
          </p>

        </div>

        <!-- END: section -->
      </div>


<!-- END: table -->
    </div>

    </form>
</body>
</html>
