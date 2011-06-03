<div id="colorpicker">

    <div id="current_colors" style="float: right; width: 150px;">
        <div class="color_label" style="font-weight:bold">{lang id="COLORS.CURRENT"}:</div>
        <div id="color_preview" style="width:82px;height:50px;border: 3px outset #EEEEEE">&nbsp;</div>
        <div class="color_label">Hex: <code id="hex_old">&nbsp;</code></div>
        <div class="color_label" style="font-weight:bold">{lang id="COLORS.NEW"}:</div>
        <div id="color" style="width:82px;height:50px;border: 3px outset #EEEEEE" onclick="fixColor()">&nbsp;</div>
        <div class="color_label">Hex: <code id="hex_new">&nbsp;</code></div>
    </div>
    <div class="description" id="color_panes">
        <img style="margin-left: 5px;  height: 256px; width: 240px;" src="color.jpg" onmousemove="moveColor(event)" onclick="pickColor(event)" onmouseout="resetColor(event)"/>
        <img style="margin-left: 10px; height: 256px; width: 10px;"  src="sw.jpg"    onmousemove="moveSw(event)"    onclick="pickSw(event)"    onmouseout="resetColor(event)"/>
    </div>
    {if $is_ajax_request}
        <button id="color_submit" type="button" onmouseover="parent.over=true" onmouseout="parent.over=false">
            {lang id="OK"}
        </button>
        <button id="color_abort" type="button" onmouseover="parent.over=true" onmouseout="parent.over=false">
            {lang id="BUTTON_ABORT"}
        </button>
    {/if}
</div>
<script type="text/javascript" language="javascript"><!--
fixColor();
showColor();
//--></script>