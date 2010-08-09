<!--  SITEMAP
{* Set default value *}
{if $ID == "" }{assign var="ID" value="default"}{/if}
-->

<div id="sitemap">

  <h1>{lang id="SITEMAP_TITLE"}</h1>

  <div class="label">{lang id="SITEMAP_DESCRIPTION"}</div>

  <div id="sitemap_box">
    {sitemap}
  </div>

</div>
