<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>{lang id="PROGRAM_TITLE"}</title>
		<link rel="stylesheet" type="text/css" href="../styles/default.css"/>
		<link rel="stylesheet" type="text/css" href="../styles/searchengine.css"/>
	</head>

<body>
<script type="text/javascript"><!--
  var language = new Array();
  language['whatsrelated.html'] = '{$LANGUAGEDIR}{lang id="WHATSRELATED"}';
//--></script>

  <form action="{$PHP_SELF}" method="post" name="suche">
    <input type="hidden" name="action" value="search_start"/>
    <input type="hidden" name="id" value="{$ID}"/>
    <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>

    <div>
      <label><b>{lang id="SEARCH"}</b>&nbsp;<input type="text" name="target" size="20" value=""/></label>&nbsp;<input type="submit" value="{lang id="BUTTON_SEARCH"}"/>
      &nbsp;&nbsp;<a title="{lang id="STATS_TITLE"}" href='{"action=search_stats"|url}' style="text-decoration: none; font-size: 9pt; font-weight: bold; color: #000;">{lang id="STATS_BUTTON"}</a>
    </div>

  </form>

  <!-- {if $ACTION=="search_stats"} -->
  <h2>{lang id="STATS_NAME"}</h2>

  <table cellspacing="0" cellpadding="8" id="statistics" align="center" summary="{lang id="STATS_NAME"}">
   <!-- {assign var="i" value=0} -->
   <!-- {assign var="last_count" value=0} -->
   {foreach item=CURRENT from=$STATS}
   <!-- {assign var="i" value=$i+1 } -->
     <!-- {if $i % 2 == 1 } -->
     <tr class="search_stat1">
     <!-- {else} -->
     <tr class="search_stat2">
     <!-- {/if} -->
       <th align="left"  class="search_label"><!-- {if $CURRENT.COUNT != $last_count } -->{$i}<!-- {/if} --></th>
       <td align="right" class="description">{$CURRENT.INFO}</td>
       <td align="right" class="comment">{$CURRENT.COUNT}&nbsp;<!-- {if $CURRENT.COUNT > 1 } -->{lang id="STATS_HITS"}<!-- {else} -->{lang id="STATS_HIT"}<!-- {/if} --></td>
     </tr>
   <!-- {assign var="last_count" value=$CURRENT.COUNT } -->
   {foreachelse}
   <tr>
     <td>
       <p>{$LANGUAGE.STATS_MISSING|embeddedTags}</p>
     </td>
   </tr>
   {/foreach}
  </table>
  <!-- {else} -->
  <!-- {if $SUBJECT}
       {assign var="ENTRIES_PER_PAGE" value=10}
  -->

  <h2>{lang id="JS_RESULTS"}</h2>

  <div id="resultset">
{foreach name="result" from=$RESULTS item="CURRENT" }
 <!-- {assign var="nr" value=$smarty.foreach.result.index} -->
  <!-- {if ($nr % $ENTRIES_PER_PAGE === 0) } -->
    <div id="page{$nr/$ENTRIES_PER_PAGE}" class="search_default_visible">

      <div class="search_toolbar">
        {lang id="JS_SHOW"} {$nr+1}-{$nr+$ENTRIES_PER_PAGE} {lang id="JS_OF"} {$smarty.foreach.result.total} {lang id="JS_HITS"}
        <div class="search_subject">{lang id="SEARCH_FOR"} <b>{$SUBJECT}</b></div>
      </div>

      <div class="search_result_head">
      <!-- {if ($nr / $ENTRIES_PER_PAGE) gt 0 } -->
        <a href="javascript:show({$nr/$ENTRIES_PER_PAGE}-1)" target="_self">
          {lang id="BUTTON_PREVIOUS"}
        </a>
      <!-- {else} -->
        <a>
          &nbsp;
        </a>
      <!-- {/if} -->
      <!-- {if $nr lt $smarty.foreach.result.total - $ENTRIES_PER_PAGE } -->
        <a href="javascript:show({$nr/$ENTRIES_PER_PAGE}+1)" target="_self">
          {lang id="BUTTON_NEXT"}
        </a>
      <!-- {else} -->
        <a>
          &nbsp;
        </a>
      <!-- {/if} -->
      </div>

 <!-- {/if} -->
      <div class="search_hit">
        <a href="{$CURRENT.URL}" target="{$CURRENT.TARGET}">{$CURRENT.TITLE}</a>
      <!-- {if $CURRENT.TEXT} -->
        <span class="search_hit_description">{$CURRENT.TEXT}</span>
      <!-- {/if} -->
        <span class="search_hit_url">{$CURRENT.URL}</span>
      </div>
 <!-- {if ($nr % $ENTRIES_PER_PAGE === $ENTRIES_PER_PAGE - 1)  || $smarty.foreach.result.last}
       {assign var="nr" value=$nr-$ENTRIES_PER_PAGE+1}-->

      <div class="search_result_foot">
      <!-- {if ($nr / $ENTRIES_PER_PAGE) gt 0 } -->
        <a href="javascript:show({$nr/$ENTRIES_PER_PAGE}-1)" target="_self">
          {lang id="BUTTON_PREVIOUS"}
        </a>
      <!-- {else} -->
        <a>
          &nbsp;
        </a>
      <!-- {/if} -->
      <!-- {if $nr lt $smarty.foreach.result.total - $ENTRIES_PER_PAGE } -->
        <a href="javascript:show({$nr/$ENTRIES_PER_PAGE}+1)" target="_self">
          {lang id="BUTTON_NEXT"}
        </a>
      <!-- {else} -->
        <a>
          &nbsp;
        </a>
      <!-- {/if} -->
      </div>

    </div>
 <!-- {/if} -->
{/foreach}
  </div>
  <script type="text/javascript">show(0)</script>
  <!-- {/if} -->
  <!-- {/if} -->

</body>

</html>
