<div class="gui_preview">
    <div id="{$ID}">&nbsp;</div>

    <input title="{lang id="TITLE_PREVIEW"}" type="button" value="{lang id="PREVIEW"}"
        onclick="
            window.yanaPreviewHeight['{$ID}'] = '{$HEIGHT|default:'50px'}';
            window.yanaPreviewWidth['{$ID}'] = '{$WIDTH|default:'380px'}';
            if (typeof window.yanaPreview == 'undefined' || typeof window.yanaPreview['{$ID}'] == 'undefined') yanaPreviewStart('{$ID}');
            yanaPreviewSend('{$ID}')"
    />

    <script type="text/javascript" language="JavaScript">
    <!--
        if (!noselection) var noselection = "{lang id="TAGS.NOSELECTION"}";
        if (!preview_js) var preview_js = "{lang id="PREVIEW_JS"}";
        if (!php_self) var php_self = "{$PHP_SELF}";
        if (!window.yanaPreviewHeight) window.yanaPreviewHeight = new Array();
        if (!window.yanaPreviewWidth) window.yanaPreviewWidth = new Array();

    //-->
    </script>
</div>
