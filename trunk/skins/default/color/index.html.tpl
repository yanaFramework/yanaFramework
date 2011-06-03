<div id="colorpicker">
    <div id="color_panes">
        <img style="margin-left: 5px;  height: 256px; width: 240px; border: 1px outset #eee;" src="color.jpg" onmousemove="moveColor(event)" onclick="pickColor(event)" onmouseout="resetColor(event)"/>
        <img style="margin-left: 10px; height: 256px; width: 10px; border: 1px outset #eee;"  src="sw.jpg"    onmousemove="moveSw(event)"    onclick="pickSw(event)"    onmouseout="resetColor(event)"/>
    </div>
    <div id="color" style="display: inline-block; text-align: center; width:80px; border: 1px outset #eee;">&nbsp;</div>
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
showColor();
//--></script>