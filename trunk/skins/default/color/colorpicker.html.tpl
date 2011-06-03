{*

	@author Mathias Weitz
	Smarty template
*}

<a target="_blank" onclick="showColorPicker(event, '{$target}'); return false;"
    href={"action=colorpicker"|href}><img border="0" alt="" hspace="5" src="button.jpg"/></a>
<script type="text/javascript" src="colorpicker.js"></script>
<script type="text/javascript">// <![CDATA[
if (!document.getElementById('_colorpicker')) {
    document.write('<div id="_colorpicker" style="display: none;" class="floating_menu"></div>');
    var colorPicker = new AjaxRequest(document.location.href);
    colorPicker.setTarget('_colorpicker');
    colorPicker.send('action=colorpicker');
}
// ]]>
</script>