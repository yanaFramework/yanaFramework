<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Colorpicker</title>
    <meta http-equiv="imagetoolbar" content="no"/>
    <link rel="stylesheet" type="text/css" href="default.css"/>
    <script type="text/javascript" src="program.js"></script>
</head>

<body>
<div id="colorpicker">

    <div id="current_colors" style="float: right; width: 150px;">
        <div class="color_label" style="font-weight:bold">{lang id="COLORS.CURRENT"}:</div>
        <div id="color_preview" style="width:82px;height:50px;border: 3px outset #EEEEEE">&nbsp;</div>
        <div class="color_label">Hex: <code id="hex_old">&nbsp;</code></div>
        <div class="color_label" style="font-weight:bold">{lang id="COLORS.NEW"}:</div>
        <div id="color" style="width:82px;height:50px;border: 3px outset #EEEEEE" onclick="fixColor()">&nbsp;</div>
        <div class="color_label">Hex: <code id="hex_new">&nbsp;</code></div>
        <div id="color_link" class="help">
            <a href={"action=colormap"|href} target="_self" class="comment">{lang id="COLORS.MORE"}</a>
        </div>
    </div>
    <div class="description" id="color_panes">
        <img style="margin-left: 5px;  height: 256px; width: 240px;" src="color.jpg" onmousemove="moveColor(event)" onclick="pickColor(event)" onmouseout="resetColor(event)"/>
        <img style="margin-left: 10px; height: 256px; width: 10px;"  src="sw.jpg"    onmousemove="moveSw(event)"    onclick="pickSw(event)"    onmouseout="resetColor(event)"/>
        <br />
        <div id="color_display" class="comment help">
            {lang id="COLORS.HUE"}:   <input id="hue"   size="3" maxlength="3" onblur="setHue(this.value)"/>
            {lang id="COLORS.LIGHT"}: <input id="light" size="3" maxlength="4" onblur="setLight(this.value)"/>
            {lang id="COLORS.SAT"}:   <input id="sat"   size="3" maxlength="3" onblur="setSat(this.value)"/><br />
            {lang id="COLORS.R"}:     <input id="red"   size="3" maxlength="3" onblur="setRed(this.value)"/>
            {lang id="COLORS.G"}:     <input id="green" size="3" maxlength="3" onblur="setGreen(this.value)"/>
            {lang id="COLORS.B"}:     <input id="blue"  size="3" maxlength="3" onblur="setBlue(this.value)"/>
            <button type="button" onmouseover="parent.over=true" onmouseout="parent.over=false" onclick="setColor('{lang id="COLORS.PROMPT"}')">
                hex
            </button>
        </div>
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
</body>

</html>
