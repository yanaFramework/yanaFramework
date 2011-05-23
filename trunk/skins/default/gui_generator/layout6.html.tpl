<div class="gui_generator_gallery_content">    
    {for $i=1 to max($form->getRowCount(), ! $form->hasRows())}
        <div class="gui_generator_gallery_entry">
            <div class="gui_generator_gallery_image">
                {foreach from=$form item="field"}
                    {if $field->getType() == 'image'}{assign var="hasImage" value=true}
                        <label title="{$field->getTitle()}">{$field}</label>
                    {/if}
                {/foreach}
                {if !$hasImage}
                    <a class="gui_generator_dummyimage" title='{lang id="TITLE_DETAILS"}'
                       href={"action=$ACTION&{$form->getName()}[entries]=1"|href}>&nbsp;</a>
                {/if}
            </div>
            <div class="gui_generator_gallery_title">
                {foreach from=$form item="field"}
                    {if ($field->getType() == 'string' || $field->getType() == 'file')}
                        <label title="{$field->getTitle()}">{$field}</label>
                    {/if}
                {/foreach}
            </div>
        </div>
        {if $form->hasRows()}
            {$form->nextRow()}
        {/if}
    {forelse}
        <div class="gui_generator_no_entries_found">{lang id="NO_ENTRIES_FOUND"}</div>
    {/for}
    <script type="text/javascript"><!--
        $(function() {
            $('.gui_generator_image a').fancybox();
        });
    //--></script>
</div>