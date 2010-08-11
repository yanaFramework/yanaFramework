<!DOCTYPE xliff PUBLIC "-//XLIFF//DTD XLIFF//EN" "http://www.oasis-open.org/committees/xliff/documents/xliff.dtd">
<xliff version="1.0">
    <file source-language="{$source}" datatype="html" original="" target-language="{$target}">
        <header/>
        <body>
{foreach from=$translations key="id" item="unit"}
            <trans-unit id="{$id}">
{if $unit.source && ($source != $target)}
                <source>{$unit.source}</source>
{else}
                <source/>
{/if}
{if $unit.target}
                <target>{$unit.target}</target>
{elseif $unit.source}
                <target>{$unit.source}</target>
{/if}
            </trans-unit>
{/foreach}
        </body>
    </file>
</xliff>
