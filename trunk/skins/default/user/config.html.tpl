<div class="config_form" id="config_user_settings">

<!-- BEGIN: table -->

    <div class="config_head">
        <div class="config_title" onclick="yanaToggleMenu(this.parentNode)">{lang id="USER.0"}{* Nutzerverwaltung *}</div>
    </div>

    <div class="help">
        <div class="help_text">
            {lang id="HELP.USER"}
            {lang id="HELP.NEW"}
        </div>
    </div>

    <div class="option">

        <div class="optionhead">{lang id="USER.3"}</div>

        <div class="optionbody" align="center" style="padding: 30px 10px;">
{create file="user_admin" id="user"}
        </div>

    </div>

<!-- END: table -->
</div>