{create file="project" id="project"}

<h1 class="header">{lang id="PRJ.LIST"}</h1>
<!-- {if $PROJECT} -->
  <ol class="label">
<!-- {foreach from=$PROJECT key="i" item="item"} -->
    <li><a href={"action=project_read_read_seperated_project&entries=1&page=$i"|href}>{$item.PROJECT_NAME}</a></li>
<!-- {/foreach} -->
  </ol>
<!-- {else} -->
  <p>{lang id="NO_ENTRIES_FOUND"}</p>
<!-- {/if} -->

<h1 class="header">{lang id="PRJ.DESC2"}</h1>

 <p><label>
     {lang id="PRJ.CHOOSE"}:
     <select onchange="window.ajaxRequest.send(this.options[this.selectedIndex].value)">
<!-- {foreach from=$PROJECT key="i" item="item"} -->
       <option value="{$item.PROJECT_ID}">{$item.PROJECT_NAME}</option>
<!-- {/foreach} -->
     </select>
   </label>
 </p>
 <div id="project_sum">
   <noscript>
     This is an Ajax-Application that requires JavaScript to work properly.
     JavaScript is currently deactivated. Please activate it and try again.
   </noscript>
 </div>
 <script type="text/javascript"><!--
 function startup()
 {ldelim}
    if (!document.body || !AjaxRequest) {ldelim}
        window.setTimeout('startup()', 500);
        return;
    {rdelim} else {ldelim}
        window.ajaxRequest = new AjaxRequest("{'&action=project_sum&target='|url:true}");
        window.ajaxRequest.setTarget('project_sum');
    {rdelim}
 {rdelim}
 startup();
 //--></script>