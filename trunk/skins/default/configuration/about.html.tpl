<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
        <script type="text/javascript" language="JavaScript" src="../styles/dynamic-styles.js"></script>
        <link rel="stylesheet" type="text/css" href="../styles/config.css"/>
    </head>

<body>
<div id="config_about" class="config_form">

  <div class="config_head">
      <!-- {if !empty($INFO.UPDATE)} -->
      <a class="buttonize" href="'{$INFO.UPDATE}'">{lang id="ADMIN.8"}</a>
      <!-- {/if} -->
      <div id="exp1" class="config_title">{$INFO.NAME}</div>
  </div>
<!-- {if !empty($INFO.VERSION) || !empty($INFO.LAST_CHANGE)} -->
  <div class="help">
      <div class="help_text" style="height: 30px;">
        {lang id="ADMIN.7"}:
        <!-- {if !empty($INFO.LAST_CHANGE)} -->
        &nbsp;
        {$INFO.LAST_CHANGE|date}
        <!-- {elseif !empty($INFO.VERSION)} -->
        {$INFO.VERSION}
        <!-- {else} -->
        1.0
        <!-- {/if} -->
      </div>
  </div>
<!-- {/if} -->

  <div class="option">

      <div class="optionbody">

<!-- {if !empty($INFO.LOGO)} -->
          <div class="white_box" style="float: right;">
            <img src="{$INFO.LOGO}" alt=""/>
          </div>
<!-- {/if} -->

<!-- {if !empty($INFO.AUTHOR)} -->
          <div class="optionitem">
            <span class="label">{lang id="ADMIN.34"}:</span>
            {$INFO.AUTHOR}
          </div>
<!-- {/if} -->

<!-- {if !empty($INFO.CONTACT)} -->
          <div class="optionitem">
            <span class="label">{lang id="ADMIN.35"}:</span>
            {$INFO.CONTACT}
          </div>
<!-- {/if} -->

        <div class="blue_box">
          {$INFO.DESCRIPTION|embeddedTags}
        </div>

      </div>

  </div>

</div>
</body>

</html>
