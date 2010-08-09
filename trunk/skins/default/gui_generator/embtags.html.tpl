<div class="embtags__list">
{foreach from=$TAGS item=tag}
    <!-- {if $USER_DEFINED.$tag }
         {assign var="id" value=$tag|upper} -->
        <span class="embtag embtag_{$tag}">
            <a title="{$USER_DEFINED.$tag.TITLE}" href="javascript://[{$tag}]">
                <img alt="[{$tag}]" onmousedown="yanaAddEmbTag('[{$tag}\]{$USER_DEFINED.$tag.TEXT}[/{$tag}]', event)" src="{$USER_DEFINED.$tag.IMAGE}"/>
            </a>
        </span>
    <!-- {elseif $tag == "|" } -->
        <img class="embtag_space" alt="[|]" src="{$DATADIR}blank.gif"/>
    <!-- {elseif $tag == "-" } -->
        <br />
    <!-- {elseif $tag == "smilies" } -->
        <div class="embtag">
            <span class="embtag_smilies">
                <a title="{lang id="TAGS.TITLE.SMILIES"}" href="javascript://[smilies]">
                    <img alt="[smilies]" src="{$DATADIR}blank.gif"/>
                </a>
            </span>
            <div class="embtag_smilies_list">{smilies width="5"}</div>
        </div>
    <!-- {elseif $tag == "mark" } -->
        <span class="embtag embtag_mark">
            <span class="embtag_nobreak">
                <a title="{lang id="TAGS.TITLE.MARK"}" href="javascript://[mark]">
                    <img alt="[mark]" onmousedown="yanaAddEmbTag('[mark'+mark+']{lang id="TAGS.TEXT.MARK"}[/mark]', event)" src="{$DATADIR}blank.gif"/>
                </a>
                <select onchange="mark=this.options[this.selectedIndex].value">
                    <option value="=white" style="background: #ffffff">{lang id="TAGS.COLORS.3"}</option>
                    <option value="" selected="selected" style="background: yellow">{lang id="TAGS.COLORS.6"}</option>
                    <option value="=orange" style="background: orange">{lang id="TAGS.COLORS.10"}</option>
                    <option value="=chartreuse" style="background: chartreuse">{lang id="TAGS.COLORS.5"}</option>
                    <option value="=aqua" style="background: aqua">{lang id="TAGS.COLORS.7"}</option>
                    <option value="=violet" style="background: violet">{lang id="TAGS.COLORS.8"}</option>
                </select>
                <script type="text/javascript" language="JavaScript"><!--
                var mark = "";
                //--></script>
            </span>
        </span>
    <!-- {elseif $tag == "color" } -->
        <span class="embtag embtag_color">
            <span class="embtag_nobreak">
                <a title="{lang id="TAGS.TITLE.COLOR"}" href="javascript://[color]">
                    <img alt="[color]" onmousedown="yanaAddEmbTag('[color'+color+']{lang id="TAGS.TEXT.COLOR"}[/color]', event)" src="{$DATADIR}blank.gif"/>
                </a>
                <select onchange="color=this.options[this.selectedIndex].value">
                    <option value="=black" selected="selected" style="color: #000000">{lang id="TAGS.COLORS.0"}</option>
                    <option value="=gray" style="color: #888888">{lang id="TAGS.COLORS.1"}</option>
                    <option value="=silver" style="color: silver">{lang id="TAGS.COLORS.2"}</option>
                    <option value="=red" style="color: red">{lang id="TAGS.COLORS.4"}</option>
                    <option value="=gold" style="color: gold">{lang id="TAGS.COLORS.6"}</option>
                    <option value="=green" style="color: green">{lang id="TAGS.COLORS.5"}</option>
                    <option value="=royalblue" style="color: royalblue">{lang id="TAGS.COLORS.7"}</option>
                    <option value="=darkorchid" style="color: darkorchid">{lang id="TAGS.COLORS.8"}</option>
                    <option value="=sienna" style="color: sienna">{lang id="TAGS.COLORS.9"}</option>
                </select>
                <script type="text/javascript" language="JavaScript"><!--
                var color = "";
                //--></script>
            </span>
        </span>
    <!-- {elseif $tag == "br" || $tag == "wbr" }
         {assign var="id" value=$tag|upper} -->
        <span class="embtag embtag_{$tag}">
            <a title="{$LANGUAGE.TAGS.TITLE.$id}" href="javascript://[{$tag}]">
                <img alt="[{$tag}]" onmousedown="yanaAddEmbTag('[{$tag}]', event)" src="{$DATADIR}blank.gif"/>
            </a>
        </span>
    <!-- {else}
         {assign var="id" value=$tag|upper} -->
        <span class="embtag embtag_{$tag}">
            <a title="{$LANGUAGE.TAGS.TITLE.$id}" href="javascript://[{$tag}]">
                <img alt="[{$tag}]" onmousedown="yanaAddEmbTag('[{$tag}\]{$LANGUAGE.TAGS.TEXT.$id}[/{$tag}]', event)" src="{$DATADIR}blank.gif"/>
            </a>
        </span>
    <!-- {/if} -->
{/foreach}
</div>

<script type="text/javascript" language="JavaScript">
<!--
    if (!noselection) var noselection = "{lang id="TAGS.NOSELECTION"}";
    if (!preview_js) var preview_js = "{lang id="PREVIEW_JS"}";
    if (!php_self) var php_self = "{$PHP_SELF}";
// -->
</script>
