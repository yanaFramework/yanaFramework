<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
        <style type="text/css">
{import file="default.css.tpl"}
        </style>
{* <!-- These styles are here to preview the template -->
        <link rel="stylesheet" type="text/css" href="styles/default.css"/>
        <link rel="stylesheet" type="text/css" href="styles/btn.css"/>
        <link rel="stylesheet" type="text/css" href="styles/menu.css"/>
        <link rel="stylesheet" type="text/css" href="styles/gui_generator.css"/>
        <link rel="stylesheet" type="text/css" href="styles/sitemap.css"/>
        <link rel="stylesheet" type="text/css" href="styles/admin.css"/>
*}
        <link rel="shortcut icon" href="favicon.ico"/>
        <link rel="stylesheet" type="text/css" media="print" href="styles/print.css" />
    </head>

<body>
  <div id="index_box" align="center">
<!-- Begin: headline -->
      <div id="index_header" class="header">
          <div id="index_header_left"><div id="index_header_right"><div id="index_header_center" class="index_{if !$PROFILE.LOGO}default{else}custom{/if}_logo">
              <!-- {if $PROFILE.LOGO} Begin: logo -->
              <div id="index_logo"><img alt="" src='{$PROFILE.LOGO}'/></div>
              <!-- End: logo {/if} -->
              <div id="index_header_appbar">{applicationBar}</div>
<!-- Begin: language block
 {sizeOf value=$INSTALLED_LANGUAGES assign="SIZE_OF_INSTALLED_LANGUAGES"}
 {if $SIZE_OF_INSTALLED_LANGUAGES > 1} -->
              <div id="index_language">{lang id="TITLE_LANG"}:
<!-- Begin: language
   {foreach from=$INSTALLED_LANGUAGES item="item" key="key"} -->
              <a href={"action=$ACTION&language=$key"|href} title="{lang id="SELECT_LANG"}: {$key}">{$key}</a>
<!-- {/foreach}
 End: language -->
            </div>
<!-- {/if}
 End: language block -->

            <div id="index_rss">{rss}</div>
            <div id="index_visitor" class="description">{visitorCount}</div>

          </div></div></div>
      </div>
<!-- End: headline -->

<!-- Begin: toolbar -->
      <div id="index_toolbar">{toolbar}</div>
<!-- End: toolbar -->

  <div id="index_body">
    <div id="index_body_left"><div id="index_body_right">
<!-- Begin: content -->
      <div id="index_content" align="center">

<!-- Begin: description
 {if $DESCRIPTION} -->
        <div id="index_description">
            {lang id="PROGRAM_TITLE"}:
            {$DESCRIPTION|embeddedTags}
        </div>
<!-- {/if}
 End: description -->
        <div align="center">
          {import id="stdout" STDOUT=$STDOUT}
        </div>
        <div id="index_display">
{import file=$SYSTEM_INSERT}
        </div>
      </div>
<!-- End: content -->
    </div></div>
    <div id="index_body_bottom"><div id="index_body_bottom_left"><div id="index_body_bottom_right">
    &nbsp;
    </div></div></div>
  </div>
  </div>

</body>

</html>
