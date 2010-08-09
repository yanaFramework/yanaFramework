<div class="slider_input">
    <input type="text" value="{$value}" maxlength="4" id="{$sliderId}_input" name="{$inputName}" onchange="slider.instance['{$sliderId}'].setSlider(this.id);" />
</div>
<script type="text/javascript">
    (new slider('{$sliderId}', {$width}, {$min}, {$max}, {$step}, {$value}, '{$inputName}', '{$background}')).setSlider('{$sliderId}_input');
</script>